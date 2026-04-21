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
            case 'updateSection': // CASE BARU
                updateSectionName($connection);
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
    $checkSql = "SELECT nama_section FROM defect_table WHERE nama_section COLLATE SQL_Latin1_General_CP1_CI_AS = ? COLLATE SQL_Latin1_General_CP1_CI_AS";
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

// function updateDefect($connection)
// {
//     $id = $_POST['id'] ?? '';
//     $nama_section = trim($_POST['nama_section'] ?? '');
//     $nama_defect = trim($_POST['nama_defect'] ?? '');

//     if (empty($id) || empty($nama_section) || empty($nama_defect)) {
//         http_response_code(400);
//         echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
//         exit;
//     }

//     // CEK APAKAH SECTION SUDAH ADA (CASE INSENSITIVE) - KECUALI DIRINYA SENDIRI
//     $checkSql = "SELECT nama_section FROM defect_table 
//                  WHERE nama_section COLLATE SQL_Latin1_General_CP1_CI_AS = ? COLLATE SQL_Latin1_General_CP1_CI_AS 
//                  AND id != ?";
//     $checkParams = [$nama_section, $id];
//     $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

//     $existingSection = null;

//     if ($checkStmt && sqlsrv_execute($checkStmt)) {
//         $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
//         if ($row) {
//             $existingSection = $row['nama_section'];
//         }
//     }

//     // Jika ada duplikat dengan section lain, gunakan format yang sudah ada
//     if ($existingSection) {
//         $nama_section = $existingSection;
//     }

//     $sql = "UPDATE defect_table SET nama_section = ?, nama_defect = ? WHERE id = ?";
//     $params = [$nama_section, $nama_defect, $id];
//     $stmt = sqlsrv_prepare($connection, $sql, $params);

//     if (!$stmt) {
//         $errors = sqlsrv_errors();
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query update', 'detail' => $errors]);
//         exit;
//     }

//     if (!sqlsrv_execute($stmt)) {
//         $errors = sqlsrv_errors();
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate defect', 'detail' => $errors]);
//         exit;
//     }

//     $message = 'Defect berhasil diupdate';
//     if ($existingSection && strcasecmp($existingSection, trim($_POST['nama_section'] ?? '')) !== 0) {
//         $message .= " (Format section disesuaikan menjadi: $existingSection)";
//     }

//     http_response_code(200);
//     echo json_encode(['status' => 'success', 'message' => $message]);
//     exit;
// }

// function updateDefect($connection)
// {
//     $id = $_POST['id'] ?? '';

//     // Handle potential array values for nama_section
//     $nama_section_raw = $_POST['nama_section'] ?? '';
//     if (is_array($nama_section_raw)) {
//         $nama_section_raw = implode(', ', $nama_section_raw);
//     }
//     $nama_section = trim($nama_section_raw);

//     // Handle potential array values for nama_defect
//     $nama_defect_raw = $_POST['nama_defect'] ?? '';
//     if (is_array($nama_defect_raw)) {
//         $nama_defect_raw = implode(', ', $nama_defect_raw);
//     }
//     $nama_defect = trim($nama_defect_raw);

//     if (empty($id) || empty($nama_section) || empty($nama_defect)) {
//         http_response_code(400);
//         echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
//         exit;
//     }

//     // CEK APAKAH SECTION SUDAH ADA (CASE INSENSITIVE) - KECUALI DIRINYA SENDIRI
//     $checkSql = "SELECT nama_section FROM defect_table 
//                  WHERE nama_section COLLATE SQL_Latin1_General_CP1_CI_AS = ? COLLATE SQL_Latin1_General_CP1_CI_AS 
//                  AND id != ?";
//     $checkParams = [$nama_section, $id];
//     $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

//     $existingSection = null;

//     if ($checkStmt && sqlsrv_execute($checkStmt)) {
//         $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
//         if ($row) {
//             $existingSection = $row['nama_section'];
//         }
//     }

//     // Jika ada duplikat dengan section lain, gunakan format yang sudah ada
//     if ($existingSection) {
//         $nama_section = $existingSection;
//     }

//     $sql = "UPDATE defect_table SET nama_section = ?, nama_defect = ? WHERE id = ?";
//     $params = [$nama_section, $nama_defect, $id];
//     $stmt = sqlsrv_prepare($connection, $sql, $params);

//     if (!$stmt) {
//         $errors = sqlsrv_errors();
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query update', 'detail' => $errors]);
//         exit;
//     }

//     if (!sqlsrv_execute($stmt)) {
//         $errors = sqlsrv_errors();
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate defect', 'detail' => $errors]);
//         exit;
//     }

//     $message = 'Defect berhasil diupdate';
//     if ($existingSection && strcasecmp($existingSection, trim($nama_section_raw)) !== 0) {
//         $message .= " (Format section disesuaikan menjadi: $existingSection)";
//     }

//     http_response_code(200);
//     echo json_encode(['status' => 'success', 'message' => $message]);
//     exit;
// }

