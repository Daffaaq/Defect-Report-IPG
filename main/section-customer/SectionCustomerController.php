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

            handleCustomerActions($connection, $action);
            break;

        case 'POST':
            $action = $_POST['action'] ?? '';

            if (empty($action)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
                exit;
            }

            handleCustomerActions($connection, $action);
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
// CUSTOMER HANDLERS
// ==========================

function handleCustomerActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getAll':
                getAllCustomers($connection);
                break;
            case 'insert':
                insertCustomer($connection);
                break;
            case 'update':
                updateCustomer($connection);
                break;
            case 'delete':
                deleteCustomer($connection);
                break;
            case 'getDetail':
                getCustomerDetail($connection);
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

function getAllCustomers($connection)
{
    $sql = "SELECT id, nama_customer, nama_singkatan, created_at FROM customer_table ORDER BY nama_customer ASC";
    $stmt = sqlsrv_prepare($connection, $sql);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query', 'detail' => $errors]);
        exit;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengeksekusi query', 'detail' => $errors]);
        exit;
    }

    $rows = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $created_at = '';
        if (!empty($row['created_at'])) {
            $created_at = $row['created_at'] instanceof DateTime
                ? $row['created_at']->format('Y-m-d H:i:s')
                : $row['created_at'];
        }

        $rows[] = [
            'id' => $row['id'],
            'nama_customer' => $row['nama_customer'],
            'nama_singkatan' => $row['nama_singkatan'],
            'created_at' => $created_at
        ];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $rows]);
    exit;
}

function insertCustomer($connection)
{
    $nama_customer = trim($_POST['nama_customer'] ?? '');
    $nama_singkatan = trim($_POST['nama_singkatan'] ?? '');

    if (empty($nama_customer)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Nama Customer wajib diisi']);
        exit;
    }

    if (empty($nama_singkatan)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Nama Singkatan wajib diisi']);
        exit;
    }

    $sql = "INSERT INTO customer_table (nama_customer, nama_singkatan, created_at) VALUES (?, ?, GETDATE())";
    $params = [$nama_customer, $nama_singkatan];
    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query', 'detail' => $errors]);
        exit;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan customer', 'detail' => $errors]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Customer berhasil ditambahkan']);
    exit;
}

function getCustomerDetail($connection)
{
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
        exit;
    }

    $sql = "SELECT id, nama_customer, nama_singkatan, created_at FROM customer_table WHERE id = ?";
    $params = [$id];
    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query', 'detail' => $errors]);
        exit;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengeksekusi query', 'detail' => $errors]);
        exit;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        exit;
    }

    $created_at = '';
    if (!empty($row['created_at'])) {
        $created_at = $row['created_at'] instanceof DateTime
            ? $row['created_at']->format('Y-m-d H:i:s')
            : $row['created_at'];
    }

    $result = [
        'id' => $row['id'],
        'nama_customer' => $row['nama_customer'],
        'nama_singkatan' => $row['nama_singkatan'],
        'created_at' => $created_at
    ];

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $result]);
    exit;
}

function updateCustomer($connection)
{
    $id = $_POST['id'] ?? '';
    $nama_customer = trim($_POST['nama_customer'] ?? '');
    $nama_singkatan = trim($_POST['nama_singkatan'] ?? '');

    if (empty($id) || empty($nama_customer) || empty($nama_singkatan)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    $sql = "UPDATE customer_table SET nama_customer = ?, nama_singkatan = ? WHERE id = ?";
    $params = [$nama_customer, $nama_singkatan, $id];
    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query update', 'detail' => $errors]);
        exit;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate customer', 'detail' => $errors]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Customer berhasil diupdate']);
    exit;
}

function deleteCustomer($connection)
{
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
        exit;
    }

    $sql = "DELETE FROM customer_table WHERE id = ?";
    $params = [$id];
    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query delete', 'detail' => $errors]);
        exit;
    }

    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus customer', 'detail' => $errors]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Customer berhasil dihapus']);
    exit;
}
