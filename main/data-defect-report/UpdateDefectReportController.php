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
            } elseif ($action === 'updateStatus') {
                updateStatus($connection);
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
    $lotno = $_POST['lotno'] ?? '';
    $aksi_claim_defect = $_POST['aksi_claim_defect'] ?? '';
    $nama_group = $_POST['nama_group'] ?? '';
    $qty = $_POST['qty'] ?? '';
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

    // PERBAIKAN: Handle nama_operator_pengambil - NULL jika kosong atau null
    // Jika value = '' atau 'null' atau null, set ke NULL untuk database
    if ($nama_operator_pengambil === '' || $nama_operator_pengambil === 'null' || $nama_operator_pengambil === null) {
        $nama_operator_pengambil = null;
    } else {
        $nama_operator_pengambil = trim($nama_operator_pengambil);
    }

    // 🔥 PERBAIKAN: Handle tanggal_pengambilan - NULL jika kosong atau null
    // Jika value = '' atau 'null' atau null, set ke NULL untuk database
    if ($tanggal_pengambilan === '' || $tanggal_pengambilan === 'null' || $tanggal_pengambilan === null) {
        $tanggal_pengambilan = null;
    } else {
        // Validasi format tanggal hanya jika diisi
        $tanggal_obj = DateTime::createFromFormat('Y-m-d', $tanggal_pengambilan);
        if (!$tanggal_obj || $tanggal_obj->format('Y-m-d') !== $tanggal_pengambilan) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD'
            ]);
            return;
        }
    }

    // Validasi qty hanya jika diisi
    if (!empty($qty) && (!is_numeric($qty) || $qty < 0)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Qty harus berupa angka positif'
        ]);
        return;
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

    // 🔥 PERBAIKAN: Query update dengan penanganan NULL yang benar
    $sql = "UPDATE report_claim_defect 
            SET lotno = ?,
                aksi_claim_defect = ?,
                nama_group = ?,
                qty = ?,
                nama_operator_pengambil = ?, 
                tanggal_pengambilan = ? 
            WHERE id = ?";

    $params = [
        !empty($lotno) ? trim($lotno) : null,
        !empty($aksi_claim_defect) ? trim($aksi_claim_defect) : null,
        !empty($nama_group) ? trim($nama_group) : null,
        !empty($qty) ? $qty : null,
        $nama_operator_pengambil,
        $tanggal_pengambilan,  // 🔥 Bisa berupa NULL atau string tanggal
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

    // 🔥 PERBAIKAN: Response yang benar, tampilkan null sebagai null
    $responseData = [
        'id' => $id,
        'lotno' => !empty($lotno) ? trim($lotno) : null,
        'aksi_claim_defect' => !empty($aksi_claim_defect) ? trim($aksi_claim_defect) : null,
        'nama_group' => !empty($nama_group) ? trim($nama_group) : null,
        'qty' => !empty($qty) ? $qty : null,
        'nama_operator_pengambil' => $nama_operator_pengambil,
        'tanggal_pengambilan' => $tanggal_pengambilan  // 🔥 Bisa null atau string
    ];

    if ($rowsAffected > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil diupdate',
            'data' => $responseData
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Tidak ada perubahan data',
            'data' => $responseData
        ]);
    }
}

function updateStatus($connection)
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Method tidak diizinkan');
        }

        $id = $_POST['id'] ?? '';
        $status = $_POST['status'] ?? '';

        // Validasi ID
        if (empty($id) || !is_numeric($id)) {
            throw new Exception('ID tidak valid');
        }

        // Validasi status (0 atau 1)
        if (!in_array($status, [0, 1, '0', '1'], true)) {
            throw new Exception('Status tidak valid');
        }

        // PERBAIKAN: Gunakan tabel yang benar: report_claim_defect
        $checkSql = "SELECT id FROM report_claim_defect WHERE id = ?";
        $checkStmt = sqlsrv_prepare($connection, $checkSql, [$id]);

        if (!$checkStmt || !sqlsrv_execute($checkStmt)) {
            $errors = sqlsrv_errors();
            throw new Exception('Gagal memeriksa data: ' . json_encode($errors));
        }

        if (!sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
            throw new Exception('Data tidak ditemukan');
        }

        // PERBAIKAN: Update tabel report_claim_defect
        $sql = "UPDATE report_claim_defect SET status = ? WHERE id = ?";
        $params = [$status, $id];

        $stmt = sqlsrv_prepare($connection, $sql, $params);

        if (!$stmt) {
            $errors = sqlsrv_errors();
            throw new Exception('Gagal menyiapkan query: ' . json_encode($errors));
        }

        if (!sqlsrv_execute($stmt)) {
            $errors = sqlsrv_errors();
            throw new Exception('Gagal update status: ' . json_encode($errors));
        }

        $rowsAffected = sqlsrv_rows_affected($stmt);

        echo json_encode([
            'status' => 'success',
            'message' => $rowsAffected > 0
                ? 'Status berhasil diupdate'
                : 'Tidak ada perubahan data'
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