function updateDefect($connection)
{
    $id = $_POST['id'] ?? '';

    // Handle potential array values for nama_section
    $nama_section_raw = $_POST['nama_section'] ?? '';
    if (is_array($nama_section_raw)) {
        $nama_section_raw = implode(', ', $nama_section_raw);
    }
    $nama_section = trim($nama_section_raw);

    // Handle potential array values for nama_defect
    $nama_defect_raw = $_POST['nama_defect'] ?? '';
    if (is_array($nama_defect_raw)) {
        $nama_defect_raw = implode(', ', $nama_defect_raw);
    }
    $nama_defect_baru = trim($nama_defect_raw);

    if (empty($id) || empty($nama_section) || empty($nama_defect_baru)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // AMBIL NAMA_DEFECT LAMA SEBELUM UPDATE
    $sql_old = "SELECT nama_defect FROM defect_table WHERE id = ?";
    $stmt_old = sqlsrv_query($connection, $sql_old, [$id]);
    $old_data = sqlsrv_fetch_array($stmt_old, SQLSRV_FETCH_ASSOC);

    if (!$old_data) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Data master tidak ditemukan']);
        exit;
    }

    $old_defect = trim($old_data['nama_defect']);

    // CEK APAKAH SECTION SUDAH ADA
    $checkSql = "SELECT nama_section FROM defect_table 
                 WHERE nama_section COLLATE SQL_Latin1_General_CP1_CI_AS = ? COLLATE SQL_Latin1_General_CP1_CI_AS 
                 AND id != ?";
    $checkParams = [$nama_section, $id];
    $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

    $existingSection = null;

    if ($checkStmt && sqlsrv_execute($checkStmt)) {
        $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $existingSection = $row['nama_section'];
        }
    }

    if ($existingSection) {
        $nama_section = $existingSection;
    }

    // MULAI TRANSACTION
    sqlsrv_begin_transaction($connection);

    try {
        // STEP 1: UPDATE MASTER defect_table
        $sql = "UPDATE defect_table SET nama_section = ?, nama_defect = ? WHERE id = ?";
        $params = [$nama_section, $nama_defect_baru, $id];
        $stmt = sqlsrv_prepare($connection, $sql, $params);

        if (!$stmt || !sqlsrv_execute($stmt)) {
            throw new Exception('Gagal mengupdate master defect');
        }

        // 🔥 STEP 2: AMBIL SEMUA TRANSAKSI YANG MENGANDUNG OLD_DEFECT
        $sql_get_claims = "SELECT id, nama_defect FROM report_claim_defect 
                          WHERE nama_defect LIKE ?";
        $pattern = '%' . $old_defect . '%';
        $stmt_claims = sqlsrv_query($connection, $sql_get_claims, [$pattern]);

        if (!$stmt_claims) {
            throw new Exception('Gagal mengambil data transaksi claim');
        }

        $updated_count = 0;
        $updated_ids = [];

        // 🔥 STEP 3: LOOP SATU PER SATU DAN UPDATE
        while ($row = sqlsrv_fetch_array($stmt_claims, SQLSRV_FETCH_ASSOC)) {
            $claim_id = $row['id'];
            $current_defects = explode(',', $row['nama_defect']);
            $updated = false;

            // Loop setiap defect dalam transaksi
            foreach ($current_defects as $key => $defect) {
                $defect_trim = trim($defect);
                // Bandingkan persis (case-sensitive atau insensitive?)
                if ($defect_trim === $old_defect) {
                    $current_defects[$key] = $nama_defect_baru;
                    $updated = true;
                }
            }

            if ($updated) {
                $new_defects = implode(',', $current_defects);
                $sql_update_claim = "UPDATE report_claim_defect 
                                    SET nama_defect = ? 
                                    WHERE id = ?";
                $stmt_update = sqlsrv_query($connection, $sql_update_claim, [$new_defects, $claim_id]);

                if ($stmt_update) {
                    $updated_count++;
                    $updated_ids[] = $claim_id;
                }
            }
        }

        // STEP 4: COMMIT
        sqlsrv_commit($connection);

        $message = 'Defect berhasil diupdate';
        if ($existingSection && strcasecmp($existingSection, trim($nama_section_raw)) !== 0) {
            $message .= " (Format section disesuaikan menjadi: $existingSection)";
        }
        $message .= " | Transaksi claim terupdate: $updated_count baris";

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'old_defect' => $old_defect,
                'new_defect' => $nama_defect_baru,
                'affected_claim_ids' => $updated_ids,
                'affected_count' => $updated_count
            ]
        ]);
    } catch (Exception $e) {
        sqlsrv_rollback($connection);
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal update: ' . $e->getMessage()
        ]);
    }
    exit;
}

// function updateDefect($connection)
// {
//     $id = $_POST['id'] ?? '';

//     // Handle potential array values for nama_section
//     $nama_section_raw = $_POST['nama_section'] ?? '';
//     if (is_array($nama_section_raw)) {
//         $nama_section_raw = implode(', ', $nama_section_raw);
//     }
//     $nama_section = trim($nama_section_raw);

