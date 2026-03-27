<?php
session_start();
include '../../helper/connection.php';
require_once '../../vendor/autoload.php'; // Jika pakai Composer untuk PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Matikan error reporting
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Cek koneksi database
if ($connection === false) {
    die('Database Connection Failed');
}

// ============================================
// AMBIL SEMUA PARAMETER FILTER
// ============================================
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$lot_nos = $_GET['lot_nos'] ?? '';
$customers = $_GET['customers'] ?? '';

// ============================================
// VALIDASI: Minimal satu filter terisi
// ============================================
$hasFilter = false;
$filterTypes = [];

if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
    $hasFilter = true;
    $filterTypes[] = 'tanggal';
}

if (!empty($lot_nos)) {
    $hasFilter = true;
    $filterTypes[] = 'lot';
}

if (!empty($customers)) {
    $hasFilter = true;
    $filterTypes[] = 'customer';
}

if (!$hasFilter) {
    http_response_code(400);
    die('Minimal satu filter (tanggal, lot no, atau customer) wajib untuk export data');
}

// Validasi format tanggal jika ada
$validateDate = function ($date) {
    return DateTime::createFromFormat('Y-m-d', $date) !== false;
};

// ============================================
// BUILD QUERY DENGAN SEMUA FILTER
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
            CONVERT(varchar, created_at, 120) as created_at
        FROM report_claim_defect";

$whereConditions = [];
$filter_text_parts = [];

// ============================================
// 1. FILTER TANGGAL
// ============================================
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    if (!$validateDate($tanggal_awal) || !$validateDate($tanggal_akhir)) {
        http_response_code(400);
        die('Format tanggal tidak valid');
    }

    if ($tanggal_awal > $tanggal_akhir) {
        http_response_code(400);
        die('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
    }

    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) BETWEEN ? AND ?";
    $params[] = $tanggal_awal;
    $params[] = $tanggal_akhir;

    $filter_text_parts[] = "Tanggal: " . date('d/m/Y', strtotime($tanggal_awal)) . " - " . date('d/m/Y', strtotime($tanggal_akhir));
} elseif (!empty($tanggal_awal)) {
    if (!$validateDate($tanggal_awal)) {
        http_response_code(400);
        die('Format tanggal awal tidak valid');
    }

    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) >= ?";
    $params[] = $tanggal_awal;

    $filter_text_parts[] = "Tanggal: >= " . date('d/m/Y', strtotime($tanggal_awal));
} elseif (!empty($tanggal_akhir)) {
    if (!$validateDate($tanggal_akhir)) {
        http_response_code(400);
        die('Format tanggal akhir tidak valid');
    }

    $whereConditions[] = "CAST(tanggal_ditemukan AS DATE) <= ?";
    $params[] = $tanggal_akhir;

    $filter_text_parts[] = "Tanggal: <= " . date('d/m/Y', strtotime($tanggal_akhir));
}

// ============================================
// 2. FILTER LOT NO (MULTIPLE)
// ============================================
if (!empty($lot_nos)) {
    $lotArray = explode(',', $lot_nos);
    $lotPlaceholders = [];
    $lotValues = [];

    foreach ($lotArray as $lot) {
        $lot = trim($lot);
        if (!empty($lot)) {
            $lotPlaceholders[] = "?";
            $params[] = $lot;
            $lotValues[] = $lot;
        }
    }

    if (!empty($lotPlaceholders)) {
        $whereConditions[] = "lotno IN (" . implode(',', $lotPlaceholders) . ")";
        $lotCount = count($lotValues);
        // Tampilkan nilai asli
        if ($lotCount > 3) {
            $filter_text_parts[] = "Lot No: " . $lotCount . " lot terpilih";
        } else {
            $filter_text_parts[] = "Lot No: " . implode(', ', $lotValues);
        }
    }
}

