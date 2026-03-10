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

            handleDashboardActions($connection, $action);
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
// DASHBOARD HANDLERS
// ==========================

function handleDashboardActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getDashboardStats':
                getDashboardStats($connection);
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

function getDashboardStats($connection)
{
    header('Content-Type: application/json');

    try {
        // Array untuk menyimpan hasil
        $stats = [
            'total_defect' => 0,
            'total_customer' => 0,
            'total_section' => 0,
            'total_problem' => 0
        ];

        // 1. Query Total Defect dari report_claim_defect
        $sql1 = "SELECT COUNT(*) AS TotalData FROM report_claim_defect";
        $stmt1 = sqlsrv_query($connection, $sql1);

        if ($stmt1 === false) {
            throw new Exception("Gagal query total defect: " . print_r(sqlsrv_errors(), true));
        }

        if ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
            $stats['total_defect'] = (int)$row1['TotalData'];
        }
        sqlsrv_free_stmt($stmt1);

        // 2. Query Total Customer dari customer_table
        $sql2 = "SELECT COUNT(*) AS TotalCustomer FROM customer_table";
        $stmt2 = sqlsrv_query($connection, $sql2);

        if ($stmt2 === false) {
            throw new Exception("Gagal query total customer: " . print_r(sqlsrv_errors(), true));
        }

        if ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            $stats['total_customer'] = (int)$row2['TotalCustomer'];
        }
        sqlsrv_free_stmt($stmt2);

        // 3. Query Total Section (DISTINCT nama_section dari defect_table)
        $sql3 = "SELECT COUNT(DISTINCT nama_section) AS TotalSection FROM defect_table";
        $stmt3 = sqlsrv_query($connection, $sql3);

        if ($stmt3 === false) {
            throw new Exception("Gagal query total section: " . print_r(sqlsrv_errors(), true));
        }

        if ($row3 = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
            $stats['total_section'] = (int)$row3['TotalSection'];
        }
        sqlsrv_free_stmt($stmt3);

        // 4. Query Total Problem (Kategori Defect) dari defect_table
        $sql4 = "SELECT COUNT(DISTINCT nama_defect) AS totalDefect FROM defect_table";
        $stmt4 = sqlsrv_query($connection, $sql4);

        if ($stmt4 === false) {
            throw new Exception("Gagal query total problem: " . print_r(sqlsrv_errors(), true));
        }

        if ($row4 = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
            $stats['total_problem'] = (int)$row4['totalDefect'];
        }
        sqlsrv_free_stmt($stmt4);

        // Kirim response sukses
        echo json_encode([
            'status' => 'success',
            'message' => 'Data dashboard berhasil dimuat',
            'data' => $stats
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data dashboard: ' . $e->getMessage()
        ]);
        exit;
    }
}
