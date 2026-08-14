<?php
session_start();
include '../../helper/connection.php';
header("Content-Type: application/json");

// Ambil parameter filter
$dateStart = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$dateEnd = isset($_GET['date_end']) ? $_GET['date_end'] : '';
$section = isset($_GET['section']) ? $_GET['section'] : 'all';

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
                WHEN DATEPART(HOUR, c.CreatedAt) >= 6 AND DATEPART(HOUR, c.CreatedAt) < 14 THEN 'Shift 1'
                WHEN DATEPART(HOUR, c.CreatedAt) >= 14 AND DATEPART(HOUR, c.CreatedAt) < 22 THEN 'Shift 2'
                ELSE 'Shift 3'
            END AS Shift,
            -- Menentukan Section berdasarkan prefix KodeMesin
            CASE
                WHEN c.KodeMesin LIKE 'CSM%' THEN 'CNC'
                WHEN c.KodeMesin LIKE 'R2CRP%' THEN 'CRIMPING_R2'
                WHEN c.KodeMesin LIKE 'R4CRP%' THEN 'CRIMPING_R4'
                WHEN c.KodeMesin LIKE 'ASSY%' THEN 'ASSY'
                ELSE 'Unknown'
            END AS Section
          FROM CNCDefect c
          LEFT JOIN trrphmesin m ON c.Lot = m.Lotno AND c.Skema = m.seqnoint
          WHERE 1=1";

// Filter tanggal
if (!empty($dateStart) && !empty($dateEnd)) {
    $query .= " AND CAST(c.CreatedAt AS DATE) BETWEEN '$dateStart' AND '$dateEnd'";
}

// Filter berdasarkan prefix KodeMesin (bukan section)
if ($section !== 'all' && array_key_exists($section, $mesinPrefix)) {
    $prefix = $mesinPrefix[$section];
    $query .= " AND c.KodeMesin LIKE '$prefix%'";
}

$query .= " ORDER BY c.CreatedAt DESC";

// Debug - uncomment untuk testing
// error_log("Query: " . $query);

$stmt = sqlsrv_query($connection, $query);

if ($stmt === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . print_r(sqlsrv_errors(), true)
    ]);
    exit;
}

$data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Convert DateTime object ke string
    if ($row['CreatedAt'] instanceof DateTime) {
        $row['CreatedAt'] = $row['CreatedAt']->format('Y-m-d H:i:s');
    }
    $data[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $data
]);
?>