// ============================================
// 3. FILTER CUSTOMER (MULTIPLE)
// ============================================
if (!empty($customers)) {
    $customerArray = explode(',', $customers);
    $customerPlaceholders = [];
    $customerValues = [];

    foreach ($customerArray as $customer) {
        $customer = trim($customer);
        if (!empty($customer)) {
            $customerPlaceholders[] = "?";
            $params[] = $customer;
            $customerValues[] = $customer;
        }
    }

    if (!empty($customerPlaceholders)) {
        $whereConditions[] = "nama_customer IN (" . implode(',', $customerPlaceholders) . ")";
        $customerCount = count($customerValues);
        // Tampilkan nilai asli
        if ($customerCount > 3) {
            $filter_text_parts[] = "Customer: " . $customerCount . " customer terpilih";
        } else {
            $filter_text_parts[] = "Customer: " . implode(', ', $customerValues);
        }
    }
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

// ============================================
// BUAT SPREADSHEET
// ============================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul
$sheet->setCellValue('A1', 'LAPORAN CLAIM DEFECT');
$sheet->setCellValue('A2', 'DATA CLAIM DEFECT');

// Ambil waktu server
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

// Tambahkan info jumlah data
$sheet->setCellValue('A4', 'Jumlah Data: ' . count($rows) . ' record');

// Header kolom
$headers = [
    'A' => 'No',
    'B' => 'Tanggal Ditemukan',
    'C' => 'Customer',
    'D' => 'Lot No',
    'E' => 'Part No',
    'F' => 'Section',
    'G' => 'Defect',
    'H' => 'Operator',
    'I' => 'Deskripsi Masalah',
    'J' => 'Aksi Claim Defect',
    'K' => 'Operator Pengambil',
    'L' => 'Tanggal Pengambilan'
];

foreach ($headers as $column => $header) {
    $sheet->setCellValue($column . '6', $header);
}

// Style header
$sheet->getStyle('A6:L6')->applyFromArray([
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

// Style judul (sesuaikan dengan jumlah kolom)
$sheet->mergeCells('A1:L1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

$sheet->mergeCells('A2:L2');
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 11,
        'color' => ['rgb' => '0D6EFD']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

$sheet->mergeCells('A3:L3');
$sheet->getStyle('A3')->applyFromArray([
    'font' => [
        'italic' => true,
        'size' => 10
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT
    ]
]);

$sheet->mergeCells('A4:L4');
$sheet->getStyle('A4')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 10,
        'color' => ['rgb' => '28A745']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT
    ]
]);

// Isi data (mulai dari baris 7)
$rowNumber = 7;
$no = 1;

if (empty($rows)) {
    // Jika tidak ada data, tampilkan pesan
    $sheet->setCellValue('A' . $rowNumber, 'TIDAK ADA DATA');
    $sheet->mergeCells('A' . $rowNumber . ':L' . $rowNumber);
    $sheet->getStyle('A' . $rowNumber)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FF0000']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ]
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
        $sheet->setCellValue('H' . $rowNumber, $row['nama_operator'] ?? '-');
        $sheet->setCellValue('I' . $rowNumber, $row['deskripsi_masalah'] ?? '-');

        // Kolom baru
        $sheet->setCellValue('J' . $rowNumber, $row['aksi_claim_defect'] ?? '-');
        $sheet->setCellValue('K' . $rowNumber, $row['nama_operator_pengambil'] ?? '-');

        // Format tanggal pengambilan
        $tanggal_pengambilan = $row['tanggal_pengambilan'] ?? null;
        if ($tanggal_pengambilan) {
            $sheet->setCellValue('L' . $rowNumber, date('d/m/Y', strtotime($tanggal_pengambilan)));
        } else {
            $sheet->setCellValue('L' . $rowNumber, '-');
        }

        $rowNumber++;
    }
}

// Style data
$lastRow = $rowNumber - 1;
if ($lastRow >= 7) {
    $sheet->getStyle('A7:L' . $lastRow)->applyFromArray([
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
    $sheet->getStyle('I7:I' . $lastRow)->getAlignment()->setWrapText(true);

    // Style khusus untuk kolom aksi claim defect
    $sheet->getStyle('J7:J' . $lastRow)->applyFromArray([
        'font' => [
            'bold' => true
        ]
    ]);
}

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);   // No
$sheet->getColumnDimension('B')->setWidth(15);  // Tanggal Ditemukan
$sheet->getColumnDimension('C')->setWidth(25);  // Customer
$sheet->getColumnDimension('D')->setWidth(20);  // Lot No
$sheet->getColumnDimension('E')->setWidth(20);  // Part No
$sheet->getColumnDimension('F')->setWidth(20);  // Section
$sheet->getColumnDimension('G')->setWidth(30);  // Defect
$sheet->getColumnDimension('H')->setWidth(20);  // Operator
$sheet->getColumnDimension('I')->setWidth(40);  // Deskripsi Masalah
$sheet->getColumnDimension('J')->setWidth(18);  // Aksi Claim Defect
$sheet->getColumnDimension('K')->setWidth(20);  // Operator Pengambil
$sheet->getColumnDimension('L')->setWidth(18);  // Tanggal Pengambilan

// Freeze pane (freeze baris 7, kolom A)
$sheet->freezePane('A7');

// ============================================
// BUAT NAMA FILE (sesuai filter yang digunakan)
// ============================================
$filename = 'Laporan_Claim_Defect_';
$filenameParts = [];

if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    if ($tanggal_awal == $tanggal_akhir) {
        $filenameParts[] = $tanggal_awal;
    } else {
        $filenameParts[] = $tanggal_awal . '_to_' . $tanggal_akhir;
    }
} elseif (!empty($tanggal_awal)) {
    $filenameParts[] = 'from_' . $tanggal_awal;
} elseif (!empty($tanggal_akhir)) {
    $filenameParts[] = 'until_' . $tanggal_akhir;
}

if (!empty($lot_nos)) {
    $lotCount = count(explode(',', $lot_nos));
    $filenameParts[] = $lotCount . 'lot';
}

if (!empty($customers)) {
    $customerCount = count(explode(',', $customers));
    $filenameParts[] = $customerCount . 'cust';
}

if (empty($filenameParts)) {
    $filenameParts[] = date('Ymd_His');
}

$filename .= implode('_', $filenameParts) . '.xlsx';

// Bersihkan output buffer sebelum mengirim header
if (ob_get_level()) {
    ob_end_clean();
}

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
