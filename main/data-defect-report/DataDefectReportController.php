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
            'debug' => $error['message'] // Hapus ini di production
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

function getReports($connection)
{
    header('Content-Type: application/json');

    // Ambil parameter filter dari URL
    $tanggal_awal = $_GET['tanggal_awal'] ?? '';
    $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
    $lot_nos = isset($_GET['lot_nos']) ? explode(',', $_GET['lot_nos']) : [];
    $customers = isset($_GET['customers']) ? explode(',', $_GET['customers']) : [];

    // Helper: validasi tanggal
    $validateDate = fn($date) => DateTime::createFromFormat('Y-m-d', $date) ?: false;

    $params = [];
    $whereConditions = [];
    $filter_message = "";
    $filter_type = "none";

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
                qty,
                aksi_claim_defect,
                CONVERT(varchar, created_at, 120) as created_at
            FROM report_claim_defect";

    // ====================================
    // FILTER TANGGAL
    // ====================================
    if ($tanggal_awal && $tanggal_akhir) {
        $awal = $validateDate($tanggal_awal);
        $akhir = $validateDate($tanggal_akhir);

        if (!$awal || !$akhir) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD'
            ]);
            return;
        }

        if ($awal > $akhir) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            ]);
            return;
        }

        $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) BETWEEN ? AND ?";
        $params[] = $tanggal_awal;
        $params[] = $tanggal_akhir;
        $filter_message = "rentang $tanggal_awal sampai $tanggal_akhir";
        $filter_type = "range";
    } elseif ($tanggal_awal) {
        if (!$validateDate($tanggal_awal)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Format tanggal tidak valid'
            ]);
            return;
        }

        $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) >= ?";
        $params[] = $tanggal_awal;
        $filter_message = "mulai $tanggal_awal sampai sekarang";
        $filter_type = "from";
    } elseif ($tanggal_akhir) {
        if (!$validateDate($tanggal_akhir)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Format tanggal tidak valid'
            ]);
            return;
        }

        $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) <= ?";
        $params[] = $tanggal_akhir;
        $filter_message = "sampai $tanggal_akhir";
        $filter_type = "until";
    }

    // ====================================
    // FILTER LOT NO
    // ====================================
    if (!empty($lot_nos) && $lot_nos[0] !== '') {
        $lot_placeholders = implode(',', array_fill(0, count($lot_nos), '?'));
        $whereConditions[] = "lotno IN ($lot_placeholders)";
        foreach ($lot_nos as $lot) {
            $params[] = trim($lot);
        }
        $filter_message = count($lot_nos) . " lot dipilih";
        $filter_type = "lot";
    }

    // ====================================
    // FILTER CUSTOMER
    // ====================================
    if (!empty($customers) && $customers[0] !== '') {
        $customer_placeholders = implode(',', array_fill(0, count($customers), '?'));
        $whereConditions[] = "nama_customer IN ($customer_placeholders)";
        foreach ($customers as $customer) {
            $params[] = trim($customer);
        }
        $filter_message = count($customers) . " customer dipilih";
        $filter_type = "customer";
    }

    // Jika tidak ada filter sama sekali
    if (empty($whereConditions)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Silakan pilih filter untuk melihat laporan',
            'data' => [],
            'filter' => [
                'tanggal_awal' => null,
                'tanggal_akhir' => null,
                'lot_nos' => [],
                'customers' => [],
                'total_data' => 0,
                'filter_type' => 'none'
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

    // Tentukan pesan berdasarkan hasil
    if ($total > 0) {
        $message = "Berhasil memuat $total laporan";
        if ($filter_type == 'range') {
            $message .= " untuk $filter_message";
        } elseif ($filter_type == 'lot') {
            $message .= " dengan $filter_message";
        } elseif ($filter_type == 'customer') {
            $message .= " dengan $filter_message";
        }
    } else {
        if ($filter_type == 'range') {
            $message = "Tidak ada laporan untuk $filter_message";
        } elseif ($filter_type == 'lot') {
            $message = "Tidak ada laporan untuk lot yang dipilih";
        } elseif ($filter_type == 'customer') {
            $message = "Tidak ada laporan untuk customer yang dipilih";
        } else {
            $message = "Tidak ada laporan";
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $rows,
        'filter' => [
            'tanggal_awal' => $tanggal_awal ?: null,
            'tanggal_akhir' => $tanggal_akhir ?: null,
            'lot_nos' => $lot_nos,
            'customers' => $customers,
            'total_data' => $total,
            'filter_type' => $filter_type
        ]
    ]);
}

function showReport($connection)
{
    header('Content-Type: application/json');

    // Ambil ID dari parameter
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
    }

    // Validasi ID harus numeric
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

    // Konversi nilai DateTime ke string
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
// FUNGSI UNTUK GET OPTIONS CUSTOMER
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
