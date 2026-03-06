<?php
session_start();
include '../../helper/connection.php';
require_once '../../vendor/autoload.php'; // Jika pakai Composer untuk PhpSpreadsheet

// Atau jika tidak pakai Composer, include manual PhpSpreadsheet
// require_once '../../vendor/phpoffice/phpspreadsheet/src/Bootstrap.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Matikan error reporting
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Cek koneksi database
if ($connection === false) {
    die('Database Connection Failed');
}

// Ambil parameter filter
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

// Validasi tanggal
$validateDate = fn($date) => DateTime::createFromFormat('Y-m-d', $date) ?: false;

// Build query
$params = [];
$sql = "SELECT 
            id, 
            nama_section, 
            nama_defect, 
            lotno, 
            partno, 
            CONVERT(varchar, tanggal_ditemukan, 120) as tanggal_ditemukan,
            nama_operator, 
            deskripsi_masalah,
            nama_customer, 
            CONVERT(varchar, created_at, 120) as created_at
        FROM report_claim_defect";

$whereConditions = [];
$filter_text = "SEMUA DATA";

// Filter berdasarkan tanggal
if ($tanggal_awal && $tanggal_akhir) {
    $tanggal_awal_obj = $validateDate($tanggal_awal);
    $tanggal_akhir_obj = $validateDate($tanggal_akhir);

    if ($tanggal_awal_obj && $tanggal_akhir_obj) {
        $whereConditions[] = "tanggal_ditemukan BETWEEN ? AND ?";
        $params[] = $tanggal_awal;
        $params[] = $tanggal_akhir;
        $filter_text = "PERIODE " . date('d/m/Y', strtotime($tanggal_awal)) . " - " . date('d/m/Y', strtotime($tanggal_akhir));
    }
} elseif ($tanggal_awal && !$tanggal_akhir) {
    $tanggal_awal_obj = $validateDate($tanggal_awal);
    if ($tanggal_awal_obj) {
        $whereConditions[] = "tanggal_ditemukan = ?";
        $params[] = $tanggal_awal;
        $filter_text = "TANGGAL " . date('d/m/Y', strtotime($tanggal_awal));
    }
}

if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY tanggal_ditemukan DESC, id DESC";

// Eksekusi query
$stmt = sqlsrv_prepare($connection, $sql, $params);
if (!$stmt || !sqlsrv_execute($stmt)) {
    die('Gagal mengambil data');
}

// Fetch data
$rows = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $formattedRow = [];
    foreach ($row as $key => $value) {
        if ($value instanceof DateTime) {
            $formattedRow[$key] = $value->format('Y-m-d H:i:s');
        } else {
            $formattedRow[$key] = $value;
        }
    }
    $rows[] = $formattedRow;
}

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul dan header
$sheet->setCellValue('A1', 'LAPORAN CLAIM DEFECT');
$sheet->setCellValue('A2', $filter_text);
$queryTime = sqlsrv_query($connection, "SELECT GETDATE() as server_time");
$rowTime = sqlsrv_fetch_array($queryTime, SQLSRV_FETCH_ASSOC);
$dbTime = $rowTime['server_time'] instanceof DateTime
    ? $rowTime['server_time']->format('d/m/Y H:i:s')
    : date('d/m/Y H:i:s', strtotime($rowTime['server_time']));

$sheet->setCellValue('A3', 'Tanggal Export: ' . $dbTime);
// Header kolom
$headers = [
    'A' => 'No',
    'B' => 'Tanggal',
    'C' => 'Customer',
    'D' => 'Lot No',
    'E' => 'Part No',
    'F' => 'Section',
    'G' => 'Defect',
    'H' => 'Operator',
    'I' => 'Deskripsi Masalah'
];

foreach ($headers as $column => $header) {
    $sheet->setCellValue($column . '5', $header);
}

// Style header
$sheet->getStyle('A5:I5')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '0D6EFD']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

// Style judul
$sheet->mergeCells('A1:I1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

$sheet->mergeCells('A2:I2');
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['rgb' => '0D6EFD']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

$sheet->mergeCells('A3:I3');
$sheet->getStyle('A3')->applyFromArray([
    'font' => [
        'italic' => true,
        'size' => 10
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT
    ]
]);

// Isi data
$rowNumber = 6;
$no = 1;

foreach ($rows as $row) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, date('d/m/Y', strtotime($row['tanggal_ditemukan'])));
    $sheet->setCellValue('C' . $rowNumber, $row['nama_customer']);
    $sheet->setCellValue('D' . $rowNumber, $row['lotno'] ?? '-');
    $sheet->setCellValue('E' . $rowNumber, $row['partno'] ?? '-');
    $sheet->setCellValue('F' . $rowNumber, $row['nama_section']);
    $sheet->setCellValue('G' . $rowNumber, $row['nama_defect']);
    $sheet->setCellValue('H' . $rowNumber, $row['nama_operator'] ?? '-');
    $sheet->setCellValue('I' . $rowNumber, $row['deskripsi_masalah'] ?? '-');

    $rowNumber++;
}

// Style data
$lastRow = $rowNumber - 1;
$sheet->getStyle('A6:I' . $lastRow)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'CCCCCC']
        ]
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

// Wrap text untuk kolom deskripsi
$sheet->getStyle('I6:I' . $lastRow)->getAlignment()->setWrapText(true);

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);   // No
$sheet->getColumnDimension('B')->setWidth(15);  // Tanggal
$sheet->getColumnDimension('C')->setWidth(25);  // Customer
$sheet->getColumnDimension('D')->setWidth(20);  // Lot No
$sheet->getColumnDimension('E')->setWidth(20);  // Part No
$sheet->getColumnDimension('F')->setWidth(20);  // Section
$sheet->getColumnDimension('G')->setWidth(30);  // Defect
$sheet->getColumnDimension('H')->setWidth(20);  // Operator
$sheet->getColumnDimension('I')->setWidth(40);  // Deskripsi

// Freeze pane
$sheet->freezePane('A6');

// Buat nama file
$filename = 'Laporan_Claim_Defect_' . date('Ymd_His') . '.xlsx';
if ($tanggal_awal && $tanggal_akhir) {
    $filename = 'Laporan_Claim_Defect_' . $tanggal_awal . '_to_' . $tanggal_akhir . '.xlsx';
} elseif ($tanggal_awal) {
    $filename = 'Laporan_Claim_Defect_' . $tanggal_awal . '.xlsx';
}

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
