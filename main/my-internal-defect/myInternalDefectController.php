<?php
session_start();
include '../../helper/connection.php';
header("Content-Type: application/json");

// Ambil parameter filter
$dateStart = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$dateEnd = isset($_GET['date_end']) ? $_GET['date_end'] : '';
$section = isset($_GET['section']) ? $_GET['section'] : 'all';
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Ambil Noreg dari session
$noreg = $_SESSION['noreg'] ?? '';

if (empty($noreg)) {
    echo json_encode([
        'success' => false,
        'message' => 'Noreg user tidak ditemukan dalam session.'
    ]);
    exit;
}

// ============================================
// 1. DELETE DATA
// ============================================
if ($action === 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID tidak valid'
        ]);
        exit;
    }

    // Cek apakah data milik user yang login
    $checkQuery = "SELECT Noreg FROM CNCDefect WHERE ID = ?";
    $checkStmt = sqlsrv_query($connection, $checkQuery, [$id]);

    if ($checkStmt === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Error checking data: ' . print_r(sqlsrv_errors(), true)
        ]);
        exit;
    }

    $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

    if (!$row) {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ]);
        exit;
    }

    // Validasi apakah Noreg sesuai
    if ($row['Noreg'] !== $noreg) {
        echo json_encode([
            'success' => false,
            'message' => 'Anda tidak memiliki izin untuk menghapus data ini'
        ]);
        exit;
    }

    // Hapus data
    $deleteQuery = "DELETE FROM CNCDefect WHERE ID = ? AND Noreg = ?";
    $deleteStmt = sqlsrv_query($connection, $deleteQuery, [$id, $noreg]);

    if ($deleteStmt === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menghapus data: ' . print_r(sqlsrv_errors(), true)
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil dihapus'
    ]);
    exit;
}

// ============================================
// 2. GET DATA (LIST)
// ============================================
// Mapping section ke prefix mesin
$mesinPrefix = [
    'CNC'          => 'CSM',
    'CRIMPING_R2'  => 'R2CRP',
    'CRIMPING_R4'  => 'R4CRP',
    'ASSY'         => 'ASSY',
    'JOINT_R2'     => 'R2JNT',
    'JOINT_R4'     => 'R4JNT'
];

// Build query
$query = "SELECT 
            c.ID,
            c.Lot,
            c.Skema,
            m.partnameFG AS PartName,
            m.TERMINALA,
            m.TERMINALB,
            c.JenisDefect,
            c.Qty,
            c.CreatedAt,
            CONVERT(VARCHAR(10), c.CreatedAt, 103) AS Tanggal,
            CONVERT(VARCHAR(5), c.CreatedAt, 108) AS Jam,
            c.KodeMesin,
            c.Noreg,
            c.terminal,
            CASE
                WHEN DATEPART(HOUR, c.CreatedAt) >= 6 
                     AND DATEPART(HOUR, c.CreatedAt) < 14 THEN 'Shift 1'
                WHEN DATEPART(HOUR, c.CreatedAt) >= 14 
                     AND DATEPART(HOUR, c.CreatedAt) < 22 THEN 'Shift 2'
                ELSE 'Shift 3'
            END AS Shift,
            CASE
                WHEN c.KodeMesin LIKE 'CSM%' THEN 'CNC'
                WHEN c.KodeMesin LIKE 'R2CRP%' THEN 'CRIMPING_R2'
                WHEN c.KodeMesin LIKE 'R4CRP%' THEN 'CRIMPING_R4'
                WHEN c.KodeMesin LIKE 'ASSY%' THEN 'ASSY'
                ELSE 'Unknown'
            END AS Section
          FROM CNCDefect c
          LEFT JOIN trrphmesin m 
              ON c.Lot = m.Lotno 
              AND c.Skema = m.seqnoint
          WHERE 1=1
          AND c.Noreg = ?";

$params = [$noreg];

// Filter tanggal
if (!empty($dateStart) && !empty($dateEnd)) {
    $query .= " AND CAST(c.CreatedAt AS DATE) BETWEEN ? AND ?";
    $params[] = $dateStart;
    $params[] = $dateEnd;
}

// Filter berdasarkan prefix KodeMesin
if ($section !== 'all' && array_key_exists($section, $mesinPrefix)) {
    $prefix = $mesinPrefix[$section];
    $query .= " AND c.KodeMesin LIKE ?";
    $params[] = $prefix . '%';
}

$query .= " ORDER BY c.CreatedAt DESC";

// Execute query
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

    if ($row['CreatedAt'] instanceof DateTime) {
        $row['CreatedAt'] = $row['CreatedAt']->format('Y-m-d H:i:s');
    }

    $data[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $data
]);
