<?php
session_start();
include '../../helper/connection.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($connection === false) {
    die('Database Connection Failed');
}

// ============================================
// AMBIL SEMUA PARAMETER FILTER (SEMUA TAB)
// ============================================
// TAB 1: Tanggal + Part No
$tanggal = $_GET['tanggal'] ?? '';
$partno = $_GET['partno'] ?? '';

// TAB 2: Lot No
$lot_nos = $_GET['lot_nos'] ?? '';

// TAB 3: Customer + Range Tanggal
$customers = $_GET['customers'] ?? '';
$tanggal_awal_customer = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir_customer = $_GET['tanggal_akhir'] ?? '';

// TAB 4: Part No + Range Tanggal
$partnos_range = $_GET['partnos'] ?? '';
$tanggal_awal_part = $_GET['tanggal_awal_part'] ?? '';
$tanggal_akhir_part = $_GET['tanggal_akhir_part'] ?? '';

// ============================================
// VALIDASI: Minimal satu filter terisi
// ============================================
$hasFilter = false;

if (!empty($tanggal)) $hasFilter = true;
if (!empty($lot_nos)) $hasFilter = true;
if (!empty($customers)) $hasFilter = true;
if (!empty($partnos_range)) $hasFilter = true;
if (!empty($tanggal_awal_customer) || !empty($tanggal_akhir_customer)) $hasFilter = true;
if (!empty($tanggal_awal_part) || !empty($tanggal_akhir_part)) $hasFilter = true;

if (!$hasFilter) {
    http_response_code(400);
    die('Minimal satu filter wajib untuk export data');
}

$validateDate = function ($date) {
    return DateTime::createFromFormat('Y-m-d', $date) !== false;
};

// ============================================
// BUILD QUERY
// ============================================
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
            aksi_claim_defect,
            nama_operator_pengambil,
            CONVERT(varchar, tanggal_pengambilan, 120) as tanggal_pengambilan,
            CONVERT(varchar, created_at, 120) as created_at,
            shift,
            status,
            qty,
            nama_group
        FROM report_claim_defect";

$whereConditions = [];

// ============================================
// TAB 1: Tanggal + Part No
// ============================================
if (!empty($tanggal)) {
    if (!$validateDate($tanggal)) {
        http_response_code(400);
        die('Format tanggal tidak valid');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) = ?";
    $params[] = $tanggal;

    // Filter Part No (multiple)
    if (!empty($partno)) {
        $partnoArray = explode(',', $partno);
        $partnoPlaceholders = [];
        foreach ($partnoArray as $pn) {
            $pn = trim($pn);
            if (!empty($pn)) {
                $partnoPlaceholders[] = "?";
                $params[] = $pn;
            }
        }
        if (!empty($partnoPlaceholders)) {
            $whereConditions[] = "partno IN (" . implode(',', $partnoPlaceholders) . ")";
        }
    }
}

// ============================================
// TAB 2: Filter Lot No
// ============================================
if (!empty($lot_nos)) {
    $lotArray = explode(',', $lot_nos);
    $lotPlaceholders = [];
    foreach ($lotArray as $lot) {
        $lot = trim($lot);
        if (!empty($lot)) {
            $lotPlaceholders[] = "?";
            $params[] = $lot;
        }
    }
    if (!empty($lotPlaceholders)) {
        $whereConditions[] = "lotno IN (" . implode(',', $lotPlaceholders) . ")";
    }
}

// ============================================
// TAB 3: Customer + Range Tanggal
// ============================================
if (!empty($customers)) {
    $customerArray = explode(',', $customers);
    $customerPlaceholders = [];
    foreach ($customerArray as $customer) {
        $customer = trim($customer);
        if (!empty($customer)) {
            $customerPlaceholders[] = "?";
            $params[] = $customer;
        }
    }
    if (!empty($customerPlaceholders)) {
        $whereConditions[] = "nama_customer IN (" . implode(',', $customerPlaceholders) . ")";
    }
}

