<?php
require_once '../../helper/auth.php';
isLogin();
?>

<?php include '../layout/head.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="../../assets/local/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../../assets/local/responsive.bootstrap5.min.css">

<!-- Select2 CSS -->
<link href="../../assets/local/select2.min.css" rel="stylesheet" />
<link href="../../assets/local/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

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

    <!-- Filter Section dengan Tabs/Panel Terpisah -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <!-- Header with Import Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold text-primary mb-0">
                    <i class="ti ti-filter me-2"></i>Filter Laporan
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn" id="btnImportData" style="background-color: #FF5722; color: white;">
                        <i class="ti ti-upload me-2"></i>Import Data
                    </button>

                    <button class="btn" id="btnExportExcel" style="background-color: #4CAF50; color: white;">
                        <i class="ti ti-download me-2"></i>Export Excel
                    </button>
                </div>
            </div>
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-fill mb-3" id="filterTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="filter-tanggal-tab" data-bs-toggle="tab"
                        data-bs-target="#filter-tanggal" type="button" role="tab"
                        aria-controls="filter-tanggal" aria-selected="true">
                        <i class="ti ti-calendar me-2"></i>Filter by Tanggal
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="filter-lot-tab" data-bs-toggle="tab"
                        data-bs-target="#filter-lot" type="button" role="tab"
                        aria-controls="filter-lot" aria-selected="false">
                        <i class="ti ti-barcode me-2"></i>Filter by Lot No
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="filter-customer-tab" data-bs-toggle="tab"
                        data-bs-target="#filter-customer" type="button" role="tab"
                        aria-controls="filter-customer" aria-selected="false">
                        <i class="ti ti-users me-2"></i>Filter by Customer
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="filterTabsContent">
                <!-- Tab 1: Filter Tanggal -->
                <div class="tab-pane fade show active" id="filter-tanggal" role="tabpanel" tabindex="0">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal Awal
                            </label>
                            <input type="date" class="form-control" id="tanggalAwal" value="">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal Akhir
                            </label>
                            <input type="date" class="form-control" id="tanggalAkhir" value="">
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="d-flex flex-column h-100 justify-content-end">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary flex-grow-1" onclick="applyDateFilter()">
                                        <i class="ti ti-filter me-2"></i>Terapkan
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="resetAllFilters()"
                                        data-bs-toggle="tooltip" title="Reset semua filter">
                                        <i class="ti ti-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Filter untuk Tanggal -->
                    <div class="d-flex flex-wrap gap-2 align-items-center pt-3 mt-3 border-top">
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

                <!-- Tab 2: Filter Lot No (INPUT MANUAL) -->
                <div class="tab-pane fade" id="filter-lot" role="tabpanel" tabindex="0">
                    <div class="row g-3">
                        <div class="col-lg-8 col-md-8">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-barcode me-1"></i>Lot Number
                            </label>
                            <input type="text" class="form-control" id="filterLotNo"
                                placeholder="Ketik nomor lot (pisahkan dengan koma untuk multiple)">
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-info-circle me-1"></i>Contoh: LOT-001, LOT-002, LOT-003
                            </small>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <!-- Label tersembunyi untuk menjaga tinggi -->
                            <label class="form-label fw-semibold text-primary invisible">
                                <i class="ti ti-filter me-1"></i>Aksi
                            </label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1" onclick="applyLotFilter()">
                                    <i class="ti ti-filter me-2"></i>Terapkan
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearLotFilter()">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <!-- Helper text tersembunyi untuk menjaga tinggi -->
                            <small class="text-muted mt-1 d-block invisible">Placeholder</small>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Filter Customer (DROPDOWN MULTIPLE dengan Select2) -->
                <div class="tab-pane fade" id="filter-customer" role="tabpanel" tabindex="0">
                    <div class="row g-3">
                        <div class="col-lg-8 col-md-8">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-users me-1"></i>Customer
                            </label>
                            <select class="form-select" id="filterCustomer" multiple="multiple" style="width: 100%;">
                                <option value="">Memuat data...</option>
                            </select>
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-info-circle me-1"></i>Pilih customer (bisa search dan multiple)
                            </small>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <!-- Label tersembunyi untuk menjaga tinggi -->
                            <label class="form-label fw-semibold text-primary invisible">
                                <i class="ti ti-filter me-1"></i>Aksi
                            </label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1" onclick="applyCustomerFilter()">
                                    <i class="ti ti-filter me-2"></i>Terapkan
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearCustomerFilter()">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <!-- Helper text tersembunyi untuk menjaga tinggi -->
                            <small class="text-muted mt-1 d-block invisible">Placeholder</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters Summary -->
            <div class="mt-3 pt-3 border-top" id="activeFiltersContainer" style="display: none;">
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2"><i class="ti ti-filter"></i> Filter aktif:</span>
                    <div class="d-flex flex-wrap gap-2" id="activeFilters"></div>
                    <button class="btn btn-link btn-sm text-danger ms-auto" onclick="resetAllFilters()">
                        <i class="ti ti-trash me-1"></i>Hapus semua
                    </button>
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
            <div>
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
                            <th>group</th>
                            <th>QTY</th>
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
                    <!-- Info Card (Tidak Bisa Diedit) -->
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

                    <!-- Grid Details - Baris 1 (Tidak Bisa Diedit) -->
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
                    </div>

                    <!-- Grid Details - Baris 2 (Tidak Bisa Diedit) -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-barcode text-info me-2"></i>
                                    <span class="text-muted small">Lot Number</span>
                                </div>
                                <h6 class="fw-semibold mb-0 detail-editable" data-field="lotno" data-id="" id="detailLotNo">-</h6>
                                <small class="text-muted edit-hint">Double-click untuk edit</small>
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
                    </div>

                    <!-- Grid Details - Baris 3 (Tidak Bisa Diedit) -->
                    <div class="row g-3 mt-2">
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

                    <!-- Grid Details - Baris 4 (DATA TAMBAHAN - BISA DIEDIT) -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-users-group text-info me-2"></i>
                                    <span class="text-muted small">Group</span>
                                </div>
                                <h6 class="fw-semibold mb-0 detail-editable" data-field="nama_group" data-id="" id="detailGroup">-</h6>
                                <small class="text-muted edit-hint">Double-click untuk edit</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-numbers text-warning me-2"></i>
                                    <span class="text-muted small">Quantity (QTY)</span>
                                </div>
                                <h6 class="fw-semibold mb-0 detail-editable" data-field="qty" data-id="" id="detailQty">-</h6>
                                <small class="text-muted edit-hint">Double-click untuk edit</small>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Details - Baris 5 (DATA TAMBAHAN - BISA DIEDIT) -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-clipboard-check text-primary me-2"></i>
                                    <span class="text-muted small">Aksi Claim Defect</span>
                                </div>
                                <h6 class="fw-semibold mb-0 detail-editable" data-field="aksi_claim_defect" data-id="" id="detailAksiClaim">-</h6>
                                <small class="text-muted edit-hint">Double-click untuk edit</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-user-check text-success me-2"></i>
                                    <span class="text-muted small">Operator Pengambil</span>
                                </div>
                                <h6 class="fw-semibold mb-0 detail-editable" data-field="nama_operator_pengambil" data-id="" id="detailOperatorPengambil">-</h6>
                                <small class="text-muted edit-hint">Double-click untuk edit</small>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Details - Baris 6 (DATA TAMBAHAN - BISA DIEDIT) -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-calendar-event text-secondary me-2"></i>
                                    <span class="text-muted small">Tanggal Pengambilan</span>
                                </div>
                                <h6 class="fw-semibold mb-0 detail-editable" data-field="tanggal_pengambilan" data-id="" id="detailTanggalPengambilan">-</h6>
                                <small class="text-muted edit-hint">Double-click untuk edit</small>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Masalah (Tidak Bisa Diedit) -->
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

