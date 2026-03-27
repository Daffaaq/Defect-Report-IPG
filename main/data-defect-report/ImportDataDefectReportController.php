<?php
session_start();
include '../../helper/connection.php';
require_once '../../vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Matikan error reporting untuk output bersih
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set header untuk response JSON
header('Content-Type: application/json');

// Cek koneksi database
if ($connection === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal'
    ]);
    exit;
}

// Ambil action
$action = $_GET['action'] ?? '';

// Handle download template (hanya 1 template dengan header)
if ($action === 'downloadTemplate') {
    downloadTemplate();
    exit;
}

// Handle import
if ($action === 'import') {
    importData($connection);
    exit;
}

// Jika action tidak dikenal
echo json_encode([
    'status' => 'error',
    'message' => 'Action tidak dikenal'
]);
exit;

/**
 * Download template Excel untuk import data
 * Hanya 1 template dengan header (baris pertama)
 */
function downloadTemplate()
{
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom (13 kolom)
        $headers = [
            'A' => 'Tanggal Ditemukan* (YYYY-MM-DD)',
            'B' => 'Customer*',
            'C' => 'Lot No*',
            'D' => 'Part No*',
            'E' => 'Section*',
            'F' => 'Defect*',
            'G' => 'Operator*',
            'H' => 'Deskripsi Masalah',
            'I' => 'Aksi Claim Defect* (Repair/Scrap)',
            'J' => 'Nama Operator Pengambil*',
            'K' => 'Tanggal Pengambilan* (YYYY-MM-DD)',
            'L' => 'Group*',
            'M' => 'QTY*'
        ];

        $row = 1;
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column . $row, $header);
        }

        // Style header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]]
        ]);

        // Generate contoh data (5 baris)
        $customers = ['PT ABC', 'PT XYZ', 'PT DEF', 'PT GHI'];
        $sections = ['Assembly', 'Painting', 'Welding', 'QC'];
        $defects = ['Crack', 'Scratch', 'Dent', 'Broken'];
        $operators = ['John Doe', 'Mike', 'Andi', 'Budi', 'Siti'];
        $actions = ['Repair', 'Scrap'];
        $groups = ['Group A', 'Group B', 'Group C'];
        $descriptions = [
            'Crack' => 'Retak pada produk',
            'Scratch' => 'Goresan pada permukaan',
            'Dent' => 'Penyok pada body',
            'Broken' => 'Produk rusak'
        ];

        function randomDate($start = '2026-01-01', $end = '2026-12-31')
        {
            $min = strtotime($start);
            $max = strtotime($end);
            return date('Y-m-d', rand($min, $max));
        }

        $exampleData = [];
        $jumlahData = 5;

        for ($i = 0; $i < $jumlahData; $i++) {
            $tanggalDitemukan = randomDate();
            $tanggalPengambilan = randomDate($tanggalDitemukan, '2026-12-31');
            $defect = $defects[array_rand($defects)];

            $exampleData[] = [
                $tanggalDitemukan,
                $customers[array_rand($customers)],
                'LOT-' . rand(100, 999),
                'PART-' . rand(100, 999),
                $sections[array_rand($sections)],
                $defect,
                $operators[array_rand($operators)],
                $descriptions[$defect],
                $actions[array_rand($actions)],
                $operators[array_rand($operators)],
                $tanggalPengambilan,
                $groups[array_rand($groups)],
                rand(1, 500)
            ];
        }

        // Set contoh data (mulai baris 2)
        $startRow = 2;
        foreach ($exampleData as $index => $data) {
            $currentRow = $startRow + $index;
            foreach ($data as $colIndex => $value) {
                $column = chr(65 + $colIndex);
                $sheet->setCellValue($column . $currentRow, $value);
            }
        }

        // Style data contoh
        $sheet->getStyle('A2:M' . ($startRow + $jumlahData - 1))->applyFromArray([
            'borders' => ['allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]]
        ]);

        // Set lebar kolom
        $columnWidths = [20, 25, 20, 20, 20, 30, 20, 40, 25, 25, 20, 15, 10];
        foreach ($columnWidths as $i => $width) {
            $column = chr(65 + $i);
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->freezePane('A2');

        // Output file
        if (ob_get_level()) {
            ob_end_clean();
        }

        $filename = 'Template_Import_Defect_Report.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal membuat template: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Import data dari file Excel
 * Selalu skip baris pertama (header)
 */
function importData($connection)
{
    try {
        // Cek apakah file diupload
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File tidak ditemukan atau gagal diupload'
            ]);
            exit;
        }

        $file = $_FILES['file'];

        // Validasi tipe file
        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Format file tidak didukung. Gunakan .xlsx, .xls, atau .csv'
            ]);
            exit;
        }

        // Baca file Excel
        try {
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal membaca file: ' . $e->getMessage()
            ]);
            exit;
        }

        // Validasi minimal data (minimal harus ada header + 1 data)
        if (count($rows) < 2) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File tidak mengandung data yang valid. Minimal harus ada header dan 1 baris data.'
            ]);
            exit;
        }

        // Skip baris pertama (header)
        $dataRows = array_slice($rows, 1);

        // Mapping kolom (sesuai template)
        $columnMapping = [
            'tanggal_ditemukan' => 0,      // Kolom A
            'nama_customer' => 1,          // Kolom B
            'lotno' => 2,                  // Kolom C
            'partno' => 3,                 // Kolom D
            'nama_section' => 4,           // Kolom E
            'nama_defect' => 5,            // Kolom F
            'nama_operator' => 6,          // Kolom G
            'deskripsi_masalah' => 7,      // Kolom H
            'aksi_claim_defect' => 8,      // Kolom I
            'nama_operator_pengambil' => 9, // Kolom J
            'tanggal_pengambilan' => 10,    // Kolom K
            'nama_group' => 11,             // Kolom L
            'qty' => 12                     // Kolom M
        ];

        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $insertedData = [];

        // Mulai transaksi
        sqlsrv_begin_transaction($connection);

        foreach ($dataRows as $rowIndex => $rowData) {
            $rowNumber = $rowIndex + 2; // +2 karena baris 1 adalah header

            // Ambil nilai per kolom
            $tanggalDitemukan = isset($rowData[$columnMapping['tanggal_ditemukan']]) ? trim($rowData[$columnMapping['tanggal_ditemukan']]) : '';
            $namaCustomer = isset($rowData[$columnMapping['nama_customer']]) ? trim($rowData[$columnMapping['nama_customer']]) : '';
            $lotno = isset($rowData[$columnMapping['lotno']]) ? trim($rowData[$columnMapping['lotno']]) : '';
            $partno = isset($rowData[$columnMapping['partno']]) ? trim($rowData[$columnMapping['partno']]) : '';
            $namaSection = isset($rowData[$columnMapping['nama_section']]) ? trim($rowData[$columnMapping['nama_section']]) : '';
            $namaDefect = isset($rowData[$columnMapping['nama_defect']]) ? trim($rowData[$columnMapping['nama_defect']]) : '';
            $namaOperator = isset($rowData[$columnMapping['nama_operator']]) ? trim($rowData[$columnMapping['nama_operator']]) : '';
            $deskripsiMasalah = isset($rowData[$columnMapping['deskripsi_masalah']]) ? trim($rowData[$columnMapping['deskripsi_masalah']]) : '';
            $aksiClaimDefect = isset($rowData[$columnMapping['aksi_claim_defect']]) ? trim($rowData[$columnMapping['aksi_claim_defect']]) : '';
            $namaOperatorPengambil = isset($rowData[$columnMapping['nama_operator_pengambil']]) ? trim($rowData[$columnMapping['nama_operator_pengambil']]) : '';
            $tanggalPengambilan = isset($rowData[$columnMapping['tanggal_pengambilan']]) ? trim($rowData[$columnMapping['tanggal_pengambilan']]) : '';
            $namaGroup = isset($rowData[$columnMapping['nama_group']]) ? trim($rowData[$columnMapping['nama_group']]) : '';
            $qty = isset($rowData[$columnMapping['qty']]) ? trim($rowData[$columnMapping['qty']]) : '';

            // Validasi kolom wajib
            $validationErrors = [];

            if (empty($tanggalDitemukan)) $validationErrors[] = 'Tanggal Ditemukan tidak boleh kosong';
            if (empty($namaCustomer)) $validationErrors[] = 'Customer tidak boleh kosong';
            if (empty($lotno)) $validationErrors[] = 'Lot No tidak boleh kosong';
            if (empty($partno)) $validationErrors[] = 'Part No tidak boleh kosong';
            if (empty($namaSection)) $validationErrors[] = 'Section tidak boleh kosong';
            if (empty($namaDefect)) $validationErrors[] = 'Defect tidak boleh kosong';
            if (empty($namaOperator)) $validationErrors[] = 'Operator tidak boleh kosong';
            if (empty($namaOperatorPengambil)) $validationErrors[] = 'Nama Operator Pengambil tidak boleh kosong';
            if (empty($tanggalPengambilan)) $validationErrors[] = 'Tanggal Pengambilan tidak boleh kosong';
            if (empty($namaGroup)) $validationErrors[] = 'Nama Group tidak boleh kosong';
            if (empty($aksiClaimDefect)) $validationErrors[] = 'Aksi Claim Defect tidak boleh kosong';
            if (empty($qty)) {
                $validationErrors[] = 'QTY tidak boleh kosong';
            } else if (!is_numeric($qty) || $qty < 0) {
                $validationErrors[] = 'QTY harus berupa angka positif';
            }

            if (!empty($validationErrors)) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: " . implode(', ', $validationErrors);
                continue;
            }

            // Format tanggal
            $formattedTanggalDitemukan = formatDateForDatabase($tanggalDitemukan);
            if ($formattedTanggalDitemukan === false) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Format tanggal ditemukan tidak valid (gunakan format YYYY-MM-DD)";
                continue;
            }

            $formattedTanggalPengambilan = formatDateForDatabase($tanggalPengambilan);
            if ($formattedTanggalPengambilan === false) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Format tanggal pengambilan tidak valid (gunakan format YYYY-MM-DD)";
                continue;
            }

            // Validasi aksi claim defect
            if (!empty($aksiClaimDefect) && !in_array($aksiClaimDefect, ['Repair', 'Scrap'])) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Aksi Claim Defect harus Repair atau Scrap";
                continue;
            }

            // Insert data
            $sql = "INSERT INTO report_claim_defect (
                        tanggal_ditemukan,
                        nama_customer,
                        lotno,
                        partno,
                        nama_section,
                        nama_defect,
                        nama_operator,
                        deskripsi_masalah,
                        aksi_claim_defect,
                        nama_operator_pengambil,
                        tanggal_pengambilan,
                        nama_group,
                        qty,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";

            $params = [
                $formattedTanggalDitemukan,
                $namaCustomer,
                $lotno,
                $partno,
                $namaSection,
                $namaDefect,
                $namaOperator,
                !empty($deskripsiMasalah) ? $deskripsiMasalah : null,
                $aksiClaimDefect,
                $namaOperatorPengambil,
                $formattedTanggalPengambilan,
                $namaGroup,
                intval($qty)
            ];

            $stmt = sqlsrv_prepare($connection, $sql, $params);

            if (!$stmt) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Gagal mempersiapkan query";
                continue;
            }

            if (sqlsrv_execute($stmt)) {
                $successCount++;
                $insertedData[] = [
                    'row' => $rowNumber,
                    'lotno' => $lotno,
                    'customer' => $namaCustomer,
                    'qty' => $qty
                ];
            } else {
                $failedCount++;
                $dbErrors = sqlsrv_errors();
                $errorMsg = !empty($dbErrors) ? $dbErrors[0]['message'] : 'Unknown error';
                $errors[] = "Baris {$rowNumber}: Gagal insert - {$errorMsg}";
            }
        }

        // Commit atau rollback
        if ($failedCount > 0) {
            sqlsrv_rollback($connection);

            echo json_encode([
                'status' => 'error',
                'message' => "Import dibatalkan karena ada {$failedCount} data gagal. Tidak ada data yang disimpan.",
                'data' => [
                    'total' => $successCount + $failedCount,
                    'success' => $successCount,
                    'failed' => $failedCount
                ],
                'errors' => array_slice($errors, 0, 20)
            ]);
        } else {
            sqlsrv_commit($connection);

            echo json_encode([
                'status' => 'success',
                'message' => "Import berhasil! {$successCount} data berhasil diimport",
                'data' => [
                    'total' => $successCount,
                    'success' => $successCount,
                    'failed' => 0,
                    'inserted' => $insertedData
                ]
            ]);
        }
    } catch (Exception $e) {
        if (isset($connection) && $connection) {
            sqlsrv_rollback($connection);
        }

        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

/**
 * Format tanggal untuk database SQL Server
 * Support format: YYYY-MM-DD (utama) dan dd/mm/yyyy (alternatif)
 */
function formatDateForDatabase($dateStr)
{
    if (empty($dateStr)) {
        return null;
    }

    $dateStr = trim($dateStr);

    // Format yyyy-mm-dd
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
        $parts = explode('-', $dateStr);
        if (count($parts) === 3) {
            $year = $parts[0];
            $month = $parts[1];
            $day = $parts[2];
            if (checkdate($month, $day, $year)) {
                return $dateStr;
            }
        }
    }

    // Format dd/mm/yyyy
    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateStr)) {
        $parts = explode('/', $dateStr);
        if (count($parts) === 3) {
            $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
            $year = $parts[2];
            if (checkdate($month, $day, $year)) {
                return "{$year}-{$month}-{$day}";
            }
        }
    }

    return false;
}
