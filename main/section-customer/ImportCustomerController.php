<?php
session_start();
include '../../helper/connection.php';
header("Content-Type: application/json");

// Load PhpSpreadsheet
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'debug' => $error['message']
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
        case 'POST':
            $action = $_POST['action'] ?? '';

            if (empty($action)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);
                exit;
            }

            handleImportActions($connection, $action);
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
// IMPORT HANDLERS
// ==========================

function handleImportActions($connection, $action)
{
    try {
        switch ($action) {
            case 'import':
                importCustomers($connection);
                break;
            case 'downloadTemplate':
                downloadTemplate();
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

function importCustomers($connection)
{
    // Cek apakah ada file yang diupload
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'File tidak valid atau gagal diupload']);
        exit;
    }

    $file = $_FILES['file'];
    $allowedExtensions = ['xls', 'xlsx', 'csv'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validasi ekstensi file
    if (!in_array($fileExtension, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format file harus XLS, XLSX, atau CSV']);
        exit;
    }

    // Validasi ukuran file (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal 5MB']);
        exit;
    }

    try {
        // Baca file Excel
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Hapus header (baris pertama)
        array_shift($rows);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $duplicates = [];

        // Mulai transaction
        sqlsrv_begin_transaction($connection);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena baris 1 adalah header

            // Skip baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            // Ambil data dari kolom
            $nama_customer = trim($row[0] ?? '');
            $nama_singkatan = trim($row[1] ?? '');

            // Validasi data
            if (empty($nama_customer)) {
                $errors[] = "Baris $rowNumber: Nama Customer tidak boleh kosong";
                $errorCount++;
                continue;
            }

            if (empty($nama_singkatan)) {
                $errors[] = "Baris $rowNumber: Nama Singkatan tidak boleh kosong";
                $errorCount++;
                continue;
            }

            // Cek duplikat Nama Customer
            $checkSql = "SELECT id FROM customer_table WHERE LOWER(nama_customer) = LOWER(?)";
            $checkParams = [$nama_customer];
            $checkStmt = sqlsrv_prepare($connection, $checkSql, $checkParams);

            if ($checkStmt && sqlsrv_execute($checkStmt)) {
                $existing = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
                if ($existing) {
                    $duplicates[] = "Baris $rowNumber: Customer '$nama_customer' sudah ada";
                    $errorCount++;
                    continue;
                }
            }

            // Insert data
            $sql = "INSERT INTO customer_table (nama_customer, nama_singkatan, created_at) VALUES (?, ?, GETDATE())";
            $params = [$nama_customer, $nama_singkatan];
            $stmt = sqlsrv_prepare($connection, $sql, $params);

            if (!$stmt || !sqlsrv_execute($stmt)) {
                $errors[] = "Baris $rowNumber: Gagal menyimpan data";
                $errorCount++;
                continue;
            }

            $successCount++;
        }

        if ($errorCount > 0) {
            // Rollback jika ada error
            sqlsrv_rollback($connection);

            $message = "Import gagal. $errorCount data error.";
            $response = [
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
                'duplicates' => $duplicates
            ];

            http_response_code(400);
            echo json_encode($response);
        } else {
            // Commit jika sukses semua
            sqlsrv_commit($connection);

            $message = "Berhasil mengimport $successCount data customer";
            if ($successCount === 0) {
                $message = "Tidak ada data yang diimport (file kosong)";
            }

            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => $message,
                'total_imported' => $successCount
            ]);
        }
        exit;
    } catch (Exception $e) {
        // Rollback jika ada exception
        sqlsrv_rollback($connection);

        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal membaca file: ' . $e->getMessage()
        ]);
        exit;
    }
}

function downloadTemplate()
{
    try {
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Nama Customer');
        $sheet->setCellValue('B1', 'Nama Singkatan');

        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '13B4E9'], // Warna info
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Set contoh data
        $sheet->setCellValue('A2', 'PT Maju Jaya');
        $sheet->setCellValue('B2', 'Maju');

        $sheet->setCellValue('A3', 'PT Sukses Selalu');
        $sheet->setCellValue('B3', 'Sukses');

        $sheet->setCellValue('A4', 'CV Karya Mandiri');
        $sheet->setCellValue('B4', 'Karya');

        // Auto size kolom
        foreach (range('A', 'B') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_customer.xlsx"');
        header('Cache-Control: max-age=0');

        // Buat writer dan output ke browser
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal membuat template: ' . $e->getMessage()
        ]);
        exit;
    }
}
