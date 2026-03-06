<?php
require_once '../../helper/auth.php';

isLogin();
?>

<?php
include '../layout/head.php';
?>
<!-- Tambahkan di bagian head atau sebelum script -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

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

    <!-- Summary Cards Row -->
    <div class="row">

        <!-- Card Total Trips Today -->
        <div class="col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body p-4">

                    <!-- Baris 1: Judul + Icon -->
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="mb-0 small fw-medium text-muted">Total Trips Today</p>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="ti ti-truck text-primary fs-5"></i>
                        </div>
                    </div>

                    <!-- Baris 2: Angka + Growth -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h2 class="mb-0 fw-semibold">156</h2>
                        <span class="text-success fw-semibold small">
                            <i class="ti ti-arrow-up me-1"></i> +12.5%
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Card Active Vehicles -->
        <div class="col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body p-4">

                    <!-- Baris 1: Judul + Icon -->
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="mb-0 small fw-medium text-muted">Active Vehicles</p>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="ti ti-truck-delivery text-success fs-5"></i>
                        </div>
                    </div>

                    <!-- Baris 2: Angka + Growth -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h2 class="mb-0 fw-semibold">42/60</h2>
                        <span class="text-success fw-semibold small">
                            <i class="ti ti-arrow-up me-1"></i> +5.2%
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Card On-Time Delivery -->
        <div class="col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body p-4">

                    <!-- Baris 1: Judul + Icon -->
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="mb-0 small fw-medium text-muted">On-Time Delivery</p>
                        <div class="p-2 rounded" style="background-color: rgba(111, 66, 193, 0.1);">
                            <i class="ti ti-clock-check fs-5" style="color: #6f42c1;"></i>
                        </div>
                    </div>

                    <!-- Baris 2: Angka + Growth -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h2 class="mb-0 fw-semibold">94.8%</h2>
                        <span class="text-success fw-semibold small">
                            <i class="ti ti-arrow-up me-1"></i> +2.3%
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Card Capacity Utilization -->
        <div class="col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body p-4">

                    <!-- Baris 1: Judul + Icon -->
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="mb-0 small fw-medium text-muted">Capacity Utilization</p>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="ti ti-chart-bar text-warning fs-5"></i>
                        </div>
                    </div>

                    <!-- Baris 2: Angka + Growth -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h2 class="mb-0 fw-semibold">78%</h2>
                        <span class="text-danger fw-semibold small">
                            <i class="ti ti-arrow-down me-1"></i> -3.1%
                        </span>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Row untuk 2 Card dengan ukuran sama -->
    <div class="row mt-4">
        <!-- Left Column: Vehicle Tracking Map -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <!-- Header Card -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-map-2 text-primary fs-5"></i>
                            <h5 class="fw-semibold mb-0">Vehicle Tracking Map</h5>
                        </div>
                    </div>

                    <!-- Container untuk Leaflet Map -->
                    <div id="vehicleTrackingMap" style="height: 380px; width: 100%; border-radius: 8px; z-index: 1;"></div>

                </div>
            </div>
        </div>

        <!-- Right Column: Trips per Day Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <!-- Header Card -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-chart-line text-primary fs-5"></i>
                            <h5 class="fw-semibold mb-0">Trips per Day (Last 7 Days)</h5>
                        </div>
                    </div>

                    <!-- Container untuk Apex Chart -->
                    <div id="tripsChart" style="height: 380px; width: 100%;"></div>

                </div>
            </div>
        </div>
    </div>

    <!-- Row untuk 2 Card baru (Cost per Route & Vehicle Utilization) -->
    <div class="row mt-4">
        <!-- Left Column: Cost per Route Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <!-- Header Card -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-currency-dollar text-primary fs-5"></i>
                            <h5 class="fw-semibold mb-0">Cost per Route (IDR x1000)</h5>
                        </div>
                    </div>

                    <!-- Container untuk Apex Chart -->
                    <div id="costPerRouteChart" style="height: 350px; width: 100%;"></div>

                </div>
            </div>
        </div>

        <!-- Right Column: Vehicle Utilization Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <!-- Header Card -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-gauge text-primary fs-5"></i>
                            <h5 class="fw-semibold mb-0">Vehicle Utilization (%)</h5>
                        </div>
                    </div>

                    <!-- Container untuk Apex Chart -->
                    <div id="vehicleUtilizationChart" style="height: 350px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row untuk Route Schedule Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header Card -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-calendar-clock text-primary fs-5"></i>
                            <h5 class="fw-semibold mb-0">Route Schedule Today</h5>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary" onclick="refreshTable()">
                                <i class="ti ti-refresh me-1"></i>Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="routeScheduleTable" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Route ID</th>
                                    <th>Route Details</th>
                                    <th>Vehicle</th>
                                    <th>Driver</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>RTE-001</td>
                                    <td>Jakarta Pusat - Bekasi (via Tol Cikunir)</td>
                                    <td>Truck A-01 (Box 10 ton)</td>
                                    <td>Budi Santoso</td>
                                    <td>08:00 - 10:30</td>
                                    <td><span class="badge bg-success">On Going</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-002</td>
                                    <td>Jakarta Selatan - Depok (via Margonda)</td>
                                    <td>Truck A-02 (Box 8 ton)</td>
                                    <td>Ahmad Hidayat</td>
                                    <td>09:00 - 11:15</td>
                                    <td><span class="badge bg-success">On Going</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-003</td>
                                    <td>Jakarta Barat - Tangerang (via Kembangan)</td>
                                    <td>Truck B-01 (Wingbox 12 ton)</td>
                                    <td>Rudi Hermawan</td>
                                    <td>07:30 - 09:45</td>
                                    <td><span class="badge bg-success">On Going</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-004</td>
                                    <td>Jakarta Utara - Cikarang (via Tol Cilincing)</td>
                                    <td>Truck C-01 (Fuso 15 ton)</td>
                                    <td>Slamet Riyadi</td>
                                    <td>10:00 - 13:30</td>
                                    <td><span class="badge bg-warning">Loading</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-005</td>
                                    <td>Jakarta Timur - Bogor (via Tol Jagorawi)</td>
                                    <td>Truck A-03 (Box 10 ton)</td>
                                    <td>Joko Susilo</td>
                                    <td>08:30 - 11:45</td>
                                    <td><span class="badge" style="background-color: gray; color: white;">Planned</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-006</td>
                                    <td>Jakarta Pusat - Cileungsi (via Tol Jorr)</td>
                                    <td>Truck B-02 (Wingbox 12 ton)</td>
                                    <td>Dedi Kurniawan</td>
                                    <td>09:15 - 12:30</td>
                                    <td><span class="badge bg-success">On Going</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-007</td>
                                    <td>Jakarta Selatan - Ciputat (via Pondok Pinang)</td>
                                    <td>Truck A-04 (Box 8 ton)</td>
                                    <td>Agus Salim</td>
                                    <td>11:00 - 13:15</td>
                                    <td><span class="badge bg-warning">Loading</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-008</td>
                                    <td>Jakarta Barat - Serpong (via Tol Ulujami)</td>
                                    <td>Truck C-02 (Fuso 15 ton)</td>
                                    <td>Hendra Wijaya</td>
                                    <td>07:00 - 09:30</td>
                                    <td><span class="badge bg-secondary">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-009</td>
                                    <td>Jakarta Utara - Karawang (via Tol Cikampek)</td>
                                    <td>Truck A-05 (Box 10 ton)</td>
                                    <td>Wawan Setiawan</td>
                                    <td>12:00 - 15:45</td>
                                    <td><span class="badge bg-danger">Delayed</span></td>
                                </tr>
                                <tr>
                                    <td>RTE-010</td>
                                    <td>Jakarta Timur - Cibubur (via Alternatif)</td>
                                    <td>Truck B-03 (Wingbox 12 ton)</td>
                                    <td>Eko Prasetyo</td>
                                    <td>13:30 - 15:45</td>
                                    <td><span class="badge bg-danger">Delayed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    // Inisialisasi DataTable
    let routeTable;
    // Inisialisasi Vehicle Tracking Map
    function initVehicleTrackingMap() {
        // Koordinat pusat (Jakarta area)
        const centerPoint = [-6.2088, 106.8456];

        // Inisialisasi map
        const vehicleMap = L.map('vehicleTrackingMap').setView(centerPoint, 11);

        // Tambah tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(vehicleMap);

        // Sample vehicle positions (mock data)
        const vehicles = [{
                pos: [-6.1754, 106.8272],
                status: 'on-route',
                name: 'Truck A-01'
            },
            {
                pos: [-6.2146, 106.8456],
                status: 'on-route',
                name: 'Truck A-02'
            },
            {
                pos: [-6.2402, 106.7889],
                status: 'loading',
                name: 'Truck B-01'
            },
            {
                pos: [-6.1890, 106.8223],
                status: 'idle',
                name: 'Truck C-01'
            },
            {
                pos: [-6.1584, 106.8836],
                status: 'on-route',
                name: 'Truck A-03'
            },
            {
                pos: [-6.1982, 106.9126],
                status: 'on-route',
                name: 'Truck A-04'
            },
            {
                pos: [-6.2287, 106.7966],
                status: 'loading',
                name: 'Truck B-02'
            }
        ];

        // Icon kustom untuk setiap status
        vehicles.forEach(v => {
            let iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-';
            let color;

            switch (v.status) {
                case 'on-route':
                    color = 'green';
                    break;
                case 'loading':
                    color = 'orange';
                    break;
                case 'idle':
                    color = 'red';
                    break;
                default:
                    color = 'blue';
            }

            const markerIcon = L.icon({
                iconUrl: iconUrl + color + '.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            L.marker(v.pos, {
                    icon: markerIcon
                })
                .bindPopup(`<b>${v.name}</b><br>Status: ${v.status}<br>Speed: ${Math.floor(Math.random() * 40 + 20)} km/h`)
                .addTo(vehicleMap);
        });

        // Tambah rute sample (rute utama)
        L.Routing.control({
            waypoints: [
                L.latLng(-6.2088, 106.8456),
                L.latLng(-6.1754, 106.8272),
                L.latLng(-6.1584, 106.8836),
                L.latLng(-6.2088, 106.8456)
            ],
            routeWhileDragging: false,
            showAlternatives: false,
            fitSelectedRoutes: false,
            show: false,
            lineOptions: {
                styles: [{
                    color: '#5D87FF',
                    opacity: 0.6,
                    weight: 4
                }]
            },
            createMarker: function() {
                return null;
            }
        }).addTo(vehicleMap);

        // Simpan ke window
        window.vehicleMap = vehicleMap;
    }

    // Inisialisasi Trips Chart dengan ApexCharts
    function initTripsChart() {
        const options = {
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            series: [{
                name: 'Trips',
                data: [165, 172, 158, 182, 178, 195, 188]
            }],
            xaxis: {
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                labels: {
                    style: {
                        colors: '#5A6A85',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#5A6A85',
                        fontSize: '12px'
                    }
                },
                min: 140,
                max: 210
            },
            grid: {
                borderColor: '#e0e6ed',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            colors: ['#5D87FF'],
            markers: {
                size: 5,
                colors: ['#5D87FF'],
                strokeColors: '#ffffff',
                strokeWidth: 2,
                hover: {
                    size: 7
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' trips'
                    }
                }
            },
            legend: {
                show: false
            }
        };

        const chart = new ApexCharts(document.querySelector("#tripsChart"), options);
        chart.render();

        // Simpan ke window
        window.tripsChart = chart;
    }

    // Inisialisasi Cost per Route Chart
    function initCostPerRouteChart() {
        const options = {
            chart: {
                type: 'bar',
                height: 350,
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
                    borderRadius: 6,
                    horizontal: false,
                    columnWidth: '55%',
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return 'Rp ' + val;
                },
                offsetY: -20,
                style: {
                    fontSize: '11px',
                    colors: ['#5A6A85']
                }
            },
            series: [{
                name: 'Cost (IDR x1000)',
                data: [425, 385, 315, 565, 685]
            }],
            colors: ['#5D87FF', '#49BEFF', '#FFAE1F', '#FA896B', '#13DEB9'],
            xaxis: {
                categories: ['Route 1', 'Route 2', 'Route 3', 'Route 4', 'Route 5'],
                labels: {
                    style: {
                        colors: '#5A6A85',
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#5A6A85',
                        fontSize: '12px'
                    },
                    formatter: function(val) {
                        return 'Rp ' + val;
                    }
                },
                title: {
                    text: 'Cost (IDR x1000)',
                    style: {
                        color: '#5A6A85',
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                min: 200,
                max: 800
            },
            grid: {
                borderColor: '#e0e6ed',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return 'Rp ' + val + ' x1000'
                    }
                }
            },
            legend: {
                show: false
            }
        };

        const chart = new ApexCharts(document.querySelector("#costPerRouteChart"), options);
        chart.render();
        window.costPerRouteChart = chart;
    }

    // Inisialisasi Vehicle Utilization Chart (Bar Chart untuk 5 Truk)
    function initVehicleUtilizationChart() {
        const options = {
            chart: {
                type: 'bar',
                height: 350,
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
                    borderRadius: 6,
                    horizontal: false,
                    columnWidth: '55%',
                    distributed: false,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val + '%';
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ['#5A6A85']
                }
            },
            series: [{
                name: 'Utilization',
                data: [92, 78, 45, 88, 65]
            }],
            colors: ['#5D87FF', '#49BEFF', '#FFAE1F', '#FA896B', '#13DEB9'],
            xaxis: {
                categories: ['Truck A-01', 'Truck B-02', 'Truck C-03', 'Truck D-04', 'Truck E-05'],
                labels: {
                    style: {
                        colors: '#5A6A85',
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#5A6A85',
                        fontSize: '12px'
                    },
                    formatter: function(val) {
                        return val + '%';
                    }
                },
                title: {
                    text: 'Utilization Percentage (%)',
                    style: {
                        color: '#5A6A85',
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                min: 0,
                max: 100
            },
            grid: {
                borderColor: '#e0e6ed',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + '% utilization'
                    }
                }
            },
            legend: {
                show: false
            },
            // annotations: {
            //     yaxis: [{
            //         y: 85,
            //         y2: 100,
            //         borderColor: '#13DEB9',
            //         fillColor: 'rgba(19, 222, 185, 0.1)',
            //         label: {
            //             text: 'Optimal',
            //             style: {
            //                 color: '#13DEB9',
            //                 fontSize: '11px',
            //                 fontWeight: 600
            //             },
            //             position: 'right'
            //         }
            //     }, {
            //         y: 70,
            //         y2: 84,
            //         borderColor: '#5D87FF',
            //         fillColor: 'rgba(93, 135, 255, 0.1)',
            //         label: {
            //             text: 'Good',
            //             style: {
            //                 color: '#5D87FF',
            //                 fontSize: '11px',
            //                 fontWeight: 600
            //             },
            //             position: 'right'
            //         }
            //     }, {
            //         y: 50,
            //         y2: 69,
            //         borderColor: '#FFAE1F',
            //         fillColor: 'rgba(255, 174, 31, 0.1)',
            //         label: {
            //             text: 'Fair',
            //             style: {
            //                 color: '#FFAE1F',
            //                 fontSize: '11px',
            //                 fontWeight: 600
            //             },
            //             position: 'right'
            //         }
            //     }, {
            //         y: 0,
            //         y2: 49,
            //         borderColor: '#FA896B',
            //         fillColor: 'rgba(250, 137, 107, 0.1)',
            //         label: {
            //             text: 'Low',
            //             style: {
            //                 color: '#FA896B',
            //                 fontSize: '11px',
            //                 fontWeight: 600
            //             },
            //             position: 'right'
            //         }
            //     }]
            // }
        };

        const chart = new ApexCharts(document.querySelector("#vehicleUtilizationChart"), options);
        chart.render();
        window.vehicleUtilizationChart = chart;
    }

    // Inisialisasi DataTable
    function initDataTable() {
        routeTable = $('#routeScheduleTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            order: [
                [4, 'asc']
            ], // Sort by Time column
            columnDefs: [{
                targets: 5, // Status column
                render: function(data, type, row) {
                    if (type === 'display') {
                        // Data sudah berisi HTML badge, jadi kembalikan apa adanya
                        return data;
                    }
                    // Untuk sorting/filtering, ambil teks saja
                    return $(data).text();
                }
            }],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    // Fungsi refresh table
    function refreshTable() {
        if (routeTable) {
            routeTable.draw();
        }
    }

    // Panggil di DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        initVehicleTrackingMap();
        initTripsChart();
        initCostPerRouteChart();
        initVehicleUtilizationChart();
        initDataTable();
    });

    // Update cleanup
    window.addEventListener('beforeunload', function() {
        if (window.vehicleMap) window.vehicleMap.remove();
        if (window.tripsChart) window.tripsChart.destroy();
        if (window.costPerRouteChart) window.costPerRouteChart.destroy();
        if (window.vehicleUtilizationChart) window.vehicleUtilizationChart.destroy();
        if (routeTable) routeTable.destroy();
    });
</script>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>