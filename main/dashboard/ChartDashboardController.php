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

            handleChartActions($connection, $action);
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
// CHART HANDLERS
// ==========================

function handleChartActions($connection, $action)
{
    try {
        switch ($action) {
            case 'getSectionDefectChart':
                getSectionDefectChart($connection);
                break;
            case 'getDefectBySectionChart':
                getDefectBySectionChart($connection);
                break;
            case 'getLineChart':
                getLineChart($connection);
                break;
            case 'getDoughnutChart':
                getDoughnutChart($connection);
                break;
            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Aksi chart tidak valid']);
                break;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// ==========================
// CHART: Repair vs Scrap per Section
// ==========================
function getSectionDefectChart($connection)
{
    header('Content-Type: application/json');

    try {
        $sql = "
        WITH SectionStats AS (
            SELECT 
                nama_section,
                aksi_claim_defect,
                COUNT(*) AS jml
            FROM report_claim_defect
            WHERE aksi_claim_defect IN ('Repair', 'Scrap')
            GROUP BY nama_section, aksi_claim_defect
        ),
        SectionPivot AS (
            SELECT 
                nama_section,
                ISNULL(MAX(CASE WHEN aksi_claim_defect = 'Repair' THEN jml END), 0) AS repair,
                ISNULL(MAX(CASE WHEN aksi_claim_defect = 'Scrap' THEN jml END), 0) AS scrap
            FROM SectionStats
            GROUP BY nama_section
        )
        SELECT
            nama_section,
            repair,
            scrap,
            (repair + scrap) AS total_defect
        FROM SectionPivot
        ORDER BY total_defect DESC;
        ";

        $stmt = sqlsrv_query($connection, $sql);

        if ($stmt === false) {
            throw new Exception("Gagal query section defect: " . print_r(sqlsrv_errors(), true));
        }

        $sections = [];
        $repairData = [];
        $scrapData = [];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $sections[] = $row['nama_section'];
            $repairData[] = (int)$row['repair'];
            $scrapData[] = (int)$row['scrap'];
        }

        sqlsrv_free_stmt($stmt);

        // Siapkan data untuk chart
        $chartData = [
            'categories' => $sections,
            'series' => [
                [
                    'name' => 'Repair',
                    'type' => 'bar',
                    'data' => $repairData
                ],
                [
                    'name' => 'Scrap',
                    'type' => 'bar',
                    'data' => $scrapData
                ]
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data chart Repair vs Scrap per section berhasil dimuat',
            'data' => $chartData
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data chart: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// CHART: Defect Distribution by Production Section (Horizontal Bar Chart)
// ==========================
function getDefectBySectionChart($connection)
{
    header('Content-Type: application/json');

    try {
        $sql = "
        SELECT 
            nama_section,
            COUNT(*) AS total_kasus
        FROM report_claim_defect
        GROUP BY nama_section
        ORDER BY total_kasus DESC;
        ";

        $stmt = sqlsrv_query($connection, $sql);

        if ($stmt === false) {
            throw new Exception("Gagal query defect by section: " . print_r(sqlsrv_errors(), true));
        }

        $sections = [];
        $totalCases = [];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $sections[] = $row['nama_section'];
            $totalCases[] = (int)$row['total_kasus'];
        }

        sqlsrv_free_stmt($stmt);

        // Hitung total keseluruhan untuk persentase
        $grandTotal = array_sum($totalCases);

        // Hitung persentase kumulatif untuk Pareto
        $cumulativePercentage = [];
        $runningTotal = 0;

        foreach ($totalCases as $value) {
            $runningTotal += $value;
            $cumulativePercentage[] = round(($runningTotal / $grandTotal) * 100, 1);
        }

        // Siapkan data untuk chart
        $chartData = [
            'categories' => $sections,
            'series' => [
                [
                    'name' => 'Jumlah Kasus',
                    'data' => $totalCases
                ]
            ],
            'cumulative_percentage' => $cumulativePercentage,
            'grand_total' => $grandTotal,
            'metadata' => [
                'title' => 'Defect Distribution by Production Section',
                'type' => 'horizontal_bar',
                'description' => 'Distribusi defect berdasarkan section produksi'
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data chart defect by section berhasil dimuat',
            'data' => $chartData
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data chart defect by section: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// CHART: Line Chart - Daily, Weekly, Monthly Trend
// ==========================
function getLineChart($connection)
{
    header('Content-Type: application/json');

    try {
        $period = $_GET['period'] ?? 'daily';

        $categories = [];
        $seriesData = [];

        switch ($period) {
            case 'daily':
                // Daily Trend - 7 Hari Terakhir (SQL 2008 compatible)
                $sql = "
                SELECT 
        -- Format: tanggal (tanpa leading zero) dan bulan singkat
        CAST(DAY(date_series.Date) AS VARCHAR) + ' ' + 
        LEFT(DATENAME(MONTH, date_series.Date), 3) AS label,
        ISNULL(COUNT(rcd.tanggal_ditemukan), 0) AS jumlah_defect
    FROM (
        SELECT DATEADD(DAY, t.number, DATEADD(DAY, -6, CAST(GETDATE() AS DATE))) AS Date
        FROM (
            SELECT number FROM master..spt_values WHERE type = 'P' AND number BETWEEN 0 AND 6
        ) t
    ) date_series
    LEFT JOIN report_claim_defect rcd ON CAST(rcd.tanggal_ditemukan AS DATE) = date_series.Date
    GROUP BY date_series.Date
    ORDER BY date_series.Date ASC;
                ";
                break;

            case 'weekly':
                // Weekly Trend - Minggu 1–4 Bulan Sekarang (Dinamis)
                $sql = "
                   DECLARE @tahun INT = YEAR(GETDATE());
            DECLARE @bulan INT = MONTH(GETDATE());

            -- tanggal awal bulan
            DECLARE @awal_bulan DATE = CAST(CAST(@tahun AS VARCHAR(4)) + RIGHT('0' + CAST(@bulan AS VARCHAR(2)),2) + '01' AS DATE);
            -- tanggal akhir bulan
            DECLARE @akhir_bulan DATE = DATEADD(DAY, -1, DATEADD(MONTH, 1, @awal_bulan));

            WITH MingguTemplate AS (
                SELECT 1 AS minggu, @awal_bulan AS mulai, DATEADD(DAY, 6, @awal_bulan) AS selesai
                UNION ALL
                SELECT 2, DATEADD(DAY, 7, @awal_bulan), DATEADD(DAY, 13, @awal_bulan)
                UNION ALL
                SELECT 3, DATEADD(DAY, 14, @awal_bulan), DATEADD(DAY, 20, @awal_bulan)
                UNION ALL
                SELECT 4, DATEADD(DAY, 21, @awal_bulan), @akhir_bulan
            )
            SELECT 
                'Minggu ' + CAST(mt.minggu AS VARCHAR) AS label,
                ISNULL(COUNT(rcd.tanggal_ditemukan), 0) AS jumlah_defect
            FROM MingguTemplate mt
            LEFT JOIN report_claim_defect rcd
                ON rcd.tanggal_ditemukan >= mt.mulai
                AND rcd.tanggal_ditemukan <= mt.selesai
            GROUP BY mt.minggu
            ORDER BY mt.minggu;
                    ";
                break;
            case 'monthly':
            default:
                // Monthly Trend - 12 Bulan Tahun Ini (SQL 2008 compatible)
                $sql = "
                SELECT 
                    CASE month_series.MonthNum
                        WHEN 1 THEN 'Jan' WHEN 2 THEN 'Feb' WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Apr' WHEN 5 THEN 'May' WHEN 6 THEN 'Jun'
                        WHEN 7 THEN 'Jul' WHEN 8 THEN 'Aug' WHEN 9 THEN 'Sep'
                        WHEN 10 THEN 'Oct' WHEN 11 THEN 'Nov' WHEN 12 THEN 'Dec'
                    END AS label,
                    ISNULL(COUNT(rcd.tanggal_ditemukan), 0) AS jumlah_defect
                FROM (
                    SELECT number AS MonthNum FROM master..spt_values 
                    WHERE type = 'P' AND number BETWEEN 1 AND 12
                ) month_series
                LEFT JOIN report_claim_defect rcd 
                    ON MONTH(rcd.tanggal_ditemukan) = month_series.MonthNum 
                    AND YEAR(rcd.tanggal_ditemukan) = YEAR(GETDATE())
                GROUP BY month_series.MonthNum
                ORDER BY month_series.MonthNum ASC;
                ";
                break;
        }

        $stmt = sqlsrv_query($connection, $sql);

        if ($stmt === false) {
            throw new Exception("Gagal query line chart: " . print_r(sqlsrv_errors(), true));
        }

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $categories[] = $row['label'];
            $seriesData[] = (int)$row['jumlah_defect'];
        }

        sqlsrv_free_stmt($stmt);

        // Siapkan data untuk chart
        $chartData = [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Jumlah Defect',
                    'data' => $seriesData
                ]
            ],
            'metadata' => [
                'period' => $period,
                'title' => $period === 'daily' ? 'Daily Defect Discovery Trend' : ($period === 'weekly' ? 'Weekly Defect Discovery Trend' : 'Monthly Defect Discovery Trend')
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data line chart berhasil dimuat',
            'data' => $chartData
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data line chart: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ==========================
// CHART: Doughnut Chart - Defect Distribution by category
// ==========================
function getDoughnutChart($connection)
{
    header('Content-Type: application/json');

    try {
        // Ambil parameter kategori dari URL, default 'customer'
        $kategori = $_GET['kategori'] ?? 'customer';

        // Validasi input supaya aman
        if (!in_array($kategori, ['customer', 'partno'])) {
            throw new Exception("Invalid kategori value. Must be 'customer' or 'partno'.");
        }

        // Pilih field sesuai kategori
        $field = $kategori === 'customer' ? 'nama_customer' : 'partno';

        // Query top 5 + persentase
        $sql = "
           WITH TotalDefect AS (
        SELECT COUNT(*) AS total FROM report_claim_defect
        )
        SELECT TOP 5
            d.$field AS kategori,
            COUNT(*) AS jumlah_defect,
            CAST(COUNT(*) * 100.0 / td.total AS DECIMAL(10,2)) AS persentase
        FROM report_claim_defect d
        CROSS JOIN TotalDefect td
        GROUP BY d.$field, td.total
        ORDER BY jumlah_defect DESC
        ";

        $stmt = sqlsrv_query($connection, $sql);

        if ($stmt === false) {
            throw new Exception("Gagal query doughnut chart: " . print_r(sqlsrv_errors(), true));
        }

        $labels = [];
        $data = [];
        $percentages = [];

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $labels[] = $row['kategori'];
            $data[] = (int)$row['jumlah_defect'];
            $percentages[] = (float)$row['persentase'];
        }

        sqlsrv_free_stmt($stmt);

        // Siapkan data untuk chart
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Defect',
                    'data' => $data,
                    'percentages' => $percentages
                ]
            ],
            'metadata' => [
                'kategori' => $kategori,
                'title' => $kategori === 'customer' ? 'Top 5 Customer Defect Distribution' : 'Top 5 Part Number Defect Distribution',
                'type' => 'doughnut'
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Data doughnut chart berhasil dimuat',
            'data' => $chartData
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memuat data doughnut chart: ' . $e->getMessage()
        ]);
        exit;
    }
}