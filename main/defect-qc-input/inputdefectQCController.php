<?php
// inputdefectCNCController.php
require_once '../../helper/auth.php';
isLogin();
require_once '../../helper/connection.php';

// ============================================
// CONTROLLER / BACKEND PROCESSING
// ============================================

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// 1. Handle Search Pegawai (GET) - dipakai untuk autocomplete Nama -> Noreg
if ($action === 'search') {
    header("Content-Type: application/json");

    $search = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (empty($search) || strlen($search) < 2) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }

    $query = "SELECT TOP 10 
                Noreg,
                Nama,
                Divisi
              FROM DB_PEGAWAI 
              WHERE Divisi = 'Plant 1' 
                AND tgl_resign IS NULL
                AND (Nama LIKE ? OR Noreg LIKE ?)
              ORDER BY Nama ASC";

    $params = array("%$search%", "%$search%");
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . print_r(sqlsrv_errors(), true)
        ]);
        exit;
    }

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = [
            'Noreg' => $row['Noreg'],
            'Nama' => $row['Nama'],
            'Divisi' => $row['Divisi']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}

// 2. Handle Get Terminal (GET) - untuk mengambil data terminal berdasarkan lot dan skema
if ($action === 'getTerminal') {
    header("Content-Type: application/json");

    $lot_no = isset($_GET['lot_no']) ? trim($_GET['lot_no']) : '';
    $skema = isset($_GET['skema']) ? trim($_GET['skema']) : '';
    $section = isset($_GET['section']) ? trim($_GET['section']) : '';

    // Validasi hanya untuk CNC dan CRIMPING
    $allowedSections = ['CNC', 'CRIMPING_R2', 'CRIMPING_R4'];
    if (!in_array($section, $allowedSections)) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }

    if (empty($lot_no) || empty($skema)) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }

    // Query untuk mengambil terminal dari TrRPHMesin
    $query = "SELECT 
                TERMINALA,
                TERMINALB,
                LotNo,
                SeqNoInt
              FROM TrRPHMesin 
              WHERE LotNo = ? AND SeqNoInt = ?
              ORDER BY TERMINALA ASC";

    $params = array($lot_no, $skema);
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . print_r(sqlsrv_errors(), true)
        ]);
        exit;
    }

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Tambahkan TERMINALA
        if (!empty(trim($row['TERMINALA']))) {
            $data[] = [
                'terminal' => trim($row['TERMINALA']),
                'terminal_display' => trim($row['TERMINALA'])
            ];
        }
        
        // Tambahkan TERMINALB jika berbeda dengan TERMINALA
        if (!empty(trim($row['TERMINALB'])) && trim($row['TERMINALB']) !== trim($row['TERMINALA'])) {
            $data[] = [
                'terminal' => trim($row['TERMINALB']),
                'terminal_display' => trim($row['TERMINALB'])
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}

// 3. Handle Save Data (POST)
if ($action === 'save') {
    header("Content-Type: application/json");

    // Ambil data dari form
    $section          = isset($_POST['section']) ? trim($_POST['section']) : '';
    $noreg_pembuat_ng = isset($_POST['noreg_pembuat_ng']) ? trim($_POST['noreg_pembuat_ng']) : '';
    $no_mesin         = isset($_POST['no_mesin']) ? trim($_POST['no_mesin']) : '';
    $lot_no           = isset($_POST['lot_no']) ? trim($_POST['lot_no']) : '';
    $skema            = isset($_POST['skema']) ? trim($_POST['skema']) : '';
    $qty              = isset($_POST['qty']) ? intval($_POST['qty']) : 0;
    $defect           = isset($_POST['defect']) ? trim($_POST['defect']) : '';
    $terminal         = isset($_POST['terminal']) ? trim($_POST['terminal']) : ''; // Terminal yang dipilih (bisa TERMINALA atau gabungan)

    // Noreg dari session (user yang login)
    $noreg = isset($_SESSION['noreg']) ? $_SESSION['noreg'] : '';

    // Validasi
    $errors = [];
    if (empty($section))  $errors[] = 'Section harus dipilih';
    if (empty($noreg_pembuat_ng)) $errors[] = 'Pembuat NG harus dipilih';
    if (empty($no_mesin) || !is_numeric($no_mesin)) $errors[] = 'No Mesin harus diisi dengan angka';
    if (empty($lot_no))   $errors[] = 'Lot No harus diisi';
    if (empty($skema))    $errors[] = 'Skema harus diisi';
    if ($qty < 1)         $errors[] = 'QTY harus lebih dari 0';
    if (empty($defect))   $errors[] = 'Defect harus dipilih';
    if (empty($noreg))    $errors[] = 'User tidak terautentikasi (session noreg kosong)';

    // Validasi terminal untuk section CNC dan CRIMPING
    $allowedSectionsWithTerminal = ['CNC', 'CRIMPING_R2', 'CRIMPING_R4'];
    if (in_array($section, $allowedSectionsWithTerminal) && empty($terminal)) {
        $errors[] = 'Terminal harus dipilih untuk section CNC atau CRIMPING';
    }

    // Mapping prefix kode mesin berdasarkan Section
    $mesinPrefix = [
        'CNC'          => 'CSM',
        'CRIMPING_R2'  => 'R2CRP',
        'CRIMPING_R4'  => 'R4CRP',
        'ASSY'         => 'ASSY',
        'JOINT_R2'     => 'R2JNT',
        'JOINT_R4'     => 'R4JNT',
    ];

    if (!isset($mesinPrefix[$section])) {
        $errors[] = 'Section tidak valid';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errors)
        ]);
        exit;
    }

    // Gabungkan prefix + nomor mesin (dipad jadi 2 digit, misal 1 -> 01)
    $kodeMesin = $mesinPrefix[$section] . str_pad($no_mesin, 2, '0', STR_PAD_LEFT);

    // Insert ke database dengan tambahan kolom terminal
    $query = "INSERT INTO CNCDefect 
              (Lot, Skema, JenisDefect, Qty, CreatedAt, KodeMesin, Noreg, noreg_pembuat_ng, type, terminal) 
              VALUES (?, ?, ?, ?, GETDATE(), ?, ?, ?, ?, ?)";

    $params = array($lot_no, $skema, $defect, $qty, $kodeMesin, $noreg, $noreg_pembuat_ng, 1, $terminal);

    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $errorMsg = 'Gagal menyimpan data';
        if ($errors) {
            $errorMsg .= ': ' . print_r($errors, true);
        }
        echo json_encode([
            'success' => false,
            'message' => $errorMsg
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Data defect berhasil disimpan!'
    ]);
    exit;
}

// Jika tidak ada action, redirect ke index
header('Location: index.php');
exit;
?>