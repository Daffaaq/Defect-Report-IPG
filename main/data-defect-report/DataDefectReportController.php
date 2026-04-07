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

            handleReportActions($connection, $action);
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
// REPORT HANDLERS
// ==========================

function handleReportActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getReports':
                getReports($connection);
                break;
            case 'show':
                showReport($connection);
                break;
            case 'getCustomerOptions':
                getCustomerOptions($connection);
                break;
            case 'getPartNoOptions':
                getPartNoOptions($connection);
                break;
            case 'getPartNoByTanggal':
                getPartNoByTanggal($connection);
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

// ====================================
// GET REPORTS - UPDATED VERSION
// ====================================
function getReports($connection)
{
    header('Content-Type: application/json');

    // Ambil parameter filter dari URL
    // TAB 1: Tanggal (single) + Part No
    $tanggal = $_GET['tanggal'] ?? '';
    $partno = isset($_GET['partno']) ? explode(',', $_GET['partno']) : [];

    // TAB 2: Lot No
    $lot_nos = isset($_GET['lot_nos']) ? explode(',', $_GET['lot_nos']) : [];

    // TAB 3: Customer + Range Tanggal
    $customers = isset($_GET['customers']) ? explode(',', $_GET['customers']) : [];
    $tanggal_awal = $_GET['tanggal_awal'] ?? '';
    $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

    // TAB 4: Part No + Range Tanggal
    $partnos = isset($_GET['partnos']) ? explode(',', $_GET['partnos']) : [];
    $tanggal_awal_part = $_GET['tanggal_awal_part'] ?? '';
    $tanggal_akhir_part = $_GET['tanggal_akhir_part'] ?? '';

    $params = [];
    $whereConditions = [];

    // Helper: validasi tanggal
    $validateDate = fn($date) => DateTime::createFromFormat('Y-m-d', $date) ?: false;

    // ====================================
    // TAB 1: FILTER TANGGAL (SINGLE) + PART NO
    // ====================================
    if ($tanggal && !empty($partno) && $partno[0] !== '') {
        if (!$validateDate($tanggal)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD']);
            return;
        }

        $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) = ?";
        $params[] = $tanggal;

        $partno_placeholders = implode(',', array_fill(0, count($partno), '?'));
        $whereConditions[] = "partno IN ($partno_placeholders)";
        foreach ($partno as $pn) {
            $params[] = trim($pn);
        }
    }
    // Hanya tanggal (single) tanpa partno - TAMPILKAN SEMUA DATA DI TANGGAL ITU
    elseif ($tanggal && (empty($partno) || $partno[0] === '')) {
        if (!$validateDate($tanggal)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD']);
            return;
        }
        $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) = ?";
        $params[] = $tanggal;
    }

    // ====================================
    // TAB 2: FILTER LOT NO
    // ====================================
    if (!empty($lot_nos) && $lot_nos[0] !== '') {
        $lot_placeholders = implode(',', array_fill(0, count($lot_nos), '?'));
        $whereConditions[] = "lotno IN ($lot_placeholders)";
        foreach ($lot_nos as $lot) {
            $params[] = trim($lot);
        }
    }

    // ====================================
    // TAB 3: FILTER CUSTOMER + RANGE TANGGAL
    // ====================================
    if (!empty($customers) && $customers[0] !== '') {
        $customer_placeholders = implode(',', array_fill(0, count($customers), '?'));
        $whereConditions[] = "nama_customer IN ($customer_placeholders)";
        foreach ($customers as $customer) {
            $params[] = trim($customer);
        }

        // Range tanggal untuk customer (opsional)
        if ($tanggal_awal && $tanggal_akhir) {
            $awal = $validateDate($tanggal_awal);
            $akhir = $validateDate($tanggal_akhir);

            if ($awal && $akhir) {
                if ($awal <= $akhir) {
                    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) BETWEEN ? AND ?";
                    $params[] = $tanggal_awal;
                    $params[] = $tanggal_akhir;
                }
            }
        } elseif ($tanggal_awal) {
            if ($validateDate($tanggal_awal)) {
                $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) >= ?";
                $params[] = $tanggal_awal;
            }
        } elseif ($tanggal_akhir) {
            if ($validateDate($tanggal_akhir)) {
                $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) <= ?";
                $params[] = $tanggal_akhir;
            }
        }
    }

    // ====================================
    // TAB 4: FILTER PART NO + RANGE TANGGAL
    // ====================================
    if (!empty($partnos) && $partnos[0] !== '') {
        $partnos_placeholders = implode(',', array_fill(0, count($partnos), '?'));
        $whereConditions[] = "partno IN ($partnos_placeholders)";
        foreach ($partnos as $pn) {
            $params[] = trim($pn);
        }

        // Range tanggal untuk partno (opsional)
        if ($tanggal_awal_part && $tanggal_akhir_part) {
            $awal = $validateDate($tanggal_awal_part);
            $akhir = $validateDate($tanggal_akhir_part);

            if ($awal && $akhir) {
                if ($awal <= $akhir) {
                    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) BETWEEN ? AND ?";
                    $params[] = $tanggal_awal_part;
                    $params[] = $tanggal_akhir_part;
                }
            }
        } elseif ($tanggal_awal_part) {
            if ($validateDate($tanggal_awal_part)) {
                $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) >= ?";
                $params[] = $tanggal_awal_part;
            }
        } elseif ($tanggal_akhir_part) {
            if ($validateDate($tanggal_akhir_part)) {
                $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) <= ?";
                $params[] = $tanggal_akhir_part;
            }
        }
    }

    // Base query
    $sql = "SELECT 
                id, 
                nama_section, 
                nama_defect, 
                lotno, 
                partno, 
                CONVERT(varchar, tanggal_ditemukan, 120) as tanggal_ditemukan,
                nama_operator, 
                nama_customer, 
                nama_operator_pengambil,
                CONVERT(varchar, tanggal_pengambilan, 120) as tanggal_pengambilan,
                nama_group,
                status,
                qty,
                aksi_claim_defect,
                CONVERT(varchar, created_at, 120) as created_at
            FROM report_claim_defect";

    // Jika tidak ada filter sama sekali
    if (empty($whereConditions)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Silakan pilih filter untuk melihat laporan',
            'data' => [],
            'filter' => [
                'tanggal' => null,
                'partno' => [],
                'lot_nos' => [],
                'customers' => [],
                'partnos' => [],
                'tanggal_awal' => null,
                'tanggal_akhir' => null,
                'total_data' => 0
            ]
        ]);
        return;
    }

    // Gabungkan WHERE conditions
    $sql .= " WHERE " . implode(" AND ", $whereConditions);

    // Order by
    $sql .= " ORDER BY CONVERT(date, tanggal_ditemukan) DESC, id DESC";

    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query',
            'detail' => $errors
        ]);
        return;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengeksekusi query',
            'detail' => $errors
        ]);
        return;
    }

    // Fetch data
    $rows = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Konversi semua nilai ke string
        $formattedRow = [];
        foreach ($row as $key => $value) {
            if ($value instanceof DateTime) {
                $formattedRow[$key] = $value->format('Y-m-d H:i:s');
            } else {
                $formattedRow[$key] = $value;
            }
        }
        $rows[] = $formattedRow;
    }

    $total = count($rows);
    $message = $total > 0 ? "Berhasil memuat $total laporan" : "Tidak ada laporan";

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $rows,
        'filter' => [
            'tanggal' => $tanggal ?: null,
            'partno' => $partno,
            'lot_nos' => $lot_nos,
            'customers' => $customers,
            'partnos' => $partnos,
            'tanggal_awal' => $tanggal_awal ?: null,
            'tanggal_akhir' => $tanggal_akhir ?: null,
            'total_data' => $total
        ]
    ]);
}

