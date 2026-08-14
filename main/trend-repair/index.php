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
                    <h4 class="fw-semibold mb-0">Trend Repair</h4>
                    <small class="text-muted" id="server-time">Loading server time...</small>
                </div>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="index.php" class="text-muted">Trend Repair</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-stats" class="text-center py-3" style="display: block;">
        <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat data...</p>
    </div>

    <!-- 3 Cards Utama -->
    <div class="row" id="dashboard-cards" style="display: none;">
        <!-- Card 1: Outstanding Repair -->
        <div class="col-md-4">
            <div class="card shadow-sm" id="outstanding-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-25 p-3 me-3">
                            <i class="ti ti-clock fs-5 text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted">Outstanding Repair</h6>
                            <h3 class="mb-0 fw-bold" id="total-outstanding">0</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none view-detail" data-type="outstanding">
                            Lihat Detail <i class="ti ti-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Data Masuk Hari Ini -->
        <div class="col-md-4">
            <div class="card shadow-sm" id="masuk-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-3">
                            <i class="ti ti-arrow-right fs-5 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted">Data Masuk Hari Ini</h6>
                            <h3 class="mb-0 fw-bold" id="total-masuk">0</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-primary-subtle text-primary">Baru</span>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none view-detail" data-type="masuk">
                            Lihat Detail <i class="ti ti-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Data Selesai Hari Ini -->
        <div class="col-md-4">
            <div class="card shadow-sm" id="selesai-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-25 p-3 me-3">
                            <i class="ti ti-check fs-5 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted">Data Selesai Hari Ini</h6>
                            <h3 class="mb-0 fw-bold" id="total-selesai">0</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-success-subtle text-success">Selesai</span>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none view-detail" data-type="selesai">
                            Lihat Detail <i class="ti ti-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row untuk 2 Charts -->
    <div class="row mt-4" id="chart-section" style="display: none;">
        <!-- Chart 1: Trend Defect 7 Hari -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Trend Defect 7 Hari Terakhir</h5>
                    <div>
                        <span class="badge bg-danger me-1">■ Data Masuk</span>
                        <span class="badge bg-success me-1">■ Data Selesai</span>
                        <span class="badge bg-warning me-1">■ Outstanding</span>
                        <span class="badge bg-primary">▲ Trend</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="trendChart" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Top 3 Defects -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top 3 Defect Tertinggi</h5>
                    <span class="badge bg-info">30 Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <div id="topDefectsChart" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Data dengan DataTables -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalTitle">Detail Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailLoading" class="text-center py-3">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
                <div id="detailContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="detailTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Section</th>
                                    <th>Defect</th>
                                    <th>Part No</th>
                                    <th>Lot No</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="detailBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal History Defect 30 Hari -->
