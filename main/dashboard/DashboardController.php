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
            case 'getTodayDefects':  // <-- TAMBAHKAN INI
                getTodayDefects($connection);
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
        $today = date('Y-m-d');
        // Array untuk menyimpan hasil
        $stats = [
            'total_defect' => 0,
            'total_customer' => 0,
            'total_section' => 0,
            'total_problem' => 0,
            'total_repair' => 0,
            'total_scrap' => 0,
            'total_ng' => 0,
            'total_ok' => 0,
            'total_shift1' => 0,
            'total_shift2' => 0,
            'total_hari_ini' => 0,
            'total_hari_ini_ng' => 0,
            'total_hari_ini_ok' => 0
        ];

        // 1. Query Total Defect dari report_claim_defect
        $sql1 = "SELECT ISNULL(SUM(qty), 0) AS TotalData FROM report_claim_defect";
        $stmt1 = sqlsrv_query($connection, $sql1);

        if ($stmt1 === false) {
            throw new Exception("Gagal query total defect: " . print_r(sqlsrv_errors(), true));
        }

        if ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
            $stats['total_defect'] = (int) $row1['TotalData'];
        }
        sqlsrv_free_stmt($stmt1);

        // 2. Query Total Customer dari customer_table
        $sql2 = "SELECT COUNT(*) AS TotalCustomer FROM customer_table";
        $stmt2 = sqlsrv_query($connection, $sql2);

        if ($stmt2 === false) {
            throw new Exception("Gagal query total customer: " . print_r(sqlsrv_errors(), true));
        }

        if ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            $stats['total_customer'] = (int) $row2['TotalCustomer'];
        }
        sqlsrv_free_stmt($stmt2);

        // 3. Query Total Section (DISTINCT nama_section dari defect_table)
        $sql3 = "SELECT COUNT(DISTINCT nama_section) AS TotalSection FROM defect_table";
        $stmt3 = sqlsrv_query($connection, $sql3);

        if ($stmt3 === false) {
            throw new Exception("Gagal query total section: " . print_r(sqlsrv_errors(), true));
        }

        if ($row3 = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
            $stats['total_section'] = (int) $row3['TotalSection'];
        }
        sqlsrv_free_stmt($stmt3);

        // 4. Query Total Problem (Kategori Defect) dari defect_table
        $sql4 = "SELECT COUNT(DISTINCT nama_defect) AS totalDefect FROM defect_table";
        $stmt4 = sqlsrv_query($connection, $sql4);

        if ($stmt4 === false) {
            throw new Exception("Gagal query total problem: " . print_r(sqlsrv_errors(), true));
        }

        if ($row4 = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
            $stats['total_problem'] = (int) $row4['totalDefect'];
        }
        sqlsrv_free_stmt($stmt4);

        // 5. Query Total REPAIR dari report_claim_defect
        $sql5 = "SELECT ISNULL(SUM(qty), 0) AS TotalRepair FROM report_claim_defect WHERE aksi_claim_defect = 'Repair'";
        $stmt5 = sqlsrv_query($connection, $sql5);

        if ($stmt5 === false) {
            throw new Exception("Gagal query total repair: " . print_r(sqlsrv_errors(), true));
        }

        if ($row5 = sqlsrv_fetch_array($stmt5, SQLSRV_FETCH_ASSOC)) {
            $stats['total_repair'] = (int) $row5['TotalRepair'];
        }
        sqlsrv_free_stmt($stmt5);

        // 6. Query Total SCRAP dari report_claim_defect
        $sql6 = "SELECT ISNULL(SUM(qty), 0) AS TotalScrap FROM report_claim_defect WHERE aksi_claim_defect = 'Scrap'";
        $stmt6 = sqlsrv_query($connection, $sql6);

        if ($stmt6 === false) {
            throw new Exception("Gagal query total scrap: " . print_r(sqlsrv_errors(), true));
        }

        if ($row6 = sqlsrv_fetch_array($stmt6, SQLSRV_FETCH_ASSOC)) {
            $stats['total_scrap'] = (int) $row6['TotalScrap'];
        }
        sqlsrv_free_stmt($stmt6);

        // 7. Query Total NG dari report_claim_defect (status = 0)
        $sql7 = "SELECT ISNULL(SUM(qty), 0) AS TotalNg FROM report_claim_defect WHERE status = 0";
        $stmt7 = sqlsrv_query($connection, $sql7);

        if ($stmt7 === false) {
            throw new Exception("Gagal query total ng: " . print_r(sqlsrv_errors(), true));
        }

        if ($row7 = sqlsrv_fetch_array($stmt7, SQLSRV_FETCH_ASSOC)) {
            $stats['total_ng'] = (int) $row7['TotalNg'];
        }
        sqlsrv_free_stmt($stmt7);

        // 8. Query Total OK dari report_claim_defect (status = 1)
        $sql8 = "SELECT ISNULL(SUM(qty), 0) AS TotalOk FROM report_claim_defect WHERE status = 1";
        $stmt8 = sqlsrv_query($connection, $sql8);

        if ($stmt8 === false) {
            throw new Exception("Gagal query total ok: " . print_r(sqlsrv_errors(), true));
        }

        if ($row8 = sqlsrv_fetch_array($stmt8, SQLSRV_FETCH_ASSOC)) {
            $stats['total_ok'] = (int) $row8['TotalOk'];
        }
        sqlsrv_free_stmt($stmt8);

        // 9. Query Total Shift 1 dari report_claim_defect
        $sql9 = "SELECT ISNULL(SUM(qty), 0) AS TotalShift1 FROM report_claim_defect WHERE shift = 1";
        $stmt9 = sqlsrv_query($connection, $sql9);

        if ($stmt9 === false) {
            throw new Exception("Gagal query total shift 1: " . print_r(sqlsrv_errors(), true));
        }

        if ($row9 = sqlsrv_fetch_array($stmt9, SQLSRV_FETCH_ASSOC)) {
            $stats['total_shift1'] = (int) $row9['TotalShift1'];
        }
        sqlsrv_free_stmt($stmt9);

        // 10. Query Total Shift 2 dari report_claim_defect
        $sql10 = "SELECT ISNULL(SUM(qty), 0) AS TotalShift2 FROM report_claim_defect WHERE shift = 2";
        $stmt10 = sqlsrv_query($connection, $sql10);

        if ($stmt10 === false) {
            throw new Exception("Gagal query total shift 2: " . print_r(sqlsrv_errors(), true));
        }

        if ($row10 = sqlsrv_fetch_array($stmt10, SQLSRV_FETCH_ASSOC)) {
            $stats['total_shift2'] = (int) $row10['TotalShift2'];
        }
        sqlsrv_free_stmt($stmt10);

        // 11. Query Total Defect Hari ini (pakai parameter tanggal)
        $sql11 = "SELECT ISNULL(SUM(qty), 0) AS total_hari_ini
                  FROM report_claim_defect
                  WHERE CAST(created_at AS DATE) = ?";
        $stmt11 = sqlsrv_query($connection, $sql11, array($today));
        
        if ($stmt11 === false) {
            throw new Exception("Gagal query total hari ini: " . print_r(sqlsrv_errors(), true));
        }
        
        if ($row11 = sqlsrv_fetch_array($stmt11, SQLSRV_FETCH_ASSOC)) {
            $stats['total_hari_ini'] = (int) $row11['total_hari_ini'];
        }
        sqlsrv_free_stmt($stmt11);
        
        // 12. Query Total NG Hari ini
        $sql12 = "SELECT ISNULL(SUM(qty), 0) AS total_ng_status_0
                  FROM report_claim_defect
                  WHERE status = 0 AND CAST(created_at AS DATE) = ?";
        $stmt12 = sqlsrv_query($connection, $sql12, array($today));
        
        if ($stmt12 === false) {
            throw new Exception("Gagal query total ng hari ini: " . print_r(sqlsrv_errors(), true));
        }
        
        if ($row12 = sqlsrv_fetch_array($stmt12, SQLSRV_FETCH_ASSOC)) {
            $stats['total_hari_ini_ng'] = (int) $row12['total_ng_status_0'];
        }
        sqlsrv_free_stmt($stmt12);
        
        // 13. Query Total OK Hari ini
        $sql13 = "SELECT ISNULL(SUM(qty), 0) AS total_ok_status_1
                  FROM report_claim_defect
                  WHERE status = 1 AND CAST(created_at AS DATE) = ?";
        $stmt13 = sqlsrv_query($connection, $sql13, array($today));
        
        if ($stmt13 === false) {
            throw new Exception("Gagal query total ok hari ini: " . print_r(sqlsrv_errors(), true));
        }
        
        if ($row13 = sqlsrv_fetch_array($stmt13, SQLSRV_FETCH_ASSOC)) {
            $stats['total_hari_ini_ok'] = (int) $row13['total_ok_status_1'];
        }
        sqlsrv_free_stmt($stmt13);

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

