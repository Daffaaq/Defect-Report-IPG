<?php
require_once '../../helper/auth.php';

isLogin();
?>

<?php
include '../layout/head.php';
?>

<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Dashboard</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="index.php" class="text-muted">Dashboard</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Loading Indicator untuk cards -->
    <div id="loading-stats" class="text-center py-3" style="display: none;">
        <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat data dashboard...</p>
    </div>

    <!-- Content - 3 Cards dengan masing-masing dibagi 2 (kiri-kanan) -->
    <div class="row" id="dashboard-cards">
        <!-- Kolom Kiri: Total Defect (kiri) & Total Customer (kanan) -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100" id="card-left">
                <div class="card-body p-0 d-flex align-items-center h-100">
                    <div class="row g-0 w-100">
                        <!-- Total Defect -->
                        <div class="col-6 border-end">
                            <div class="p-3 text-center">
                                <div class="bg-light-danger rounded-2 p-2 d-inline-block mb-2">
                                    <i class="ti ti-bug fs-6 text-danger"></i>
                                </div>
                                <p class="text-dark mb-1 fw-semibold">Total Defect</p>
                                <h4 class="mb-0 fw-bold" id="total-defect">0</h4>
                            </div>
                        </div>
                        <!-- Total Customer -->
                        <div class="col-6">
                            <div class="p-3 text-center">
                                <div class="bg-light-success rounded-2 p-2 d-inline-block mb-2">
                                    <i class="ti ti-building-skyscraper fs-6 text-success"></i>
                                </div>
                                <p class="text-dark mb-1 fw-semibold">Total Customer</p>
                                <h4 class="mb-0 fw-bold" id="total-customer">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Tengah: Total Repair (kiri) & Total Scrap (kanan) -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100" id="card-middle">
                <div class="card-body p-0 d-flex align-items-center h-100">
                    <div class="row g-0 w-100">
                        <!-- Total Repair -->
                        <div class="col-6 border-end">
                            <div class="p-3 text-center">
                                <div class="bg-light-primary rounded-2 p-2 d-inline-block mb-2">
                                    <i class="ti ti-tools fs-6 text-primary"></i>
                                </div>
                                <p class="text-dark mb-1 fw-semibold">Total Repair</p>
                                <h4 class="mb-0 fw-bold" id="total-repair">0</h4>
                            </div>
                        </div>
                        <!-- Total Scrap -->
                        <div class="col-6">
                            <div class="p-3 text-center">
                                <div class="bg-light-danger rounded-2 p-2 d-inline-block mb-2">
                                    <i class="ti ti-trash fs-6 text-danger"></i>
                                </div>
                                <p class="text-dark mb-1 fw-semibold">Total Scrap</p>
                                <h4 class="mb-0 fw-bold" id="total-scrap">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Total Section (kiri) & Total Problem (kanan) -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100" id="card-right">
                <div class="card-body p-0 d-flex align-items-center h-100">
                    <div class="row g-0 w-100">
                        <!-- Total Section -->
                        <div class="col-6 border-end">
                            <div class="p-3 text-center">
                                <div class="bg-light-primary rounded-2 p-2 d-inline-block mb-2">
                                    <i class="ti ti-section fs-6 text-primary"></i>
                                </div>
                                <p class="text-dark mb-1 fw-semibold">Total Section</p>
                                <h4 class="mb-0 fw-bold" id="total-section">0</h4>
                            </div>
                        </div>
                        <!-- Total Problem -->
                        <div class="col-6">
                            <div class="p-3 text-center">
                                <div class="bg-light-danger rounded-2 p-2 d-inline-block mb-2">
                                    <i class="ti ti-alert-triangle fs-6 text-danger"></i>
                                </div>
                                <p class="text-dark mb-1 fw-semibold">Total Problem</p>
                                <h4 class="mb-0 fw-bold" id="total-problem">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section - 2 charts per row -->
    <div class="row mt-4">
        <!-- Chart 1: Repair vs Scrap per Section -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Repair vs Scrap per Section</h5>
                </div>
                <div class="card-body">
                    <!-- Loading indicator untuk Repair vs Scrap chart -->
                    <div id="loading-repair-scrap" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data chart...</p>
                    </div>
                    <div id="repair-scrap-chart"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Horizontal Bar Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Defect per Section</h5>
                </div>
                <div class="card-body">
                    <!-- Tambahkan loading indicator untuk horizontal bar chart -->
                    <div id="loading-horizontal-bar" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data chart...</p>
                    </div>
                    <div id="horizontal-bar-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart 3: Line Chart dengan Filter -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Trend Defect</h5>
                    <!-- Filter Buttons -->
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary filter-period active" data-period="daily">
                            Daily
                        </button>
                        <button type="button" class="btn btn-outline-primary filter-period" data-period="weekly">
                            Weekly
                        </button>
                        <button type="button" class="btn btn-outline-primary filter-period" data-period="monthly">
                            Monthly
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading indicator untuk line chart -->
                    <div id="loading-line-chart" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data trend...</p>
                    </div>
                    <div id="line-chart"></div>
                </div>
            </div>
        </div>

        <!-- Chart 4: Donut Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Komposisi Defect</h5>
                </div>
                <div class="card-body">
                    <div id="donut-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Anda bisa tambahkan konten lain di sini -->