<div class="modal fade" id="historyDefectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyDefectTitle">History Defect 30 Hari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyLoading" class="text-center py-3">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
                <div id="historyContent" style="display: none;">
                    <!-- Summary Cards -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total QTY</h6>
                                    <h3 class="mb-0" id="historyTotalQty">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Kejadian</h6>
                                    <h3 class="mb-0" id="historyTotalKejadian">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart -->
                    <div id="historyChart" style="height: 300px;"></div>
                    
                    <!-- Tabel Detail -->
                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-striped" id="historyTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Total Qty</th>
                                    <th>Total Kejadian</th>
                                    <th>Section</th>
                                    <th>Part No</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Tambahan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- Gunakan ApexCharts CDN yang stabil -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Definisikan API URL
    const API_URL = 'TrendRepairController.php';
    let dataTable = null;
    let historyDataTable = null;
    let currentData = [];
    let historyChartInstance = null;
    
    // Load server time
    loadServerTime();
    
    // Load data dashboard
    loadDashboardStats();
    loadWeeklyTrend();
    loadTopDefects();

    // Event listener untuk link "Lihat Detail"
    $(document).on('click', '.view-detail', function() {
        const type = $(this).data('type');
        const titles = {
            'outstanding': 'Outstanding Repair',
            'masuk': 'Data Masuk Hari Ini',
            'selesai': 'Data Selesai Hari Ini'
        };
        
        $('#detailModalTitle').text(titles[type] || 'Detail Data');
        loadDetailData(type);
        $('#detailModal').modal('show');
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
                    $('#total-outstanding').text(formatNumber(data.outstanding_repair));
                    $('#total-masuk').text(formatNumber(data.masuk_hari_ini));
                    $('#total-selesai').text(formatNumber(data.selesai_hari_ini));
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

    function loadWeeklyTrend() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getWeeklyTrend' },
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

    function loadTopDefects() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { action: 'getTopDefects' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderTopDefectsChart(response.data);
                } else {
                    showError('Gagal memuat top defects: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
            }
        });
    }

    function renderTopDefectsChart(data) {
        var chartContainer = document.querySelector("#topDefectsChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        var options = {
            series: data.map(function(item) {
                return item.total_qty;
            }),
            chart: {
                type: 'donut',
                height: 350,
                toolbar: {
                    show: true
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const dataPointIndex = config.dataPointIndex;
                        if (dataPointIndex !== undefined) {
                            const defectName = data[dataPointIndex].nama_defect;
                            if (defectName && defectName !== 'Tidak ada data') {
                                loadDefectHistory(defectName);
                            }
                        }
                    }
                }
            },
            labels: data.map(function(item) {
                return item.nama_defect;
            }),
            colors: ['#4361ee', '#ef233c', '#f77f00'],
            legend: {
                position: 'bottom',
                formatter: function(seriesName, opts) {
                    const index = opts.seriesIndex;
                    return seriesName + ': ' + formatNumber(data[index].total_qty);
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return formatNumber(total);
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return formatNumber(val) + ' defect';
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        try {
            var chart = new ApexCharts(chartContainer, options);
            chart.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart: ' + error.message + '</div>';
        }
    }

    // Fungsi untuk load history defect 30 hari
    function loadDefectHistory(defectName) {
        // Reset chart instance jika ada
        if (historyChartInstance) {
            historyChartInstance.destroy();
            historyChartInstance = null;
        }
        
        // Reset DataTable jika ada
        if (historyDataTable) {
            historyDataTable.destroy();
            historyDataTable = null;
        }
        
        $('#historyDefectModal').modal('show');
        $('#historyDefectTitle').text('History Defect: ' + defectName + ' (30 Hari Terakhir)');
        $('#historyLoading').show();
        $('#historyContent').hide();
        $('#historyChart').html('');

        $.ajax({
            url: API_URL,
            type: 'GET',
            data: { 
                action: 'getDefectHistory',
                defect: defectName
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayDefectHistory(response.data);
                } else {
                    showError('Gagal memuat history defect: ' + response.message);
                    $('#historyLoading').hide();
                }
            },
            error: function(xhr, status, error) {
                showError('Terjadi kesalahan: ' + error);
                $('#historyLoading').hide();
            }
        });
    }

    // Fungsi untuk display history defect
    function displayDefectHistory(data) {
        $('#historyLoading').hide();
        $('#historyContent').show();
        
        // Update summary
        $('#historyTotalQty').text(formatNumber(data.total_qty));
        $('#historyTotalKejadian').text(formatNumber(data.total_kejadian));

        // Render chart history - VERSI SMOOTH & OK
        renderHistoryChart(data);
        
        // Render tabel history
        const tbody = $('#historyBody');
        tbody.empty();

        if (!data.daily_data || data.daily_data.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
            return;
        }

        $.each(data.daily_data, function(index, item) {
            const row = `
                <tr>
                    <td>${item.tanggal}</td>
                    <td>${item.hari}</td>
                    <td class="text-end ${item.total_qty > 0 ? 'fw-bold text-primary' : 'text-muted'}">${formatNumber(item.total_qty)}</td>
                    <td class="text-end ${item.total_kejadian > 0 ? 'fw-bold text-info' : 'text-muted'}">${formatNumber(item.total_kejadian)}</td>
                    <td>${item.sections}</td>
                    <td>${item.part_numbers}</td>
                </tr>
            `;
            tbody.append(row);
        });

        // Inisialisasi DataTable untuk history
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
                    title: 'History Defect ' + data.defect_name
                },
                {
                    extend: 'pdf',
                    text: '<i class="ti ti-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'History Defect ' + data.defect_name,
                    orientation: 'landscape',
                    pageSize: 'A4'
                }
            ],
            order: [[0, 'asc']],
            destroy: true
        });
    }
    
    // ===== CHART HISTORY - VERSI SMOOTH & OK =====
    function renderHistoryChart(data) {
        var chartContainer = document.querySelector("#historyChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        // Bersihkan container
        chartContainer.innerHTML = '';

        // Jika tidak ada data, tampilkan pesan
        if (!data.categories || data.categories.length === 0) {
            chartContainer.innerHTML = '<div class="text-center text-muted">Tidak ada data untuk ditampilkan</div>';
            return;
        }

        // Format angka dengan pemisah ribuan
        function formatNumber(val) {
            if (val === undefined || val === null) return '0';
            return new Intl.NumberFormat('id-ID').format(val);
        }

        var options = {
            series: [
                {
                    name: 'Total Qty',
                    type: 'bar',
                    data: data.qty_data,
                    color: '#4361ee'
                },
                {
                    name: 'Total Kejadian',
                    type: 'line',
                    data: data.kejadian_data,
                    color: '#ef233c'
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
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    }
                },
                background: '#ffffff',
                foreColor: '#333'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    dataLabels: {
                        position: 'top'
                    }
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
                },
                background: {
                    enabled: false
                }
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth', // <-- INI YANG MEMBUAT SMOOTH
                dashArray: [0, 0],
                lineCap: 'round'
            },
            markers: {
                size: [0, 5],
                strokeWidth: 2,
                strokeColor: '#fff',
                hover: {
                    size: 8,
                    strokeWidth: 2
                },
                shape: 'circle'
            },
            fill: {
                opacity: [1, 1],
                gradient: {
                    enabled: false
                }
            },
            xaxis: {
                categories: data.categories,
                labels: {
                    style: {
                        fontSize: '10px',
                        fontWeight: '500',
                        colors: '#555'
                    },
                    rotate: -45,
                    rotateAlways: false,
                    hideOverlappingLabels: true,
                    trim: true
                },
                axisBorder: {
                    color: '#e0e0e0'
                },
                axisTicks: {
                    color: '#e0e0e0'
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Total Qty',
                        style: {
                            fontSize: '11px',
                            fontWeight: '600',
                            color: '#4361ee'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            fontSize: '9px',
                            colors: '#4361ee'
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5
                },
                {
                    opposite: true,
                    title: {
                        text: 'Total Kejadian',
                        style: {
                            fontSize: '11px',
                            fontWeight: '600',
                            color: '#ef233c'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return formatNumber(val);
                        },
                        style: {
                            fontSize: '9px',
                            colors: '#ef233c'
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
                        return formatNumber(val);
                    },
                    title: {
                        formatter: function(seriesName) {
                            return seriesName + ': ';
                        }
                    }
                },
                marker: {
                    show: true
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                offsetY: 5,
                markers: {
                    width: 12,
                    height: 12,
                    radius: 2,
                    strokeWidth: 0
                },
                itemMargin: {
                    horizontal: 15,
                    vertical: 5
                },
                fontSize: '11px',
                fontWeight: '600',
                labels: {
                    colors: '#333'
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
                        chart: {
                            height: 250
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '60%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'bottom',
                            offsetY: 0
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
                },
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 200
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '70%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '10px'
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '7px'
                                }
                            }
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    fontSize: '7px'
                                },
                                rotate: -90
                            }
                        }
                    }
                }
            ]
        };

        try {
            historyChartInstance = new ApexCharts(chartContainer, options);
            historyChartInstance.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart: ' + error.message + '</div>';
        }
    }

    function renderTrendChart(data) {
        var chartContainer = document.querySelector("#trendChart");
        if (!chartContainer) {
            console.error("Chart container tidak ditemukan");
            return;
        }

        chartContainer.innerHTML = '';

        var trendData = data.series[0].data.map(function(val, index) {
            return val;
        });

        var options = {
            series: [
                {
                    name: 'Data Masuk',
                    type: 'bar',
                    data: data.series[0].data,
                    color: '#dc3545'
                },
                {
                    name: 'Data Selesai',
                    type: 'bar',
                    data: data.series[1].data,
                    color: '#198754'
                },
                {
                    name: 'Outstanding',
                    type: 'bar',
                    data: data.series[2].data,
                    color: '#ffc107'
                },
                {
                    name: 'Trend',
                    type: 'line',
                    data: trendData,
                    color: '#0d6efd'
                }
            ],
            chart: {
                type: 'bar',
                height: 400,
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
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    if (opts.seriesIndex < 2) {
                        return val > 0 ? val : '';
                    }
                    return '';
                },
                style: {
                    fontSize: '9px'
                }
            },
            stroke: {
                width: [0, 0, 0, 3],
                curve: 'smooth',
                colors: ['#0d6efd']
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
            yaxis: {
                title: {
                    text: 'Jumlah Defect',
                    style: {
                        fontSize: '12px'
                    }
                },
                min: 0
            },
            fill: {
                opacity: [0.9, 0.9, 0.9, 1]
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return val + " defect"
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
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
            }
        };

        try {
            var chart = new ApexCharts(chartContainer, options);
            chart.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
            chartContainer.innerHTML = '<div class="text-center text-danger">Gagal memuat chart: ' + error.message + '</div>';
        }
    }

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

    function displayDetailData(data) {
        $('#detailLoading').hide();
        $('#detailContent').show();

        const tbody = $('#detailBody');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>');
            $('#detailTable').show();
            return;
        }

        $.each(data, function(index, item) {
            const statusBadge = item.status == 0 ? 
                '<span class="badge bg-danger">NG</span>' : 
                '<span class="badge bg-success">OK</span>';
            
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_customer || '-'}</td>
                    <td>${item.nama_section || '-'}</td>
                    <td>${item.nama_defect || '-'}</td>
                    <td>${item.partno || '-'}</td>
                    <td>${item.lotno || '-'}</td>
                    <td class="text-end">${formatNumber(item.qty)}</td>
                    <td>${statusBadge}</td>
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
                    className: 'btn btn-success btn-sm',
                    title: 'Detail Data'
                },
                {
                    extend: 'pdf',
                    text: '<i class="ti ti-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Detail Data',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<i class="ti ti-printer"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            columnDefs: [
                { targets: [0], searchable: false, orderable: true },
                { targets: [7], orderable: true }
            ],
            order: [[0, 'asc']],
            destroy: true
        });
    }

    function formatNumber(num) {
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

    // Reset DataTable saat modal ditutup
    $('#detailModal').on('hidden.bs.modal', function() {
        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }
        $('#detailBody').empty();
        $('#detailContent').hide();
        $('#detailLoading').show();
    });

    // Reset history modal saat ditutup
    $('#historyDefectModal').on('hidden.bs.modal', function() {
        // Destroy chart instance
        if (historyChartInstance) {
            historyChartInstance.destroy();
            historyChartInstance = null;
        }
        
        // Destroy DataTable
        if (historyDataTable) {
            historyDataTable.destroy();
            historyDataTable = null;
        }
        
        // Reset konten
        $('#historyBody').empty();
        $('#historyContent').hide();
        $('#historyLoading').show();
        $('#historyChart').html('');
        
        // Reset summary
        $('#historyTotalQty').text('0');
        $('#historyTotalKejadian').text('0');
    });
});
</script>

