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
            'message' => 'Terjadi kesalahan internal server'
        ]);
        exit;
    }
}

function handleError($errno, $errstr, $errfile, $errline)
{
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $errstr
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
        case 'POST':
            $action = $_GET['action'] ?? '';

            if ($action === 'update') {
                updateReport($connection);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
            }
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

function updateReport($connection)
{
    // Ambil data dari POST
    $id = $_POST['id'] ?? '';
    $nama_operator_pengambil = $_POST['nama_operator_pengambil'] ?? '';
    $tanggal_pengambilan = $_POST['tanggal_pengambilan'] ?? '';

    // Validasi ID
    if (empty($id) || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'ID tidak valid'
        ]);
        return;
    }

    // Validasi tanggal_pengambilan (boleh kosong)
    if (!empty($tanggal_pengambilan)) {
        // Cek format tanggal YYYY-MM-DD
        if (!DateTime::createFromFormat('Y-m-d', $tanggal_pengambilan)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD'
            ]);
            return;
        }
    } else {
        $tanggal_pengambilan = null; // Set ke NULL jika kosong
    }

    // Cek apakah data dengan ID tersebut ada
    $checkSql = "SELECT id FROM report_claim_defect WHERE id = ?";
    $checkParams = [$id];
    $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

    if (!$checkStmt || !sqlsrv_execute($checkStmt)) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memeriksa data'
        ]);
        return;
    }

    if (!sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
        return;
    }

    // Query update
    $sql = "UPDATE report_claim_defect 
            SET nama_operator_pengambil = ?, 
                tanggal_pengambilan = ? 
            WHERE id = ?";

    $params = [
        $nama_operator_pengambil ?: null, // Jika empty string, jadi NULL
        $tanggal_pengambilan,
        $id
    ];

    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query update',
            'detail' => $errors
        ]);
        return;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengupdate data',
            'detail' => $errors
        ]);
        return;
    }

    $rowsAffected = sqlsrv_rows_affected($stmt);

    if ($rowsAffected > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil diupdate',
            'data' => [
                'id' => $id,
                'nama_operator_pengambil' => $nama_operator_pengambil,
                'tanggal_pengambilan' => $tanggal_pengambilan
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Tidak ada perubahan data',
            'data' => [
                'id' => $id,
                'nama_operator_pengambil' => $nama_operator_pengambil,
                'tanggal_pengambilan' => $tanggal_pengambilan
            ]
        ]);
    }
}
