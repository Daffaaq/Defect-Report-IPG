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

            handleClaimActions($connection, $action);
            break;

        case 'POST':
            $action = $_POST['action'] ?? '';

            if (empty($action)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
                exit;
            }

            handleClaimActions($connection, $action);
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
// CLAIM HANDLERS
// ==========================

function handleClaimActions($connection, $action)
{
    try {
        switch ($action) {
            // POST actions
            case 'insert':
                insertClaim($connection);
                break;

            // GET actions
            case 'getPartNo':
                getPartNo($connection);
                break;
            case 'getPartNoByLotNo':
                $lotno = $_GET['lotno'] ?? '';
                getPartNoByLotNo($connection, $lotno);
                break;
            case 'getCustomerByPartNo':
                $partno = $_GET['partno'] ?? '';
                getCustomerByPartno($connection, $partno);
                break;
            case 'getSections':
                getDistinctSections($connection);
                break;
            case 'getDefectsBySection':
                $section = $_GET['section'] ?? '';
                getDefectsBySection($connection, $section);
                break;
            case 'getCustomers':
                getCustomers($connection);
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

function insertClaim($connection)
{
    // Ambil data dari POST
    $nama_section = trim($_POST['nama_section'] ?? '');
    $nama_defect = trim($_POST['nama_defect'] ?? '');
    $nama_group = trim($_POST['nama_group'] ?? '');
    $qty = $_POST['qty'] ?? '';

    $lotno = trim($_POST['lotno'] ?? '');
    $partno = trim($_POST['partno'] ?? '');
    $tanggal_ditemukan = $_POST['tanggal_ditemukan'] ?? '';
    $nama_operator = trim($_POST['nama_operator'] ?? '');
    $deskripsi_masalah = trim($_POST['deskripsi_masalah'] ?? '');
    $nama_customer = trim($_POST['nama_customer'] ?? '');
    $aksi_claim_defect = trim($_POST['aksi_claim_defect'] ?? '');
    $shift = trim($_POST['shift'] ?? '');

    // Validasi data wajib
    $errors = [];

    if (empty($nama_section)) {
        $errors[] = 'Nama Section wajib diisi';
    }

    if (empty($nama_defect)) {
        $errors[] = 'Nama Defect wajib diisi';
    }

    if (empty($nama_group)) {
        $errors[] = 'Nama Group wajib diisi';
    }

    if ($qty === '' || !is_numeric($qty)) {
        $errors[] = 'Qty wajib diisi dan harus berupa angka';
    }

    if (empty($lotno)) {
        $errors[] = 'Lot No wajib diisi';
    }

    if (empty($partno)) {
        $errors[] = 'Part No wajib diisi';
    }

    if (empty($tanggal_ditemukan)) {
        $errors[] = 'Tanggal Ditemukan wajib diisi';
    }

    if (empty($nama_operator)) {
        $errors[] = 'Nama Operator wajib diisi';
    }

    if (empty($deskripsi_masalah)) {
        $errors[] = 'Deskripsi Masalah wajib diisi';
    }

    if (empty($nama_customer)) {
        $errors[] = 'Nama Customer wajib diisi';
    }

    if (empty($aksi_claim_defect)) {
        $errors[] = 'Aksi Claim Defect wajib diisi';
    }

    if (empty($shift)) {
        $errors[] = 'Shift wajib diisi';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Validasi gagal',
            'errors' => $errors
        ]);
        exit;
    }

    // Validasi format tanggal
    $tanggal_obj = DateTime::createFromFormat('Y-m-d', $tanggal_ditemukan);
    if (!$tanggal_obj || $tanggal_obj->format('Y-m-d') !== $tanggal_ditemukan) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Format tanggal ditemukan tidak valid. Gunakan YYYY-MM-DD'
        ]);
        exit;
    }

    // Query insert
    $sql = "INSERT INTO report_claim_defect (
        nama_section,
        nama_defect,
        nama_group,
        qty,
        lotno,
        partno,
        tanggal_ditemukan,
        nama_operator,
        deskripsi_masalah,
        nama_customer,
        aksi_claim_defect,
        shift,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, GETDATE())";

    $params = [
        $nama_section,
        $nama_defect,
        $nama_group,
        $qty,
        $lotno,
        $partno,
        $tanggal_ditemukan,
        $nama_operator,
        $deskripsi_masalah,
        $nama_customer,
        $aksi_claim_defect,
        $shift
    ];

    $stmt = sqlsrv_prepare($connection, $sql, $params);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyiapkan query',
            'detail' => sqlsrv_errors()
        ]);
        exit;
    }

    if (!sqlsrv_execute($stmt)) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data claim',
            'detail' => sqlsrv_errors()
        ]);
        exit;
    }

    // Ambil ID terakhir
    $sql_get_id = "SELECT SCOPE_IDENTITY() AS id";
    $stmt_id = sqlsrv_query($connection, $sql_get_id);
    $row_id = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC);
    $new_id = $row_id['id'] ?? null;

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Data claim berhasil disimpan',
        'data' => [
            'id' => $new_id,
            'nama_section' => $nama_section,
            'nama_defect' => $nama_defect,
            'nama_group' => $nama_group,
            'qty' => $qty,
            'lotno' => $lotno,
            'partno' => $partno,
            'tanggal_ditemukan' => $tanggal_ditemukan,
            'nama_operator' => $nama_operator,
            'deskripsi_masalah' => $deskripsi_masalah,
            'nama_customer' => $nama_customer,
            'aksi_claim_defect' => $aksi_claim_defect,
            'shift' => $shift
        ]
    ]);
    exit;
}