//     // Handle potential array values for nama_defect
//     $nama_defect_raw = $_POST['nama_defect'] ?? '';
//     if (is_array($nama_defect_raw)) {
//         $nama_defect_raw = implode(', ', $nama_defect_raw);
//     }
//     $nama_defect_baru = trim($nama_defect_raw);

//     if (empty($id) || empty($nama_section) || empty($nama_defect_baru)) {
//         http_response_code(400);
//         echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
//         exit;
//     }

//     // 🔥 PANGGIL STORED PROCEDURE DENGAN PREPARE + EXECUTE
//     $sql = "EXEC sp_sync_defect_to_claims ?, ?, ?";
//     $params = [$id, $nama_section, $nama_defect_baru];

//     $stmt = sqlsrv_prepare($connection, $sql, $params);

//     if ($stmt === false) {
//         http_response_code(500);
//         echo json_encode([
//             'status' => 'error',
//             'message' => 'Gagal menyiapkan query',
//             'detail' => sqlsrv_errors()
//         ]);
//         exit;
//     }

//     if (!sqlsrv_execute($stmt)) {
//         http_response_code(500);
//         echo json_encode([
//             'status' => 'error',
//             'message' => 'Gagal mengeksekusi stored procedure',
//             'detail' => sqlsrv_errors()
//         ]);
//         exit;
//     }

//     // Ambil result dari SP
//     $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
//     sqlsrv_free_stmt($stmt);

//     // Cek apakah ada error dari SP
//     if (!$result) {
//         http_response_code(500);
//         echo json_encode([
//             'status' => 'error',
//             'message' => 'Tidak ada response dari stored procedure'
//         ]);
//         exit;
//     }

//     // Cek apakah SP mengembalikan error (dari CATCH block)
//     if (isset($result['error_number'])) {
//         http_response_code(500);
//         echo json_encode([
//             'status' => 'error',
//             'message' => $result['error_message'] ?? 'Gagal update defect'
//         ]);
//         exit;
//     }

//     if (isset($result['status']) && $result['status'] === 'GAGAL') {
//         http_response_code(500);
//         echo json_encode([
//             'status' => 'error',
//             'message' => $result['error_message'] ?? 'Gagal update defect'
//         ]);
//         exit;
//     }

//     // Kirim response sukses
//     http_response_code(200);
//     echo json_encode([
//         'status' => 'success',
//         'message' => $result['status_message'] ?? 'Defect berhasil diupdate',
//         'data' => [
//             'old_defect' => $result['old_defect'] ?? '',
//             'new_defect' => $result['new_defect'] ?? $nama_defect_baru,
//             'affected_count' => isset($result['affected_count']) ? (int)$result['affected_count'] : 0
//         ]
//     ]);
//     exit;
// }

// Function baru untuk update section name secara massal
function updateSectionName($connection)
{
    $old_section = trim($_POST['old_section'] ?? '');
    $new_section = trim($_POST['new_section'] ?? '');

    if (empty($old_section) || empty($new_section)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    if ($old_section === $new_section) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Nama section baru harus berbeda']);
        exit;
    }

    // CEK APAKAH SECTION BARU SUDAH ADA (CASE INSENSITIVE)
    $checkSql = "SELECT nama_section FROM defect_table 
                 WHERE nama_section COLLATE SQL_Latin1_General_CP1_CI_AS = ? COLLATE SQL_Latin1_General_CP1_CI_AS";
    $checkParams = [$new_section];
    $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

    $existingSection = null;

    if ($checkStmt && sqlsrv_execute($checkStmt)) {
        $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $existingSection = $row['nama_section'];
        }
    }

    // Jika section baru sudah ada, gunakan format yang sudah ada
    if ($existingSection) {
        $new_section_final = $existingSection;
    } else {
        $new_section_final = $new_section;
    }

    // Mulai transaction
    sqlsrv_begin_transaction($connection);

    try {
        // UPDATE SEMUA DEFECT DENGAN SECTION LAMA KE SECTION BARU
        $updateSql = "UPDATE defect_table 
                      SET nama_section = ? 
                      WHERE nama_section COLLATE SQL_Latin1_General_CP1_CI_AS = ? COLLATE SQL_Latin1_General_CP1_CI_AS";
        $updateParams = [$new_section_final, $old_section];
        $updateStmt = sqlsrv_prepare($connection, $updateSql, $updateParams);

        if (!$updateStmt || !sqlsrv_execute($updateStmt)) {
            throw new Exception("Gagal mengupdate section");
        }

        $affectedRows = sqlsrv_rows_affected($updateStmt);

        if ($affectedRows === 0) {
            throw new Exception("Tidak ada data yang diupdate");
        }

        sqlsrv_commit($connection);

        // Buat pesan response
        $message = "Berhasil mengupdate section dari '{$old_section}' menjadi '{$new_section_final}'";

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'updated_count' => $affectedRows,
            'old_section' => $old_section,
            'new_section' => $new_section_final
        ]);
    } catch (Exception $e) {
        sqlsrv_rollback($connection);
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
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
