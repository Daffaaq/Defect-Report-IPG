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
            'total_problem' => 0,
            'total_repair' => 0,  // Tambahan
            'total_scrap' => 0     // Tambahan
        ];

        // 1. Query Total Defect dari report_claim_defect
        $sql1 = "SELECT ISNULL(SUM(qty), 0) AS TotalData FROM report_claim_defect";
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

        // 5. Query Total REPAIR dari report_claim_defect
        $sql5 = "SELECT ISNULL(SUM(qty), 0) AS TotalRepair
FROM report_claim_defect
WHERE aksi_claim_defect = 'Repair'";
        $stmt5 = sqlsrv_query($connection, $sql5);

        if ($stmt5 === false) {
            throw new Exception("Gagal query total repair: " . print_r(sqlsrv_errors(), true));
        }

        if ($row5 = sqlsrv_fetch_array($stmt5, SQLSRV_FETCH_ASSOC)) {
            $stats['total_repair'] = (int)$row5['TotalRepair'];
        }
        sqlsrv_free_stmt($stmt5);

        // 6. Query Total SCRAP dari report_claim_defect
        $sql6 = "SELECT ISNULL(SUM(qty), 0) AS TotalScrap FROM report_claim_defect WHERE aksi_claim_defect = 'Scrap'";
        $stmt6 = sqlsrv_query($connection, $sql6);

        if ($stmt6 === false) {
            throw new Exception("Gagal query total scrap: " . print_r(sqlsrv_errors(), true));
        }

        if ($row6 = sqlsrv_fetch_array($stmt6, SQLSRV_FETCH_ASSOC)) {
            $stats['total_scrap'] = (int)$row6['TotalScrap'];
        }
        sqlsrv_free_stmt($stmt6);

        // 7. Query Total NG dari defect_table (qty)
        $sql7 = "SELECT ISNULL(SUM(qty), 0) AS TotalNg FROM report_claim_defect WHERE status = 0";
        $stmt7 = sqlsrv_query($connection, $sql7);

        if ($stmt7 === false) {
            throw new Exception("Gagal query total ng: " . print_r(sqlsrv_errors(), true));
        }

        if ($row7 = sqlsrv_fetch_array($stmt7, SQLSRV_FETCH_ASSOC)) {
            $stats['total_ng'] = (int)$row7['TotalNg'];
        }
        sqlsrv_free_stmt($stmt7);

        // 8. Query Total OK dari report_claim_defect
        $sql8 = "SELECT ISNULL(SUM(qty), 0) AS TotalOk FROM report_claim_defect WHERE status = 1";
        $stmt8 = sqlsrv_query($connection, $sql8);

        if ($stmt8 === false) {
            throw new Exception("Gagal query total ok: " . print_r(sqlsrv_errors(), true));
        }

        if ($row8 = sqlsrv_fetch_array($stmt8, SQLSRV_FETCH_ASSOC)) {
            $stats['total_ok'] = (int)$row8['TotalOk'];
        }
        sqlsrv_free_stmt($stmt8);
                
        // 9. Query Total Pershift dari report_claim_defect
        $sql9 = "SELECT ISNULL(SUM(qty), 0) AS TotalShift1 FROM report_claim_defect WHERE shift = 1";
        $stmt9 = sqlsrv_query($connection, $sql9);

        if ($stmt9 === false) {
            throw new Exception("Gagal query total shift 1: " . print_r(sqlsrv_errors(), true));
        }

        if ($row9 = sqlsrv_fetch_array($stmt9, SQLSRV_FETCH_ASSOC)) {
            $stats['total_shift1'] = (int)$row9['TotalShift1'];
        }
        sqlsrv_free_stmt($stmt9);

        // 10. Query Total Pershift dari report_claim_defect
        $sql10 = "SELECT ISNULL(SUM(qty), 0) AS TotalShift2 FROM report_claim_defect WHERE shift = 2";
        $stmt10 = sqlsrv_query($connection, $sql10);

        if ($stmt10 === false) {
            throw new Exception("Gagal query total shift 2: " . print_r(sqlsrv_errors(), true));
        }

        if ($row10 = sqlsrv_fetch_array($stmt10, SQLSRV_FETCH_ASSOC)) {
            $stats['total_shift2'] = (int)$row10['TotalShift2'];
        }
        sqlsrv_free_stmt($stmt10);

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