function getPartNo($connection)
{
    // Perbaikan: Sesuaikan nama kolom yang diambil
    $sql = "SELECT DISTINCT PartNameFG AS partno FROM TRRPHMESIN WHERE PartNameFG IS NOT NULL AND PartNameFG != '' AND PartNameFG != 'Coba-JanganDipakai' ORDER BY PartNameFG ASC";
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

    $partnos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $partnos[] = $row['partno'];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $partnos]);
    exit;
}

function getPartNoByLotNo($connection, $lotno)
{
    if (empty($lotno)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Lot No wajib diisi']);
        exit;
    }

    // Gunakan LIKE supaya bisa match LotNo yang ada suffix .001, .002, dll
    $sql = "SELECT TOP 1 PartNameFG AS partno
            FROM dbo.TRRPHMESIN
            WHERE LotNo LIKE ?";

    $params = [$lotno . '%'];
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

    if ($row) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $row['partno']]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Lot No tidak ditemukan']);
    }
    exit;
}

function getCustomerByPartno($connection, $partno)
{
    if (empty($partno)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Part No wajib diisi']);
        exit;
    }

    // Query Development
    // Gunakan LEFT + CHARINDEX untuk mengambil prefix sebelum titik
    $sql = "SELECT TOP 1 m.namacustomer
FROM dbo.TRRPHMESIN t
LEFT JOIN mapping_lot_customer m 
    ON t.PartNameFG = m.partno
WHERE t.PartNameFG LIKE ?";

    // Query Production

    $params = [$partno . '%'];
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

    if ($row && $row['namacustomer']) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $row['namacustomer']]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Customer tidak ditemukan']);
    }
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

function getDefectsBySection($connection, $section)
{
    if (empty($section)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Section wajib dipilih']);
        exit;
    }

    // Query ambil semua nama_defect berdasarkan nama_section
    $sql = "SELECT DISTINCT nama_defect 
            FROM defect_table 
            WHERE nama_section = ? 
            ORDER BY nama_defect ASC";

    $params = [$section];
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

    $defects = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $defects[] = $row['nama_defect'];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $defects]);
    exit;
}

function getCustomers($connection)
{
    $sql = "SELECT nama_customer FROM customer_table ORDER BY nama_customer ASC";
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

    $customers = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $customers[] = $row['nama_customer'];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $customers]);
    exit;
}