function getTodayDefects($connection)
{
    header('Content-Type: application/json');

    try {
        // Ambil tanggal hari ini dari PHP
        $today = date('Y-m-d');
        
        // Query dengan parameter
        $sql = "SELECT 
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
                WHERE CAST(created_at AS DATE) = ?
                ORDER BY created_at DESC";

        $stmt = sqlsrv_query($connection, $sql, array($today));

        if ($stmt === false) {
            throw new Exception("Gagal query data defect hari ini: " . print_r(sqlsrv_errors(), true));
        }

        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Handle DateTime objects
            $tanggalDitemukan = $row['tanggal_ditemukan'];
            if ($tanggalDitemukan instanceof DateTime) {
                $tanggalDitemukan = $tanggalDitemukan->format('Y-m-d');
            } elseif (is_string($tanggalDitemukan)) {
                $tanggalDitemukan = date('Y-m-d', strtotime($tanggalDitemukan));
            }
            
            $createdAt = $row['created_at'];
            if ($createdAt instanceof DateTime) {
                $createdAt = $createdAt->format('Y-m-d H:i:s');
            } elseif (is_string($createdAt)) {
                $createdAt = date('Y-m-d H:i:s', strtotime($createdAt));
            }
            
            $data[] = [
                'nama_section' => $row['nama_section'],
                'nama_defect' => $row['nama_defect'],
                'lotno' => $row['lotno'],
                'partno' => $row['partno'],
                'tanggal_ditemukan' => $tanggalDitemukan,
                'created_at' => $createdAt,
                'qty' => (int) $row['qty'],
                'status' => (int) $row['status'],
                'nama_customer' => $row['nama_customer']
            ];
        }

        sqlsrv_free_stmt($stmt);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data defect hari ini berhasil dimuat',
            'data' => $data,
            'total' => count($data),
            'tanggal' => $today // untuk debugging
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data defect hari ini: ' . $e->getMessage()
        ]);
        exit;
    }
}