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

            handleTrendDefectActions($connection, $action);
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
// HELPER FUNCTIONS
// ==========================

/**
 * Mendapatkan range tanggal untuk 1 shift produksi (6:00 - 18:00 atau 18:00 - 06:00)
 * 
 * @return array ['start_date' => 'Y-m-d H:i:s', 'end_date' => 'Y-m-d H:i:s']
 */
function getProductionDateRange()
{
    date_default_timezone_set('Asia/Jakarta');
    $now = new DateTime('now');
    $today = clone $now;
    $today->setTime(0, 0, 0);
    
    // Cek apakah sekarang sebelum jam 6 pagi
    $hour = (int)$now->format('H');
    
    if ($hour < 6) {
        // Shift malam sebelumnya (18:00 - 06:00)
        $startDate = clone $today;
        $startDate->modify('-1 day');
        $startDate->setTime(18, 0, 0);
        
        $endDate = clone $today;
        $endDate->setTime(6, 0, 0);
    } else {
        // Shift siang hari ini (06:00 - 18:00)
        $startDate = clone $today;
        $startDate->setTime(6, 0, 0);
        
        $endDate = clone $today;
        $endDate->setTime(18, 0, 0);
    }
    
    return [
        'start_date' => $startDate->format('Y-m-d H:i:s'),
        'end_date' => $endDate->format('Y-m-d H:i:s')
    ];
}

/**
 * Mendapatkan range tanggal untuk shift produksi tertentu
 * 
 * @param string $shift 'morning' (06:00-18:00) atau 'night' (18:00-06:00)
 * @param string $date Tanggal referensi (Y-m-d)
 * @return array ['start_date' => 'Y-m-d H:i:s', 'end_date' => 'Y-m-d H:i:s']
 */
function getSpecificProductionDateRange($shift = 'morning', $date = null)
{
    date_default_timezone_set('Asia/Jakarta');
    
    if ($date === null) {
        $date = date('Y-m-d');
    }
    
    $startDate = new DateTime($date);
    $endDate = new DateTime($date);
    
    if ($shift === 'morning') {
        $startDate->setTime(6, 0, 0);
        $endDate->setTime(18, 0, 0);
    } else { // night shift
        $startDate->setTime(18, 0, 0);
        $endDate->modify('+1 day');
        $endDate->setTime(6, 0, 0);
    }
    
    return [
        'start_date' => $startDate->format('Y-m-d H:i:s'),
        'end_date' => $endDate->format('Y-m-d H:i:s')
    ];
}

/**
 * Mendapatkan 7 hari terakhir dengan cutoff jam 6 pagi
 * 
 * @return array Array of dates (Y-m-d)
 */
function getLast7Days()
{
    date_default_timezone_set('Asia/Jakarta');
    $dates = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = new DateTime();
        $date->modify("-{$i} days");
        $dates[] = $date->format('Y-m-d');
    }
    
    return $dates;
}

// ==========================
// TREND DEFECT HANDLERS
// ==========================

function handleTrendDefectActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getDashboardStats':
                getDashboardStats($connection);
                break;
            case 'getDetailData':
                getDetailData($connection);
                break;
            case 'getWeeklyTrend':
                getWeeklyTrend($connection);
                break;
            case 'getTopDefects':
                getTopDefects($connection);
                break;
            case 'getDefectHistory':
                getDefectHistory($connection);
                break;
            case 'getServerTime':
                getServerTime($connection);
                break;
            case 'getProductionShift':
                getProductionShift($connection);
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
// GET PRODUCTION SHIFT
// ==========================
function getProductionShift($connection)
{
    header('Content-Type: application/json');
    
    try {
        $shiftRange = getProductionDateRange();
        $now = date('Y-m-d H:i:s');
        
        // Cek shift aktif
        $hour = (int)date('H');
        $shift = ($hour >= 6 && $hour < 18) ? 'morning' : 'night';
        $shiftName = ($shift === 'morning') ? 'Shift Siang (06:00-18:00)' : 'Shift Malam (18:00-06:00)';
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'current_shift' => $shift,
                'shift_name' => $shiftName,
                'current_time' => $now,
                'start_date' => $shiftRange['start_date'],
                'end_date' => $shiftRange['end_date'],
                'is_morning_shift' => ($shift === 'morning')
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengambil info shift: ' . $e->getMessage()
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
        // Ambil waktu server dari SQL Server
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
                'timezone' => date_default_timezone_get()
            ]
        ]);
        
        sqlsrv_free_stmt($stmt);
        
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
// GET DASHBOARD STATS (CARD COUNT)
// ==========================
function getDashboardStats($connection)
{
    header('Content-Type: application/json');

    try {
        // Gunakan fungsi helper untuk mendapatkan range shift
        $shiftRange = getProductionDateRange();
        $startDate = $shiftRange['start_date'];
        $endDate = $shiftRange['end_date'];

        // 1. OUTSTANDING REPAIR
        $sql_outstanding = "
            SELECT COUNT(*) AS total_outstanding
            FROM report_claim_defect
            WHERE status = 0
            AND aksi_claim_defect = 'Repair'
        ";

        $stmt_outstanding = sqlsrv_query($connection, $sql_outstanding);
        if ($stmt_outstanding === false) {
            throw new Exception("Gagal query outstanding: " . print_r(sqlsrv_errors(), true));
        }

        $row_outstanding = sqlsrv_fetch_array($stmt_outstanding, SQLSRV_FETCH_ASSOC);
        $total_outstanding = (int)$row_outstanding['total_outstanding'];
        sqlsrv_free_stmt($stmt_outstanding);

        // 2. DATA MASUK SHIFT BERJALAN
        $sql_masuk = "
            SELECT COUNT(*) AS total_masuk
            FROM report_claim_defect
            WHERE created_at >= ?
            AND created_at < ?
        ";

        $stmt_masuk = sqlsrv_query($connection, $sql_masuk, array($startDate, $endDate));
        if ($stmt_masuk === false) {
            throw new Exception("Gagal query data masuk: " . print_r(sqlsrv_errors(), true));
        }

        $row_masuk = sqlsrv_fetch_array($stmt_masuk, SQLSRV_FETCH_ASSOC);
        $total_masuk_hari_ini = (int)$row_masuk['total_masuk'];
        sqlsrv_free_stmt($stmt_masuk);

        // 3. DATA SELESAI SHIFT BERJALAN
        $sql_selesai = "
            SELECT COUNT(*) AS total_selesai
            FROM report_claim_defect
            WHERE status = 1
            AND created_at >= ?
            AND created_at < ?
        ";

        $stmt_selesai = sqlsrv_query($connection, $sql_selesai, array($startDate, $endDate));
        if ($stmt_selesai === false) {
            throw new Exception("Gagal query data selesai: " . print_r(sqlsrv_errors(), true));
        }

        $row_selesai = sqlsrv_fetch_array($stmt_selesai, SQLSRV_FETCH_ASSOC);
        $total_selesai_hari_ini = (int)$row_selesai['total_selesai'];
        sqlsrv_free_stmt($stmt_selesai);

        // Response JSON dengan tambahan info shift
        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dimuat',
            'data' => [
                'outstanding_repair' => $total_outstanding,
                'masuk_hari_ini' => $total_masuk_hari_ini,
                'selesai_hari_ini' => $total_selesai_hari_ini
            ],
            'shift_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate
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
// GET DETAIL DATA (KONSISTEN DENGAN CARD COUNT)
// ==========================
function getDetailData($connection)
{
    header('Content-Type: application/json');

    try {
        $type = $_GET['type'] ?? '';

        if (empty($type)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Type tidak valid'
            ]);
            exit;
        }

        // Gunakan fungsi helper untuk mendapatkan range shift
        $shiftRange = getProductionDateRange();
        $startDate = $shiftRange['start_date'];
        $endDate = $shiftRange['end_date'];

        // ==========================
        // TOTAL COUNT
        // ==========================

        $sql_count = "";
        $params = [];

        switch ($type) {
            case 'outstanding':
                $sql_count = "
                    SELECT COUNT(*) AS total
                    FROM report_claim_defect
                    WHERE status = 0
                    AND aksi_claim_defect = 'Repair'
                ";
                break;

            case 'masuk':
                $sql_count = "
                    SELECT COUNT(*) AS total
                    FROM report_claim_defect
                    WHERE created_at >= ?
                    AND created_at < ?
                ";
                $params = [$startDate, $endDate];
                break;

            case 'selesai':
                $sql_count = "
                    SELECT COUNT(*) AS total
                    FROM report_claim_defect
                    WHERE status = 1
                    AND created_at >= ?
                    AND created_at < ?
                ";
                $params = [$startDate, $endDate];
                break;

            default:
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Type tidak dikenal'
                ]);
                exit;
        }

        $stmt_count = sqlsrv_query($connection, $sql_count, $params);
        if ($stmt_count === false) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }

        $row_count = sqlsrv_fetch_array($stmt_count, SQLSRV_FETCH_ASSOC);
        $totalCount = (int)$row_count['total'];
        sqlsrv_free_stmt($stmt_count);

        // ==========================
        // DETAIL QUERY
        // ==========================

        $sql_detail = "";
        $params_detail = [];

        switch ($type) {
            case 'outstanding':
                $sql_detail = "
                    SELECT
                        nama_section,
                        nama_defect,
                        lotno,
                        partno,
                        tanggal_ditemukan,
                        created_at,
                        qty,
                        status,
                        nama_customer
                    FROM report_claim_defect
                    WHERE status = 0
                    AND aksi_claim_defect = 'Repair'
                    ORDER BY created_at DESC
                ";
                break;

            case 'masuk':
                $sql_detail = "
                    SELECT
                        nama_section,
                        nama_defect,
                        lotno,
                        partno,
                        tanggal_ditemukan,
                        created_at,
                        qty,
                        status,
                        nama_customer
                    FROM report_claim_defect
                    WHERE created_at >= ?
                    AND created_at < ?
                    ORDER BY created_at DESC
                ";
                $params_detail = [$startDate, $endDate];
                break;

            case 'selesai':
                $sql_detail = "
                    SELECT
                        nama_section,
                        nama_defect,
                        lotno,
                        partno,
                        tanggal_ditemukan,
                        created_at,
                        qty,
                        status,
                        nama_customer
                    FROM report_claim_defect
                    WHERE status = 1
                    AND created_at >= ?
                    AND created_at < ?
                    ORDER BY created_at DESC
                ";
                $params_detail = [$startDate, $endDate];
                break;
        }

        $stmt_detail = sqlsrv_query($connection, $sql_detail, $params_detail);
        if ($stmt_detail === false) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }

        $data = [];

        while ($row = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC)) {
            $tanggalDitemukan = '';
            if ($row['tanggal_ditemukan'] instanceof DateTime) {
                $tanggalDitemukan = $row['tanggal_ditemukan']->format('Y-m-d');
            }

            $createdAt = '';
            if ($row['created_at'] instanceof DateTime) {
                $createdAt = $row['created_at']->format('Y-m-d H:i:s');
            }

            $data[] = [
                'nama_section' => $row['nama_section'],
                'nama_defect' => $row['nama_defect'],
                'lotno' => $row['lotno'],
                'partno' => $row['partno'],
                'tanggal_ditemukan' => $tanggalDitemukan,
                'created_at' => $createdAt,
                'qty' => (int)$row['qty'],
                'status' => (int)$row['status'],
                'nama_customer' => $row['nama_customer']
            ];
        }

        sqlsrv_free_stmt($stmt_detail);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dimuat',
            'data' => $data,
            'total' => $totalCount,
            'total_rows' => count($data),
            'shift_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// CHART: Weekly Trend (7 Hari Terakhir)