// Range tanggal untuk Customer
if (!empty($tanggal_awal_customer) && !empty($tanggal_akhir_customer)) {
    if (!$validateDate($tanggal_awal_customer) || !$validateDate($tanggal_akhir_customer)) {
        http_response_code(400);
        die('Format tanggal tidak valid');
    }
    if ($tanggal_awal_customer > $tanggal_akhir_customer) {
        http_response_code(400);
        die('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) BETWEEN ? AND ?";
    $params[] = $tanggal_awal_customer;
    $params[] = $tanggal_akhir_customer;
} elseif (!empty($tanggal_awal_customer)) {
    if (!$validateDate($tanggal_awal_customer)) {
        http_response_code(400);
        die('Format tanggal awal tidak valid');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) >= ?";
    $params[] = $tanggal_awal_customer;
} elseif (!empty($tanggal_akhir_customer)) {
    if (!$validateDate($tanggal_akhir_customer)) {
        http_response_code(400);
        die('Format tanggal akhir tidak valid');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) <= ?";
    $params[] = $tanggal_akhir_customer;
}

// ============================================
// TAB 4: Part No + Range Tanggal
// ============================================
if (!empty($partnos_range)) {
    $partnoRangeArray = explode(',', $partnos_range);
    $partnoRangePlaceholders = [];
    foreach ($partnoRangeArray as $pn) {
        $pn = trim($pn);
        if (!empty($pn)) {
            $partnoRangePlaceholders[] = "?";
            $params[] = $pn;
        }
    }
    if (!empty($partnoRangePlaceholders)) {
        $whereConditions[] = "partno IN (" . implode(',', $partnoRangePlaceholders) . ")";
    }
}

// Range tanggal untuk Part No
if (!empty($tanggal_awal_part) && !empty($tanggal_akhir_part)) {
    if (!$validateDate($tanggal_awal_part) || !$validateDate($tanggal_akhir_part)) {
        http_response_code(400);
        die('Format tanggal tidak valid');
    }
    if ($tanggal_awal_part > $tanggal_akhir_part) {
        http_response_code(400);
        die('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) BETWEEN ? AND ?";
    $params[] = $tanggal_awal_part;
    $params[] = $tanggal_akhir_part;
} elseif (!empty($tanggal_awal_part)) {
    if (!$validateDate($tanggal_awal_part)) {
        http_response_code(400);
        die('Format tanggal awal tidak valid');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) >= ?";
    $params[] = $tanggal_awal_part;
} elseif (!empty($tanggal_akhir_part)) {
    if (!$validateDate($tanggal_akhir_part)) {
        http_response_code(400);
        die('Format tanggal akhir tidak valid');
    }
    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) <= ?";
    $params[] = $tanggal_akhir_part;
}

// ============================================
// GABUNGKAN WHERE CONDITIONS
// ============================================
if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY tanggal_ditemukan DESC, id DESC";

// ============================================
// EKSEKUSI QUERY
// ============================================
$stmt = sqlsrv_prepare($connection, $sql, $params);
if (!$stmt) {
    $errors = sqlsrv_errors();
    die('Gagal menyiapkan query: ' . print_r($errors, true));
}

if (!sqlsrv_execute($stmt)) {
    $errors = sqlsrv_errors();
    die('Gagal mengeksekusi query: ' . print_r($errors, true));
}

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

// ============================================
// BUAT SPREADSHEET
// ============================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'LAPORAN CLAIM DEFECT');
$sheet->setCellValue('A2', 'DATA CLAIM DEFECT');

$queryTime = sqlsrv_query($connection, "SELECT GETDATE() as server_time");
if ($queryTime) {
    $rowTime = sqlsrv_fetch_array($queryTime, SQLSRV_FETCH_ASSOC);
    $dbTime = $rowTime['server_time'] instanceof DateTime
        ? $rowTime['server_time']->format('d/m/Y H:i:s')
        : date('d/m/Y H:i:s', strtotime($rowTime['server_time']));
} else {
    $dbTime = date('d/m/Y H:i:s');
}
$sheet->setCellValue('A3', 'Tanggal Export: ' . $dbTime);
$sheet->setCellValue('A4', 'Jumlah Data: ' . count($rows) . ' record');

// Header kolom - 16 kolom (A sampai P)
$headers = [
    'A' => 'No',
    'B' => 'Tanggal Ditemukan',
    'C' => 'Customer',
    'D' => 'Lot No',
    'E' => 'Part No',
    'F' => 'Section',
    'G' => 'Defect',
    'H' => 'Group',
    'I' => 'QTY',
    'J' => 'Operator',
    'K' => 'Shift',
    'L' => 'Deskripsi Masalah',
    'M' => 'Aksi Claim Defect',
    'N' => 'Status',
    'O' => 'Operator Pengambil',
    'P' => 'Tanggal Pengambilan'
];

foreach ($headers as $column => $header) {
    $sheet->setCellValue($column . '6', $header);
}

// Style header
$sheet->getStyle('A6:P6')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
]);

// Style judul
$sheet->mergeCells('A1:P1');
$sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

$sheet->mergeCells('A2:P2');
$sheet->getStyle('A2')->applyFromArray(['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0D6EFD']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

$sheet->mergeCells('A3:P3');
$sheet->getStyle('A3')->applyFromArray(['font' => ['italic' => true, 'size' => 10], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);

$sheet->mergeCells('A4:P4');
$sheet->getStyle('A4')->applyFromArray(['font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '28A745']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);

// Isi data
$rowNumber = 7;
$no = 1;

if (empty($rows)) {
    $sheet->setCellValue('A' . $rowNumber, 'TIDAK ADA DATA');
    $sheet->mergeCells('A' . $rowNumber . ':P' . $rowNumber);
    $sheet->getStyle('A' . $rowNumber)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    $rowNumber++;
} else {
    foreach ($rows as $row) {
        $sheet->setCellValue('A' . $rowNumber, $no++);
        $sheet->setCellValue('B' . $rowNumber, date('d/m/Y', strtotime($row['tanggal_ditemukan'])));
        $sheet->setCellValue('C' . $rowNumber, $row['nama_customer'] ?? '-');
        $sheet->setCellValue('D' . $rowNumber, $row['lotno'] ?? '-');
        $sheet->setCellValue('E' . $rowNumber, $row['partno'] ?? '-');
        $sheet->setCellValue('F' . $rowNumber, $row['nama_section'] ?? '-');
        $sheet->setCellValue('G' . $rowNumber, $row['nama_defect'] ?? '-');
        $sheet->setCellValue('H' . $rowNumber, $row['nama_group'] ?? '-');
        $sheet->setCellValue('I' . $rowNumber, $row['qty'] ?? '0');
        $sheet->setCellValue('J' . $rowNumber, $row['nama_operator'] ?? '-');
        $sheet->setCellValue('K' . $rowNumber, $row['shift'] ?? '-');
        $sheet->setCellValue('L' . $rowNumber, $row['deskripsi_masalah'] ?? '-');
        $sheet->setCellValue('M' . $rowNumber, $row['aksi_claim_defect'] ?? '-');

        // Status
        $statusValue = $row['status'] ?? 0;
        $statusText = ($statusValue == 1 || $statusValue === true || $statusValue === '1') ? 'OK' : 'NG';
        $sheet->setCellValue('N' . $rowNumber, $statusText);

        if ($statusText == 'OK') {
            $sheet->getStyle('N' . $rowNumber)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '28A745']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']]
            ]);
        } else {
            $sheet->getStyle('N' . $rowNumber)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'DC3545']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFEBEE']]
            ]);
        }

        $sheet->setCellValue('O' . $rowNumber, $row['nama_operator_pengambil'] ?? '-');

        $tanggal_pengambilan = $row['tanggal_pengambilan'] ?? null;
        if ($tanggal_pengambilan) {
            $sheet->setCellValue('P' . $rowNumber, date('d/m/Y', strtotime($tanggal_pengambilan)));
        } else {
            $sheet->setCellValue('P' . $rowNumber, '-');
        }

        $rowNumber++;
    }
}

// Style data
$lastRow = $rowNumber - 1;
if ($lastRow >= 7) {
    $sheet->getStyle('A7:P' . $lastRow)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
    ]);

    $sheet->getStyle('L7:L' . $lastRow)->getAlignment()->setWrapText(true);
    $sheet->getStyle('K7:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('N7:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(30);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(8);
$sheet->getColumnDimension('J')->setWidth(20);
$sheet->getColumnDimension('K')->setWidth(10);
$sheet->getColumnDimension('L')->setWidth(40);
$sheet->getColumnDimension('M')->setWidth(18);
$sheet->getColumnDimension('N')->setWidth(10);
$sheet->getColumnDimension('O')->setWidth(20);
$sheet->getColumnDimension('P')->setWidth(18);

$sheet->freezePane('A7');

// Nama file
$filename = 'Laporan_Claim_Defect_' . date('Ymd_His') . '.xlsx';

if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
