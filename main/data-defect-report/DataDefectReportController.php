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
            case 'show':  // Tambahkan case baru
                showReport($connection);
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

    // Helper: validasi tanggal
    $validateDate = fn($date) => DateTime::createFromFormat('Y-m-d', $date) ?: false;

    // Jika kedua tanggal kosong, kembalikan response kosong
    if (empty($tanggal_awal) && empty($tanggal_akhir)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Silakan pilih rentang tanggal untuk melihat laporan',
            'data' => [],
            'filter' => [
                'tanggal_awal' => null,
                'tanggal_akhir' => null,
                'total_data' => 0,
                'filter_type' => 'none'
            ]
        ]);
        return;
    }

    $params = [];

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
                CONVERT(varchar, created_at, 120) as created_at
            FROM report_claim_defect";

    // Build WHERE clause berdasarkan filter
    $whereConditions = [];
    $filter_message = "";

    // KASUS 1: Filter rentang tanggal (kedua tanggal diisi)
    if ($tanggal_awal && $tanggal_akhir) {
        $tanggal_awal_obj = $validateDate($tanggal_awal);
        $tanggal_akhir_obj = $validateDate($tanggal_akhir);

        if (!$tanggal_awal_obj || !$tanggal_akhir_obj) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD']);
            return;
        }

        if ($tanggal_awal_obj > $tanggal_akhir_obj) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir']);
            return;
        }

        $whereConditions[] = "tanggal_ditemukan BETWEEN ? AND ?";
        $params[] = $tanggal_awal;
        $params[] = $tanggal_akhir;
        $filter_message = "rentang $tanggal_awal sampai $tanggal_akhir";
    }
    // KASUS 2: Filter satu tanggal (hanya tanggal awal diisi)
    elseif ($tanggal_awal && !$tanggal_akhir) {
        $tanggal_awal_obj = $validateDate($tanggal_awal);
        if (!$tanggal_awal_obj) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD']);
            return;
        }

        $whereConditions[] = "tanggal_ditemukan = ?";
        $params[] = $tanggal_awal;
        $filter_message = "tanggal $tanggal_awal";
    }

    // Gabungkan WHERE conditions jika ada
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // Order by
    $sql .= " ORDER BY nama_section ASC, id ASC";

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
        $message = "Berhasil memuat $total laporan untuk $filter_message";
    } else {
        $message = "Tidak ada laporan untuk $filter_message";
    }

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $rows,
        'filter' => [
            'tanggal_awal' => $tanggal_awal ?: null,
            'tanggal_akhir' => $tanggal_akhir ?: null,
            'total_data' => $total,
            'filter_type' => $tanggal_awal && $tanggal_akhir ? 'range' : 'single'
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