// ==========================
function getWeeklyTrend($connection)
{
    header('Content-Type: application/json');

    try {
        // Gunakan fungsi helper untuk mendapatkan 7 hari terakhir
        $last7Days = getLast7Days();
        
        // Query untuk data trend 7 hari terakhir
        $sql = "
        WITH DateSeries AS (
            SELECT 
                DATEADD(DAY, t.number, DATEADD(DAY, -6, CAST(GETDATE() AS DATE))) AS Date
            FROM (
                SELECT number FROM master..spt_values WHERE type = 'P' AND number BETWEEN 0 AND 6
            ) t
        )
        SELECT 
            CONVERT(VARCHAR, ds.Date, 103) AS tanggal,
            DATENAME(WEEKDAY, ds.Date) AS hari,
            ISNULL(SUM(CASE WHEN CAST(rcd.created_at AS DATE) = ds.Date THEN rcd.qty ELSE 0 END), 0) AS masuk,
            ISNULL(SUM(CASE WHEN rcd.status = 1 AND CAST(rcd.created_at AS DATE) = ds.Date THEN rcd.qty ELSE 0 END), 0) AS selesai,
            ISNULL(SUM(CASE WHEN rcd.status = 0 AND rcd.aksi_claim_defect = 'Repair' AND CAST(rcd.created_at AS DATE) <= ds.Date THEN rcd.qty ELSE 0 END), 0) AS outstanding
        FROM DateSeries ds
        LEFT JOIN report_claim_defect rcd ON 1=1
        GROUP BY ds.Date
        ORDER BY ds.Date ASC
        ";

        $stmt = sqlsrv_query($connection, $sql);
        if ($stmt === false) {
            // Fallback query
            $sql_fallback = "
            SELECT 
                CONVERT(VARCHAR, DATEADD(DAY, t.number, DATEADD(DAY, -6, CAST(GETDATE() AS DATE))), 103) AS tanggal,
                DATENAME(WEEKDAY, DATEADD(DAY, t.number, DATEADD(DAY, -6, CAST(GETDATE() AS DATE)))) AS hari,
                0 AS masuk,
                0 AS selesai,
                0 AS outstanding
            FROM (
                SELECT number FROM master..spt_values WHERE type = 'P' AND number BETWEEN 0 AND 6
            ) t
            ORDER BY t.number ASC
            ";
            $stmt = sqlsrv_query($connection, $sql_fallback);
            
            if ($stmt === false) {
                throw new Exception("Gagal query weekly trend: " . print_r(sqlsrv_errors(), true));
            }
        }

        $categories = [];
        $masukData = [];
        $selesaiData = [];
        $outstandingData = [];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $categories[] = $row['hari'] . ' ' . $row['tanggal'];
            $masukData[] = (int) $row['masuk'];
            $selesaiData[] = (int) $row['selesai'];
            $outstandingData[] = (int) $row['outstanding'];
        }

        sqlsrv_free_stmt($stmt);

        // Siapkan data untuk chart
        $chartData = [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Data Masuk',
                    'data' => $masukData,
                    'color' => '#0d6efd'
                ],
                [
                    'name' => 'Data Selesai',
                    'data' => $selesaiData,
                    'color' => '#198754'
                ],
                [
                    'name' => 'Outstanding',
                    'data' => $outstandingData,
                    'color' => '#ffc107'
                ]
            ],
            'metadata' => [
                'title' => 'Trend Defect 7 Hari Terakhir',
                'period' => 'weekly'
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data weekly trend berhasil dimuat',
            'data' => $chartData
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data weekly trend: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET TOP 3 DEFECTS (30 Hari Terakhir)
// ==========================
function getTopDefects($connection)
{
    header('Content-Type: application/json');

    try {
        // Ambil top 3 defect berdasarkan total qty dalam 30 hari terakhir
        $sql = "
        SELECT TOP 3
            nama_defect,
            ISNULL(SUM(qty), 0) AS total_qty,
            COUNT(*) AS total_kejadian
        FROM report_claim_defect
        WHERE CAST(created_at AS DATE) >= DATEADD(DAY, -30, CAST(GETDATE() AS DATE))
        GROUP BY nama_defect
        ORDER BY total_qty DESC
        ";

        $stmt = sqlsrv_query($connection, $sql);
        if ($stmt === false) {
            throw new Exception("Gagal query top defects: " . print_r(sqlsrv_errors(), true));
        }

        $topDefects = [];
        $colors = ['#dc3545', '#fd7e14', '#ffc107'];
        $index = 0;

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $topDefects[] = [
                'nama_defect' => $row['nama_defect'],
                'total_qty' => (int) $row['total_qty'],
                'total_kejadian' => (int) $row['total_kejadian'],
                'color' => $colors[$index % count($colors)]
            ];
            $index++;
        }

        sqlsrv_free_stmt($stmt);

        // Jika data kurang dari 3, tambahkan dummy
        while (count($topDefects) < 3) {
            $topDefects[] = [
                'nama_defect' => 'Tidak ada data',
                'total_qty' => 0,
                'total_kejadian' => 0,
                'color' => '#6c757d'
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Data top defects berhasil dimuat',
            'data' => $topDefects
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data top defects: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// GET DEFECT HISTORY (30 Hari)
// ==========================
function getDefectHistory($connection)
{
    header('Content-Type: application/json');

    try {
        $defectName = $_GET['defect'] ?? '';
        
        if (empty($defectName)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Nama defect tidak valid']);
            exit;
        }

        // Ambil data history per hari untuk defect tertentu dalam 30 hari terakhir
        $sql = "
        WITH DateSeries AS (
            SELECT 
                DATEADD(DAY, t.number, DATEADD(DAY, -29, CAST(GETDATE() AS DATE))) AS Date
            FROM (
                SELECT number FROM master..spt_values WHERE type = 'P' AND number BETWEEN 0 AND 29
            ) t
        )
        SELECT 
            CONVERT(VARCHAR, ds.Date, 103) AS tanggal,
            DATENAME(WEEKDAY, ds.Date) AS hari,
            ISNULL(SUM(CASE WHEN rcd.nama_defect = ? AND CAST(rcd.created_at AS DATE) = ds.Date THEN rcd.qty ELSE 0 END), 0) AS total_qty,
            ISNULL(COUNT(CASE WHEN rcd.nama_defect = ? AND CAST(rcd.created_at AS DATE) = ds.Date THEN 1 ELSE NULL END), 0) AS total_kejadian,
            ISNULL(STRING_AGG(DISTINCT CASE WHEN rcd.nama_defect = ? AND CAST(rcd.created_at AS DATE) = ds.Date THEN rcd.nama_section END, ', '), '-') AS sections,
            ISNULL(STRING_AGG(DISTINCT CASE WHEN rcd.nama_defect = ? AND CAST(rcd.created_at AS DATE) = ds.Date THEN rcd.partno END, ', '), '-') AS part_numbers
        FROM DateSeries ds
        LEFT JOIN report_claim_defect rcd ON 1=1
        GROUP BY ds.Date
        ORDER BY ds.Date ASC
        ";

        $stmt = sqlsrv_query($connection, $sql, array($defectName, $defectName, $defectName, $defectName));
        
        if ($stmt === false) {
            // Fallback query jika STRING_AGG tidak support
            $sql_fallback = "
            WITH DateSeries AS (
                SELECT 
                    DATEADD(DAY, t.number, DATEADD(DAY, -29, CAST(GETDATE() AS DATE))) AS Date
                FROM (
                    SELECT number FROM master..spt_values WHERE type = 'P' AND number BETWEEN 0 AND 29
                ) t
            )
            SELECT 
                CONVERT(VARCHAR, ds.Date, 103) AS tanggal,
                DATENAME(WEEKDAY, ds.Date) AS hari,
                ISNULL(SUM(CASE WHEN rcd.nama_defect = ? AND CAST(rcd.created_at AS DATE) = ds.Date THEN rcd.qty ELSE 0 END), 0) AS total_qty,
                ISNULL(COUNT(CASE WHEN rcd.nama_defect = ? AND CAST(rcd.created_at AS DATE) = ds.Date THEN 1 ELSE NULL END), 0) AS total_kejadian,
                '-' AS sections,
                '-' AS part_numbers
            FROM DateSeries ds
            LEFT JOIN report_claim_defect rcd ON 1=1
            GROUP BY ds.Date
            ORDER BY ds.Date ASC
            ";
            $stmt = sqlsrv_query($connection, $sql_fallback, array($defectName, $defectName));
            
            if ($stmt === false) {
                throw new Exception("Gagal query history defect: " . print_r(sqlsrv_errors(), true));
            }
        }

        $data = [];
        $totalQty = 0;
        $totalKejadian = 0;
        $categories = [];
        $qtyData = [];
        $kejadianData = [];
        
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $qty = (int) $row['total_qty'];
            $kejadian = (int) $row['total_kejadian'];
            
            $totalQty += $qty;
            $totalKejadian += $kejadian;
            
            $categories[] = $row['hari'] . ' ' . $row['tanggal'];
            $qtyData[] = $qty;
            $kejadianData[] = $kejadian;
            
            $data[] = [
                'tanggal' => $row['tanggal'],
                'hari' => $row['hari'],
                'total_qty' => $qty,
                'total_kejadian' => $kejadian,
                'sections' => $row['sections'] ?? '-',
                'part_numbers' => $row['part_numbers'] ?? '-'
            ];
        }

        sqlsrv_free_stmt($stmt);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data history defect berhasil dimuat',
            'data' => [
                'defect_name' => $defectName,
                'total_qty' => $totalQty,
                'total_kejadian' => $totalKejadian,
                'categories' => $categories,
                'qty_data' => $qtyData,
                'kejadian_data' => $kejadianData,
                'daily_data' => $data
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat history defect: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>