<?php
require_once '../../helper/auth.php';
isLogin();
?>

<?php include '../layout/head.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-0">📊 Trend Defect CNC</h4>
                    <small class="text-muted" id="server-time">Loading server time...</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="ti ti-refresh me-1"></i> Auto Refresh 5m
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-stats" class="text-center py-5" style="display: block;">
        <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Memuat data dashboard...</p>
    </div>

    <!-- 3 Cards Utama -->
    <div class="row g-3" id="dashboard-cards" style="display: none;">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="ti ti-alert-triangle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted fw-normal">Total Defect</h6>
                            <h3 class="mb-0 fw-bold" id="total-defect">0</h3>
                        </div>
                        <div>
                            <span class="badge bg-danger-subtle text-danger">7 Hari</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none view-detail stretched-link" data-type="all">
                            Lihat Detail <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="ti ti-box fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted fw-normal">Total QTY Defect</h6>
                            <h3 class="mb-0 fw-bold" id="total-qty">0</h3>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary">Qty</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none view-detail stretched-link" data-type="qty">
                            Lihat Detail <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="ti ti-server fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted fw-normal">Mesin Terbanyak</h6>
                            <h3 class="mb-0 fw-bold" id="top-machine">-</h3>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success">Top 1</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none view-machine stretched-link" data-machine="">
                            Lihat Detail <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row untuk Charts - Trend + Pie Chart -->
    <div class="row mt-4" id="chart-section" style="display: none;">
        <!-- Chart 1: Trend Defect 7 Hari (kiri) -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">📈 Trend Defect CNC 7 Hari Terakhir</h5>
                    <div>
                        <span class="badge bg-danger me-2">■ Jumlah Defect</span>
                        <span class="badge bg-primary">■ Total Qty</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="trendChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Pie Chart TOP 3 Defect (kanan) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">🍩 TOP 3 Jenis Defect</h5>
                    <span class="badge bg-info">1 Bulan</span>
                </div>
                <div class="card-body">
                    <div id="topDefectPieChart" style="height: 350px;"></div>
                    <div id="top-defect-legend" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Pareto Mesin (full width) -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">📊 Pareto Defect per Mesin</h5>
                    <span class="badge bg-info">7 Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <div id="paretoChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 4: Defect Ratio Harian (full width) -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">📊 Trend Rasio Defect Harian (30 Hari)</h5>
                    <div>
                        <span class="badge bg-primary me-2">■ Total Qty</span>
                        <span class="badge bg-warning text-dark">● Rasio Defect (%)</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="defectRatioChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Data -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold" id="detailModalTitle">
                    <i class="ti ti-file-text me-2"></i>Detail Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailLoading" class="text-center py-5">
                    <div class="spinner-border text-info" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data...</p>
                </div>
                <div id="detailContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="detailTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Kode Mesin</th>
                                    <th>Qty</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody id="detailBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal History Machine 30 Hari -->
<div class="modal fade" id="historyMachineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold" id="historyMachineTitle">
                    <i class="ti ti-history me-2"></i>History Machine
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyLoading" class="text-center py-5">
                    <div class="spinner-border text-info" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data...</p>
                </div>
                <div id="historyContent" style="display: none;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card bg-danger text-white border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-normal opacity-75">Total Data Defect</h6>
                                    <h3 class="mb-0 fw-bold" id="historyTotalDefect">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-normal opacity-75">Total Produksi</h6>
                                    <h3 class="mb-0 fw-bold" id="historyTotalQty">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="historyChart" style="height: 300px;"></div>
                    
                    <div class="table-responsive mt-4">
                        <table class="table table-hover table-striped" id="historyTable">
                            <thead>
                                <tr>
                                    <th>Tanggal Produksi</th>
                                    <th>Hari</th>
                                    <th class="text-end">Jumlah Defect</th>
                                    <th class="text-end">Total Qty</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Ratio -->
<div class="modal fade" id="ratioDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-percentage me-2"></i>Detail Rasio Defect
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted fw-normal">📅 Tanggal</h6>
                                <h4 class="fw-bold mb-0" id="ratioDate">-</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted fw-normal">📦 Total Produksi</h6>
                                <h4 class="fw-bold text-primary mb-0" id="ratioQty">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted fw-normal">⚠️ Total Defect</h6>
                                <h4 class="fw-bold text-danger mb-0" id="ratioDefect">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-warning bg-opacity-10 border-0">
                    <div class="card-body text-center py-4">
                        <h6 class="text-muted fw-normal">📊 Rasio Defect</h6>
                        <h2 class="fw-bold" id="ratioPercentage">0%</h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Defect Type -->