<!-- Modal Import Data -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-semibold" id="importModalLabel">
                    <i class="ti ti-upload me-2"></i>Import Data Defect Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Section -->
                <div class="alert alert-info border-0 mb-4">
                    <i class="ti ti-info-circle fs-5 me-2"></i>
                    <strong>Petunjuk:</strong>
                    <ul class="mb-0 mt-2">
                        <li>File harus berformat <strong>.xlsx, .xls, atau .csv</strong></li>
                        <li>Maksimal ukuran file: <strong>10 MB</strong></li>
                        <li>Pastikan struktur kolom sesuai dengan template yang telah disediakan</li>
                    </ul>
                </div>

                <!-- Download Template Button -->
                <div class="mb-4">
                    <button class="btn btn-outline-secondary w-100" id="btnDownloadTemplate">
                        <i class="ti ti-download me-2"></i>Download Template Excel
                    </button>
                    <small class="text-muted mt-1 d-block text-center">
                        <i class="ti ti-info-circle me-1"></i>
                        Tersedia 2 template: dengan header dan tanpa header
                    </small>
                </div>

                <!-- File Upload Form -->
                <form id="importForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="ti ti-file-excel me-1 text-success"></i>Pilih File Excel
                        </label>
                        <input type="file" class="form-control" id="importFile"
                            accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted mt-1 d-block">
                            <i class="ti ti-alert-circle me-1"></i>
                            Pilih file yang akan diimport
                        </small>
                    </div>

                    <!-- Import Options -->
                    <div class="mb-3">
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="skipFirstRow" checked>
                                    <label class="form-check-label fw-semibold" for="skipFirstRow">
                                        Lewati baris pertama (header)
                                    </label>
                                </div>
                                <div class="ms-4 mt-2">
                                    <small class="text-muted d-block mb-1">
                                        <i class="ti ti-info-circle me-1"></i>
                                        <strong>Centang jika:</strong> File Anda memiliki baris header (judul kolom) di baris pertama
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="ti ti-info-circle me-1"></i>
                                        <strong>Tidak centang jika:</strong> File Anda langsung berisi data tanpa header (contoh: template tanpa header)
                                    </small>
                                    <div class="mt-2 p-2 bg-white rounded border">
                                        <small class="text-primary d-block">
                                            <i class="ti ti-file-excel me-1"></i>
                                            <strong>Contoh penggunaan:</strong>
                                        </small>
                                        <small class="text-secondary d-block mt-1">
                                            ✅ Centang → Menggunakan <strong>Template Dengan Header</strong><br>
                                            ❌ Tidak centang → Menggunakan <strong>Template Tanpa Header</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar (Hidden by default) -->
                    <div id="importProgress" style="display: none;">
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar"
                                style="width: 0%">
                                0%
                            </div>
                        </div>
                        <p class="text-muted small text-center" id="progressStatus">
                            <i class="ti ti-loader me-1"></i>Memproses data...
                        </p>
                    </div>

                    <!-- Preview Section (Hidden by default) -->
                    <div id="previewSection" style="display: none;">
                        <hr class="my-3">
                        <h6 class="fw-semibold mb-2">
                            <i class="ti ti-eye me-1"></i>Preview Data
                        </h6>
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm table-bordered" id="previewTable">
                                <thead id="previewHeader">
                                    <tr>
                                        <th>#</th>
                                        <th>Preview</th>
                                    </tr>
                                </thead>
                                <tbody id="previewBody">
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            Belum ada data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-success" id="btnImportSubmit" disabled>
                    <i class="ti ti-upload me-1"></i>Import Data
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- SweetAlert2 & DataTables -->
<script src="../../assets/local/sweetalert2@11.js"></script>
<script src="../../assets/local/dataTables.bootstrap5.min.js"></script>
<script src="../../assets/local/dataTables.responsive.min.js"></script>
<script src="../../assets/local/responsive.bootstrap5.min.js"></script>

<!-- Moment.js -->
<script src="../../assets/local/moment.min.js"></script>
<!-- Select2 JS -->
<script src="../../assets/local/select2.min.js"></script>

