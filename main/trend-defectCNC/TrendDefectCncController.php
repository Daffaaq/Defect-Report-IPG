<?php
session_start();
include '../../helper/connection.php';
header("Content-Type: application/json");

// Matikan error reporting ke output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set handler untuk error fatal
register_shutdown_function('handleShutdown');
set_error_handler('handleError');

function handleShutdown()
{
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan internal server',
            'debug' => $error['message']
        ]);
        exit;
    }
}

function handleError($errno, $errstr, $errfile, $errline)
{
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
    exit;
}

if ($connection === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? '';

            if (empty($action)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
                exit;
            }

            handleTrendDefectCncActions($connection, $action);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
    exit;
}

// ==========================
// FUNGSI HELPER
// ==========================
function formatMachineCode($code)
{
    if (empty($code)) {
        return null; // atau return '';
    }

    return str_replace('CSM', 'CNC', $code);
}
// ==========================
// TREND DEFECT CNC HANDLERS
// ==========================

function handleTrendDefectCncActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getDashboardStats':
                getDashboardStats($connection);
                break;
            case 'getTrendChart':
                getTrendChart($connection);
                break;
            case 'getParetoChart':
                getParetoChart($connection);
                break;
            case 'getMachineHistory':
                getMachineHistory($connection);
                break;
            case 'getDetailData':
                getDetailData($connection);
                break;
            case 'getServerTime':
                getServerTime($connection);
                break;
            case 'getDefectRatioChart':
                getDefectRatioChart($connection);
                break;
            case 'getTopDefectTypes':
                getTopDefectTypes($connection);
                break;
            case 'getDefectTypeDetail':
                getDefectTypeDetail($connection);
                break;
            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
                break;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// ==========================
