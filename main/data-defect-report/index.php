<?php
require_once '../../helper/auth.php';
isLogin();
?>

<?php include '../layout/head.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Laporan Claim Defect</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="../dashboard/index.php" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Laporan Claim Defect</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Filter Section Simple -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary">
                        <i class="ti ti-calendar me-1"></i>Tanggal Awal
                    </label>
                    <input type="date" class="form-control" id="tanggalAwal" value="">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary">
                        <i class="ti ti-calendar me-1"></i>Tanggal Akhir
                    </label>
                    <input type="date" class="form-control" id="tanggalAkhir" value="">
                </div>
                <div class="col-lg-auto col-md-6">
                    <label class="form-label fw-semibold text-primary opacity-0 d-none d-md-block">Aksi</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="resetFilter()"
                            data-bs-toggle="tooltip" title="Reset filter">
                            <i class="ti ti-refresh"></i>
                        </button>
                        <button class="btn btn-success" onclick="exportExcel()"
                            data-bs-toggle="tooltip" title="Export ke Excel">
                            <i class="ti ti-download"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center pt-3 border-top">
                        <span class="text-muted me-2"><i class="ti ti-bolt"></i> Quick Filter:</span>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setQuickFilter('today')">
                            Hari Ini
                        </button>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setQuickFilter('yesterday')">
                            Kemarin
                        </button>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setQuickFilter('week')">
                            7 Hari Terakhir
                        </button>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="setQuickFilter('month')">
                            Bulan Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Memuat data...</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4" id="summaryCards" style="display: none;">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="ti ti-file-text text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Laporan</span>
                            <h3 class="mb-0 fw-bold" id="totalLaporan">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="ti ti-users text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Customer</span>
                            <h3 class="mb-0 fw-bold" id="totalCustomer">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="ti ti-layout-grid text-info" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Section</span>
                            <h3 class="mb-0 fw-bold" id="totalSection">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="ti ti-alert-triangle text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase small fw-semibold">Total Defect</span>
                            <h3 class="mb-0 fw-bold" id="totalDefect">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Filter -->
    <div class="alert alert-info border-0 align-items-center mb-4" style="display:none;" id="filterInfo">
        <i class="ti ti-info-circle fs-5 me-2"></i>
        <span id="filterMessage"></span>
        <button type="button" class="btn-close ms-auto" id="closeFilterInfo"></button>
    </div>

    <!-- DataTables Report -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable" class="table table-hover align-middle nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Lot No</th>
                            <th>Part No</th>
                            <th>Section</th>
                            <th>Defect</th>
                            <th>Operator</th>
                            <th>Aksi Claim Defect</th>
                            <th>Nama Operator Pengambil</th>
                            <th>Tanggal Pengambilan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <!-- Akan diisi oleh DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Laporan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold" id="detailModalLabel">
                    <i class="ti ti-file-text me-2 text-primary"></i>
                    Detail Laporan Claim Defect
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="detailLoading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail data...</p>
                </div>

                <div id="detailContent" style="display: none;">
                    <!-- Info Card -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">ID Laporan</small>
                                <h6 class="fw-bold mb-0" id="detailId">-</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Tanggal Ditemukan</small>
                                <h6 class="fw-bold mb-0" id="detailTanggal">-</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Details -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-building text-primary me-2"></i>
                                    <span class="text-muted small">Section</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailSection">-</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-alert-triangle text-warning me-2"></i>
                                    <span class="text-muted small">Defect</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailDefect">-</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-barcode text-info me-2"></i>
                                    <span class="text-muted small">Lot Number</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailLotNo">-</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-package text-secondary me-2"></i>
                                    <span class="text-muted small">Part Number</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailPartNo">-</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-users text-success me-2"></i>
                                    <span class="text-muted small">Customer</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailCustomer">-</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-user text-danger me-2"></i>
                                    <span class="text-muted small">Operator</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailOperator">-</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Masalah -->
                    <div class="mt-4">
                        <div class="border rounded-3 p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-message text-info me-2"></i>
                                <span class="text-muted small">Deskripsi Masalah</span>
                            </div>
                            <p class="mb-0" id="detailDeskripsi" style="white-space: pre-wrap;">-</p>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="mt-3 text-end">
                        <small class="text-muted">
                            <i class="ti ti-clock me-1"></i>
                            Dibuat: <span id="detailCreatedAt">-</span>
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- SweetAlert2 & DataTables -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    let reportTable;
    let baseUrl = 'DataDefectReportController.php';
    let searchTimeout;

    $(document).ready(function() {
        $('#closeFilterInfo').on('click', function() {
            $('#filterInfo').fadeOut();
        });
        initializeTable();
        initializeEventListeners();
        loadReports(); // Auto load on page load
    });

    // Fungsi untuk menampilkan detail laporan
    function showDetail(id) {
        // Tampilkan loading, sembunyikan content
        $('#detailLoading').show();
        $('#detailContent').hide();

        // Tampilkan modal
        $('#detailModal').modal('show');

        $.ajax({
            url: baseUrl + '?action=show&id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#detailLoading').hide();

                if (response.status === 'success' && response.data) {
                    let data = response.data;

                    // Isi data ke modal
                    $('#detailId').text(data.id || '-');
                    $('#detailTanggal').text(formatDate(data.tanggal_ditemukan) || '-');
                    $('#detailSection').text(escapeHtml(data.nama_section) || '-');
                    $('#detailDefect').text(escapeHtml(data.nama_defect) || '-');
                    $('#detailLotNo').text(escapeHtml(data.lotno) || '-');
                    $('#detailPartNo').text(escapeHtml(data.partno) || '-');
                    $('#detailCustomer').text(escapeHtml(data.nama_customer) || '-');
                    $('#detailOperator').text(escapeHtml(data.nama_operator) || '-');
                    $('#detailDeskripsi').text(escapeHtml(data.deskripsi_masalah) || '-');
                    $('#detailCreatedAt').text(formatDateTime(data.created_at) || '-');

                    $('#detailContent').fadeIn();
                } else {
                    Swal.fire('Error', response.message || 'Gagal memuat detail data', 'error');
                    $('#detailModal').modal('hide');
                }
            },
            error: function(xhr) {
                $('#detailLoading').hide();
                Swal.fire('Error', 'Gagal memuat detail data', 'error');
                $('#detailModal').modal('hide');
            }
        });
    }

    function initializeTable() {
        reportTable = $('#reportTable').DataTable({
            columns: [{
                    data: null,
                    width: '50px',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return '';
                    }
                },
                {
                    data: 'tanggal_ditemukan',
                    width: '100px',
                    className: 'text-nowrap',
                    render: function(data) {
                        if (!data) return '-';
                        return formatDate(data);
                    }
                },
                {
                    data: 'nama_customer',
                    render: function(data) {
                        return escapeHtml(data);
                    }
                },
                {
                    data: 'lotno',
                    render: function(data) {
                        return escapeHtml(data) || '-';
                    }
                },
                {
                    data: 'partno',
                    render: function(data) {
                        return escapeHtml(data) || '-';
                    }
                },
                {
                    data: 'nama_section',
                    render: function(data) {
                        return `<span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">${escapeHtml(data)}</span>`;
                    }
                },
                {
                    data: 'nama_defect',
                    render: function(data) {
                        return escapeHtml(data);
                    }
                },
                {
                    data: 'nama_operator',
                    render: function(data) {
                        return escapeHtml(data) || '-';
                    }
                },
                {
                    data: 'aksi_claim_defect',
                    render: function(data) {

                        if (!data) return '-';

                        if (data === 'Repair') {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Repair</span>`;
                        }

                        if (data === 'Scrap') {
                            return `<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Scrap</span>`;
                        }

                        return escapeHtml(data);
                    }
                },
                {
                    data: 'nama_operator_pengambil',
                    width: '150px',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Jika row dalam mode edit, tampilkan input
                            if (row._editing) {
                                return `<input type="text" class="form-control form-control-sm inline-edit" 
                               value="${escapeHtml(data || '')}" 
                               data-field="nama_operator_pengambil"
                               data-id="${row.id}"
                               placeholder="Nama operator">`;
                            }
                            // Tampilan normal
                            return data ? escapeHtml(data) : '-';
                        }
                        return data;
                    }
                },
                {
                    data: 'tanggal_pengambilan',
                    width: '120px',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Jika row dalam mode edit, tampilkan input date
                            if (row._editing) {
                                let dateValue = data ? data.split(' ')[0] : '';
                                return `<input type="date" class="form-control form-control-sm inline-edit" 
                               value="${dateValue}" 
                               data-field="tanggal_pengambilan"
                               data-id="${row.id}">`;
                            }
                            // Tampilan normal
                            return data ? formatDate(data) : '-';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    width: '150px',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        // Cek apakah row ini sedang dalam mode edit
                        if (row._editing) {
                            return `
                <button class="btn btn-sm btn-success me-1" onclick="saveInlineEdit(${row.id})">
                    <i class="ti ti-check"></i> Simpan
                </button>
                <button class="btn btn-sm btn-secondary" onclick="cancelInlineEdit(${row.id})">
                    <i class="ti ti-x"></i> Batal
                </button>
            `;
                        } else {
                            return `
                <button class="btn btn-sm btn-primary me-1" onclick="editInline(${row.id})" title="Edit Data">
                    <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-info" onclick="showDetail(${row.id})" title="Lihat Detail">
                    <i class="ti ti-eye"></i>
                </button>
            `;
                        }
                    }
                }
            ],
            ordering: true,
            order: [
                [1, 'desc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            scrollX: true,
            responsive: false,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            rowCallback: function(row, data, displayIndex) {
                let pageStart = this.api().page.info().start;
                $('td:first-child', row).html(pageStart + displayIndex + 1);
            },
            drawCallback: function() {
                let data = reportTable.rows().data().toArray();
                if (data.length > 0) {
                    updateSummary(data);
                }
            }
        });
    }

    function initializeEventListeners() {
        // Auto load on input change with debounce
        $('#tanggalAwal, #tanggalAkhir').on('change input', function() {
            // Validate dates
            let tanggalAwal = $('#tanggalAwal').val();
            let tanggalAkhir = $('#tanggalAkhir').val();

            if (tanggalAwal && tanggalAkhir && tanggalAwal > tanggalAkhir) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            debounceLoadReports();
        });
    }

    function debounceLoadReports() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadReports();
        }, 500); // Delay 500ms
    }

    function loadReports() {
        let tanggalAwal = $('#tanggalAwal').val();
        let tanggalAkhir = $('#tanggalAkhir').val();

        $('#loadingSpinner').fadeIn();
        $('#summaryCards').hide();
        $('#filterInfo').hide();

        let url = baseUrl + '?action=getReports';

        if (tanggalAwal || tanggalAkhir) {
            if (tanggalAwal) url += '&tanggal_awal=' + tanggalAwal;
            if (tanggalAkhir) url += '&tanggal_akhir=' + tanggalAkhir;
        }

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loadingSpinner').fadeOut();

                if (response.status === 'success') {
                    // HAPUS semua data lama
                    reportTable.clear();

                    // Tambah data baru
                    reportTable.rows.add(response.data);

                    // DRAW ulang dengan mempertahankan order
                    reportTable.draw();

                    if (response.data.length > 0) {
                        updateSummaryCards(response);

                        let hasDateFilter = tanggalAwal || tanggalAkhir;
                        let hasMessage = response.message && response.message.trim() !== '';

                        if (hasDateFilter && hasMessage) {
                            showFilterInfo(response);
                        }
                    } else {
                        showEmptyResult(response);
                        $('#summaryCards').hide();
                        $('#filterInfo').hide();
                    }
                } else {
                    Swal.fire('Error', response.message, 'error');
                    reportTable.clear().draw();
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').fadeOut();
                Swal.fire('Error', 'Gagal memuat data', 'error');
                reportTable.clear().draw();
            }
        });
    }

    function setQuickFilter(type) {
        let today = moment();
        let tanggalAwal, tanggalAkhir;

        switch (type) {
            case 'today':
                tanggalAwal = today.format('YYYY-MM-DD');
                tanggalAkhir = today.format('YYYY-MM-DD');
                break;
            case 'yesterday':
                tanggalAwal = today.subtract(1, 'days').format('YYYY-MM-DD');
                tanggalAkhir = tanggalAwal;
                break;
            case 'week':
                tanggalAwal = today.subtract(7, 'days').format('YYYY-MM-DD');
                tanggalAkhir = moment().format('YYYY-MM-DD');
                break;
            case 'month':
                tanggalAwal = today.startOf('month').format('YYYY-MM-DD');
                tanggalAkhir = today.endOf('month').format('YYYY-MM-DD');
                break;
        }

        $('#tanggalAwal').val(tanggalAwal);
        $('#tanggalAkhir').val(tanggalAkhir);
        loadReports();
    }

    function resetFilter() {
        $('#tanggalAwal').val('');
        $('#tanggalAkhir').val('');
        loadReports();

        Swal.fire({
            icon: 'success',
            title: 'Reset Filter',
            text: 'Filter telah direset',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function exportExcel() {
        let tanggalAwal = $('#tanggalAwal').val();
        let tanggalAkhir = $('#tanggalAkhir').val();

        console.log('tanggalAwal', tanggalAwal);
        console.log('tanggalAkhir', tanggalAkhir);
        // breakpoint;

        if (!tanggalAwal && !tanggalAkhir) {
            Swal.fire({
                icon: 'warning',
                title: 'Filter diperlukan',
                text: 'Silakan pilih minimal 1 tanggal untuk export data'
            });
            return;
        }

        // Cek apakah ada data di tabel
        let dataCount = reportTable.rows().count();

        if (dataCount === 0) {
            let pesanTanggal = '';

            if (tanggalAwal && tanggalAkhir) {
                // Format tanggal ke format dd/mm/yyyy
                let tglAwal = formatDate(tanggalAwal);
                let tglAkhir = formatDate(tanggalAkhir);
                pesanTanggal = `Tidak ada laporan untuk rentang ${tglAwal} hingga ${tglAkhir}`;
            } else if (tanggalAwal && !tanggalAkhir) {
                let tglAwal = formatDate(tanggalAwal);
                pesanTanggal = `Tidak ada laporan untuk tanggal ${tglAwal}`;
            } else if (!tanggalAwal && tanggalAkhir) {
                let tglAkhir = formatDate(tanggalAkhir);
                pesanTanggal = `Tidak ada laporan untuk tanggal ${tglAkhir}`;
            }

            Swal.fire({
                icon: 'error',
                title: 'Tidak Dapat Export',
                html: `
            <div class="mb-3">
                <i class="ti ti-database-off" style="font-size: 3rem; color: #dc3545;"></i>
            </div>
            <p class="fw-semibold">Tidak ada laporan untuk diexport!</p>
            <p class="text-muted small">${pesanTanggal}</p>
        `,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Mengerti'
            });
            return;
        }

        Swal.fire({
            title: 'Menyiapkan Export',
            html: `
            <div class="mb-3">
                <i class="ti ti-file-spreadsheet" style="font-size: 3rem; color: #198754;"></i>
            </div>
            <p class="mb-1">Mohon tunggu sebentar...</p>
            <p class="text-muted small">Mengexport ${dataCount} data laporan</p>
        `,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            showConfirmButton: false
        });

        // Buat URL dengan parameter secara manual
        let params = [];

        // Selalu tambahkan parameter jika ada nilainya
        if (tanggalAwal) {
            params.push('tanggal_awal=' + encodeURIComponent(tanggalAwal));
        }
        if (tanggalAkhir) {
            params.push('tanggal_akhir=' + encodeURIComponent(tanggalAkhir));
        }

        // Gabungkan parameter dengan &
        let url = 'ExportDefectReportController.php';
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        console.log('URL Export:', url); // Untuk debugging

        // Gunakan window.location.href
        window.location.href = url;

        setTimeout(() => {
            Swal.close();
            // Tampilkan notifikasi sukses
            Swal.fire({
                icon: 'success',
                title: 'Export Berhasil',
                html: `
                <p class="mb-0">${dataCount} data laporan berhasil diexport</p>
                <p class="text-muted small mt-2">File akan terdownload otomatis</p>
            `,
                timer: 2000,
                showConfirmButton: false
            });
        }, 1500);
    }

    function updateSummaryCards(response) {
        $('#summaryCards').fadeIn();

        let data = response.data;
        let totalLaporan = response.filter.total_data || data.length;

        $('#totalLaporan').text(totalLaporan.toLocaleString());

        let uniqueCustomers = [...new Set(data.map(item => item.nama_customer))];
        let uniqueSections = [...new Set(data.map(item => item.nama_section))];
        let uniqueDefects = [...new Set(data.map(item => item.nama_defect))];

        $('#totalCustomer').text(uniqueCustomers.length.toLocaleString());
        $('#totalSection').text(uniqueSections.length.toLocaleString());
        $('#totalDefect').text(uniqueDefects.length.toLocaleString());
    }

    function showFilterInfo(response) {
        if (response.message && response.message.trim() !== '') {
            $('#filterMessage').text(response.message);

            $('#filterInfo')
                .removeClass('d-none')
                .hide()
                .fadeIn();
        }
    }

    function showEmptyResult(response) {
        let message = 'Tidak ada data untuk ditampilkan';

        if (response.filter && response.filter.tanggal_awal && response.filter.tanggal_akhir) {
            message = `Tidak ada laporan untuk rentang ${formatDate(response.filter.tanggal_awal)} - ${formatDate(response.filter.tanggal_akhir)}`;
        } else if (response.filter && response.filter.tanggal_awal) {
            message = `Tidak ada laporan untuk tanggal ${formatDate(response.filter.tanggal_awal)}`;
        }

        $('#reportTable tbody').html(`
            <tr>
                <td colspan="12" class="text-center py-5">
                    <i class="ti ti-database-off" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="mt-3 text-muted">${message}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="setQuickFilter('week')">
                        <i class="ti ti-calendar"></i> Lihat 7 Hari Terakhir
                    </button>
                </td>
            </tr>
        `);
    }

    function updateSummary(data) {
        if (!data || data.length === 0) return;

        let uniqueCustomers = [...new Set(data.map(item => item.nama_customer))];
        let uniqueSections = [...new Set(data.map(item => item.nama_section))];
        let uniqueDefects = [...new Set(data.map(item => item.nama_defect))];

        $('#totalCustomer').text(uniqueCustomers.length.toLocaleString());
        $('#totalSection').text(uniqueSections.length.toLocaleString());
        $('#totalDefect').text(uniqueDefects.length.toLocaleString());
    }

    // Utility Functions
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        let parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return dateStr;
    }

    function formatDateTime(dateTimeStr) {
        if (!dateTimeStr) return '-';
        let parts = dateTimeStr.split(' ');
        if (parts.length >= 2) {
            return formatDate(parts[0]) + ' ' + parts[1].substring(0, 5);
        }
        return dateTimeStr;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Tooltip initialization
    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Tambahkan fungsi untuk handle double click
    $(document).on('dblclick', '#reportTable tbody td', function(e) {
        let table = $('#reportTable').DataTable();
        let columnIndex = $(this).index();
        let rowData = table.row($(this).closest('tr')).data();

        if (!rowData) return;

        // Cek apakah kolom yang di-double-click adalah nama_operator_pengambil (index 9) atau tanggal_pengambilan (index 10)
        if (columnIndex === 9 || columnIndex === 10) {
            // Nonaktifkan edit mode untuk semua row
            table.rows().every(function() {
                let rowData = this.data();
                if (rowData._editing) {
                    rowData._editing = false;
                    this.data(rowData);
                }
            });

            // Aktifkan edit mode untuk row yang dipilih
            rowData._editing = true;
            table.row($(this).closest('tr')).data(rowData).draw(false);

            // Auto focus ke input yang sesuai
            setTimeout(function() {
                $(`input[data-id="${rowData.id}"]`).first().focus();
            }, 100);
        }
    });

    // Update fungsi editInline untuk konsistensi
    function editInline(id) {
        let table = $('#reportTable').DataTable();

        // Nonaktifkan edit mode untuk semua row
        table.rows().every(function() {
            let rowData = this.data();
            if (rowData._editing) {
                rowData._editing = false;
                this.data(rowData);
            }
        });

        // Aktifkan edit mode untuk row yang dipilih
        table.rows().every(function() {
            let rowData = this.data();
            if (rowData.id == id) {
                rowData._editing = true;
                this.data(rowData);
            }
        });

        // Redraw dengan mempertahankan sorting
        table.draw(false);
    }

    // Update fungsi cancelInlineEdit untuk memastikan konsistensi
    function cancelInlineEdit(id) {
        let table = $('#reportTable').DataTable();

        table.rows().every(function() {
            let rowData = this.data();
            if (rowData.id == id) {
                rowData._editing = false;
                this.data(rowData);
            }
        });

        table.draw(false);

        Swal.fire({
            icon: 'info',
            title: 'Dibatalkan',
            text: 'Perubahan dibatalkan',
            timer: 1000,
            showConfirmButton: false
        });
    }

    // Modifikasi fungsi saveInlineEdit untuk handle kedua field
    function saveInlineEdit(id) {
        let operatorValue = $(`input[data-field="nama_operator_pengambil"][data-id="${id}"]`).val();
        let tanggalValue = $(`input[data-field="tanggal_pengambilan"][data-id="${id}"]`).val();

        if (!operatorValue || operatorValue.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Nama operator pengambil tidak boleh kosong'
            });
            return;
        }

        if (!tanggalValue || tanggalValue.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tanggal pengambilan tidak boleh kosong'
            });
            return;
        }

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Harap tunggu',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: 'UpdateDefectReportController.php?action=update',
            type: 'POST',
            data: {
                id: id,
                nama_operator_pengambil: operatorValue.trim(),
                tanggal_pengambilan: tanggalValue || ''
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let table = $('#reportTable').DataTable();

                    // Update data di row yang benar
                    table.rows().every(function() {
                        let rowData = this.data();
                        if (rowData.id == id) {
                            rowData.nama_operator_pengambil = operatorValue.trim();
                            rowData.tanggal_pengambilan = tanggalValue || null;
                            rowData._editing = false;
                            this.data(rowData);
                        }
                    });

                    // Redraw dengan mempertahankan sorting
                    table.draw(false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil diupdate',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(response.message || 'Gagal mengupdate data');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);

                let table = $('#reportTable').DataTable();
                table.rows().every(function() {
                    let rowData = this.data();
                    if (rowData.id == id) {
                        rowData._editing = false;
                        this.data(rowData);
                    }
                });

                table.draw(false);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengupdate data'
                });
            }
        });
    }

    // Handle Enter key untuk save
    $(document).on('keypress', '.inline-edit', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            let id = $(this).data('id');
            saveInlineEdit(id);
        }
    });

    // Handle Escape key untuk cancel
    $(document).on('keydown', '.inline-edit', function(e) {
        if (e.which === 27) { // Escape key
            e.preventDefault();
            let id = $(this).data('id');
            cancelInlineEdit(id);
        }
    });
</script>

<style>
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    #reportTable thead th {
        background: #f8fafc;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 2px solid #e9ecef;
        padding: 14px 12px;
    }

    #reportTable tbody td {
        padding: 12px;
        font-size: 14px;
        vertical-align: middle;
    }

    #reportTable tbody tr {
        transition: all .2s ease;
    }

    #reportTable tbody tr:hover {
        background-color: #f1f5ff;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
    }

    /* Simple Clean Design */
    .card {
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 0.6rem 1rem;
        height: auto;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }

    .btn-outline-primary {
        border-color: #e0e0e0;
        color: #495057;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    /* Summary Cards */
    .rounded-circle {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Badge */
    .badge {
        font-weight: 500;
    }

    /* Table */
    #reportTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 2px solid #dee2e6;
    }

    #reportTable tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    /* Alert */
    .alert-info {
        background-color: #e6f5fe;
        border: none;
        color: #055160;
        border-radius: 10px;
    }

    /* Loading Spinner */
    #loadingSpinner {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        min-height: 200px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .btn {
            width: 100%;
            margin: 5px 0;
        }

        .rounded-circle {
            width: 40px;
            height: 40px;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        .rounded-circle i {
            font-size: 1.2rem !important;
        }

        h3 {
            font-size: 1.5rem;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #summaryCards {
        animation: fadeIn 0.3s ease;
    }

    /* Empty State */
    .dataTables_empty td {
        padding: 3rem !important;
    }

    /* Border Top */
    .border-top {
        border-top: 1px solid #e0e0e0 !important;
    }

    /* Tambahkan di bagian style */
    .inline-edit {
        border: 2px solid #0d6efd !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    tr.inline-editing-row {
        background-color: #fff3cd !important;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .inline-edit:focus {
        border-color: #0a58ca !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
    }

    /* Indikasi bahwa kolom bisa di-double-click */
    #reportTable tbody td:nth-child(10),
    #reportTable tbody td:nth-child(11) {
        cursor: pointer;
        position: relative;
    }

    #reportTable tbody td:nth-child(10):hover::after,
    #reportTable tbody td:nth-child(11):hover::after {
        content: "Double-click untuk edit";
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        margin-bottom: 5px;
    }

    /* Styling untuk row yang sedang diedit */
    tr.inline-editing-row {
        background-color: #fff3cd !important;
    }

    .inline-edit {
        border: 2px solid #0d6efd !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        transition: all 0.2s ease;
    }

    .inline-edit:focus {
        border-color: #0a58ca !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
        outline: none;
    }

    /* Tooltip untuk double-click */
    [data-dblclick="true"] {
        cursor: pointer;
    }
</style>