<script>
    let reportTable;
    let isSaving = false;
    let baseUrl = 'DataDefectReportController.php';

    // ====================================
    // KOLOM YANG BISA DI-EDIT PER CELL
    // ====================================
    const EDITABLE_COLS = {
        3: 'lotno',
        8: 'nama_group',
        9: 'qty',
        10: 'aksi_claim_defect',
        11: 'nama_operator_pengambil',
        12: 'tanggal_pengambilan'
    };

    // ====================================
    // VARIABEL FILTER PER TAB
    // ====================================
    let filters = {
        tanggal: {
            aktif: false,
            tanggalAwal: null,
            tanggalAkhir: null
        },
        lot: {
            aktif: false,
            lotNos: []
        },
        customer: {
            aktif: false,
            customers: []
        }
    };

    // Tab aktif saat ini
    let activeTab = 'tanggal';

    $(document).ready(function() {
        initializeTable();
        loadCustomerOptions();
        showEmptyInitialState();

        // Event listeners
        $('#closeFilterInfo').on('click', function() {
            $('#filterInfo').fadeOut();
        });

        // Tooltip initialization
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Event listener untuk pindah tab - RESET SEMUA
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            let targetId = $(e.target).attr('id');

            // RESET SEMUA FILTER DAN INPUT
            resetAllFiltersSilent();

            // Set tab aktif
            switch (targetId) {
                case 'filter-tanggal-tab':
                    activeTab = 'tanggal';
                    break;
                case 'filter-lot-tab':
                    activeTab = 'lot';
                    break;
                case 'filter-customer-tab':
                    activeTab = 'customer';
                    break;
            }

            // Tampilkan state kosong
            showEmptyInitialState();
        });
    });

    // ====================================
    // FUNGSI INISIALISASI
    // ====================================
    function initializeTable() {
        reportTable = $('#reportTable').DataTable({
            columns: [{
                    data: null,
                    width: '5px',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return '';
                    }
                },
                {
                    data: 'tanggal_ditemukan',
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
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['lotno']) {
                            return `<input type="text" class="form-control form-control-sm inline-edit"
                       value="${escapeHtml(data || '')}"
                       data-field="lotno" data-id="${row.id}"
                       placeholder="Lot No">`;
                        }
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
                    data: 'nama_group',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['nama_group']) {
                            return `<input type="text" class="form-control form-control-sm inline-edit"
                       value="${escapeHtml(data || '')}"
                       data-field="nama_group" data-id="${row.id}"
                       placeholder="Nama Group">`;
                        }
                        return escapeHtml(data) || '-';
                    }
                },

                // Col index 9 - QTY
                {
                    data: 'qty',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['qty']) {
                            return `<input type="number" class="form-control form-control-sm inline-edit text-end"
                       value="${data || ''}"
                       data-field="qty" data-id="${row.id}"
                       min="0" placeholder="0">`;
                        }
                        return data !== null ? data : '-';
                    }
                },

                // Col index 10 - Aksi Claim Defect
                {
                    data: 'aksi_claim_defect',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['aksi_claim_defect']) {
                            return `<select class="form-select form-select-sm inline-edit"
                        data-field="aksi_claim_defect" data-id="${row.id}">
                        <option value="">-- Pilih --</option>
                        <option value="Repair" ${data === 'Repair' ? 'selected' : ''}>Repair</option>
                        <option value="Scrap"  ${data === 'Scrap'  ? 'selected' : ''}>Scrap</option>
                    </select>`;
                        }
                        if (!data) return '-';
                        if (data === 'Repair') return `<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Repair</span>`;
                        if (data === 'Scrap') return `<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Scrap</span>`;
                        return escapeHtml(data);
                    }
                },

                // Col index 11 - Nama Operator Pengambil
                {
                    data: 'nama_operator_pengambil',
                    width: '150px',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['nama_operator_pengambil']) {
                            return `<input type="text" class="form-control form-control-sm inline-edit"
                       value="${escapeHtml(data || '')}"
                       data-field="nama_operator_pengambil" data-id="${row.id}"
                       placeholder="Nama operator">`;
                        }
                        return data ? escapeHtml(data) : '-';
                    }
                },

                // Col index 12 - Tanggal Pengambilan
                {
                    data: 'tanggal_pengambilan',
                    width: '120px',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['tanggal_pengambilan']) {
                            let dateValue = data ? data.split(' ')[0] : '';
                            return `<input type="date" class="form-control form-control-sm inline-edit"
                       value="${dateValue}"
                       data-field="tanggal_pengambilan" data-id="${row.id}">`;
                        }
                        return data ? formatDate(data) : '-';
                    }
                },
                // Col index 13 - Aksi
                {
                    data: null,
                    width: '80px',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
            <button class="btn btn-sm btn-info" onclick="showDetail(${row.id})" title="Lihat Detail">
                <i class="ti ti-eye"></i>
            </button>
        `;
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
            autoWidth: false,
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

    // Tampilkan state awal (table kosong)
    function showEmptyInitialState() {
        $('#loadingSpinner').fadeOut();
        $('#summaryCards').hide();
        $('#filterInfo').hide();

        if (reportTable) {
            reportTable.clear().draw();
        }

        let message = '';
        switch (activeTab) {
            case 'tanggal':
                message = 'Silakan pilih rentang tanggal dan klik Terapkan Filter Tanggal';
                break;
            case 'lot':
                message = 'Silakan masukkan nomor lot dan klik Terapkan Filter Lot';
                break;
            case 'customer':
                message = 'Silakan pilih customer dan klik Terapkan Filter Customer';
                break;
        }

        $('#reportTable tbody').html(`
            <tr>
                <td colspan="12" class="text-center py-5">
                    <i class="ti ti-filter" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="mt-3 text-muted">${message}</p>
                </td>
            </tr>
        `);
    }

    // ====================================
    // LOAD DATA CUSTOMER (DROPDOWN dengan Select2)
    // ====================================
    function loadCustomerOptions() {
        $.ajax({
            url: baseUrl + '?action=getCustomerOptions',
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#filterCustomer').html('<option value="">Memuat data...</option>');
            },
            success: function(response) {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    let options = '';
                    response.data.forEach(customer => {
                        let customerName = customer.nama_customer || customer.customer || '';
                        if (customerName && customerName.trim() !== '') {
                            options += `<option value="${escapeHtml(customerName)}">${escapeHtml(customerName)}</option>`;
                        }
                    });
                    $('#filterCustomer').html(options);

                    // Inisialisasi Select2 setelah data dimuat
                    $('#filterCustomer').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Pilih customer...',
                        allowClear: true,
                        width: '100%'
                    });
                } else {
                    $('#filterCustomer').html('<option value="">Tidak ada data customer</option>');
                    $('#filterCustomer').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Tidak ada data',
                        disabled: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr) {
                console.error('Gagal load customer options:', xhr);
                $('#filterCustomer').html('<option value="">Gagal memuat data</option>');
                $('#filterCustomer').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Gagal memuat data',
                    disabled: true,
                    width: '100%'
                });
            }
        });
    }

    // ====================================
    // FUNGSI FILTER TANGGAL
    // ====================================
    function applyDateFilter() {
        let tanggalAwal = $('#tanggalAwal').val();
        let tanggalAkhir = $('#tanggalAkhir').val();

        if (tanggalAwal && tanggalAkhir && tanggalAwal > tanggalAkhir) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            });
            return;
        }

        if (!tanggalAwal && !tanggalAkhir) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Tanggal',
                text: 'Silakan pilih minimal 1 tanggal'
            });
            return;
        }

        // Set filter tanggal
        filters.tanggal.aktif = true;
        filters.tanggal.tanggalAwal = tanggalAwal || null;
        filters.tanggal.tanggalAkhir = tanggalAkhir || null;

        // Load data
        loadReportsByTab('tanggal');
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

        // Langsung terapkan filter
        applyDateFilter();
    }

    // ====================================
    // FUNGSI FILTER LOT (INPUT MANUAL)
    // ====================================
    function applyLotFilter() {
        let lotInput = $('#filterLotNo').val().trim();

        if (lotInput === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan masukkan nomor lot'
            });
            return;
        }

        // Set filter lot
        filters.lot.aktif = true;
        filters.lot.lotNos = lotInput.split(',').map(item => item.trim()).filter(item => item !== '');

        // Load data
        loadReportsByTab('lot');
    }

    function clearLotFilter() {
        filters.lot.aktif = false;
        filters.lot.lotNos = [];
        $('#filterLotNo').val('');

        if (activeTab === 'lot') {
            showEmptyInitialState();
        }

        Swal.fire({
            icon: 'info',
            title: 'Filter Lot Dihapus',
            text: 'Filter lot telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // ====================================
    // FUNGSI FILTER CUSTOMER (DROPDOWN) - TANPA SEARCH
    // ====================================
    function applyCustomerFilter() {
        // Ambil nilai dari Select2
        let selectedCustomers = $('#filterCustomer').val() || [];

        if (selectedCustomers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Customer',
                text: 'Silakan pilih minimal 1 customer'
            });
            return;
        }

        // Set filter customer
        filters.customer.aktif = true;
        filters.customer.customers = selectedCustomers;

        // Load data
        loadReportsByTab('customer');
    }

    function clearCustomerFilter() {
        filters.customer.aktif = false;
        filters.customer.customers = [];

        // Reset Select2
        $('#filterCustomer').val([]).trigger('change');

        if (activeTab === 'customer') {
            showEmptyInitialState();
        }

        Swal.fire({
            icon: 'info',
            title: 'Filter Customer Dihapus',
            text: 'Filter customer telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // ====================================
    // FUNGSI LOAD REPORTS PER TAB
    // ====================================
    function loadReportsByTab(tab) {
        $('#loadingSpinner').fadeIn();
        $('#summaryCards').hide();
        $('#filterInfo').hide();

        let url = baseUrl + '?action=getReports';
        let params = [];

        switch (tab) {
            case 'tanggal':
                if (filters.tanggal.aktif) {
                    if (filters.tanggal.tanggalAwal) {
                        params.push('tanggal_awal=' + filters.tanggal.tanggalAwal);
                    }
                    if (filters.tanggal.tanggalAkhir) {
                        params.push('tanggal_akhir=' + filters.tanggal.tanggalAkhir);
                    }
                }
                break;

            case 'lot':
                if (filters.lot.aktif && filters.lot.lotNos.length > 0) {
                    params.push('lot_nos=' + encodeURIComponent(filters.lot.lotNos.join(',')));
                }
                break;

            case 'customer':
                if (filters.customer.aktif && filters.customer.customers.length > 0) {
                    params.push('customers=' + encodeURIComponent(filters.customer.customers.join(',')));
                }
                break;
        }

        if (params.length === 0) {
            $('#loadingSpinner').fadeOut();
            showEmptyInitialState();
            return;
        }

        url += '&' + params.join('&');

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loadingSpinner').fadeOut();

                if (response.status === 'success') {
                    reportTable.clear();
                    reportTable.rows.add(response.data);
                    reportTable.draw();

                    if (response.data.length > 0) {
                        updateSummaryCards(response);
                        $('#summaryCards').fadeIn();
                    } else {
                        $('#summaryCards').hide();
                        showEmptyResult(response, tab);
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

    // ====================================
    // FUNGSI RESET ALL FILTERS (SILENT - TANPA SWEETALERT)
    // ====================================
    function resetAllFiltersSilent() {
        // Reset semua filter
        filters.tanggal.aktif = false;
        filters.tanggal.tanggalAwal = null;
        filters.tanggal.tanggalAkhir = null;

        filters.lot.aktif = false;
        filters.lot.lotNos = [];

        filters.customer.aktif = false;
        filters.customer.customers = [];

        // Reset semua input
        $('#tanggalAwal').val('');
        $('#tanggalAkhir').val('');
        $('#filterLotNo').val('');

        // Reset Select2
        $('#filterCustomer').val([]).trigger('change');
    }
    // ====================================
    // FUNGSI RESET ALL FILTERS (DENGAN SWEETALERT)
    // ====================================
    function resetAllFilters() {
        resetAllFiltersSilent();

        // Tampilkan state kosong
        showEmptyInitialState();

        Swal.fire({
            icon: 'success',
            title: 'Semua Filter Direset',
            text: 'Semua filter telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // ====================================
    // FUNGSI UPDATE TAMPILAN
    // ====================================
    function updateSummaryCards(response) {
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

    function updateSummary(data) {
        if (!data || data.length === 0) return;

        let uniqueCustomers = [...new Set(data.map(item => item.nama_customer))];
        let uniqueSections = [...new Set(data.map(item => item.nama_section))];
        let uniqueDefects = [...new Set(data.map(item => item.nama_defect))];

        $('#totalCustomer').text(uniqueCustomers.length.toLocaleString());
        $('#totalSection').text(uniqueSections.length.toLocaleString());
        $('#totalDefect').text(uniqueDefects.length.toLocaleString());
    }

    function showEmptyResult(response, tab) {
        let message = 'Tidak ada data untuk ditampilkan';

        switch (tab) {
            case 'tanggal':
                if (response.filter && response.filter.tanggal_awal && response.filter.tanggal_akhir) {
                    message = `Tidak ada laporan untuk rentang ${formatDate(response.filter.tanggal_awal)} - ${formatDate(response.filter.tanggal_akhir)}`;
                }
                break;
            case 'lot':
                message = `Tidak ada laporan untuk lot yang dipilih`;
                break;
            case 'customer':
                message = `Tidak ada laporan untuk customer yang dipilih`;
                break;
        }

        $('#reportTable tbody').html(`
            <tr>
                <td colspan="14" class="text-center py-5">
                    <i class="ti ti-database-off" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="mt-3 text-muted">${message}</p>
                </td>
            </tr>
        `);
    }

    // ====================================
    // FUNGSI DETAIL LAPORAN
    // ====================================
    function showDetail(id) {
        $('#detailLoading').show();
        $('#detailContent').hide();

        // Reset semua field editable dan hapus class editing
        $('.detail-editable')
            .removeClass('editing saving save-success')
            .removeAttr('style')
            .html('-');

        $('#detailModal').modal('show');

        $.ajax({
            url: baseUrl + '?action=show&id=' + id,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                $('#detailLoading').hide();

                if (response.status === 'success' && response.data) {
                    let data = response.data;

                    // Set data-id untuk semua field editable
                    $('.detail-editable').attr('data-id', data.id);

                    // Data existing (tidak bisa diedit)
                    $('#detailId').text(data.id || '-');
                    $('#detailTanggal').text(formatDate(data.tanggal_ditemukan) || '-');
                    $('#detailSection').text(escapeHtml(data.nama_section) || '-');
                    $('#detailDefect').text(escapeHtml(data.nama_defect) || '-');
                    $('#detailPartNo').text(escapeHtml(data.partno) || '-');
                    $('#detailCustomer').text(escapeHtml(data.nama_customer) || '-');
                    $('#detailOperator').text(escapeHtml(data.nama_operator) || '-');
                    $('#detailDeskripsi').text(escapeHtml(data.deskripsi_masalah) || '-');

                    // Data editable
                    $('#detailLotNo').text(escapeHtml(data.lotno) || '-');
                    $('#detailGroup').text(escapeHtml(data.nama_group) || '-');
                    $('#detailQty').text(data.qty || '0');

                    // Format Aksi Claim Defect
                    if (data.aksi_claim_defect) {
                        let badgeClass = data.aksi_claim_defect === 'Repair' ? 'bg-primary' : 'bg-danger';
                        let textClass = data.aksi_claim_defect === 'Repair' ? 'primary' : 'danger';
                        $('#detailAksiClaim').html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(data.aksi_claim_defect)}</span>`);
                    } else {
                        $('#detailAksiClaim').text('-');
                    }

                    $('#detailOperatorPengambil').text(escapeHtml(data.nama_operator_pengambil) || '-');
                    $('#detailTanggalPengambilan').text(formatDate(data.tanggal_pengambilan) || '-');
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

    // ====================================
    // DOUBLE-CLICK EDIT DI MODAL
    // ====================================
    $(document).on('dblclick', '.detail-editable', function(e) {
        e.preventDefault();
        e.stopPropagation();

        let $this = $(this);
        let field = $this.data('field');
        let id = $this.data('id');

        // Cek jika sedang dalam mode edit atau sedang menyimpan
        if ($this.hasClass('editing') || isSaving) return;

        let currentValue = $this.text().trim();

        // Handle khusus untuk aksi_claim_defect yang punya badge
        if (field === 'aksi_claim_defect') {
            let badgeText = $this.find('.badge').text().trim();
            if (badgeText) {
                currentValue = badgeText;
            }
        }

        // Handle khusus untuk tanggal
        if (field === 'tanggal_pengambilan') {
            currentValue = $this.text().trim();
            if (currentValue !== '-' && currentValue !== '') {
                // Konversi dari dd/mm/yyyy ke yyyy-mm-dd untuk input date
                if (currentValue.includes('/')) {
                    let parts = currentValue.split('/');
                    if (parts.length === 3) {
                        currentValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
            } else {
                currentValue = '';
            }
        }

        // Bersihkan nilai dari karakter khusus
        currentValue = currentValue.replace('✎', '').trim();
        if (currentValue === '-' || currentValue === '') currentValue = '';

        // Buat input berdasarkan field
        let inputHtml = '';

        if (field === 'aksi_claim_defect') {
            inputHtml = `<select class="detail-edit-select" data-field="${field}" data-id="${id}">
            <option value="" ${currentValue === '' ? 'selected' : ''}>-- Pilih --</option>
            <option value="Repair" ${currentValue === 'Repair' ? 'selected' : ''}>Repair</option>
            <option value="Scrap" ${currentValue === 'Scrap' ? 'selected' : ''}>Scrap</option>
        </select>`;
        } else if (field === 'tanggal_pengambilan') {
            inputHtml = `<input type="date" class="detail-edit-input" data-field="${field}" data-id="${id}" value="${currentValue}">`;
        } else if (field === 'qty') {
            inputHtml = `<input type="number" class="detail-edit-input" data-field="${field}" data-id="${id}" value="${currentValue}" min="0" step="1">`;
        } else {
            inputHtml = `<input type="text" class="detail-edit-input" data-field="${field}" data-id="${id}" value="${escapeHtml(currentValue)}" placeholder="Masukkan ${field}">`;
        }

        // Ganti konten dengan input
        $this.addClass('editing').html(inputHtml);

        // Focus ke input
        setTimeout(function() {
            $this.find('input, select').focus();
        }, 100);
    });


    // ====================================
    // SAVE EDIT DARI MODAL - VIA BLUR (DENGAN DELAY & VALIDASI)
    // ====================================
    $(document).on('blur', '.detail-edit-input, .detail-edit-select', function(e) {
        let $this = $(this);
        let $parent = $this.closest('.detail-editable');

        // Jika parent sudah tidak dalam mode editing, abaikan
        if (!$parent.hasClass('editing')) return;

        let field = $this.data('field');
        let id = $this.data('id');
        let value = $this.val();

        // Delay untuk memastikan tidak konflik dengan klik button atau Enter
        setTimeout(function() {
            // Cek lagi apakah masih dalam mode editing dan tidak sedang menyimpan
            if ($parent.hasClass('editing') && !isSaving) {
                saveDetailEdit(field, id, value, $parent);
            }
        }, 300);
    });

    $(document).on('keypress', '.detail-edit-input, .detail-edit-select', function(e) {
        if (e.which === 13) { // Enter
            e.preventDefault();
            e.stopPropagation();

            if (isSaving) return;

            let $this = $(this);
            let $parent = $this.closest('.detail-editable');
            let field = $this.data('field');
            let id = $this.data('id');
            let value = $this.val();

            saveDetailEdit(field, id, value, $parent);
        }
    });

    // ====================================
    // CANCEL EDIT DARI MODAL - VIA ESCAPE
    // ====================================
    $(document).on('keydown', '.detail-edit-input, .detail-edit-select', function(e) {
        if (e.which === 27) { // Escape
            e.preventDefault();
            e.stopPropagation();

            let $this = $(this);
            let $parent = $this.closest('.detail-editable');
            let field = $this.data('field');
            let id = $this.data('id');

            cancelDetailEdit(field, id, $parent);
        }
    });

    // ====================================
    // FUNGSI SAVE EDIT DARI MODAL
    // ====================================
    function saveDetailEdit(field, id, value, $element) {
        // Cegah double save
        if (isSaving) return;
        isSaving = true;

        if (!$element || !$element.length) {
            $element = $(`.detail-editable[data-field="${field}"][data-id="${id}"]`);
        }

        // Validasi khusus per field
        if (field === 'nama_operator_pengambil' && (!value || !value.trim())) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Nama operator pengambil tidak boleh kosong',
                timer: 1500,
                showConfirmButton: false
            });
            cancelDetailEdit(field, id, $element);
            isSaving = false;
            return;
        }

        if (field === 'qty') {
            if (value === '' || value === null || value === undefined) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'QTY harus diisi',
                    timer: 1500,
                    showConfirmButton: false
                });
                cancelDetailEdit(field, id, $element);
                isSaving = false;
                return;
            }

            let qty = parseInt(value);
            if (isNaN(qty) || qty < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'QTY harus berupa angka positif',
                    timer: 1500,
                    showConfirmButton: false
                });
                cancelDetailEdit(field, id, $element);
                isSaving = false;
                return;
            }
            value = qty;
        }

        // Validasi format tanggal
        if (field === 'tanggal_pengambilan') {
            if (value && value.trim() !== '') {
                let datePattern = /^\d{4}-\d{2}-\d{2}$/;
                if (!datePattern.test(value)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Format tanggal harus YYYY-MM-DD',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    cancelDetailEdit(field, id, $element);
                    isSaving = false;
                    return;
                }
            } else {
                value = null;
            }
        }

        // Tampilkan loading di dalam modal (lebih halus)
        $element.addClass('saving').append('<small class="saving-indicator">Menyimpan...</small>');

        // Dapatkan data lain dari row yang sama
        let table = $('#reportTable').DataTable();
        let rowData = null;

        table.rows().every(function() {
            if (this.data().id == id) {
                rowData = this.data();
            }
        });

        if (!rowData) {
            $element.removeClass('saving').find('.saving-indicator').remove();
            Swal.fire('Error', 'Data tidak ditemukan', 'error');
            cancelDetailEdit(field, id, $element);
            isSaving = false;
            return;
        }

        // Siapkan payload
        let payload = {
            id: id,
            lotno: field === 'lotno' ? value : (rowData.lotno || ''),
            aksi_claim_defect: field === 'aksi_claim_defect' ? value : (rowData.aksi_claim_defect || ''),
            nama_group: field === 'nama_group' ? value : (rowData.nama_group || ''),
            qty: field === 'qty' ? value : (rowData.qty || 0),
            nama_operator_pengambil: field === 'nama_operator_pengambil' ? value : (rowData.nama_operator_pengambil || ''),
            tanggal_pengambilan: field === 'tanggal_pengambilan' ? value : formatDateForServer(rowData.tanggal_pengambilan)
        };

        console.log('Sending payload:', payload);

        $.ajax({
            url: 'UpdateDefectReportController.php?action=update',
            type: 'POST',
            data: payload,
            dataType: 'json',
            timeout: 10000, // Timeout 10 detik
            success: function(response) {
                $element.removeClass('saving').find('.saving-indicator').remove();

                if (response.status === 'success') {
                    // Update tampilan di modal
                    updateDetailDisplay(field, value, $element);

                    // Update di tabel utama
                    updateTableRow(id, field, value);

                    // Beri feedback sukses (tanpa SweetAlert toast yang mengganggu)
                    $element.addClass('save-success');
                    setTimeout(function() {
                        $element.removeClass('save-success');
                    }, 1000);
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal menyimpan', 'error');
                    cancelDetailEdit(field, id, $element);
                }

                isSaving = false;
            },
            error: function(xhr, status, error) {
                $element.removeClass('saving').find('.saving-indicator').remove();
                console.error('Error response:', xhr.responseText);

                let errorMsg = 'Gagal menyimpan data';
                if (status === 'timeout') {
                    errorMsg = 'Timeout, silakan coba lagi';
                } else {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                    }
                }

                Swal.fire('Error', errorMsg, 'error');
                cancelDetailEdit(field, id, $element);
                isSaving = false;
            }
        });
    }

    // ====================================
    // FUNGSI FORMAT DATE UNTUK SERVER
    // ====================================
    function formatDateForServer(dateStr) {
        if (!dateStr) return '';

        // Jika sudah format YYYY-MM-DD
        if (typeof dateStr === 'string' && dateStr.match(/^\d{4}-\d{2}-\d{2}/)) {
            return dateStr;
        }

        // Jika format dd/mm/yyyy
        if (typeof dateStr === 'string' && dateStr.includes('/')) {
            let parts = dateStr.split('/');
            if (parts.length === 3) {
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            }
        }

        return dateStr;
    }

    // ====================================
    // FUNGSI CANCEL EDIT DARI MODAL
    // ====================================
    function cancelDetailEdit(field, id, $element) {
        if (!$element || !$element.length) {
            $element = $(`.detail-editable[data-field="${field}"][data-id="${id}"]`);
        }

        // Dapatkan nilai asli dari DataTable
        let table = $('#reportTable').DataTable();
        let originalValue = null;

        table.rows().every(function() {
            let rowData = this.data();
            if (rowData.id == id) {
                originalValue = rowData[field];
            }
        });

        // Update tampilan
        if (field === 'aksi_claim_defect' && originalValue) {
            let badgeClass = originalValue === 'Repair' ? 'bg-primary' : 'bg-danger';
            let textClass = originalValue === 'Repair' ? 'primary' : 'danger';
            $element.html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(originalValue)}</span>`);
        } else if (field === 'tanggal_pengambilan') {
            $element.text(formatDate(originalValue) || '-');
        } else {
            $element.text(originalValue || '-');
        }

        $element.removeClass('editing saving');
        $element.find('.saving-indicator').remove();
    }

    // ====================================
    // FUNGSI UPDATE DISPLAY DI MODAL
    // ====================================
    function updateDetailDisplay(field, value, $element) {
        if (field === 'aksi_claim_defect' && value) {
            let badgeClass = value === 'Repair' ? 'bg-primary' : 'bg-danger';
            let textClass = value === 'Repair' ? 'primary' : 'danger';
            $element.html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(value)}</span>`);
        } else if (field === 'tanggal_pengambilan') {
            $element.text(formatDate(value) || '-');
        } else {
            $element.text(value || '-');
        }
        $element.removeClass('editing');
    }

    // ====================================
    // FUNGSI UPDATE TABLE ROW
    // ====================================
    function updateTableRow(id, field, value) {
        let table = $('#reportTable').DataTable();

        table.rows().every(function() {
            let rowData = this.data();
            if (rowData.id == id) {
                rowData[field] = value;
                this.data(rowData);
            }
        });

        table.draw(false);
    }

    // Mencegah blur saat klik di dalam input
    $(document).on('mousedown', '.detail-edit-input, .detail-edit-select', function(e) {
        e.stopPropagation();
    });

    // ====================================
    // FUNGSI EDIT INLINE
    // ====================================
    function editInline(id) {
        let table = $('#reportTable').DataTable();

        table.rows().every(function() {
            let rowData = this.data();
            if (rowData._editing) {
                rowData._editing = false;
                this.data(rowData);
            }
        });

        table.rows().every(function() {
            let rowData = this.data();
            if (rowData.id == id) {
                rowData._editing = true;
                this.data(rowData);
            }
        });

        table.draw(false);
    }

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

                    table.rows().every(function() {
                        let rowData = this.data();
                        if (rowData.id == id) {
                            rowData.nama_operator_pengambil = operatorValue.trim();
                            rowData.tanggal_pengambilan = tanggalValue || null;
                            rowData._editing = false;
                            this.data(rowData);
                        }
                    });

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

    // ====================================
    // FUNGSI EXPORT EXCEL
    // ====================================
    $('#btnExportExcel').on('click', function() {
        exportExcel();
    });

    function exportExcel() {
        let params = [];

        // Cek filter yang aktif
        if (filters.tanggal.aktif) {
            if (filters.tanggal.tanggalAwal) {
                params.push('tanggal_awal=' + encodeURIComponent(filters.tanggal.tanggalAwal));
            }
            if (filters.tanggal.tanggalAkhir) {
                params.push('tanggal_akhir=' + encodeURIComponent(filters.tanggal.tanggalAkhir));
            }
        } else if (filters.lot.aktif) {
            if (filters.lot.lotNos.length > 0) {
                params.push('lot_nos=' + encodeURIComponent(filters.lot.lotNos.join(',')));
            }
        } else if (filters.customer.aktif) {
            if (filters.customer.customers.length > 0) {
                params.push('customers=' + encodeURIComponent(filters.customer.customers.join(',')));
            }
        }

        if (params.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Filter diperlukan',
                text: 'Silakan terapkan filter terlebih dahulu untuk export data'
            });
            return;
        }

        let dataCount = reportTable.rows().count();

        if (dataCount === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Dapat Export',
                text: 'Tidak ada data untuk diexport'
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

        let url = 'ExportDefectReportController.php?' + params.join('&');
        window.location.href = url;

        setTimeout(() => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Export Berhasil',
                text: `${dataCount} data laporan berhasil diexport`,
                timer: 2000,
                showConfirmButton: false
            });
        }, 1500);
    }

    // ====================================
    // UTILITY FUNCTIONS
    // ====================================
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

    // ====================================
    // EVENT HANDLERS - PER CELL EDIT
    // ====================================

    // Double-click cell untuk edit
    $(document).on('dblclick', '#reportTable tbody td', function(e) {
        let table = $('#reportTable').DataTable();
        let colIdx = $(this).index();
        let field = EDITABLE_COLS[colIdx];

        if (!field) return; // kolom tidak bisa diedit

        let row = table.row($(this).closest('tr'));
        let rowData = row.data();
        if (!rowData) return;

        // Init _editingCells jika belum ada
        if (!rowData._editingCells) rowData._editingCells = {};

        // Kalau sudah dalam mode edit, abaikan
        if (rowData._editingCells[field]) return;

        rowData._editingCells[field] = true;
        row.data(rowData).draw(false);

        // Focus ke input/select setelah render
        setTimeout(function() {
            $(`[data-field="${field}"][data-id="${rowData.id}"]`).focus();
        }, 80);
    });

    $(document).on('keypress', '.inline-edit', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            let id = $(this).data('id');
            saveInlineEdit(id);
        }
    });

    // Enter = Simpan, Escape = Batal (untuk input)
    $(document).on('keydown', '.inline-edit', function(e) {
        let field = $(this).data('field');
        let id = $(this).data('id');

        if (e.which === 13) {
            e.preventDefault();
            saveCellEdit(field, id, $(this).val());
        }
        if (e.which === 27) {
            e.preventDefault();
            cancelCellEdit(field, id);
        }
    });

    // Blur = otomatis simpan
    $(document).on('blur', '.inline-edit', function() {
        let field = $(this).data('field');
        let id = $(this).data('id');
        let val = $(this).val();

        // Delay kecil supaya Escape sempat dicegah duluan
        setTimeout(function() {
            let stillEditing = $(`[data-field="${field}"][data-id="${id}"]`).length > 0;
            if (stillEditing) saveCellEdit(field, id, val);
        }, 150);
    });

    // ====================================
    // FUNGSI SAVE / CANCEL PER CELL
    // ====================================

    function saveCellEdit(field, id, value) {
        let table = $('#reportTable').DataTable();
        let rowRef = null;
        let rowData = null;

        table.rows().every(function() {
            if (this.data().id == id) {
                rowData = this.data();
                rowRef = this;
            }
        });

        if (!rowData || !rowRef) return;

        // Susun semua nilai yang akan dikirim ke server
        // Gunakan nilai terbaru untuk field yang diedit, sisanya pakai rowData
        let payload = {
            id: id,
            lotno: field === 'lotno' ? value : (rowData.lotno || ''),
            aksi_claim_defect: field === 'aksi_claim_defect' ? value : (rowData.aksi_claim_defect || ''),
            nama_group: field === 'nama_group' ? value : (rowData.nama_group || ''),
            qty: field === 'qty' ? value : (rowData.qty || ''),
            nama_operator_pengambil: field === 'nama_operator_pengambil' ?
                value : (rowData.nama_operator_pengambil || ''),
            tanggal_pengambilan: field === 'tanggal_pengambilan' ?
                value : (rowData.tanggal_pengambilan ?
                    rowData.tanggal_pengambilan.split(' ')[0] :
                    '')
        };

        $.ajax({
            url: 'UpdateDefectReportController.php?action=update',
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update nilai di rowData
                    rowData[field] = value !== '' ? value : null;
                    delete rowData._editingCells[field];
                    rowRef.data(rowData).draw(false);

                    // Toast kecil
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan',
                        text: `Kolom berhasil diperbarui`,
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal menyimpan', 'error');
                    cancelCellEdit(field, id);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal menyimpan data', 'error');
                cancelCellEdit(field, id);
            }
        });
    }

    function cancelCellEdit(field, id) {
        let table = $('#reportTable').DataTable();

        table.rows().every(function() {
            let d = this.data();
            if (d.id == id && d._editingCells && d._editingCells[field]) {
                delete d._editingCells[field];
                this.data(d).draw(false);
            }
        });
    }

    // ====================================
    // IMPORT DATA FUNCTIONALITY
    // ====================================

    // Event handler untuk tombol import
    $('#btnImportData').on('click', function() {
        // Reset form
        $('#importForm')[0].reset();
        $('#skipFirstRow').prop('checked', true);
        $('#importProgress').hide();
        $('#previewSection').hide();
        $('#btnImportSubmit').prop('disabled', true);

        // Reset progress bar
        $('.progress-bar').css('width', '0%').text('0%');

        // Clear preview
        $('#previewHeader').html('<tr><th>#</th><th>Preview</th></tr>');
        $('#previewBody').html('<tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>');

        // Show modal
        $('#importModal').modal('show');
    });

    // Download template
    $('#btnDownloadTemplate').on('click', function() {
        window.location.href = 'ImportDataDefectReportController.php?action=downloadTemplateWithHeader';

        // Download template tanpa header (delay 1 detik)
        setTimeout(function() {
            window.location.href = 'ImportDataDefectReportController.php?action=downloadTemplateWithoutHeader';
        }, 3000);
    });

    // Handle file selection
    $('#importFile').on('change', function() {
        const file = this.files[0];

        if (!file) {
            $('#btnImportSubmit').prop('disabled', true);
            $('#previewSection').hide();
            return;
        }

        // Validate file size (10 MB)
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Maksimal ukuran file adalah 10 MB'
            });
            $(this).val('');
            $('#btnImportSubmit').prop('disabled', true);
            $('#previewSection').hide();
            return;
        }

        // Validate file extension
        const validExtensions = ['.xlsx', '.xls', '.csv'];
        const fileName = file.name;
        const fileExt = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();

        if (!validExtensions.includes(fileExt)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid',
                text: 'Hanya file dengan format .xlsx, .xls, atau .csv yang diperbolehkan'
            });
            $(this).val('');
            $('#btnImportSubmit').prop('disabled', true);
            $('#previewSection').hide();
            return;
        }

        // Enable import button
        $('#btnImportSubmit').prop('disabled', false);

        // Show preview (optional - can be implemented later)
        // For now, just show a simple preview message
        $('#previewSection').show();
        $('#previewHeader').html('<tr><th>#</th><th>Informasi File</th></tr>');
        $('#previewBody').html(`
        <tr>
            <td>1</td>
            <td>
                <strong>Nama File:</strong> ${escapeHtml(fileName)}<br>
                <strong>Ukuran:</strong> ${(file.size / 1024).toFixed(2)} KB<br>
                <strong>Status:</strong> <span class="text-success">Siap diimport</span>
            </td>
        </tr>
    `);
    });

    // Handle import submit
    $('#btnImportSubmit').on('click', function() {
        const file = $('#importFile')[0].files[0];

        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih file terlebih dahulu'
            });
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('file', file);
        formData.append('skip_first_row', $('#skipFirstRow').is(':checked') ? '1' : '0');

        // Show progress bar
        $('#importProgress').show();
        $('#btnImportSubmit').prop('disabled', true);
        $('#importFile').prop('disabled', true);
        $('#skipFirstRow').prop('disabled', true);
        $('#btnDownloadTemplate').prop('disabled', true);

        // Update progress status
        let progressInterval = setInterval(function() {
            let currentWidth = parseInt($('.progress-bar').css('width'));
            if (currentWidth < 90) {
                let newWidth = currentWidth + 10;
                $('.progress-bar').css('width', newWidth + '%').text(newWidth + '%');
            }
        }, 500);

        // Send AJAX request
        $.ajax({
            url: 'ImportDataDefectReportController.php?action=import',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 300000, // 5 minutes timeout for large files
            success: function(response) {
                clearInterval(progressInterval);
                $('.progress-bar').css('width', '100%').text('100%');

                setTimeout(function() {
                    $('#importProgress').hide();

                    if (response.status === 'success') {
                        let message = response.message || 'Data berhasil diimport';

                        // Show detailed results if available
                        if (response.data) {
                            let details = `
                            <div class="text-start mt-2">
                                <strong>Detail Import:</strong><br>
                                Total data: ${response.data.total || 0}<br>
                                Berhasil: ${response.data.success || 0}<br>
                                Gagal: ${response.data.failed || 0}
                            </div>
                        `;
                            message += details;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Import Berhasil',
                            html: message,
                            timer: 3000,
                            showConfirmButton: true
                        }).then(() => {
                            // Close modal
                            $('#importModal').modal('hide');

                            // Reset filters and reload data based on active tab
                            if (activeTab === 'tanggal' && filters.tanggal.aktif) {
                                loadReportsByTab('tanggal');
                            } else if (activeTab === 'lot' && filters.lot.aktif) {
                                loadReportsByTab('lot');
                            } else if (activeTab === 'customer' && filters.customer.aktif) {
                                loadReportsByTab('customer');
                            } else {
                                showEmptyInitialState();
                            }
                        });
                    } else {
                        let errorMsg = response.message || 'Gagal mengimport data';

                        // Show error details if available
                        if (response.errors && response.errors.length > 0) {
                            errorMsg += '<br><br><strong>Detail Error:</strong><br>';
                            response.errors.forEach(err => {
                                errorMsg += `- ${escapeHtml(err)}<br>`;
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Import Gagal',
                            html: errorMsg,
                            confirmButtonText: 'Tutup'
                        });
                    }

                    // Reset form
                    resetImportForm();
                }, 1000);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $('#importProgress').hide();

                let errorMsg = 'Gagal mengimport data';

                if (status === 'timeout') {
                    errorMsg = 'Import timeout. File terlalu besar atau koneksi lambat.';
                } else if (xhr.responseText) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    } catch (e) {
                        errorMsg = xhr.responseText;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal',
                    text: errorMsg,
                    confirmButtonText: 'Tutup'
                });

                resetImportForm();
            }
        });
    });

    // Reset import form
    function resetImportForm() {
        $('#importForm')[0].reset();
        $('#skipFirstRow').prop('checked', true);
        $('#importProgress').hide();
        $('#previewSection').hide();
        $('#btnImportSubmit').prop('disabled', true);
        $('#importFile').prop('disabled', false);
        $('#skipFirstRow').prop('disabled', false);
        $('#btnDownloadTemplate').prop('disabled', false);
        $('.progress-bar').css('width', '0%').text('0%');

        // Clear preview
        $('#previewHeader').html('<tr><th>#</th><th>Preview</th></tr>');
        $('#previewBody').html('<tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>');
    }

    // Clear form when modal is closed
    $('#importModal').on('hidden.bs.modal', function() {
        resetImportForm();
    });