<div class="modal fade" id="defectTypeDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold" id="defectTypeDetailTitle">
                    <i class="ti ti-details me-2"></i>Detail Defect Type
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="defectTypeDetailLoading" class="text-center py-5">
                    <div class="spinner-border text-info" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data detail...</p>
                </div>
                <div id="defectTypeDetailContent" style="display: none;">
                    <!-- Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-danger text-white border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-normal opacity-75">Total Defect</h6>
                                    <h3 class="mb-0 fw-bold" id="dtTotalDefect">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-normal opacity-75">Total Qty</h6>
                                    <h3 class="mb-0 fw-bold" id="dtTotalQty">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-normal opacity-75">Rata-rata Qty</h6>
                                    <h3 class="mb-0 fw-bold" id="dtAvgQty">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-normal opacity-75">Max / Min Qty</h6>
                                    <h3 class="mb-0 fw-bold" id="dtMaxMinQty">0 / 0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div id="defectTypeDetailChart" style="height: 300px;"></div>

                    <!-- Table -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover table-striped" id="defectTypeDetailTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Hari</th>
                                    <th>Kode Mesin</th>
                                    <th class="text-end">Qty</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody id="defectTypeDetailBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Tambahan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    const API_URL = 'TrendDefectCncController.php';
    let dataTable = null;
    let historyDataTable = null;
    let historyChartInstance = null;
    let ratioChartInstance = null;
    let trendChartInstance = null;
    let paretoChartInstance = null;
    let defectTypeDetailTable = null;
    let defectTypeDetailChartInstance = null;
    let topDefectPieChartInstance = null;
    let currentData = [];
    let ratioDataCache = null;
    
    // Load semua data
    loadDashboardStats();
    loadTrendChart();
    loadParetoChart();
    loadDefectRatioChart();
    loadTopDefectTypes();
    loadServerTime();

    // Event listener untuk link "Lihat Detail"
    $(document).on('click', '.view-detail', function(e) {
        e.preventDefault();
        const type = $(this).data('type');
        const titles = {
            'all': 'Detail Defect 7 Hari',
            'qty': 'Detail Qty Defect 7 Hari'
        };
        
        $('#detailModalTitle').text(titles[type] || 'Detail Data');
        loadDetailData(type);
        $('#detailModal').modal('show');
    });

    // Event listener untuk link mesin
    $(document).on('click', '.view-machine', function(e) {
        e.preventDefault();
        const machine = $(this).data('machine');
        if (machine && machine !== '-') {
            loadMachineHistory(machine);
        }
    });

    // Event listener untuk pie chart - klik segment
    $(document).on('click', '.pie-segment-click', function(e) {
        const defectType = $(this).data('defect-type');
        if (defectType && defectType !== 'Tidak ada data') {
            loadDefectTypeDetail(defectType);
        }
    });

    // ===== LOAD SERVER TIME =====
    function loadServerTime() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getServerTime' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const serverTime = response.data.server_time;
                    const date = new Date(serverTime);
                    $('#server-time').text('🕐 ' + date.toLocaleString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }));
                }
            },
            error: function() {
                $('#server-time').text('🕐 Gagal memuat waktu server');
            }
        });
    }

    // ===== LOAD DASHBOARD STATS =====
    function loadDashboardStats() {
        $('#loading-stats').show();
        $('#dashboard-cards').hide();

        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getDashboardStats' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    animateNumber('#total-defect', data.total_defect);
                    animateNumber('#total-qty', data.total_qty);
                    $('#top-machine').text(data.top_machine || '-');
                    $('.view-machine').data('machine', data.top_machine || '');
                } else {
                    showError('Gagal memuat data: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
            },
            complete: function() {
                $('#loading-stats').hide();
                $('#dashboard-cards').show();
                $('#chart-section').show();
            }
        });
    }

    // ===== LOAD TOP DEFECT TYPES =====
    function loadTopDefectTypes() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getTopDefectTypes' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderTopDefectPieChart(response.data);
                } else {
                    showError('Gagal memuat top defect types: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
            }
        });
    }

    // ===== RENDER TOP DEFECT PIE CHART =====
    function renderTopDefectPieChart(data) {
        var chartContainer = document.querySelector("#topDefectPieChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        if (!data.top_defects || data.top_defects.length === 0 || data.top_defects[0].jenis_defect === 'Tidak ada data') {
            chartContainer.innerHTML = '<div class="text-center text-muted py-5">Tidak ada data defect</div>';
            $('#top-defect-legend').html('');
            return;
        }

        var series = data.top_defects.map(function(item) {
            return item.jumlah_defect;
        });

        var labels = data.top_defects.map(function(item) {
            return item.jenis_defect + ' (' + item.persentase + '%)';
        });

        var colors = data.top_defects.map(function(item) {
            return item.color;
        });

        var options = {
            series: series,
            labels: labels,
            colors: colors,
            chart: {
                type: 'donut',
                height: 300,
                toolbar: {
                    show: true
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        var defectType = data.top_defects[config.dataPointIndex].jenis_defect;
                        if (defectType && defectType !== 'Tidak ada data') {
                            loadDefectTypeDetail(defectType);
                        }
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Defect',
                                formatter: function(w) {
                                    return data.total_defect || 0;
                                }
                            }
                        }
                    },
                    expandOnClick: true,
                    customScale: 1
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return opts.w.globals.series[opts.seriesIndex];
                },
                style: {
                    fontSize: '12px',
                    fontWeight: '600'
                }
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: '500',
                labels: {
                    colors: '#333'
                },
                markers: {
                    width: 12,
                    height: 12,
                    radius: 2
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' defect';
                    }
                }
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 250 },
                        legend: { position: 'bottom', fontSize: '10px' }
                    }
                }
            ]
        };

        try {
            topDefectPieChartInstance = new ApexCharts(chartContainer, options);
            topDefectPieChartInstance.render();
        } catch (error) {
            console.error("Error rendering pie chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart</div>';
        }
    }

    // ===== LOAD DEFECT TYPE DETAIL =====
    function loadDefectTypeDetail(defectType) {
        if (defectTypeDetailTable) {
            defectTypeDetailTable.destroy();
            defectTypeDetailTable = null;
        }
        if (defectTypeDetailChartInstance) {
            defectTypeDetailChartInstance.destroy();
            defectTypeDetailChartInstance = null;
        }

        $('#defectTypeDetailModal').modal('show');
        $('#defectTypeDetailTitle').text('Detail Defect: ' + defectType + ' (1 Bulan)');
        $('#defectTypeDetailLoading').show();
        $('#defectTypeDetailContent').hide();
        $('#defectTypeDetailChart').html('');

        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { 
                action: 'getDefectTypeDetail',
                defect_type: defectType
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayDefectTypeDetail(response.data);
                } else {
                    showError('Gagal memuat detail: ' + response.message);
                    $('#defectTypeDetailLoading').hide();
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
                $('#defectTypeDetailLoading').hide();
            }
        });
    }

    // ===== DISPLAY DEFECT TYPE DETAIL =====
    function displayDefectTypeDetail(data) {
        $('#defectTypeDetailLoading').hide();
        $('#defectTypeDetailContent').show();

        animateNumber('#dtTotalDefect', data.total_defect);
        animateNumber('#dtTotalQty', data.total_qty);
        $('#dtAvgQty').text(data.rata_rata_qty);
        $('#dtMaxMinQty').text(data.max_qty + ' / ' + data.min_qty);

        renderDefectTypeDetailChart(data.daily_summary);

        const tbody = $('#defectTypeDetailBody');
        tbody.empty();

        if (!data.details || data.details.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>');
            return;
        }

        $.each(data.details, function(index, item) {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.tanggal_produksi || '-'}</td>
                    <td>${item.hari || '-'}</td>
                    <td>${item.kode_mesin || '-'}</td>
                    <td class="text-end fw-bold text-primary">${formatNumber(item.qty)}</td>
                    <td>${formatDate(item.created_at)}</td>
                </tr>
            `;
            tbody.append(row);
        });

        defectTypeDetailTable = $('#defectTypeDetailTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: {
                "emptyTable": "Tidak ada data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="ti ti-file-spreadsheet"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Detail Defect ' + data.defect_type
                },
                {
                    extend: 'pdf',
                    text: '<i class="ti ti-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Detail Defect ' + data.defect_type,
                    orientation: 'landscape',
                    pageSize: 'A4'
                }
            ],
            order: [[0, 'asc']],
            destroy: true
        });
    }

    // ===== RENDER DEFECT TYPE DETAIL CHART =====
    function renderDefectTypeDetailChart(dailyData) {
        var chartContainer = document.querySelector("#defectTypeDetailChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        if (!dailyData || dailyData.length === 0) {
            chartContainer.innerHTML = '<div class="text-center text-muted">Tidak ada data</div>';
            return;
        }

        const categories = dailyData.map(item => item.tanggal);
        const qtyData = dailyData.map(item => item.total_qty);
        const defectData = dailyData.map(item => item.jumlah_defect);

        var options = {
            series: [
                {
                    name: 'Total Qty',
                    type: 'bar',
                    data: qtyData,
                    color: '#0d6efd'
                },
                {
                    name: 'Jumlah Defect',
                    type: 'line',
                    data: defectData,
                    color: '#dc3545'
                }
            ],
            chart: {
                type: 'bar',
                height: 300,
                stacked: false,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    },
                    autoSelected: 'zoom'
                },
                zoom: {
                    enabled: true,
                    type: 'x',
                    autoScaleYaxis: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                background: '#ffffff'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -15,
                style: {
                    fontSize: '10px',
                    fontWeight: '600'
                },
                formatter: function(val) {
                    return formatNumber(val);
                }
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            markers: {
                size: [0, 5],
                strokeWidth: 2,
                strokeColor: '#fff',
                hover: {
                    size: 8,
                    strokeWidth: 2
                }
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        fontSize: '10px',
                        fontWeight: 500
                    },
                    rotate: -45
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Total Qty',
                        style: {
                            fontSize: '11px',
                            fontWeight: '600',
                            color: '#0d6efd'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#0d6efd'
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5
                },
                {
                    opposite: true,
                    title: {
                        text: 'Jumlah Defect',
                        style: {
                            fontSize: '11px',
                            fontWeight: '600',
                            color: '#dc3545'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#dc3545'
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return formatNumber(val);
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '11px',
                fontWeight: '600'
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                }
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 250 },
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom' }
                    }
                }
            ]
        };

        try {
            defectTypeDetailChartInstance = new ApexCharts(chartContainer, options);
            defectTypeDetailChartInstance.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart</div>';
        }
    }

    // ===== ANIMATE NUMBER =====
    function animateNumber(element, target) {
        let current = 0;
        const duration = 1000;
        const steps = 60;
        const increment = target / steps;
        const interval = duration / steps;
        
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            $(element).text(Math.round(current).toLocaleString('id-ID'));
        }, interval);
    }

    // ===== LOAD TREND CHART =====
    function loadTrendChart() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getTrendChart' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderTrendChart(response.data);
                } else {
                    showError('Gagal memuat trend chart: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
            }
        });
    }

    // ===== RENDER TREND CHART =====
    function renderTrendChart(data) {
        var chartContainer = document.querySelector("#trendChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        var options = {
            series: [
                {
                    name: 'Jumlah Defect',
                    type: 'bar',
                    data: data.defect_data,
                    color: '#dc3545'
                },
                {
                    name: 'Total Qty',
                    type: 'line',
                    data: data.qty_data,
                    color: '#0d6efd'
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                background: '#ffffff'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val > 0 ? formatNumber(val) : '';
                },
                style: {
                    fontSize: '10px',
                    fontWeight: '600'
                },
                offsetY: -10
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth',
                lineCap: 'round'
            },
            markers: {
                size: [0, 5],
                strokeWidth: 2,
                strokeColor: '#fff',
                hover: {
                    size: 8
                }
            },
            xaxis: {
                categories: data.categories,
                labels: {
                    style: {
                        fontSize: '11px',
                        fontWeight: 500
                    }
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Jumlah Defect',
                        style: {
                            fontSize: '12px',
                            fontWeight: '600',
                            color: '#dc3545'
                        }
                    },
                    min: 0,
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#dc3545'
                        }
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Total Qty',
                        style: {
                            fontSize: '12px',
                            fontWeight: '600',
                            color: '#0d6efd'
                        }
                    },
                    min: 0,
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#0d6efd'
                        }
                    }
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return formatNumber(val);
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: '600',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 2
                }
            },
            grid: {
                borderColor: '#e0e0e0',
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                }
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 250 },
                        plotOptions: {
                            bar: { columnWidth: '50%' }
                        },
                        dataLabels: { enabled: false }
                    }
                }
            ]
        };

        try {
            trendChartInstance = new ApexCharts(chartContainer, options);
            trendChartInstance.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart: ' + error.message + '</div>';
        }
    }

    // ===== LOAD PARETO CHART =====
    function loadParetoChart() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getParetoChart' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderParetoChart(response.data);
                } else {
                    showError('Gagal memuat pareto chart: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
            }
        });
    }

    // ===== RENDER PARETO CHART =====
    function renderParetoChart(data) {
        var chartContainer = document.querySelector("#paretoChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        if (!data.machines || data.machines.length === 0) {
            chartContainer.innerHTML = '<div class="text-center text-muted py-5">Tidak ada data</div>';
            return;
        }

        var total = data.total_qty.reduce((a, b) => a + b, 0);
        var cumulative = 0;
        var cumulativePercent = data.total_qty.map(function(val) {
            cumulative += val;
            return Math.round((cumulative / total) * 100);
        });

        var options = {
            series: [
                {
                    name: 'Total Qty',
                    type: 'bar',
                    data: data.total_qty,
                    color: '#4361ee'
                },
                {
                    name: 'Kumulatif %',
                    type: 'line',
                    data: cumulativePercent,
                    color: '#ef233c'
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const dataPointIndex = config.dataPointIndex;
                        if (dataPointIndex !== undefined) {
                            const machine = data.machines[dataPointIndex];
                            if (machine) {
                                loadMachineHistory(machine);
                            }
                        }
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                background: '#ffffff'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    if (opts.seriesIndex === 0) {
                        return formatNumber(val);
                    }
                    return val + '%';
                },
                style: {
                    fontSize: '10px',
                    fontWeight: '600'
                },
                offsetY: -10
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth',
                lineCap: 'round'
            },
            markers: {
                size: [0, 5],
                strokeWidth: 2,
                strokeColor: '#fff',
                hover: {
                    size: 8
                }
            },
            xaxis: {
                categories: data.machines,
                labels: {
                    style: {
                        fontSize: '11px',
                        fontWeight: 500
                    }
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Total Qty',
                        style: {
                            fontSize: '12px',
                            fontWeight: '600',
                            color: '#4361ee'
                        }
                    },
                    min: 0,
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#4361ee'
                        }
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Kumulatif %',
                        style: {
                            fontSize: '12px',
                            fontWeight: '600',
                            color: '#ef233c'
                        }
                    },
                    min: 0,
                    max: 100,
                    labels: {
                        formatter: function(val) {
                            return val + '%';
                        },
                        style: {
                            colors: '#ef233c'
                        }
                    }
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val, { seriesIndex }) {
                        if (seriesIndex === 0) {
                            return formatNumber(val) + ' qty';
                        }
                        return val + '%';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: '600',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 2
                }
            },
            grid: {
                borderColor: '#e0e0e0',
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                }
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 250 },
                        plotOptions: {
                            bar: { columnWidth: '60%' }
                        },
                        dataLabels: { enabled: false }
                    }
                }
            ]
        };

        try {
            paretoChartInstance = new ApexCharts(chartContainer, options);
            paretoChartInstance.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart: ' + error.message + '</div>';
        }
    }

    // ===== LOAD DEFECT RATIO CHART =====
    function loadDefectRatioChart() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getDefectRatioChart' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    ratioDataCache = response.data;
                    renderDefectRatioChart(response.data);
                } else {
                    showError('Gagal memuat defect ratio chart: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
            }
        });
    }

    // ===== RENDER DEFECT RATIO CHART =====
    function renderDefectRatioChart(data) {
        var chartContainer = document.querySelector("#defectRatioChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        if (!data.categories || data.categories.length === 0) {
            chartContainer.innerHTML = '<div class="text-center text-muted py-5">Tidak ada data</div>';
            return;
        }

        var hasValidData = data.ratio_data.some(function(val) {
            return val !== null && val !== undefined && !isNaN(val);
        });

        if (!hasValidData) {
            chartContainer.innerHTML = '<div class="text-center text-muted py-5">Belum ada data rasio defect</div>';
            return;
        }

        var options = {
            series: [
                {
                    name: 'Total Qty',
                    type: 'bar',
                    data: data.qty_data,
                    color: '#0d6efd'
                },
                {
                    name: 'Rasio Defect (%)',
                    type: 'line',
                    data: data.ratio_data,
                    color: '#ffc107'
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    },
                    autoSelected: 'zoom'
                },
                zoom: {
                    enabled: true,
                    type: 'x',
                    autoScaleYaxis: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                background: '#ffffff',
                foreColor: '#333',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        var dataPointIndex = config.dataPointIndex;
                        if (dataPointIndex !== undefined && ratioDataCache) {
                            var dailyData = ratioDataCache.daily_data[dataPointIndex];
                            if (dailyData && dailyData.qty > 0) {
                                showRatioDetail(dailyData);
                            }
                        }
                    }
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -10,
                style: {
                    fontSize: '10px',
                    fontWeight: '600',
                    colors: ['#333']
                },
                formatter: function(val, opts) {
                    if (opts.seriesIndex === 0) {
                        return formatNumber(val);
                    }
                    return val + '%';
                },
                background: {
                    enabled: false
                }
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth',
                lineCap: 'round'
            },
            markers: {
                size: [0, 6],
                strokeWidth: 2,
                strokeColor: '#fff',
                hover: {
                    size: 10,
                    strokeWidth: 2
                }
            },
            fill: {
                opacity: [1, 1]
            },
            xaxis: {
                categories: data.categories,
                labels: {
                    style: {
                        fontSize: '11px',
                        fontWeight: 500,
                        colors: '#555'
                    },
                    rotate: -45,
                    rotateAlways: false,
                    hideOverlappingLabels: true,
                    trim: true
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Total Qty',
                        style: {
                            fontSize: '12px',
                            fontWeight: '600',
                            color: '#0d6efd'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            fontSize: '11px',
                            colors: '#0d6efd'
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5
                },
                {
                    opposite: true,
                    title: {
                        text: 'Rasio Defect (%)',
                        style: {
                            fontSize: '12px',
                            fontWeight: '600',
                            color: '#ffc107'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        },
                        style: {
                            fontSize: '11px',
                            colors: '#ffc107'
                        }
                    },
                    min: 0,
                    max: function(max) {
                        max = Math.round((max + Number.EPSILON) * 100) / 100;
                        return max <= 0.5 ? 0.5 : Math.ceil(max / 4) * 4;
                    },
                    forceNiceScale: true,
                    tickAmount: 5
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                backgroundColor: '#ffffff',
                borderColor: '#e0e0e0',
                borderWidth: 1,
                borderRadius: 8,
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'inherit'
                },
                y: {
                    formatter: function(val, { seriesIndex }) {
                        if (seriesIndex === 0) {
                            return formatNumber(val);
                        }
                        return val + '%';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: '600',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 2,
                    strokeWidth: 0
                },
                itemMargin: {
                    horizontal: 15,
                    vertical: 5
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                },
                padding: {
                    top: 0,
                    right: 10,
                    bottom: 0,
                    left: 10
                }
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 250 },
                        plotOptions: {
                            bar: { columnWidth: '50%' }
                        },
                        dataLabels: { enabled: false },
                        legend: {
                            position: 'bottom',
                            offsetY: 0
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '9px'
                                }
                            }
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    fontSize: '9px'
                                },
                                rotate: -90
                            }
                        }
                    }
                },
                {
                    breakpoint: 480,
                    options: {
                        chart: { height: 200 },
                        plotOptions: {
                            bar: { columnWidth: '60%' }
                        },
                        dataLabels: { enabled: false },
                        markers: {
                            size: [0, 4]
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '10px'
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '8px'
                                }
                            }
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    fontSize: '8px'
                                },
                                rotate: -90
                            }
                        }
                    }
                }
            ]
        };

        try {
            ratioChartInstance = new ApexCharts(chartContainer, options);
            ratioChartInstance.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart: ' + error.message + '</div>';
        }
    }

    // ===== SHOW RATIO DETAIL =====
    function showRatioDetail(data) {
        if (!data) {
            console.warn('Data tidak valid');
            return;
        }
        
        $('#ratioDate').text(data.tanggal || '-');
        $('#ratioQty').text(formatNumber(data.qty || 0));
        $('#ratioDefect').text(formatNumber(data.defect || 0));
        
        var ratioValue = data.ratio || 0;
        $('#ratioPercentage').text(ratioValue + '%');
        
        var ratioColor = 'text-success';
        if (ratioValue > 15) {
            ratioColor = 'text-danger';
        } else if (ratioValue > 5) {
            ratioColor = 'text-warning';
        }
        
        $('#ratioPercentage').removeClass('text-success text-warning text-danger').addClass(ratioColor);
        $('#ratioDetailModal').modal('show');
    }

    // ===== LOAD MACHINE HISTORY =====
    function loadMachineHistory(machine) {
        if (historyChartInstance) {
            historyChartInstance.destroy();
            historyChartInstance = null;
        }
        
        if (historyDataTable) {
            historyDataTable.destroy();
            historyDataTable = null;
        }
        
        $('#historyMachineModal').modal('show');
        $('#historyMachineTitle').text('History Machine: ' + machine + ' (30 Hari)');
        $('#historyLoading').show();
        $('#historyContent').hide();
        $('#historyChart').html('');

        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { 
                action: 'getMachineHistory',
                machine: machine
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayMachineHistory(response.data);
                } else {
                    showError('Gagal memuat history machine: ' + response.message);
                    $('#historyLoading').hide();
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
                $('#historyLoading').hide();
            }
        });
    }

    // ===== DISPLAY MACHINE HISTORY =====
    function displayMachineHistory(data) {
        $('#historyLoading').hide();
        $('#historyContent').show();
        
        animateNumber('#historyTotalDefect', data.total_defect);
        animateNumber('#historyTotalQty', data.total_qty);

        renderHistoryChart(data);
        
        const tbody = $('#historyBody');
        tbody.empty();

        if (!data.daily_data || data.daily_data.length === 0) {
            tbody.html('<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>');
            return;
        }

        $.each(data.daily_data, function(index, item) {
            const row = `
                <tr>
                    <td>${item.tanggal}</td>
                    <td>${item.hari}</td>
                    <td class="text-end ${item.jumlah_defect > 0 ? 'fw-bold text-danger' : 'text-muted'}">${formatNumber(item.jumlah_defect)}</td>
                    <td class="text-end ${item.total_qty > 0 ? 'fw-bold text-primary' : 'text-muted'}">${formatNumber(item.total_qty)}</td>
                </tr>
            `;
            tbody.append(row);
        });

        historyDataTable = $('#historyTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: {
                "emptyTable": "Tidak ada data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="ti ti-file-spreadsheet"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'History Machine ' + data.machine
                },
                {
                    extend: 'pdf',
                    text: '<i class="ti ti-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'History Machine ' + data.machine,
                    orientation: 'landscape',
                    pageSize: 'A4'
                }
            ],
            order: [[0, 'asc']],
            destroy: true
        });
    }
    
    // ===== RENDER HISTORY CHART =====
    function renderHistoryChart(data) {
        var chartContainer = document.querySelector("#historyChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        if (!data.categories || data.categories.length === 0) {
            chartContainer.innerHTML = '<div class="text-center text-muted">Tidak ada data</div>';
            return;
        }

        var options = {
            series: [
                {
                    name: 'Jumlah Defect',
                    type: 'bar',
                    data: data.defect_data,
                    color: '#dc3545'
                },
                {
                    name: 'Total Qty',
                    type: 'line',
                    data: data.qty_data,
                    color: '#0d6efd'
                }
            ],
            chart: {
                type: 'bar',
                height: 300,
                stacked: false,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    },
                    autoSelected: 'zoom'
                },
                zoom: {
                    enabled: true,
                    type: 'x',
                    autoScaleYaxis: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                background: '#ffffff'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 8,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '10px',
                    fontWeight: '600',
                    colors: ['#333']
                },
                formatter: function(val) {
                    return formatNumber(val);
                }
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            markers: {
                size: [0, 5],
                strokeWidth: 2,
                strokeColor: '#fff',
                hover: {
                    size: 8,
                    strokeWidth: 2
                }
            },
            xaxis: {
                categories: data.categories,
                labels: {
                    style: {
                        fontSize: '10px',
                        fontWeight: 500
                    },
                    rotate: -45
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Jumlah Defect',
                        style: {
                            fontSize: '11px',
                            fontWeight: '600',
                            color: '#dc3545'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#dc3545'
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5
                },
                {
                    opposite: true,
                    title: {
                        text: 'Total Qty',
                        style: {
                            fontSize: '11px',
                            fontWeight: '600',
                            color: '#0d6efd'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            colors: '#0d6efd'
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return formatNumber(val);
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '11px',
                fontWeight: '600'
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                }
            },
            responsive: [
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 250 },
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom' }
                    }
                }
            ]
        };

        try {
            historyChartInstance = new ApexCharts(chartContainer, options);
            historyChartInstance.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart</div>';
        }
    }

    // ===== LOAD DETAIL DATA =====
    function loadDetailData(type) {
        $('#detailLoading').show();
        $('#detailContent').hide();

        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }

        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { 
                action: 'getDetailData',
                type: type
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    currentData = response.data;
                    displayDetailData(response.data);
                } else {
                    showError('Gagal memuat detail: ' + response.message);
                    $('#detailLoading').hide();
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
                $('#detailLoading').hide();
            }
        });
    }

    // ===== DISPLAY DETAIL DATA =====
    function displayDetailData(data) {
        $('#detailLoading').hide();
        $('#detailContent').show();

        const tbody = $('#detailBody');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>');
            $('#detailTable').show();
            return;
        }

        $.each(data, function(index, item) {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.tanggal_produksi || '-'}</td>
                    <td>${item.kode_mesin || '-'}</td>
                    <td class="text-end">${formatNumber(item.qty)}</td>
                    <td>${formatDate(item.created_at)}</td>
                </tr>
            `;
            tbody.append(row);
        });

        dataTable = $('#detailTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            language: {
                "emptyTable": "Tidak ada data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="ti ti-file-spreadsheet"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="ti ti-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<i class="ti ti-printer"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            order: [[0, 'asc']],
            destroy: true
        });
    }

    // ===== UTILITY FUNCTIONS =====
    function formatNumber(num) {
        if (num === undefined || num === null) return '0';
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function showError(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message, 'Error');
        } else {
            alert('Error: ' + message);
        }
    }

    // ===== RESET MODALS =====
    $('#detailModal').on('hidden.bs.modal', function() {
        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }
        $('#detailBody').empty();
        $('#detailContent').hide();
        $('#detailLoading').show();
    });

    $('#historyMachineModal').on('hidden.bs.modal', function() {
        if (historyChartInstance) {
            historyChartInstance.destroy();
            historyChartInstance = null;
        }
        
        if (historyDataTable) {
            historyDataTable.destroy();
            historyDataTable = null;
        }
        
        $('#historyBody').empty();
        $('#historyContent').hide();
        $('#historyLoading').show();
        $('#historyChart').html('');
        $('#historyTotalDefect').text('0');
        $('#historyTotalQty').text('0');
    });

    $('#defectTypeDetailModal').on('hidden.bs.modal', function() {
        if (defectTypeDetailTable) {
            defectTypeDetailTable.destroy();
            defectTypeDetailTable = null;
        }
        if (defectTypeDetailChartInstance) {
            defectTypeDetailChartInstance.destroy();
            defectTypeDetailChartInstance = null;
        }
        $('#defectTypeDetailBody').empty();
        $('#defectTypeDetailContent').hide();
        $('#defectTypeDetailLoading').show();
        $('#defectTypeDetailChart').html('');
    });

    // ===== AUTO REFRESH SETIAP 5 MENIT =====
    setInterval(function() {
        loadDashboardStats();
        loadTrendChart();
        loadParetoChart();
        loadDefectRatioChart();
        loadTopDefectTypes();
        loadServerTime();
    }, 300000);
});
</script>

<style>
#dashboard-cards .hover-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

#dashboard-cards .hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

#dashboard-cards .stretched-link {
    color: #6c757d;
    font-size: 0.875rem;
}

#dashboard-cards .stretched-link:hover {
    color: #0d6efd !important;
}

.badge {
    font-weight: 500;
    padding: 0.35rem 0.75rem;
}

.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.12);
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.12);
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.12);
}

#chart-section .card {
    border-radius: 12px;
    overflow: hidden;
}

#chart-section .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 5px 12px;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 5px 12px;
}

.modal-content {
    border-radius: 12px;
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
}

.pie-segment-click {
    cursor: pointer;
}

@media (max-width: 768px) {
    #dashboard-cards .col-md-4 {
        margin-bottom: 1rem;
    }
    
    #chart-section .card {
        margin-bottom: 1rem;
    }
    
    .modal-xl {
        max-width: 95%;
    }
}
</style>