</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Definisikan konstanta API -->
<script>
    // API Constants - Biar gampang maintenance
    const API = {
        dashboard: 'DashboardController.php',
        chart: 'ChartDashboardController.php'
    };
</script>

<!-- Script untuk mengambil data statistik dan render charts -->
<script>
    // Variabel global untuk menyimpan instance chart
    let lineChartInstance = null;
    let donutChartInstance = null;
    let repairScrapChartInstance = null;
    let horizontalBarChartInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardStats();
        loadRepairScrapChart();
        loadDefectBySectionChart();
        loadLineChart('daily'); // Default daily

        // Event listeners untuk filter buttons line chart
        const filterButtons = document.querySelectorAll('.filter-period');
        filterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Get period and load chart
                const period = this.getAttribute('data-period');
                loadLineChart(period);
            });
        });

        // Panggil donut chart dengan filter
        renderDonutChart();
    });

    // Function untuk load statistik
    function loadDashboardStats() {
        // Tampilkan loading
        document.getElementById('loading-stats').style.display = 'block';

        // Panggil API dashboard
        fetch(API.dashboard + '?action=getDashboardStats')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                // Sembunyikan loading
                document.getElementById('loading-stats').style.display = 'none';

                if (result.status === 'success') {
                    // Isi data ke element
                    document.getElementById('total-defect').textContent = result.data.total_defect;
                    document.getElementById('total-customer').textContent = result.data.total_customer;
                    document.getElementById('total-repair').textContent = result.data.total_repair;
                    document.getElementById('total-scrap').textContent = result.data.total_scrap;
                    document.getElementById('total-section').textContent = result.data.total_section;
                    document.getElementById('total-problem').textContent = result.data.total_problem;

                    console.log('Data dashboard berhasil dimuat:', result.data);
                } else {
                    console.error('Gagal:', result.message);
                    // Tampilkan 0 jika gagal
                    document.getElementById('total-defect').textContent = '0';
                    document.getElementById('total-customer').textContent = '0';
                    document.getElementById('total-repair').textContent = '0';
                    document.getElementById('total-scrap').textContent = '0';
                    document.getElementById('total-section').textContent = '0';
                    document.getElementById('total-problem').textContent = '0';
                }
            })
            .catch(error => {
                // Sembunyikan loading
                document.getElementById('loading-stats').style.display = 'none';
                console.error('Error:', error);

                // Tampilkan 0 jika error
                document.getElementById('total-defect').textContent = '0';
                document.getElementById('total-customer').textContent = '0';
                document.getElementById('total-repair').textContent = '0';
                document.getElementById('total-scrap').textContent = '0';
                document.getElementById('total-section').textContent = '0';
                document.getElementById('total-problem').textContent = '0';
            });
    }

    // Function untuk menampilkan pesan no data
    function showNoDataMessage(elementId, message = 'Tidak ada data tersedia') {
        const element = document.getElementById(elementId);
        if (element) {
            // Jika element adalah chart container
            if (elementId.includes('chart')) {
                element.innerHTML = `
                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 350px;">
                        <i class="ti ti-database-off" style="font-size: 64px; color: #adb5bd;"></i>
                        <p class="mt-3 text-muted">${message}</p>
                    </div>
                `;
            }
        }
    }

    // ============================================
    // REPAIR VS SCRAP PER SECTION CHART - Grouped Bar Chart
    // ============================================
    function loadRepairScrapChart() {
        // Cek apakah element loading ada
        const loadingElement = document.getElementById('loading-repair-scrap');
        const chartElement = document.querySelector('#repair-scrap-chart');

        if (!chartElement) {
            console.error('Element repair-scrap-chart tidak ditemukan');
            return;
        }

        // Tampilkan loading
        if (loadingElement) {
            loadingElement.style.display = 'block';
        }

        // Kosongkan chart
        chartElement.innerHTML = '';

        // Hapus chart sebelumnya jika ada
        if (repairScrapChartInstance) {
            repairScrapChartInstance.destroy();
        }

        // Panggil API chart untuk Repair vs Scrap
        fetch(API.chart + '?action=getSectionDefectChart')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                // Sembunyikan loading
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }

                if (result.status === 'success' && result.data) {
                    // CEK DATA KOSONG
                    if (!result.data.categories ||
                        result.data.categories.length === 0 ||
                        !result.data.series ||
                        result.data.series.length < 2) {

                        showNoDataMessage('repair-scrap-chart', 'Tidak ada data repair vs scrap');
                        return;
                    }

                    // Render chart dengan data dari API
                    renderRepairScrapChart(result.data);
                    console.log('Data repair vs scrap chart berhasil dimuat:', result.data);
                } else {
                    console.error('Gagal load repair vs scrap chart:', result.message);
                    showNoDataMessage('repair-scrap-chart', 'Gagal memuat data');
                }
            })
            .catch(error => {
                // Sembunyikan loading
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
                console.error('Error load repair vs scrap chart:', error);
                showNoDataMessage('repair-scrap-chart', 'Terjadi kesalahan koneksi');
            });
    }

    // Function render Repair vs Scrap Chart dengan data dari API
    function renderRepairScrapChart(data) {
        const chartElement = document.querySelector("#repair-scrap-chart");

        if (!chartElement) {
            console.error('Element repair-scrap-chart tidak ditemukan');
            return;
        }

        // CEK DATA KOSONG
        if (!data || !data.categories || data.categories.length === 0) {
            showNoDataMessage('repair-scrap-chart', 'Tidak ada data untuk ditampilkan');
            return;
        }

        // Ambil data series
        const series = data.series || [];

        // Cari data Repair dan Scrap
        let repairData = null;
        let scrapData = null;

        series.forEach(s => {
            if (s.name.toLowerCase() === 'repair') {
                repairData = s;
            } else if (s.name.toLowerCase() === 'scrap') {
                scrapData = s;
            }
        });

        // Jika data tidak lengkap, tampilkan pesan error
        if (!repairData || !scrapData) {
            console.error('Data repair atau scrap tidak lengkap');
            showNoDataMessage('repair-scrap-chart', 'Data repair atau scrap tidak lengkap');
            return;
        }

        const options = {
            series: [{
                    name: 'Repair',
                    type: 'bar',
                    data: repairData.data
                },
                {
                    name: 'Scrap',
                    type: 'bar',
                    data: scrapData.data
                }
            ],
            chart: {
                height: 350,
                type: 'bar',
                stacked: false,
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#28a745', '#dc3545'], // Hijau untuk Repair, Merah untuk Scrap
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: data.categories,
                title: {
                    text: 'Production Section',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    style: {
                        fontSize: '11px'
                    },
                    rotate: -45,
                    rotateAlways: true,
                    trim: true,
                    maxHeight: 120
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Defect',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                min: 0,
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " defects";
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
                    opacity: 0.3
                }
            },
            noData: {
                text: 'Tidak ada data tersedia',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '14px',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 400
                    },
                    xaxis: {
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '10px'
                            }
                        }
                    }
                }
            }]
        };

        // Simpan instance chart ke variabel global
        window.repairScrapChartInstance = new ApexCharts(chartElement, options);
        window.repairScrapChartInstance.render();
    }

    // ============================================
    // HORIZONTAL BAR CHART - Defect Distribution by Production Section
    // ============================================
    function loadDefectBySectionChart() {
        // Cek apakah element loading ada
        const loadingElement = document.getElementById('loading-horizontal-bar');
        const chartElement = document.querySelector('#horizontal-bar-chart');

        if (!chartElement) {
            console.error('Element horizontal-bar-chart tidak ditemukan');
            return;
        }

        // Tampilkan loading
        if (loadingElement) {
            loadingElement.style.display = 'block';
        }

        // Kosongkan chart
        chartElement.innerHTML = '';

        // Hapus chart sebelumnya jika ada
        if (horizontalBarChartInstance) {
            horizontalBarChartInstance.destroy();
        }

        // Panggil API chart untuk defect by section
        fetch(API.chart + '?action=getDefectBySectionChart')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                // Sembunyikan loading
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }

                if (result.status === 'success' && result.data) {
                    // CEK DATA KOSONG
                    const categories = result.data.categories || [];
                    const seriesData = result.data.series?.[0]?.data || [];

                    if (categories.length === 0 || seriesData.length === 0) {
                        showNoDataMessage('horizontal-bar-chart', 'Tidak ada data defect per section');
                        return;
                    }

                    // Render chart dengan data dari API
                    renderHorizontalBarChart(result.data);
                    console.log('Data defect by section berhasil dimuat:', result.data);
                } else {
                    console.error('Data defect by section kosong atau gagal');
                    showNoDataMessage('horizontal-bar-chart', 'Gagal memuat data');
                }
            })
            .catch(error => {
                // Sembunyikan loading
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
                console.error('Error load defect by section chart:', error);
                showNoDataMessage('horizontal-bar-chart', 'Terjadi kesalahan koneksi');
            });
    }

    // Function render Horizontal Bar Chart dengan data dari API
    function renderHorizontalBarChart(data) {
        const chartElement = document.querySelector("#horizontal-bar-chart");

        if (!chartElement) {
            console.error('Element horizontal-bar-chart tidak ditemukan');
            return;
        }

        // Hapus chart sebelumnya jika ada
        chartElement.innerHTML = '';

        // CEK DATA KOSONG
        if (!data) {
            showNoDataMessage('horizontal-bar-chart', 'Tidak ada data untuk ditampilkan');
            return;
        }

        // Pastikan categories ada
        const categories = data.categories || [];

        // Ambil data dari series dengan format yang benar
        let seriesData = [];
        if (data.series && data.series[0] && data.series[0].data) {
            seriesData = data.series[0].data;
        } else if (data.series && data.series[0]) {
            seriesData = data.series[0];
        }

        // Jika categories atau seriesData kosong
        if (categories.length === 0 || seriesData.length === 0) {
            showNoDataMessage('horizontal-bar-chart', 'Tidak ada data untuk ditampilkan');
            return;
        }

        renderHorizontalBarChartWithOptions(chartElement, categories, seriesData);
    }

    // Helper function untuk render chart dengan opsi yang sudah jadi
    function renderHorizontalBarChartWithOptions(chartElement, categories, seriesData) {
        // Generate warna berdasarkan jumlah data
        const colors = [
            '#4F81BD', '#9BBB59', '#F79646', '#C0504E',
            '#8064A2', '#4BACC6', '#F15B5B', '#9E5F9E',
            '#5A9E6B', '#E78C35', '#5A9E9E', '#B55A5A'
        ];

        const options = {
            series: [{
                name: 'Jumlah Defect',
                data: seriesData
            }],
            chart: {
                type: 'bar',
                height: Math.max(350, categories.length * 35), // Tinggi dinamis
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    distributed: true,
                    barHeight: '70%',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: colors.slice(0, categories.length),
            xaxis: {
                categories: categories,
                title: {
                    text: 'Jumlah Defect',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Production Section',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    style: {
                        fontSize: '12px'
                    },
                    trim: true,
                    maxWidth: 200
                }
            },
            title: {
                text: 'Defect Distribution by Production Section',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 600,
                    display: 'none'
                }
            },
            legend: {
                show: false
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " defects";
                    }
                }
            },
            grid: {
                borderColor: '#e0e0e0',
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    left: 20,
                    right: 20
                }
            },
            states: {
                hover: {
                    filter: {
                        type: 'lighten',
                        value: 0.1
                    }
                }
            },
            noData: {
                text: 'Tidak ada data tersedia',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '14px',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 400
                    },
                    yaxis: {
                        labels: {
                            maxWidth: 120,
                            style: {
                                fontSize: '11px'
                            }
                        }
                    }
                }
            }]
        };

        // Render chart
        horizontalBarChartInstance = new ApexCharts(chartElement, options);
        horizontalBarChartInstance.render();
    }

    // ============================================
    // LINE CHART - Trend Defect dengan Filter Daily, Weekly, Monthly
    // ============================================
    function loadLineChart(period = 'daily') {
        const chartElement = document.querySelector("#line-chart");
        const loadingElement = document.getElementById('loading-line-chart');

        if (!chartElement) {
            console.error('Element line-chart tidak ditemukan');
            return;
        }

        // Tampilkan loading
        if (loadingElement) {
            loadingElement.style.display = 'block';
        }
        chartElement.innerHTML = '';

        // Hapus chart sebelumnya jika ada
        if (lineChartInstance) {
            lineChartInstance.destroy();
        }

        // Panggil API
        fetch(API.chart + `?action=getLineChart&period=${period}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                // Sembunyikan loading
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }

                if (result.status === 'success' && result.data) {
                    // CEK DATA KOSONG
                    const categories = result.data.categories || [];
                    const seriesData = result.data.series?.[0]?.data || [];

                    if (categories.length === 0 || seriesData.length === 0) {
                        showNoDataMessage('line-chart', 'Tidak ada data trend defect');
                        return;
                    }

                    // Render chart dengan data dari API
                    renderLineChart(result.data, period);
                    console.log(`Line chart (${period}) berhasil dimuat:`, result.data);
                } else {
                    console.error('Gagal load line chart:', result.message);
                    showNoDataMessage('line-chart', 'Gagal memuat data');
                }
            })
            .catch(error => {
                // Sembunyikan loading
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
                console.error('Error load line chart:', error);
                showNoDataMessage('line-chart', 'Terjadi kesalahan koneksi');
            });
    }

    // Function render Line Chart dengan data dari API
    function renderLineChart(data, period) {
        const chartElement = document.querySelector("#line-chart");

        // CEK DATA KOSONG
        if (!data || !data.categories || data.categories.length === 0) {
            showNoDataMessage('line-chart', 'Tidak ada data untuk ditampilkan');
            return;
        }

        // Bersihkan element
        chartElement.innerHTML = '';

        // Siapkan title berdasarkan period
        let titleText = 'Trend Defect ';
        if (period === 'daily') {
            titleText += '7 Hari Terakhir';
        } else if (period === 'weekly') {
            titleText += 'Minggu Ini';
        } else {
            titleText += 'Tahun ' + new Date().getFullYear();
        }

        const options = {
            series: data.series,
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            dataLabels: {
                enabled: false,
                offsetY: -10,
                style: {
                    fontSize: '12px',
                    colors: ['#304758']
                },
                background: {
                    enabled: true,
                    padding: 4,
                    borderRadius: 2,
                    borderWidth: 0,
                    opacity: 0.8
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#4F81BD'],
            markers: {
                size: 5,
                colors: ['#fff'],
                strokeColors: '#4F81BD',
                strokeWidth: 2,
                hover: {
                    size: 7
                }
            },
            grid: {
                borderColor: '#e0e0e0',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
                padding: {
                    top: 20,
                    bottom: 20
                }
            },
            xaxis: {
                categories: data.categories,
                title: {
                    text: getXAxisTitle(period),
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                labels: {
                    rotate: period === 'monthly' || period === 'daily' ? -45 : 0,
                    rotateAlways: period === 'monthly' || period === 'daily',
                    style: {
                        fontSize: '12px'
                    },
                    trim: true,
                    maxHeight: 120
                },
                tickAmount: data.categories ? data.categories.length : (period === 'daily' ? 7 : (period === 'weekly' ? 4 : 12))
            },
            yaxis: {
                title: {
                    text: 'Jumlah Defect',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                min: 0,
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            title: {
                text: titleText,
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 600,
                    display: 'none'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " defects";
                    }
                }
            },
            noData: {
                text: 'Tidak ada data tersedia',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '14px',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '10px'
                            }
                        }
                    }
                }
            }]
        };

        // Render chart
        lineChartInstance = new ApexCharts(chartElement, options);
        lineChartInstance.render();

        console.log(`Line chart rendered with ${period} data:`, data);
    }

    // Helper function untuk mendapatkan title X axis
    function getXAxisTitle(period) {
        switch (period) {
            case 'daily':
                return '';
            case 'weekly':
                return '';
            default:
                return '';
        }
    }

    // ============================================
    // DONUT CHART - Dengan Filter Customer/Part No (Top 5)
    // =============================================
    function renderDonutChart() {
        const chartElement = document.querySelector("#donut-chart");

        // Bersihkan element dan buat container untuk filter jika belum ada
        if (!document.querySelector('#donut-chart-container')) {
            const container = document.createElement('div');
            container.id = 'donut-chart-container';
            container.innerHTML = `
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary filter-donut active" data-donut-type="customer">
                        <i class="ti ti-users me-1"></i>Per Customer
                    </button>
                    <button type="button" class="btn btn-outline-primary filter-donut" data-donut-type="partno">
                        <i class="ti ti-package me-1"></i>Per Part No
                    </button>
                </div>
            </div>
            <div id="donut-chart-content"></div>
        `;
            chartElement.parentNode.insertBefore(container, chartElement);
            chartElement.remove();
            container.querySelector('#donut-chart-content').id = 'donut-chart';

            // Add event listeners untuk filter buttons
            const filterButtons = container.querySelectorAll('.filter-donut');
            filterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Update active state
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    // Get type and load chart
                    const type = this.getAttribute('data-donut-type');
                    loadDonutChart(type);
                });
            });
        }

        // Load default chart (customer)
        loadDonutChart('customer');
    }

    function loadDonutChart(type = 'customer') {
        const chartElement = document.querySelector("#donut-chart");
        const loadingElement = document.getElementById('loading-donut');

        if (!chartElement) {
            console.error('Element donut-chart tidak ditemukan');
            return;
        }

        // Buat loading indicator jika belum ada
        if (!loadingElement) {
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'loading-donut';
            loadingDiv.className = 'text-center py-3';
            loadingDiv.style.display = 'none';
            loadingDiv.innerHTML = `
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data donut chart...</p>
        `;
            chartElement.parentNode.insertBefore(loadingDiv, chartElement);
        }

        // Tampilkan loading
        document.getElementById('loading-donut').style.display = 'block';
        chartElement.innerHTML = '';

        // Hapus chart sebelumnya jika ada
        if (donutChartInstance) {
            donutChartInstance.destroy();
        }

        // Panggil API dengan parameter kategori (customer/partno)
        fetch(API.chart + `?action=getDoughnutChart&kategori=${type}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                // Sembunyikan loading
                document.getElementById('loading-donut').style.display = 'none';

                if (result.status === 'success' && result.data) {
                    // CEK DATA KOSONG
                    const labels = result.data.labels || [];
                    const seriesData = result.data.datasets?.[0]?.data || [];

                    if (labels.length === 0 || seriesData.length === 0) {
                        const title = type === 'customer' ? 'customer' : 'part number';
                        showNoDataMessage('donut-chart', `Tidak ada data defect per ${title}`);
                        return;
                    }

                    // Render chart dengan data dari API
                    renderDonutChartWithData(result.data, type);
                    console.log(`Donut chart (${type}) berhasil dimuat:`, result.data);
                } else {
                    console.error('Gagal load donut chart:', result.message);
                    showNoDataMessage('donut-chart', 'Gagal memuat data');
                }
            })
            .catch(error => {
                // Sembunyikan loading
                document.getElementById('loading-donut').style.display = 'none';
                console.error('Error load donut chart:', error);
                showNoDataMessage('donut-chart', 'Terjadi kesalahan koneksi');
            });
    }

    function renderDonutChartWithData(data, type) {
        const chartElement = document.querySelector("#donut-chart");

        if (!chartElement) {
            console.error('Element donut-chart tidak ditemukan');
            return;
        }

        // CEK DATA KOSONG
        if (!data || !data.labels || data.labels.length === 0) {
            showNoDataMessage('donut-chart', 'Tidak ada data untuk ditampilkan');
            return;
        }

        // Pastikan data memiliki format yang benar (dari API)
        if (!data || !data.labels || !data.datasets || !data.datasets[0]) {
            console.error('Data tidak valid:', data);
            return;
        }

        // Ambil data dari struktur API
        const labels = data.labels;
        const series = data.datasets[0].data;
        const percentages = data.datasets[0].percentages || [];

        // Siapkan title berdasarkan type
        const titleText = type === 'customer' ? 'Customer' : 'Part Number';

        const options = {
            series: series,
            chart: {
                type: 'donut',
                height: 380,
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: labels,
            // Warna untuk 5 item
            colors: ['#4F81BD', '#9BBB59', '#F79646', '#C0504E', '#8064A2'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%',
                        labels: {
                            show: true,
                            name: {
                                show: false // MATIKAN NAMA AGAR TIDAK MUNCUL
                            },
                            value: {
                                show: false // MATIKAN VALUE AGAR TIDAK MUNCUL
                            },
                            total: {
                                show: true,
                                showAlways: true, // PAKSA SELALU TAMPIL
                                label: 'Total Defects',
                                fontSize: '16px',
                                fontFamily: 'Helvetica, Arial, sans-serif',
                                fontWeight: 600,
                                color: '#373d3f',
                                formatter: function(w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total + " defects";
                                }
                            }
                        }
                    },
                    expandOnClick: true,
                    offset: 0
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '13px',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 2
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                },
                formatter: function(seriesName, opts) {
                    const percentage = percentages[opts.seriesIndex] ?
                        ` (${percentages[opts.seriesIndex]}%)` :
                        '';
                    return seriesName + percentage;
                }
            },
            stroke: {
                width: 2,
                colors: ['#fff']
            },
            fill: {
                opacity: 0.9
            },
            tooltip: {
                y: {
                    formatter: function(val, {
                        seriesIndex
                    }) {
                        const percentage = percentages[seriesIndex] ?
                            ` (${percentages[seriesIndex]}%)` :
                            '';
                        return val + " defects" + percentage;
                    }
                }
            },
            states: {
                hover: {
                    filter: {
                        type: 'lighten',
                        value: 0.1
                    }
                },
                active: {
                    filter: {
                        type: 'none'
                    },
                    allowMultipleDataPointsSelection: false
                }
            },
            noData: {
                text: 'Tidak ada data tersedia',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '14px',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 350
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '11px',
                        itemMargin: {
                            horizontal: 5,
                            vertical: 3
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        fontSize: '14px'
                                    }
                                }
                            }
                        }
                    }
                }
            }],
            title: {
                text: `Komposisi Defect ${titleText}`,
                align: 'center',
                style: {
                    fontSize: '15px',
                    fontWeight: 600,
                    display: 'none'
                }
            }
        };

        // Render chart
        donutChartInstance = new ApexCharts(chartElement, options);
        donutChartInstance.render();
    }
</script>

<!-- Tambahan CSS untuk styling card -->
<style>
    #card-left .border-end,
    #card-middle .border-end,
    #card-right .border-end {
        border-right: 1px solid rgba(0, 0, 0, 0.1) !important;
    }

    #card-left .p-3,
    #card-middle .p-3,
    #card-right .p-3 {
        padding: 1.5rem 0.5rem !important;
    }

    .bg-light-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-light-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .bg-light-success {
        background-color: rgba(25, 135, 84, 0.1);
    }

    @media (max-width: 768px) {

        #card-left .p-3,
        #card-middle .p-3,
        #card-right .p-3 {
            padding: 1rem 0.25rem !important;
        }

        .rounded-circle {
            width: 40px;
            height: 40px;
        }

        h4 {
            font-size: 1.25rem;
        }
    }
</style>