// GET TOP 3 DEFECT TYPES
// ==========================
function getTopDefectTypes($connection)
{
    header('Content-Type: application/json');

    try {
        // Query untuk mengambil TOP 3 jenis defect terbanyak dalam 1 bulan
        $sql = "
            SELECT TOP 3
                JenisDefect,
                COUNT(*) AS JumlahDefect,
                SUM(Qty) AS TotalQty,
                CAST(AVG(Qty * 1.0) AS DECIMAL(10,2)) AS RataRataQty
            FROM CNCDefect
            WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                  DATEADD(MONTH, -1, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
            GROUP BY JenisDefect
            ORDER BY JumlahDefect DESC, TotalQty DESC
        ";

        $stmt = sqlsrv_query($connection, $sql);
        if ($stmt === false) {
            throw new Exception("Gagal query top defect types: " . print_r(sqlsrv_errors(), true));
        }

        $topDefects = [];
        $rank = 1;
        
        // Ambil total semua defect untuk perhitungan persentase
        $sqlTotal = "
            SELECT COUNT(*) AS TotalDefect
            FROM CNCDefect
            WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                  DATEADD(MONTH, -1, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
        ";
        $stmtTotal = sqlsrv_query($connection, $sqlTotal);
        $totalDefect = 0;
        if ($stmtTotal !== false) {
            $rowTotal = sqlsrv_fetch_array($stmtTotal, SQLSRV_FETCH_ASSOC);
            $totalDefect = (int) $rowTotal['TotalDefect'];
            sqlsrv_free_stmt($stmtTotal);
        }

        $defectColors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $percentage = $totalDefect > 0 ? round(($row['JumlahDefect'] / $totalDefect) * 100, 1) : 0;
            
            $topDefects[] = [
                'rank' => $rank,
                'jenis_defect' => $row['JenisDefect'],
                'jumlah_defect' => (int) $row['JumlahDefect'],
                'total_qty' => (int) $row['TotalQty'],
                'rata_rata_qty' => (float) $row['RataRataQty'],
                'persentase' => $percentage,
                'color' => $defectColors[$rank - 1] ?? '#6c757d'
            ];
            
            $rank++;
        }
        sqlsrv_free_stmt($stmt);

        // Jika tidak ada data
        if (empty($topDefects)) {
            $topDefects = [
                [
                    'rank' => 1,
                    'jenis_defect' => 'Tidak ada data',
                    'jumlah_defect' => 0,
                    'total_qty' => 0,
                    'rata_rata_qty' => 0,
                    'persentase' => 0,
                    'color' => '#6c757d'
                ]
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Data top 3 defect types berhasil dimuat',
            'data' => [
                'top_defects' => $topDefects,
                'periode' => '1 Bulan Terakhir',
                'total_defect' => $totalDefect
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat top defect types: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET DETAIL DEFECT TYPE
// ==========================
function getDefectTypeDetail($connection)
{
    header('Content-Type: application/json');

    try {
        $defectType = $_GET['defect_type'] ?? '';
        
        if (empty($defectType)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Jenis defect tidak valid']);
            exit;
        }

        // Ambil semua data defect berdasarkan jenis
        $sql = "
            SELECT 
                CAST(DATEADD(HOUR, -6, CreatedAt) AS DATE) AS TanggalProduksi,
                DATENAME(WEEKDAY, DATEADD(HOUR, -6, CreatedAt)) AS Hari,
                KodeMesin,
                Qty,
                CreatedAt
            FROM CNCDefect
            WHERE JenisDefect = ?
              AND DATEADD(HOUR, -6, CreatedAt) >= 
                  DATEADD(MONTH, -1, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
            ORDER BY CreatedAt DESC
        ";

        $stmt = sqlsrv_query($connection, $sql, array($defectType));
        if ($stmt === false) {
            throw new Exception("Gagal query detail defect type: " . print_r(sqlsrv_errors(), true));
        }

        $details = [];
        $totalQty = 0;
        $totalDefect = 0;
        $machineData = [];
        
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $tanggal = $row['TanggalProduksi'];
            if ($tanggal instanceof DateTime) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            
            $createdAt = $row['CreatedAt'];
            if ($createdAt instanceof DateTime) {
                $createdAt = $createdAt->format('Y-m-d H:i:s');
            }
            
            $qty = (int) $row['Qty'];
            $totalQty += $qty;
            $totalDefect++;
            
            $machineCode = formatMachineCode($row['KodeMesin']);
            if (!isset($machineData[$machineCode])) {
                $machineData[$machineCode] = 0;
            }
            $machineData[$machineCode] += $qty;
            
            $details[] = [
                'tanggal_produksi' => $tanggal,
                'hari' => $days[$row['Hari']] ?? $row['Hari'],
                'kode_mesin' => $machineCode,
                'qty' => $qty,
                'created_at' => $createdAt
            ];
        }
        sqlsrv_free_stmt($stmt);

        // Hitung statistik
        $avgQty = count($details) > 0 ? round($totalQty / count($details), 2) : 0;
        $maxQty = count($details) > 0 ? max(array_column($details, 'qty')) : 0;
        $minQty = count($details) > 0 ? min(array_column($details, 'qty')) : 0;

        // Data mesin penyumbang
        arsort($machineData);
        $topMachines = array_slice($machineData, 0, 5, true);

        // Kelompokkan berdasarkan tanggal
        $dailyData = [];
        foreach ($details as $detail) {
            $date = $detail['tanggal_produksi'];
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    'tanggal' => $date,
                    'total_qty' => 0,
                    'jumlah_defect' => 0,
                    'hari' => $detail['hari']
                ];
            }
            $dailyData[$date]['total_qty'] += $detail['qty'];
            $dailyData[$date]['jumlah_defect']++;
        }
        ksort($dailyData);
        $dailyData = array_values($dailyData);

        // Data mesin untuk chart
        $machineChartData = [];
        foreach ($topMachines as $machine => $qty) {
            $machineChartData[] = [
                'machine' => $machine,
                'total_qty' => $qty
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Data detail defect type berhasil dimuat',
            'data' => [
                'defect_type' => $defectType,
                'total_defect' => $totalDefect,
                'total_qty' => $totalQty,
                'rata_rata_qty' => $avgQty,
                'max_qty' => $maxQty,
                'min_qty' => $minQty,
                'periode' => '1 Bulan Terakhir',
                'details' => $details,
                'daily_summary' => $dailyData,
                'top_machines' => $machineChartData
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat detail defect type: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET DASHBOARD STATS
// ==========================
function getDashboardStats($connection)
{
    header('Content-Type: application/json');

    try {
        // 1. TOTAL DEFECT 7 HARI
        $sql_defect = "SELECT 
                            ISNULL(COUNT(*), 0) AS total_defect,
                            ISNULL(SUM(Qty), 0) AS total_qty
                       FROM CNCDefect
                       WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                             DATEADD(DAY, -7, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))";
        
        $stmt_defect = sqlsrv_query($connection, $sql_defect);
        if ($stmt_defect === false) {
            throw new Exception("Gagal query total defect: " . print_r(sqlsrv_errors(), true));
        }
        $row_defect = sqlsrv_fetch_array($stmt_defect, SQLSRV_FETCH_ASSOC);
        $total_defect = (int) $row_defect['total_defect'];
        $total_qty = (int) $row_defect['total_qty'];
        sqlsrv_free_stmt($stmt_defect);

        // 2. MESIN DENGAN DEFECT TERBANYAK
        $sql_machine = "SELECT TOP 1
                            KodeMesin,
                            COUNT(*) AS JumlahDefect,
                            SUM(Qty) AS TotalQty
                        FROM CNCDefect
                        WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                              DATEADD(DAY, -7, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
                        GROUP BY KodeMesin
                        ORDER BY TotalQty DESC";
        
        $stmt_machine = sqlsrv_query($connection, $sql_machine);
        if ($stmt_machine === false) {
            throw new Exception("Gagal query top machine: " . print_r(sqlsrv_errors(), true));
        }
        $row_machine = sqlsrv_fetch_array($stmt_machine, SQLSRV_FETCH_ASSOC);
        $top_machine = formatMachineCode($row_machine['KodeMesin'] ?? '-');
        sqlsrv_free_stmt($stmt_machine);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dimuat',
            'data' => [
                'total_defect' => $total_defect,
                'total_qty' => $total_qty,
                'top_machine' => $top_machine
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// CHART: Trend Defect 7 Hari
// ==========================
function getTrendChart($connection)
{
    header('Content-Type: application/json');

    try {
        $dates = [];
        $defectData = [];
        $qtyData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateFormatted = date('d M', strtotime($date));
            $dates[] = $dateFormatted;
            
            $sql = "SELECT 
                        ISNULL(COUNT(*), 0) AS jumlah_defect,
                        ISNULL(SUM(Qty), 0) AS total_qty
                    FROM CNCDefect
                    WHERE CAST(DATEADD(HOUR, -6, CreatedAt) AS DATE) = ?";
            
            $stmt = sqlsrv_query($connection, $sql, array($date));
            if ($stmt === false) {
                throw new Exception("Gagal query trend: " . print_r(sqlsrv_errors(), true));
            }
            
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $defectData[] = (int) $row['jumlah_defect'];
            $qtyData[] = (int) $row['total_qty'];
            sqlsrv_free_stmt($stmt);
        }

        $chartData = [
            'categories' => $dates,
            'defect_data' => $defectData,
            'qty_data' => $qtyData
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data trend chart berhasil dimuat',
            'data' => $chartData
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat trend chart: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// CHART: Pareto Defect per Mesin
// ==========================
function getParetoChart($connection)
{
    header('Content-Type: application/json');

    try {
        $sql = "SELECT 
                    KodeMesin,
                    COUNT(*) AS JumlahDefect,
                    SUM(Qty) AS TotalQty
                FROM CNCDefect
                WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                      DATEADD(DAY, -7, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
                GROUP BY KodeMesin
                ORDER BY TotalQty DESC";
        
        $stmt = sqlsrv_query($connection, $sql);
        if ($stmt === false) {
            throw new Exception("Gagal query pareto: " . print_r(sqlsrv_errors(), true));
        }

        $machines = [];
        $totalQty = [];
        $totalDefect = [];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $machines[] = formatMachineCode($row['KodeMesin']);
            $totalQty[] = (int) $row['TotalQty'];
            $totalDefect[] = (int) $row['JumlahDefect'];
        }
        sqlsrv_free_stmt($stmt);

        $chartData = [
            'machines' => $machines,
            'total_qty' => $totalQty,
            'total_defect' => $totalDefect
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data pareto chart berhasil dimuat',
            'data' => $chartData
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat pareto chart: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET MACHINE HISTORY 30 HARI
// ==========================
function getMachineHistory($connection)
{
    header('Content-Type: application/json');

    try {
        $machine = $_GET['machine'] ?? '';
        
        if (empty($machine)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Machine tidak valid']);
            exit;
        }

        $machineQuery = str_replace('CNC', 'CSM', $machine);

        $categories = [];
        $defectData = [];
        $qtyData = [];
        $dailyData = [];
        $totalDefect = 0;
        $totalQty = 0;
        
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        $sql = "SELECT 
                    CAST(DATEADD(HOUR, -6, CreatedAt) AS DATE) AS TanggalProduksi,
                    DATENAME(WEEKDAY, DATEADD(HOUR, -6, CreatedAt)) AS Hari,
                    COUNT(*) AS JumlahDefect,
                    SUM(Qty) AS TotalQty
                FROM CNCDefect
                WHERE KodeMesin = ?
                  AND DATEADD(HOUR, -6, CreatedAt) >= 
                      DATEADD(DAY, -30, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
                GROUP BY CAST(DATEADD(HOUR, -6, CreatedAt) AS DATE),
                         DATENAME(WEEKDAY, DATEADD(HOUR, -6, CreatedAt))
                ORDER BY TanggalProduksi ASC";
        
        $stmt = sqlsrv_query($connection, $sql, array($machineQuery));
        if ($stmt === false) {
            throw new Exception("Gagal query machine history: " . print_r(sqlsrv_errors(), true));
        }

        $historyMap = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $tanggal = $row['TanggalProduksi'];
            if ($tanggal instanceof DateTime) {
                $tanggal = $tanggal->format('Y-m-d');
            }
            $historyMap[$tanggal] = [
                'hari' => $days[$row['Hari']] ?? $row['Hari'],
                'jumlah_defect' => (int) $row['JumlahDefect'],
                'total_qty' => (int) $row['TotalQty']
            ];
            
            $totalDefect += (int) $row['JumlahDefect'];
            $totalQty += (int) $row['TotalQty'];
        }
        sqlsrv_free_stmt($stmt);

        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateFormatted = date('d M', strtotime($date));
            $categories[] = $dateFormatted;
            
            if (isset($historyMap[$date])) {
                $defectData[] = $historyMap[$date]['jumlah_defect'];
                $qtyData[] = $historyMap[$date]['total_qty'];
                $dailyData[] = [
                    'tanggal' => date('d/m/Y', strtotime($date)),
                    'hari' => $historyMap[$date]['hari'],
                    'jumlah_defect' => $historyMap[$date]['jumlah_defect'],
                    'total_qty' => $historyMap[$date]['total_qty']
                ];
            } else {
                $defectData[] = 0;
                $qtyData[] = 0;
                $dailyData[] = [
                    'tanggal' => date('d/m/Y', strtotime($date)),
                    'hari' => $days[date('l', strtotime($date))] ?? date('l', strtotime($date)),
                    'jumlah_defect' => 0,
                    'total_qty' => 0
                ];
            }
        }

        $chartData = [
            'machine' => $machine,
            'total_defect' => $totalDefect,
            'total_qty' => $totalQty,
            'categories' => $categories,
            'defect_data' => $defectData,
            'qty_data' => $qtyData,
            'daily_data' => $dailyData
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data machine history berhasil dimuat',
            'data' => $chartData
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat machine history: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET DETAIL DATA
// ==========================
function getDetailData($connection)
{
    header('Content-Type: application/json');

    try {
        $type = $_GET['type'] ?? '';
        
        if (empty($type)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Type tidak valid']);
            exit;
        }

        $sql_detail = "";
        $params = [];

        switch ($type) {
            case 'all':
                $sql_detail = "SELECT 
                                    CAST(DATEADD(HOUR, -6, CreatedAt) AS DATE) AS tanggal_produksi,
                                    KodeMesin,
                                    Qty,
                                    CreatedAt
                                FROM CNCDefect
                                WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                                      DATEADD(DAY, -7, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
                                ORDER BY CreatedAt DESC";
                break;

            case 'qty':
                $sql_detail = "SELECT 
                                    CAST(DATEADD(HOUR, -6, CreatedAt) AS DATE) AS tanggal_produksi,
                                    KodeMesin,
                                    Qty,
                                    CreatedAt
                                FROM CNCDefect
                                WHERE DATEADD(HOUR, -6, CreatedAt) >= 
                                      DATEADD(DAY, -7, CAST(DATEADD(HOUR, -6, GETDATE()) AS DATE))
                                ORDER BY Qty DESC, CreatedAt DESC";
                break;

            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Type tidak dikenal']);
                exit;
        }

        $stmt_detail = sqlsrv_query($connection, $sql_detail, $params);
        if ($stmt_detail === false) {
            throw new Exception("Gagal query detail data: " . print_r(sqlsrv_errors(), true));
        }

        $data = [];
        while ($row = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC)) {
            $tanggalProduksi = $row['tanggal_produksi'];
            if ($tanggalProduksi instanceof DateTime) {
                $tanggalProduksi = $tanggalProduksi->format('Y-m-d');
            }
            
            $createdAt = $row['CreatedAt'];
            if ($createdAt instanceof DateTime) {
                $createdAt = $createdAt->format('Y-m-d H:i:s');
            }
            
            $data[] = [
                'tanggal_produksi' => $tanggalProduksi,
                'kode_mesin' => formatMachineCode($row['KodeMesin']),
                'qty' => (int) $row['Qty'],
                'created_at' => $createdAt
            ];
        }
        sqlsrv_free_stmt($stmt_detail);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data detail berhasil dimuat',
            'data' => $data,
            'total_rows' => count($data)
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat detail data: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET SERVER TIME
// ==========================
function getServerTime($connection)
{
    header('Content-Type: application/json');
    
    try {
        $sql = "SELECT GETDATE() AS server_time";
        $stmt = sqlsrv_query($connection, $sql);
        
        if ($stmt === false) {
            throw new Exception("Gagal mengambil waktu server: " . print_r(sqlsrv_errors(), true));
        }
        
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $serverTime = $row['server_time'];
        
        if ($serverTime instanceof DateTime) {
            $serverTime = $serverTime->format('Y-m-d H:i:s');
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'server_time' => $serverTime,
                'timezone' => 'Asia/Jakarta'
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengambil waktu server: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET DEFECT RATIO CHART
// ==========================
function getDefectRatioChart($connection)
{
    header('Content-Type: application/json');

    try {
        $sql = "
            WITH QtyHarian AS
            (
                SELECT
                    CAST(
                        DATEADD(
                            HOUR,-6,
                            DATEADD(
                                SECOND,
                                (
                                    CAST(SUBSTRING(JamSelesai,1,2) AS INT) * 3600 +
                                    CAST(SUBSTRING(JamSelesai,4,2) AS INT) * 60 +
                                    CAST(SUBSTRING(JamSelesai,7,2) AS INT)
                                ),
                                TanggalHasil
                            )
                        )
                    AS DATE) AS TanggalProduksi,

                    CAST(SUM(QtyHasil) AS INT) AS SumQtyHasil

                FROM trrphmesin
                WHERE JamSelesai IS NOT NULL
                  AND LEN(JamSelesai)=8
                  AND SUBSTRING(JamSelesai,3,1)=':'
                  AND SUBSTRING(JamSelesai,6,1)=':'

                  AND DATEADD(
                        HOUR,-6,
                        DATEADD(
                            SECOND,
                            (
                                CAST(SUBSTRING(JamSelesai,1,2) AS INT) * 3600 +
                                CAST(SUBSTRING(JamSelesai,4,2) AS INT) * 60 +
                                CAST(SUBSTRING(JamSelesai,7,2) AS INT)
                            ),
                            TanggalHasil
                        )
                      ) >= DATEADD(
                            MONTH,-1,
                            DATEADD(HOUR,-6,GETDATE())
                      )

                GROUP BY
                    CAST(
                        DATEADD(
                            HOUR,-6,
                            DATEADD(
                                SECOND,
                                (
                                    CAST(SUBSTRING(JamSelesai,1,2) AS INT) * 3600 +
                                    CAST(SUBSTRING(JamSelesai,4,2) AS INT) * 60 +
                                    CAST(SUBSTRING(JamSelesai,7,2) AS INT)
                                ),
                                TanggalHasil
                            )
                        )
                    AS DATE)
            ),

            DefectHarian AS
            (
                SELECT
                    CAST(
                        DATEADD(HOUR,-6,CreatedAt)
                    AS DATE) AS TanggalProduksi,

                    SUM(Qty) AS SumDefect

                FROM CNCDefect
                WHERE DATEADD(HOUR,-6,CreatedAt) >=
                      DATEADD(
                            MONTH,-1,
                            DATEADD(HOUR,-6,GETDATE())
                      )

                GROUP BY
                    CAST(DATEADD(HOUR,-6,CreatedAt) AS DATE)
            )

            SELECT
                d.TanggalProduksi,
                ISNULL(q.SumQtyHasil,0) AS SumQtyHasil,
                d.SumDefect,

                CAST(
                    d.SumDefect * 100.0 /
                    NULLIF(
                        ISNULL(q.SumQtyHasil,0) + d.SumDefect,
                        0
                    )
                AS DECIMAL(10,2)) AS RasioDefectPersen

            FROM DefectHarian d
            LEFT JOIN QtyHarian q
            ON d.TanggalProduksi = q.TanggalProduksi

            ORDER BY d.TanggalProduksi DESC
        ";

        $stmt = sqlsrv_query($connection, $sql);
        if ($stmt === false) {
            throw new Exception("Gagal query defect ratio chart: " . print_r(sqlsrv_errors(), true));
        }

        $data = [];
        $categories = [];
        $qtyData = [];
        $defectData = [];
        $ratioData = [];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $tanggal = $row['TanggalProduksi'];
            if ($tanggal instanceof DateTime) {
                $tanggal = $tanggal->format('Y-m-d');
            }

            $data[] = [
                'tanggal' => $tanggal,
                'qty' => (int) $row['SumQtyHasil'],
                'defect' => (int) $row['SumDefect'],
                'ratio' => (float) $row['RasioDefectPersen']
            ];
        }
        sqlsrv_free_stmt($stmt);

        $data = array_reverse($data);

        foreach ($data as $item) {
            $categories[] = date('d M', strtotime($item['tanggal']));
            $qtyData[] = $item['qty'];
            $defectData[] = $item['defect'];
            $ratioData[] = $item['ratio'];
        }

        $chartData = [
            'categories' => $categories,
            'qty_data' => $qtyData,
            'defect_data' => $defectData,
            'ratio_data' => $ratioData,
            'daily_data' => $data
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data defect ratio chart berhasil dimuat',
            'data' => $chartData
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat defect ratio chart: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>