<style>
/* Styling tambahan */
#dashboard-cards .card {
    transition: transform 0.2s;
    border: none;
    border-radius: 12px;
}

#dashboard-cards .card:hover {
    transform: translateY(-5px);
}

#dashboard-cards .card .card-body {
    padding: 1.5rem;
}

.view-detail {
    font-size: 0.875rem;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.view-detail:hover {
    opacity: 1;
    color: #0d6efd !important;
}

.badge {
    font-weight: 500;
    padding: 0.35rem 0.75rem;
}

.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.15);
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.15);
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.15);
}

/* Chart section */
#chart-section .card {
    border: none;
    border-radius: 12px;
}

#chart-section .card-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 1.5rem;
}

/* DataTables styling */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px 10px;
    margin-left: 5px;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px 10px;
}

/* Modal XL */
.modal-xl {
    max-width: 90%;
}

/* Button di DataTables */
.dt-buttons {
    margin-bottom: 10px;
}

.dt-buttons .btn {
    margin-right: 5px;
}

/* Styling tabel */
#detailTable th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
}

#detailTable td {
    vertical-align: middle;
}

/* Hover effect on donut chart */
.apexcharts-donut-series path {
    cursor: pointer !important;
}

/* Summary cards in history modal */
#historyContent .card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    #detailTable {
        font-size: 12px;
    }
    
    #detailTable th,
    #detailTable td {
        padding: 5px 8px;
    }
    
    #historyTable {
        font-size: 12px;
    }
    
    #historyTable th,
    #historyTable td {
        padding: 5px 8px;
    }
}
</style>