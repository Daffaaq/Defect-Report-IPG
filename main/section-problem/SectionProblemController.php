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

            handleDefectActions($connection, $action);
            break;

        case 'POST':
            $action = $_POST['action'] ?? '';

            if (empty($action)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
                exit;
            }

            handleDefectActions($connection, $action);
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
// DEFECT HANDLERS
// ==========================

function handleDefectActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getAll':
                getAllDefects($connection);
                break;
            case 'getSections':
                getDistinctSections($connection);
                break;
            case 'insert':
                insertDefect($connection);
                break;
            case 'update':
                updateDefect($connection);
                break;
            case 'delete':
                deleteDefect($connection);
                break;
            case 'getDetail':
                getDefectDetail($connection);
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

function getAllDefects($connection)
{
    $sql = "SELECT id, nama_section, nama_defect, created_at FROM defect_table ORDER BY nama_section ASC, id ASC";
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
            'nama_section' => $row['nama_section'],
            'nama_defect' => $row['nama_defect'],
            'created_at' => $created_at
        ];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $rows]);
    exit;
}

function getDistinctSections($connection)
{
    $sql = "SELECT DISTINCT nama_section FROM defect_table ORDER BY nama_section ASC";
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
        $rows[] = $row['nama_section'];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $rows]);
    exit;
}

function insertDefect($connection)
{
    $nama_section = trim($_POST['nama_section'] ?? '');
    $nama_defects = $_POST['nama_defect'] ?? []; // Array of defects

    if (empty($nama_section)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Nama Section wajib diisi']);
        exit;
    }

    if (empty($nama_defects) || !is_array($nama_defects)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Minimal harus mengisi 1 defect']);
        exit;
    }

    // CEK APAKAH SECTION SUDAH ADA (CASE INSENSITIVE)
    $checkSql = "SELECT nama_section FROM defect_table WHERE LOWER(nama_section) = LOWER(?)";
    $checkParams = [$nama_section];
    $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

    $existingSection = null;

    if ($checkStmt && sqlsrv_execute($checkStmt)) {
        $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $existingSection = $row['nama_section']; // Ambil format yang sudah ada di DB
        }
    }

    // Jika ada duplikat, gunakan format yang sudah ada di DB
    if ($existingSection) {
        $nama_section = $existingSection; // AUTO-FORMAT: pake format yang sudah ada
    }

    // Filter defects yang tidak kosong
    $valid_defects = array_filter($nama_defects, function ($defect) {
        return !empty(trim($defect));
    });

    if (empty($valid_defects)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Defect tidak boleh kosong']);
        exit;
    }

    // Mulai transaction
    sqlsrv_begin_transaction($connection);

    $success_count = 0;
    $errors = [];

    foreach ($valid_defects as $nama_defect) {
        $nama_defect = trim($nama_defect);

        $sql = "INSERT INTO defect_table (nama_section, nama_defect, created_at) VALUES (?, ?, GETDATE())";
        $params = [$nama_section, $nama_defect];
        $stmt = sqlsrv_prepare($connection, $sql, $params);

        if (!$stmt) {
            $errors[] = "Gagal menyiapkan query untuk defect: $nama_defect";
            continue;
        }

        if (!sqlsrv_execute($stmt)) {
            $errors[] = "Gagal menambahkan defect: $nama_defect";
            continue;
        }

        $success_count++;
    }

    if ($success_count > 0) {
        sqlsrv_commit($connection);

        $message = $success_count > 1
            ? "Berhasil menambahkan $success_count defect"
            : "Defect berhasil ditambahkan";

        // Tambahkan info jika format section disesuaikan
        if ($existingSection && strcasecmp($existingSection, trim($_POST['nama_section'] ?? '')) !== 0) {
            $message .= " (Format section disesuaikan menjadi: $existingSection)";
        }

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'total_inserted' => $success_count,
            'final_section_format' => $nama_section
        ]);
    } else {
        sqlsrv_rollback($connection);
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menambahkan defect: ' . implode(', ', $errors)
        ]);
    }
    exit;
}

function getDefectDetail($connection)
{
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
        exit;
    }

    $sql = "SELECT id, nama_section, nama_defect, created_at FROM defect_table WHERE id = ?";
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
        'nama_section' => $row['nama_section'],
        'nama_defect' => $row['nama_defect'],
        'created_at' => $created_at
    ];

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $result]);
    exit;
}

function updateDefect($connection)
{
    $id = $_POST['id'] ?? '';
    $nama_section = trim($_POST['nama_section'] ?? '');
    $nama_defect = trim($_POST['nama_defect'] ?? '');

    if (empty($id) || empty($nama_section) || empty($nama_defect)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // CEK APAKAH SECTION SUDAH ADA (CASE INSENSITIVE) - KECUALI DIRINYA SENDIRI
    $checkSql = "SELECT nama_section FROM defect_table WHERE LOWER(nama_section) = LOWER(?) AND id != ?";
    $checkParams = [$nama_section, $id];
    $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

    $existingSection = null;

    if ($checkStmt && sqlsrv_execute($checkStmt)) {
        $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $existingSection = $row['nama_section'];
        }
    }

    // Jika ada duplikat dengan section lain, gunakan format yang sudah ada
    if ($existingSection) {
        $nama_section = $existingSection;
    }

    $sql = "UPDATE defect_table SET nama_section = ?, nama_defect = ? WHERE id = ?";
    $params = [$nama_section, $nama_defect, $id];
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
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate defect', 'detail' => $errors]);
        exit;
    }

    $message = 'Defect berhasil diupdate';
    if ($existingSection && strcasecmp($existingSection, trim($_POST['nama_section'] ?? '')) !== 0) {
        $message .= " (Format section disesuaikan menjadi: $existingSection)";
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => $message]);
    exit;
}

function deleteDefect($connection)
{
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
        exit;
    }

    $sql = "DELETE FROM defect_table WHERE id = ?";
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
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus defect', 'detail' => $errors]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Defect berhasil dihapus']);
    exit;
}