// ====================================
// GET PART NO BY TANGGAL (untuk Tab 1)
// ====================================
function getPartNoByTanggal($connection)
{
    header('Content-Type: application/json');

    $tanggal = $_GET['tanggal'] ?? '';

    if (empty($tanggal)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Tanggal tidak boleh kosong']);
        return;
    }

    $validateDate = DateTime::createFromFormat('Y-m-d', $tanggal);
    if (!$validateDate) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD']);
        return;
    }

    $sql = "SELECT DISTINCT partno 
            FROM report_claim_defect 
            WHERE CAST(tanggal_ditemukan AS DATE) = ?
            AND partno IS NOT NULL 
            AND partno != ''
            ORDER BY partno";

    $params = [$tanggal];
    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query',
            'detail' => $errors
        ]);
        return;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengeksekusi query',
            'detail' => $errors
        ]);
        return;
    }

    $partnos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['partno'])) {
            $partnos[] = ['partno' => $row['partno']];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $partnos,
        'tanggal' => $tanggal,
        'total' => count($partnos)
    ]);
}

// ====================================
// GET PART NO OPTIONS (untuk Tab 4)
// ====================================
function getPartNoOptions($connection)
{
    header('Content-Type: application/json');

    $sql = "SELECT DISTINCT partno 
            FROM report_claim_defect 
            WHERE partno IS NOT NULL AND partno != ''
            ORDER BY partno";

    $stmt = sqlsrv_query($connection, $sql);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengambil data part number',
            'detail' => $errors
        ]);
        return;
    }

    $partnos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $partnos[] = ['partno' => $row['partno']];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $partnos,
        'total' => count($partnos)
    ]);
}

// ====================================
// SHOW REPORT (detail)
// ====================================
function showReport($connection)
{
    header('Content-Type: application/json');

    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
    }

    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID harus berupa angka']);
        return;
    }

    $sql = "SELECT 
                id, 
                nama_section, 
                nama_defect, 
                lotno, 
                partno, 
                CONVERT(varchar, tanggal_ditemukan, 120) as tanggal_ditemukan,
                nama_operator, 
                deskripsi_masalah,
                nama_customer, 
                nama_group,
                qty,
                aksi_claim_defect,
                nama_operator_pengambil,
                shift,
                status,
                CONVERT(varchar, tanggal_pengambilan, 120) as tanggal_pengambilan,
                CONVERT(varchar, created_at, 120) as created_at
            FROM report_claim_defect 
            WHERE id = ?";

    $params = [$id];
    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query',
            'detail' => $errors
        ]);
        return;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengeksekusi query',
            'detail' => $errors
        ]);
        return;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        return;
    }

    $formattedRow = [];
    foreach ($row as $key => $value) {
        if ($value instanceof DateTime) {
            $formattedRow[$key] = $value->format('Y-m-d H:i:s');
        } else {
            $formattedRow[$key] = $value;
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil dimuat',
        'data' => $formattedRow
    ]);
}

// ====================================
// GET CUSTOMER OPTIONS
// ====================================
function getCustomerOptions($connection)
{
    header('Content-Type: application/json');

    $sql = "SELECT DISTINCT nama_customer 
            FROM report_claim_defect 
            WHERE nama_customer IS NOT NULL AND nama_customer != ''
            ORDER BY nama_customer";

    $stmt = sqlsrv_query($connection, $sql);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengambil data customer',
            'detail' => $errors
        ]);
        return;
    }

    $customers = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $customers[] = ['nama_customer' => $row['nama_customer']];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $customers
    ]);
}