</script>

<style>
    /* Memastikan kolom button memiliki tinggi yang sama dengan kolom input */
    .row.g-3>[class*="col-"] {
        display: flex;
        flex-direction: column;
    }

    /* Container untuk button agar selalu rata bawah */
    .d-flex.flex-column.h-100 {
        height: 100% !important;
    }

    .justify-content-end {
        justify-content: flex-end !important;
    }

    /* Memastikan label memiliki tinggi yang konsisten */
    .form-label {
        height: auto;
        min-height: 24px;
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    /* Menyamakan tinggi button container dengan input */
    .d-flex.flex-column.h-100 .d-flex.gap-2 {
        margin-top: 0;
    }

    .table-responsive {
        border-radius: 12px;
    }

    #reportTable thead th {
        background: #f8fafc;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 2px solid #e9ecef;
    }

    #reportTable tbody td {
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

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 0.6rem 1rem;
        height: auto;
    }

    .form-control:focus,
    .form-select:focus {
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

    .rounded-circle {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge {
        font-weight: 500;
    }

    .alert-info {
        background-color: #e6f5fe;
        border: none;
        color: #055160;
        border-radius: 10px;
    }

    #loadingSpinner {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        min-height: 200px;
    }

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

    .dataTables_empty td {
        padding: 3rem !important;
    }

    .border-top {
        border-top: 1px solid #e0e0e0 !important;
    }

    .inline-edit {
        border: 2px solid #0d6efd !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        transition: all 0.2s ease;
        min-width: 90px;
        /* ← tambahan: lebar minimum semua inline input */
        width: 100%;
        /* ← tambahan: isi lebar cell */
    }

    .inline-edit:focus {
        border-color: #0a58ca !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
        outline: none;
    }

    #reportTable tbody td:nth-child(12),
    #reportTable tbody td:nth-child(13) {
        cursor: pointer;
        position: relative;
    }

    #reportTable tbody td:nth-child(12):hover::after,
    #reportTable tbody td:nth-child(13):hover::after {
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

    [data-dblclick="true"] {
        cursor: pointer;
    }

    /* Style untuk field yang bisa diedit di modal */
    .detail-editable {
        cursor: pointer;
        position: relative;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .detail-editable:hover {
        background-color: #f0f7ff;
        border: 1px dashed #0d6efd;
    }

    .detail-editable:hover::after {
        content: "✎";
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 14px;
    }

    /* Style saat sedang menyimpan */
    .detail-editable.saving {
        opacity: 0.7;
        pointer-events: none;
        background-color: #f8f9fa;
        border: 1px solid #6c757d;
    }

    .detail-editable.saving::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 8px;
        width: 16px;
        height: 16px;
        margin-top: -8px;
        border: 2px solid #0d6efd;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to {
            transform: rotate(360deg);
        }
    }

    /* Style saat sukses */
    .detail-editable.save-success {
        background-color: #d4edda;
        border: 1px solid #28a745;
        transition: background-color 0.3s ease;
    }

    .saving-indicator {
        position: absolute;
        top: -20px;
        right: 0;
        font-size: 10px;
        color: #0d6efd;
        background: white;
        padding: 2px 6px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .edit-hint {
        font-size: 10px;
        opacity: 0.7;
        margin-top: 4px;
        color: #6c757d;
    }

    /* Style untuk input edit di modal */
    .detail-edit-input,
    .detail-edit-select {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid #0d6efd;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        transition: all 0.2s ease;
    }

    .detail-edit-input:focus,
    .detail-edit-select:focus {
        outline: none;
        border-color: #0a58ca;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .detail-edit-input:hover,
    .detail-edit-select:hover {
        border-color: #0a58ca;
    }

    /* Import Modal Styles */
    #importProgress .progress {
        border-radius: 8px;
        background-color: #f0f0f0;
    }

    #importProgress .progress-bar {
        transition: width 0.3s ease;
        background-color: #28a745;
    }

    #previewSection .table {
        font-size: 12px;
    }

    #previewSection .table th {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        font-weight: 600;
    }

    #previewSection {
        animation: fadeIn 0.3s ease;
    }

    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }
</style>