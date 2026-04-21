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

            <!-- Tab Navigation - 4 TABS -->
            <ul class="nav nav-tabs nav-fill mb-3" id="filterTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="filter-tanggal-tab" data-bs-toggle="tab"
                        data-bs-target="#filter-tanggal" type="button" role="tab"
                        aria-controls="filter-tanggal" aria-selected="true">
                        <i class="ti ti-calendar me-2"></i>Tanggal + Part No
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
                        <i class="ti ti-users me-2"></i>Customer + Range Tanggal
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="filter-partno-tab" data-bs-toggle="tab"
                        data-bs-target="#filter-partno" type="button" role="tab"
                        aria-controls="filter-partno" aria-selected="false">
                        <i class="ti ti-package me-2"></i>Part No + Range Tanggal
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="filterTabsContent">
                <!-- TAB 1: Tanggal (Single) + Part No -->
                <div class="tab-pane fade show active" id="filter-tanggal" role="tabpanel" tabindex="0">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal
                            </label>
                            <input type="date" class="form-control" id="filterTanggal" value="">
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-info-circle me-1"></i>Pilih tanggal (1 hari)
                            </small>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-package me-1"></i>Part No
                            </label>
                            <select class="form-select" id="filterPartNoByTanggal" multiple="multiple" style="width: 100%;" disabled>
                                <option value="">Pilih tanggal terlebih dahulu</option>
                            </select>
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-info-circle me-1"></i>Pilih part no (bisa multiple) - muncul setelah pilih tanggal
                            </small>
                        </div>
                        <div class="col-lg-2 col-md-12">
                            <label class="form-label fw-semibold text-primary invisible">Aksi</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1" id="btnApplyTanggalPartNo" disabled>
                                    <i class="ti ti-filter me-2"></i>Terapkan
                                </button>
                                <button class="btn btn-outline-secondary" onclick="resetAllFilters()">
                                    <i class="ti ti-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center pt-3 mt-3 border-top">
                        <span class="text-muted me-2"><i class="ti ti-bolt"></i> Quick Filter:</span>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 quick-filter-btn" onclick="setSingleQuickFilter('today')">Hari Ini</button>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 quick-filter-btn" onclick="setSingleQuickFilter('yesterday')">Kemarin</button>
                    </div>
                </div>

                <!-- TAB 2: Filter Lot No (INPUT MANUAL + SCAN) -->
                <div class="tab-pane fade" id="filter-lot" role="tabpanel" tabindex="0">
                    <div class="row g-3">
                        <div class="col-lg-8 col-md-8">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-barcode me-1"></i>Lot Number
                            </label>
                            <input type="text" class="form-control" id="filterLotNo"
                                placeholder="Scan atau ketik nomor lot (pisahkan dengan koma untuk multiple)">
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-info-circle me-1"></i>Scan langsung terapkan, atau ketik manual lalu tekan Terapkan
                            </small>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label class="form-label fw-semibold text-primary invisible">Aksi</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1" onclick="applyLotFilter()">
                                    <i class="ti ti-filter me-2"></i>Terapkan
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearLotFilter()">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Customer + Range Tanggal -->
                <div class="tab-pane fade" id="filter-customer" role="tabpanel" tabindex="0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-users me-1"></i>Customer
                            </label>
                            <select class="form-select" id="filterCustomer" multiple="multiple" style="width: 100%;">
                                <option value="">Memuat data...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal Awal (Opsional)
                            </label>
                            <input type="date" class="form-control" id="customerTanggalAwal">
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal Akhir (Opsional)
                            </label>
                            <input type="date" class="form-control" id="customerTanggalAkhir">
                        </div>
                        <div class="col-lg-2 col-md-12">
                            <label class="form-label fw-semibold text-primary invisible">Aksi</label>
                            <button class="btn btn-outline-secondary w-100" id="btnClearCustomerRange">
                                <i class="ti ti-trash me-1"></i>Clear Range
                            </button>
                        </div>
                    </div>
                    <!-- Tombol aksi di bawah -->
                    <div class="row g-3 mt-3 pt-3 border-top">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-secondary" onclick="clearCustomerFilter()">
                                    <i class="ti ti-x me-1"></i>Reset
                                </button>
                                <button class="btn btn-primary" onclick="applyCustomerFilter()">
                                    <i class="ti ti-filter me-2"></i>Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: Part No + Range Tanggal -->
                <div class="tab-pane fade" id="filter-partno" role="tabpanel" tabindex="0">
                    <!-- Pilih Part Number -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-package me-1"></i>Part Number <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="filterPartNo" multiple="multiple" style="width: 100%;">
                                <option value="">Memuat data...</option>
                            </select>
                            <small class="text-muted mt-1 d-block">
                                <i class="ti ti-info-circle me-1"></i>Pilih minimal 1 part number (bisa multiple & search)
                            </small>
                        </div>
                    </div>

                    <!-- Range Tanggal (Opsional) - Langsung aktif -->
                    <div class="row g-3 mt-3">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal Awal (Opsional)
                            </label>
                            <input type="date" class="form-control" id="partNoTanggalAwal">
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label fw-semibold text-primary">
                                <i class="ti ti-calendar me-1"></i>Tanggal Akhir (Opsional)
                            </label>
                            <input type="date" class="form-control" id="partNoTanggalAkhir">
                        </div>
                        <div class="col-lg-2 col-md-12">
                            <label class="form-label fw-semibold text-primary invisible">Aksi</label>
                            <button class="btn btn-outline-secondary w-100" id="btnClearPartNoRange">
                                <i class="ti ti-trash me-1"></i>Clear Range
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Aksi di Bawah Semua Filter -->
                    <div class="row g-3 mt-4 pt-3 border-top">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-danger" onclick="clearPartNoFilter()">
                                    <i class="ti ti-refresh me-1"></i>Reset
                                </button>
                                <button class="btn btn-primary px-4" onclick="applyPartNoFilter()">
                                    <i class="ti ti-filter me-2"></i>Terapkan Filter
                                </button>
                            </div>
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
                            <th>Part No</th>
                            <th>Lot No</th>
                            <th>Group</th>
                            <th>QTY</th>
                            <th>Aksi Claim Defect</th>
                            <th>Status</th>
                            <th>Nama Operator Pengambil</th>
                            <th>Tanggal Pengambilan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody"></tbody>
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
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-clock text-info me-2"></i>
                                    <span class="text-muted small">Shift</span>
                                </div>
                                <h6 class="fw-semibold mb-0" id="detailShift">-</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-check-circle text-success me-2"></i>
                                    <span class="text-muted small">Status</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input status-toggle-modal" id="detailStatusToggle" style="width: 50px; height: 24px; cursor: pointer;">
                                        <label class="form-check-label ms-2" id="detailStatusLabel">
                                            <span class="badge" id="detailStatusBadge">-</span>
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted edit-hint">Toggle untuk mengubah status (OK/NG)</small>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="mt-4">
                        <div class="border rounded-3 p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-message text-info me-2"></i>
                                <span class="text-muted small">Deskripsi Masalah</span>
                            </div>
                            <p class="mb-0" id="detailDeskripsi" style="white-space: pre-wrap;">-</p>
                        </div>
                    </div>
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
                    <i class="ti ti-x me-1"></i>Tutup
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
                <div class="alert alert-info border-0 mb-4">
                    <i class="ti ti-info-circle fs-5 me-2"></i>
                    <strong>Petunjuk:</strong>
                    <ul class="mb-0 mt-2">
                        <li>File harus berformat <strong>.xlsx, .xls, atau .csv</strong></li>
                        <li>Maksimal ukuran file: <strong>10 MB</strong></li>
                        <li>Pastikan struktur kolom sesuai dengan template yang telah disediakan</li>
                        <li><strong class="text-danger">Baris pertama akan diabaikan (header), pastikan file memiliki header</strong></li>
                    </ul>
                </div>
                <div class="mb-4">
                    <button class="btn btn-outline-secondary w-100" id="btnDownloadTemplate">
                        <i class="ti ti-download me-2"></i>Download Template Excel
                    </button>
                    <small class="text-muted mt-1 d-block text-center">
                        <i class="ti ti-info-circle me-1"></i>Template dengan header (baris pertama adalah judul kolom)
                    </small>
                </div>
                <form id="importForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="ti ti-file-excel me-1 text-success"></i>Pilih File Excel
                        </label>
                        <input type="file" class="form-control" id="importFile" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted mt-1 d-block">
                            <i class="ti ti-alert-circle me-1"></i>Pilih file yang akan diimport (pastikan format sesuai template)
                        </small>
                    </div>
                    <div id="importProgress" style="display: none;">
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <p class="text-muted small text-center" id="progressStatus">
                            <i class="ti ti-loader me-1"></i>Memproses data...
                        </p>
                    </div>
                    <div id="previewSection" style="display: none;">
                        <hr class="my-3">
                        <h6 class="fw-semibold mb-2"><i class="ti ti-eye me-1"></i>Preview Data</h6>
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
                                        <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ti ti-x me-1"></i>Batal</button>
                <button type="button" class="btn btn-success" id="btnImportSubmit" disabled><i class="ti ti-upload me-1"></i>Import Data</button>
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
<script src="../../assets/local/moment.min.js"></script>
<script src="../../assets/local/select2.min.js"></script>
<!-- FixedColumns DataTables -->
<script src="../../assets/local/dataTables.fixedColumns.min.js"></script>
<link rel="stylesheet" href="../../assets/local/fixedColumns.bootstrap5.min.css">

<script>
    let reportTable;
    let baseUrl = 'DataDefectReportController.php';

    const EDITABLE_COLS = {
        3: 'lotno',
        4: 'nama_group',
        5: 'qty',
        6: 'aksi_claim_defect',
        8: 'nama_operator_pengambil',
        9: 'tanggal_pengambilan'
    };

    // VARIABEL FILTER PER TAB
    let filters = {
        tanggalPartNo: {
            aktif: false,
            tanggal: null,
            partnos: []
        },
        lot: {
            aktif: false,
            lotNos: []
        },
        customer: {
            aktif: false,
            customers: [],
            tanggalAwal: null,
            tanggalAkhir: null
        },
        partNoRange: {
            aktif: false,
            partnos: [],
            tanggalAwal: null,
            tanggalAkhir: null
        }
    };

    let activeTab = 'tanggalPartNo';

    // CEK apakah sedang ada input/edit yang aktif di tabel
    function isAnyEditActive() {
        return $('#reportTable .inline-edit').length > 0;
    }

    // SIMPAN semua edit yang aktif (panggil sebelum edit baru)
    function saveCurrentEditIfAny() {
        let $activeInput = $('#reportTable .inline-edit');
        if ($activeInput.length === 0) return false;

        let $input = $activeInput.first();
        let field = $input.data('field');
        let id = $input.data('id');
        let newValue = $input.val();
        let $cell = $input.closest('td');
        let row = reportTable.row($cell.closest('tr'));
        let rowData = row.data();

        if (!rowData) return false;

        // Validasi

        if (field === 'qty') {
            let qty = parseInt(newValue);
            if (isNaN(qty) || qty < 0) {
                showToast('Peringatan', 'QTY harus berupa angka positif', 'warning', 1500);
                return false;
            }
            newValue = qty;
        }

        // Siapkan payload
        let payload = {
            id: id,
            lotno: field === 'lotno' ? newValue : (rowData.lotno || ''),
            aksi_claim_defect: field === 'aksi_claim_defect' ? newValue : (rowData.aksi_claim_defect || ''),
            nama_group: field === 'nama_group' ? newValue : (rowData.nama_group || ''),
            qty: field === 'qty' ? newValue : (rowData.qty || ''),
            nama_operator_pengambil: field === 'nama_operator_pengambil' ? newValue : (rowData.nama_operator_pengambil || ''),
            tanggal_pengambilan: field === 'tanggal_pengambilan' ? newValue : (rowData.tanggal_pengambilan ? rowData.tanggal_pengambilan.split(' ')[0] : '')
        };

        // Simpan via AJAX (synchronous biar aman)
        let saved = false;
        $.ajax({
            url: 'UpdateDefectReportController.php?action=update',
            type: 'POST',
            data: payload,
            dataType: 'json',
            async: false,
            timeout: 10000,
            success: function(response) {
                if (response.status === 'success') {
                    let scrollTop = $(window).scrollTop(); // ← simpan posisi
                    rowData[field] = newValue !== '' ? newValue : null;
                    delete rowData._editingCells[field];
                    row.data(rowData).draw(false);
                    $(window).scrollTop(scrollTop); // ← restore posisi
                    showToast('Tersimpan', 'Nilai berhasil disimpan', 'success', 1000);
                    saved = true;
                } else {
                    showToast('Gagal', response.message || 'Gagal menyimpan', 'error', 1500);
                }
            },
            error: function() {
                showToast('Error', 'Gagal menyimpan data', 'error', 1500);
            }
        });

        return saved;
    }

    $(document).ready(function() {
        initializeTable();
        showEmptyInitialState();

        $('#closeFilterInfo').on('click', function() {
            $('#filterInfo').fadeOut();
        });

        // TAB 1
        $('#filterTanggal').on('change', function() {
            let tanggal = $(this).val();
            if (tanggal) loadPartNoByTanggal(tanggal);
            else {
                $('#filterPartNoByTanggal').html('<option value="">Pilih tanggal terlebih dahulu</option>').prop('disabled', true);
                $('#btnApplyTanggalPartNo').prop('disabled', true);
            }
        });
        $('#filterPartNoByTanggal').on('change', function() {
            $('#btnApplyTanggalPartNo').prop('disabled', !$('#filterTanggal').val());
        });
        $('#btnApplyTanggalPartNo').on('click', function() {
            applyTanggalPartNoFilter();
        });

        // TAB 2
        $('#filterLotNo').on('change', function() {
            if ($(this).val().trim() !== '') applyLotFilter();
        });

        // TAB 3
        $('#filterCustomer').on('change', function() {
            let selected = $(this).val();
            if (selected && selected.length > 0) {
                $('#customerTanggalAwal, #customerTanggalAkhir, #btnClearCustomerRange').prop('disabled', false);
            } else {
                $('#customerTanggalAwal, #customerTanggalAkhir, #btnClearCustomerRange').prop('disabled', true);
                $('#customerTanggalAwal, #customerTanggalAkhir').val('');
            }
        });
        $('#btnClearCustomerRange').on('click', function() {
            $('#customerTanggalAwal, #customerTanggalAkhir').val('');
            showToast('Range Dihapus', 'Filter range tanggal customer dihapus', 'info', 1500);
        });

        // TAB 4
        $('#filterPartNo').on('change', function() {
            let selected = $(this).val();
            if (selected && selected.length > 0) {
                $('#partNoTanggalAwal, #partNoTanggalAkhir, #btnClearPartNoRange').prop('disabled', false);
            } else {
                $('#partNoTanggalAwal, #partNoTanggalAkhir, #btnClearPartNoRange').prop('disabled', true);
                $('#partNoTanggalAwal, #partNoTanggalAkhir').val('');
            }
        });
        $('#btnClearPartNoRange').on('click', function() {
            $('#partNoTanggalAwal, #partNoTanggalAkhir').val('');
            showToast('Range Dihapus', 'Filter range tanggal part no dihapus', 'info', 1500);
        });

        // TAB SWITCH
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            let targetId = $(e.target).attr('id');
            resetAllFiltersSilent();
            switch (targetId) {
                case 'filter-tanggal-tab':
                    activeTab = 'tanggalPartNo';
                    break;
                case 'filter-lot-tab':
                    activeTab = 'lot';
                    break;
                case 'filter-customer-tab':
                    activeTab = 'customer';
                    loadCustomerOptions();
                    break;
                case 'filter-partno-tab':
                    activeTab = 'partNoRange';
                    loadPartNoOptions();
                    break;
            }
            showEmptyInitialState();
        });
    });

    function initializeTable() {
        reportTable = $('#reportTable').DataTable({
            columns: [{
                    data: null,
                    width: '5px',
                    className: 'text-center',
                    render: function() {
                        return '';
                    }
                },
                {
                    data: 'tanggal_ditemukan',
                    className: 'text-nowrap',
                    render: function(data) {
                        return data ? formatDate(data) : '-';
                    }
                },
                {
                    data: 'partno',
                    render: function(data) {
                        return escapeHtml(data) || '-';
                    }
                },
                {
                    data: 'lotno',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['lotno']) {
                            return `<input type="text" class="form-control form-control-sm inline-edit" value="${escapeHtml(data || '')}" data-field="lotno" data-id="${row.id}" placeholder="Lot No">`;
                        }
                        return escapeHtml(data) || '-';
                    }
                },
                {
                    data: 'nama_group',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['nama_group']) {
                            return `<input type="text" class="form-control form-control-sm inline-edit" value="${escapeHtml(data || '')}" data-field="nama_group" data-id="${row.id}" placeholder="Nama Group">`;
                        }
                        return escapeHtml(data) || '-';
                    }
                },
                {
                    data: 'qty',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['qty']) {
                            return `<input type="number" class="form-control form-control-sm inline-edit text-end" value="${data || ''}" data-field="qty" data-id="${row.id}" min="0" placeholder="0">`;
                        }
                        return data !== null ? data : '-';
                    }
                },
                {
                    data: 'aksi_claim_defect',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['aksi_claim_defect']) {
                            return `<select class="form-select form-select-sm inline-edit" data-field="aksi_claim_defect" data-id="${row.id}">
                            <option value="">-- Pilih --</option>
                            <option value="Repair" ${data === 'Repair' ? 'selected' : ''}>Repair</option>
                            <option value="Scrap" ${data === 'Scrap' ? 'selected' : ''}>Scrap</option>
                        </select>`;
                        }
                        if (!data) return '-';
                        if (data === 'Repair') return `<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Repair</span>`;
                        if (data === 'Scrap') return `<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Scrap</span>`;
                        return escapeHtml(data);
                    }
                },
                {
                    data: 'status',
                    width: '100px',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let isChecked = (data == 1 || data === true || data === '1');
                            let statusText = isChecked ? 'OK' : 'NG';
                            let statusClass = isChecked ? 'success' : 'danger';
                            return `<div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="form-check form-switch m-0">
                                <input type="checkbox" class="form-check-input status-toggle" data-id="${row.id}" ${isChecked ? 'checked' : ''} style="cursor: pointer; width: 44px; height: 22px; margin: 0;">
                            </div>
                            <span class="badge bg-${statusClass} bg-opacity-10 text-${statusClass} px-2 py-1 rounded-pill" style="min-width: 45px;">${statusText}</span>
                        </div>`;
                        }
                        return data;
                    }
                },
                {
                    data: 'nama_operator_pengambil',
                    width: '150px',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['nama_operator_pengambil']) {
                            return `<input type="text" class="form-control form-control-sm inline-edit" value="${escapeHtml(data || '')}" data-field="nama_operator_pengambil" data-id="${row.id}" placeholder="Nama operator">`;
                        }
                        return data ? escapeHtml(data) : '-';
                    }
                },
                {
                    data: 'tanggal_pengambilan',
                    width: '120px',
                    className: 'text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'display' && row._editingCells && row._editingCells['tanggal_pengambilan']) {
                            let dateValue = data ? data.split(' ')[0] : '';
                            return `<input type="date" class="form-control form-control-sm inline-edit" value="${dateValue}" data-field="tanggal_pengambilan" data-id="${row.id}">`;
                        }
                        return data ? formatDate(data) : '-';
                    }
                },
                {
                    data: null,
                    width: '80px',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-info" onclick="showDetail(${row.id})" title="Lihat Detail"><i class="ti ti-eye"></i></button>`;
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
            scrollY: '60vh', // ← tambahkan ini (header sticky)
            scrollCollapse: true, // ← tambahkan ini
            fixedHeader: true, // ← tambahkan ini (sticky header)
            responsive: false,
            fixedColumns: { // ← frozen 4 kolom kiri (No, Tanggal, Part No, Lot No)
                left: 4
            },
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
                if (data.length > 0) updateSummary(data);
            }
        });
    }

    // ==================== INLINE EDITING ====================

    // Double klik - mulai edit, TAPI cek dulu apakah sedang ada edit aktif
    $(document).on('dblclick', '#reportTable tbody td', function(e) {
        e.preventDefault(); // ← tambahkan ini
        e.stopPropagation(); // ← tambahkan ini

        if (isAnyEditActive()) {
            showToast('Peringatan', 'Selesaikan edit sebelumnya dulu', 'warning', 1500);
            return;
        }

        let $td = $(this);
        let colIdx = $td.index();
        let field = EDITABLE_COLS[colIdx];
        if (!field) return;

        let row = reportTable.row($td.closest('tr'));
        let rowData = row.data();
        if (!rowData) return;

        // Simpan posisi scroll sebelum draw
        let scrollTop = $(window).scrollTop(); // ← tambahkan ini

        if (!rowData._editingCells) rowData._editingCells = {};
        if (rowData._editingCells[field]) return;

        rowData._editingCells[field] = true;
        row.data(rowData).draw(false);

        // Restore scroll position setelah draw
        $(window).scrollTop(scrollTop); // ← tambahkan ini

        setTimeout(function() {
            let $input = $(`[data-field="${field}"][data-id="${rowData.id}"]`);
            if ($input.length) {
                // Gunakan preventScroll agar focus tidak scroll otomatis
                $input[0].focus({
                    preventScroll: true
                }); // ← ubah ini
                if ($input.is('input[type="text"], input[type="number"]')) $input.select();
            }
        }, 50);
    });

    // Enter = simpan
    $(document).on('keydown', '.inline-edit', function(e) {
        if (e.which === 13) { // Enter
            e.preventDefault();
            e.stopPropagation();
            saveCurrentEditIfAny();
        }
        if (e.which === 27) { // ESC = batal
            e.preventDefault();
            e.stopPropagation();
            let $input = $(this);
            let field = $input.data('field');
            let id = $input.data('id');
            let table = $('#reportTable').DataTable();
            table.rows().every(function() {
                let d = this.data();
                if (d.id == id && d._editingCells && d._editingCells[field]) {
                    delete d._editingCells[field];
                    this.data(d).draw(false);
                }
            });
        }
    });

    // Blur = simpan
    $(document).on('blur', '.inline-edit', function() {
        setTimeout(function() {
            saveCurrentEditIfAny();
        }, 200);
    });

    // ==================== END INLINE EDITING ====================

    function showEmptyInitialState() {
        $('#loadingSpinner').fadeOut();
        $('#filterInfo').hide();
        if (reportTable) reportTable.clear().draw();

        let message = '';
        switch (activeTab) {
            case 'tanggalPartNo':
                message = 'Silakan pilih tanggal dan part number, lalu klik Terapkan';
                break;
            case 'lot':
                message = 'Silakan scan atau ketik nomor lot (scan langsung terapkan)';
                break;
            case 'customer':
                message = 'Silakan pilih customer (bisa pilih range tanggal opsional)';
                break;
            case 'partNoRange':
                message = 'Silakan pilih part number (bisa pilih range tanggal opsional)';
                break;
        }
        $('#reportTable tbody').html(`<tr><td colspan="9" class="text-center py-5"><i class="ti ti-filter" style="font-size: 3rem; color: #dee2e6;"></i><p class="mt-3 text-muted">${message}</p></td></tr>`);
    }

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
                        if (customerName && customerName.trim() !== '') options += `<option value="${escapeHtml(customerName)}">${escapeHtml(customerName)}</option>`;
                    });
                    $('#filterCustomer').html(options);
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

    function loadPartNoOptions() {
        $.ajax({
            url: baseUrl + '?action=getPartNoOptions',
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#filterPartNo').html('<option value="">Memuat data...</option>');
            },
            success: function(response) {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    let options = '';
                    response.data.forEach(item => {
                        let partno = item.partno || '';
                        if (partno && partno.trim() !== '') options += `<option value="${escapeHtml(partno)}">${escapeHtml(partno)}</option>`;
                    });
                    $('#filterPartNo').html(options);
                    $('#filterPartNo').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Pilih part number...',
                        allowClear: true,
                        width: '100%'
                    });
                } else {
                    $('#filterPartNo').html('<option value="">Tidak ada data part number</option>');
                    $('#filterPartNo').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Tidak ada data',
                        disabled: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr) {
                console.error('Gagal load part no options:', xhr);
                $('#filterPartNo').html('<option value="">Gagal memuat data</option>');
                $('#filterPartNo').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Gagal memuat data',
                    disabled: true,
                    width: '100%'
                });
            }
        });
    }

    function loadPartNoByTanggal(tanggal) {
        $.ajax({
            url: baseUrl + '?action=getPartNoByTanggal&tanggal=' + tanggal,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#filterPartNoByTanggal').html('<option value="">Memuat data...</option>').prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    let options = '';
                    response.data.forEach(item => {
                        let partno = item.partno || '';
                        if (partno && partno.trim() !== '') options += `<option value="${escapeHtml(partno)}">${escapeHtml(partno)}</option>`;
                    });
                    $('#filterPartNoByTanggal').html(options).prop('disabled', false);
                    $('#filterPartNoByTanggal').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Pilih part number...',
                        allowClear: true,
                        width: '100%'
                    });
                    $('#btnApplyTanggalPartNo').prop('disabled', false);
                } else {
                    $('#filterPartNoByTanggal').html('<option value="">Tidak ada part number untuk tanggal ini</option>').prop('disabled', true);
                    $('#filterPartNoByTanggal').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Tidak ada data',
                        disabled: true,
                        width: '100%'
                    });
                    $('#btnApplyTanggalPartNo').prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error('Gagal load part no by tanggal:', xhr);
                $('#filterPartNoByTanggal').html('<option value="">Gagal memuat data</option>').prop('disabled', true);
            }
        });
    }

    function applyTanggalPartNoFilter() {
        let tanggal = $('#filterTanggal').val();
        let partnos = $('#filterPartNoByTanggal').val() || [];
        if (!tanggal) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih tanggal terlebih dahulu'
            });
            return;
        }
        if (partnos.length === 0) showToast('Info', 'Filter hanya berdasarkan tanggal (semua part number)', 'info', 2000);
        filters.tanggalPartNo = {
            aktif: true,
            tanggal: tanggal,
            partnos: partnos
        };
        showToast('Filter Diterapkan', `Tanggal: ${formatDate(tanggal)} | ${partnos.length} part no dipilih`, 'info', 2000);
        loadReportsByTab('tanggalPartNo');
    }

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
        filters.lot = {
            aktif: true,
            lotNos: lotInput.split(',').map(item => item.trim()).filter(item => item !== '')
        };
        loadReportsByTab('lot');
    }

    function clearLotFilter() {
        filters.lot = {
            aktif: false,
            lotNos: []
        };
        $('#filterLotNo').val('');
        if (activeTab === 'lot') showEmptyInitialState();
        Swal.fire({
            icon: 'info',
            title: 'Filter Lot Dihapus',
            text: 'Filter lot telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function applyCustomerFilter() {
        let selectedCustomers = $('#filterCustomer').val() || [];
        let tanggalAwal = $('#customerTanggalAwal').val();
        let tanggalAkhir = $('#customerTanggalAkhir').val();
        if (selectedCustomers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Customer',
                text: 'Silakan pilih minimal 1 customer'
            });
            return;
        }
        if (tanggalAwal && tanggalAkhir && tanggalAwal > tanggalAkhir) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            });
            return;
        }
        filters.customer = {
            aktif: true,
            customers: selectedCustomers,
            tanggalAwal: tanggalAwal || null,
            tanggalAkhir: tanggalAkhir || null
        };
        let msg = `${selectedCustomers.length} customer dipilih`;
        if (tanggalAwal && tanggalAkhir) msg += ` | Range: ${formatDate(tanggalAwal)} - ${formatDate(tanggalAkhir)}`;
        else if (tanggalAwal) msg += ` | Mulai: ${formatDate(tanggalAwal)}`;
        else if (tanggalAkhir) msg += ` | Sampai: ${formatDate(tanggalAkhir)}`;
        showToast('Filter Diterapkan', msg, 'info', 2000);
        loadReportsByTab('customer');
    }

    function clearCustomerFilter() {
        filters.customer = {
            aktif: false,
            customers: [],
            tanggalAwal: null,
            tanggalAkhir: null
        };
        $('#filterCustomer').val([]).trigger('change');
        $('#customerTanggalAwal, #customerTanggalAkhir').val('').prop('disabled', true);
        $('#btnClearCustomerRange').prop('disabled', true);
        if (activeTab === 'customer') showEmptyInitialState();
        Swal.fire({
            icon: 'info',
            title: 'Filter Customer Dihapus',
            text: 'Filter customer telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function applyPartNoFilter() {
        let selectedPartNos = $('#filterPartNo').val() || [];
        let tanggalAwal = $('#partNoTanggalAwal').val();
        let tanggalAkhir = $('#partNoTanggalAkhir').val();
        if (selectedPartNos.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Part Number',
                text: 'Silakan pilih minimal 1 part number'
            });
            return;
        }
        if (tanggalAwal && tanggalAkhir && tanggalAwal > tanggalAkhir) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            });
            return;
        }
        filters.partNoRange = {
            aktif: true,
            partnos: selectedPartNos,
            tanggalAwal: tanggalAwal || null,
            tanggalAkhir: tanggalAkhir || null
        };
        let msg = `${selectedPartNos.length} part no dipilih`;
        if (tanggalAwal && tanggalAkhir) msg += ` | Range: ${formatDate(tanggalAwal)} - ${formatDate(tanggalAkhir)}`;
        else if (tanggalAwal) msg += ` | Mulai: ${formatDate(tanggalAwal)}`;
        else if (tanggalAkhir) msg += ` | Sampai: ${formatDate(tanggalAkhir)}`;
        showToast('Filter Diterapkan', msg, 'info', 2000);
        loadReportsByTab('partNoRange');
    }

    function clearPartNoFilter() {
        filters.partNoRange = {
            aktif: false,
            partnos: [],
            tanggalAwal: null,
            tanggalAkhir: null
        };
        $('#filterPartNo').val([]).trigger('change');
        $('#partNoTanggalAwal, #partNoTanggalAkhir').val('').prop('disabled', true);
        $('#btnClearPartNoRange').prop('disabled', true);
        if (activeTab === 'partNoRange') showEmptyInitialState();
        Swal.fire({
            icon: 'info',
            title: 'Filter Part No Dihapus',
            text: 'Filter part number telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function setSingleQuickFilter(type) {
        let today = moment();
        let tanggal;
        switch (type) {
            case 'today':
                tanggal = today.format('YYYY-MM-DD');
                break;
            case 'yesterday':
                tanggal = today.subtract(1, 'days').format('YYYY-MM-DD');
                break;
        }

        // 🔥 Panggil loadPartNoByTanggal dengan callback
        loadPartNoByTanggalWithCallback(tanggal, function() {
            // Setelah part no selesai load, langsung apply filter
            applyTanggalPartNoFilter();
        });

        $('#filterTanggal').val(tanggal).trigger('change');
        showToast('Quick Filter', `Tanggal: ${formatDate(tanggal)}`, 'info', 1500);
    }

    // 🔥 MODIFIKASI loadPartNoByTanggal dengan callback
    function loadPartNoByTanggalWithCallback(tanggal, callback) {
        $.ajax({
            url: baseUrl + '?action=getPartNoByTanggal&tanggal=' + tanggal,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#filterPartNoByTanggal').html('<option value="">Memuat data...</option>').prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    let options = '';
                    response.data.forEach(item => {
                        let partno = item.partno || '';
                        if (partno && partno.trim() !== '') options += `<option value="${escapeHtml(partno)}">${escapeHtml(partno)}</option>`;
                    });
                    $('#filterPartNoByTanggal').html(options).prop('disabled', false);
                    $('#filterPartNoByTanggal').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Pilih part number...',
                        allowClear: true,
                        width: '100%'
                    });
                    $('#btnApplyTanggalPartNo').prop('disabled', false);
                } else {
                    $('#filterPartNoByTanggal').html('<option value="">Tidak ada part number untuk tanggal ini</option>').prop('disabled', true);
                    $('#filterPartNoByTanggal').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Tidak ada data',
                        disabled: true,
                        width: '100%'
                    });
                    $('#btnApplyTanggalPartNo').prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error('Gagal load part no by tanggal:', xhr);
                $('#filterPartNoByTanggal').html('<option value="">Gagal memuat data</option>').prop('disabled', true);
                $('#btnApplyTanggalPartNo').prop('disabled', false);
            },
            complete: function() {
                if (callback && typeof callback === 'function') {
                    callback();
                }
            }
        });
    }

    function loadReportsByTab(tab) {
        $('#loadingSpinner').fadeIn();
        let url = baseUrl + '?action=getReports';
        let params = [];
        switch (tab) {
            case 'tanggalPartNo':
                if (filters.tanggalPartNo.aktif && filters.tanggalPartNo.tanggal) {
                    params.push('tanggal=' + filters.tanggalPartNo.tanggal);
                    if (filters.tanggalPartNo.partnos.length > 0) params.push('partno=' + encodeURIComponent(filters.tanggalPartNo.partnos.join(',')));
                }
                break;
            case 'lot':
                if (filters.lot.aktif && filters.lot.lotNos.length > 0) params.push('lot_nos=' + encodeURIComponent(filters.lot.lotNos.join(',')));
                break;
            case 'customer':
                if (filters.customer.aktif && filters.customer.customers.length > 0) {
                    params.push('customers=' + encodeURIComponent(filters.customer.customers.join(',')));
                    if (filters.customer.tanggalAwal) params.push('tanggal_awal=' + filters.customer.tanggalAwal);
                    if (filters.customer.tanggalAkhir) params.push('tanggal_akhir=' + filters.customer.tanggalAkhir);
                }
                break;
            case 'partNoRange':
                if (filters.partNoRange.aktif && filters.partNoRange.partnos.length > 0) {
                    params.push('partnos=' + encodeURIComponent(filters.partNoRange.partnos.join(',')));
                    if (filters.partNoRange.tanggalAwal) params.push('tanggal_awal_part=' + filters.partNoRange.tanggalAwal);
                    if (filters.partNoRange.tanggalAkhir) params.push('tanggal_akhir_part=' + filters.partNoRange.tanggalAkhir);
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
                    if (response.data.length === 0) showEmptyResult(response, tab);
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

    function resetAllFiltersSilent() {
        filters = {
            tanggalPartNo: {
                aktif: false,
                tanggal: null,
                partnos: []
            },
            lot: {
                aktif: false,
                lotNos: []
            },
            customer: {
                aktif: false,
                customers: [],
                tanggalAwal: null,
                tanggalAkhir: null
            },
            partNoRange: {
                aktif: false,
                partnos: [],
                tanggalAwal: null,
                tanggalAkhir: null
            }
        };
        $('#filterTanggal').val('');
        $('#filterPartNoByTanggal').html('<option value="">Pilih tanggal terlebih dahulu</option>').prop('disabled', true);
        if ($('#filterPartNoByTanggal').hasClass('select2-hidden-accessible')) $('#filterPartNoByTanggal').select2('destroy');
        $('#btnApplyTanggalPartNo').prop('disabled', true);
        $('#filterLotNo').val('');
        $('#filterCustomer').val([]).trigger('change');
        $('#customerTanggalAwal, #customerTanggalAkhir').val('').prop('disabled', true);
        $('#btnClearCustomerRange').prop('disabled', true);
        $('#filterPartNo').val([]).trigger('change');
        $('#partNoTanggalAwal, #partNoTanggalAkhir').val('').prop('disabled', true);
        $('#btnClearPartNoRange').prop('disabled', true);
    }

    function resetAllFilters() {
        resetAllFiltersSilent();
        showEmptyInitialState();
        Swal.fire({
            icon: 'success',
            title: 'Semua Filter Direset',
            text: 'Semua filter telah dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function showEmptyResult(response, tab) {
        let message = 'Tidak ada data untuk ditampilkan';
        switch (tab) {
            case 'tanggalPartNo':
                if (response.filter && response.filter.tanggal) message = `Tidak ada laporan untuk tanggal ${formatDate(response.filter.tanggal)} dengan part no yang dipilih`;
                break;
            case 'lot':
                message = 'Tidak ada laporan untuk lot yang dipilih';
                break;
            case 'customer':
                message = 'Tidak ada laporan untuk customer yang dipilih';
                break;
            case 'partNoRange':
                message = 'Tidak ada laporan untuk part number yang dipilih';
                break;
        }
        $('#reportTable tbody').html(`<tr><td colspan="9" class="text-center py-5"><i class="ti ti-database-off" style="font-size: 3rem; color: #dee2e6;"></i><p class="mt-3 text-muted">${message}</p></td></tr>`);
    }

    function showDetail(id) {
        $('#detailLoading').show();
        $('#detailContent').hide();
        $('.detail-editable').removeClass('editing saving save-success').removeAttr('style').html('-');
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
                    $('.detail-editable').attr('data-id', data.id);
                    $('#detailId').text(data.id || '-');
                    $('#detailTanggal').text(formatDate(data.tanggal_ditemukan) || '-');
                    $('#detailSection').text(escapeHtml(data.nama_section) || '-');
                    $('#detailShift').text(escapeHtml(data.shift) || '-');
                    $('#detailDefect').text(data.nama_defect || '-');
                    $('#detailPartNo').text(escapeHtml(data.partno) || '-');
                    $('#detailCustomer').text(escapeHtml(data.nama_customer) || '-');
                    $('#detailOperator').text(escapeHtml(data.nama_operator) || '-');
                    $('#detailDeskripsi').text(escapeHtml(data.deskripsi_masalah) || '-');
                    $('#detailLotNo').text(escapeHtml(data.lotno) || '-');
                    $('#detailGroup').text(escapeHtml(data.nama_group) || '-');
                    $('#detailQty').text(data.qty || '0');
                    if (data.aksi_claim_defect) {
                        let badgeClass = data.aksi_claim_defect === 'Repair' ? 'bg-primary' : 'bg-danger';
                        let textClass = data.aksi_claim_defect === 'Repair' ? 'primary' : 'danger';
                        $('#detailAksiClaim').html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(data.aksi_claim_defect)}</span>`);
                    } else $('#detailAksiClaim').text('-');
                    $('#detailOperatorPengambil').text(escapeHtml(data.nama_operator_pengambil) || '-');
                    $('#detailTanggalPengambilan').text(formatDate(data.tanggal_pengambilan) || '-');
                    $('#detailCreatedAt').text(formatDateTime(data.created_at) || '-');
                    let statusValue = data.status == 1 || data.status === true || data.status === '1';
                    let $statusToggle = $('#detailStatusToggle');
                    let $statusBadge = $('#detailStatusBadge');
                    $statusToggle.prop('checked', statusValue);
                    updateStatusBadge($statusBadge, statusValue);
                    $statusToggle.data('id', data.id);
                    $statusToggle.off('change').on('change', function() {
                        let isChecked = $(this).is(':checked');
                        let id = $(this).data('id');
                        updateStatusBadge($statusBadge, isChecked);
                        saveStatusEdit(id, isChecked ? 1 : 0);
                    });
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

    // Detail modal edit handlers
    $(document).on('dblclick', '.detail-editable', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let $this = $(this);
        let field = $this.data('field');
        let id = $this.data('id');
        if ($this.hasClass('editing') || isSaving) return;
        let currentValue = $this.text().trim();
        if (field === 'aksi_claim_defect') {
            let badgeText = $this.find('.badge').text().trim();
            if (badgeText) currentValue = badgeText;
        }
        if (field === 'tanggal_pengambilan') {
            currentValue = $this.text().trim();
            if (currentValue !== '-' && currentValue !== '' && currentValue.includes('/')) {
                let parts = currentValue.split('/');
                if (parts.length === 3) currentValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
            } else currentValue = '';
        }
        currentValue = currentValue.replace('✎', '').trim();
        if (currentValue === '-' || currentValue === '') currentValue = '';
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
        $this.addClass('editing').html(inputHtml);
        setTimeout(function() {
            $this.find('input, select').focus();
        }, 100);
    });

    let isSaving = false;
    $(document).on('blur', '.detail-edit-input, .detail-edit-select', function(e) {
        let $this = $(this);
        let $parent = $this.closest('.detail-editable');
        if (!$parent.hasClass('editing')) return;
        let field = $this.data('field');
        let id = $this.data('id');
        let value = $this.val();
        setTimeout(function() {
            if ($parent.hasClass('editing') && !isSaving) saveDetailEdit(field, id, value, $parent);
        }, 300);
    });

    $(document).on('keypress', '.detail-edit-input, .detail-edit-select', function(e) {
        if (e.which === 13) {
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

    $(document).on('keydown', '.detail-edit-input, .detail-edit-select', function(e) {
        if (e.which === 27) {
            e.preventDefault();
            e.stopPropagation();
            let $this = $(this);
            let $parent = $this.closest('.detail-editable');
            let field = $this.data('field');
            let id = $this.data('id');
            cancelDetailEdit(field, id, $parent);
        }
    });

    // ==================== PERBAIKAN DI FUNGSI saveDetailEdit ====================
    function saveDetailEdit(field, id, value, $element) {
        if (isSaving) return;
        isSaving = true;

        if (!$element || !$element.length) $element = $(`.detail-editable[data-field="${field}"][data-id="${id}"]`);

        // Validasi nama_operator_pengambil bisa null
        if (field === 'nama_operator_pengambil') {
            if (value === '') value = null; // ✅ Simpan sebagai NULL di database
        }

        // Validasi qty
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

        // 🔥 PERBAIKAN UNTUK TANGGAL PENGAMBILAN
        if (field === 'tanggal_pengambilan') {
            // Jika value kosong/null/undefined, set ke null (artinya hapus tanggal)
            if (!value || value.trim() === '') {
                value = null; // ✅ Simpan sebagai NULL di database
            } else {
                // Validasi format YYYY-MM-DD
                if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
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
            }
        }

        $element.addClass('saving').append('<small class="saving-indicator">Menyimpan...</small>');

        let table = $('#reportTable').DataTable();
        let rowData = null;
        table.rows().every(function() {
            if (this.data().id == id) rowData = this.data();
        });

        if (!rowData) {
            $element.removeClass('saving').find('.saving-indicator').remove();
            Swal.fire('Error', 'Data tidak ditemukan', 'error');
            cancelDetailEdit(field, id, $element);
            isSaving = false;
            return;
        }

        // 🔥 PERBAIKAN: Kirim tanggal_pengambilan dengan nilai yang benar (bisa NULL)
        let payload = {
            id: id,
            lotno: field === 'lotno' ? value : (rowData.lotno || ''),
            aksi_claim_defect: field === 'aksi_claim_defect' ? value : (rowData.aksi_claim_defect || ''),
            nama_group: field === 'nama_group' ? value : (rowData.nama_group || ''),
            qty: field === 'qty' ? value : (rowData.qty || 0),
            nama_operator_pengambil: field === 'nama_operator_pengambil' ? value : (rowData.nama_operator_pengambil || null),
            tanggal_pengambilan: field === 'tanggal_pengambilan' ? value : (rowData.tanggal_pengambilan || null)
        };

        $.ajax({
            url: 'UpdateDefectReportController.php?action=update',
            type: 'POST',
            data: payload,
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                $element.removeClass('saving').find('.saving-indicator').remove();
                if (response.status === 'success') {
                    // 🔥 PERBAIKAN: Update display dengan nilai yang benar
                    updateDetailDisplay(field, value, $element);
                    updateTableRow(id, field, value);
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
                let errorMsg = 'Gagal menyimpan data';
                if (status === 'timeout') errorMsg = 'Timeout, silakan coba lagi';
                else if (xhr.responseText) {
                    try {
                        let resp = JSON.parse(xhr.responseText);
                        if (resp.message) errorMsg = resp.message;
                    } catch (e) {}
                }
                Swal.fire('Error', errorMsg, 'error');
                cancelDetailEdit(field, id, $element);
                isSaving = false;
            }
        });
    }

    // 🔥 PERBAIKAN DI FUNGSI updateDetailDisplay
    function updateDetailDisplay(field, value, $element) {
        if (field === 'aksi_claim_defect' && value) {
            let badgeClass = value === 'Repair' ? 'bg-primary' : 'bg-danger';
            let textClass = value === 'Repair' ? 'primary' : 'danger';
            $element.html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(value)}</span>`);
        } else if (field === 'tanggal_pengambilan') {
            // 🔥 PERBAIKAN: Kalo value null/empty, tampilkan "-"
            if (!value || value === '' || value === null) {
                $element.text('-');
            } else {
                $element.text(formatDate(value) || '-');
            }
        } else {
            $element.text(value || '-');
        }
        $element.removeClass('editing');
    }

    // 🔥 PERBAIKAN DI FUNGSI cancelDetailEdit
    function cancelDetailEdit(field, id, $element) {
        if (!$element || !$element.length) $element = $(`.detail-editable[data-field="${field}"][data-id="${id}"]`);

        let table = $('#reportTable').DataTable();
        let originalValue = null;
        table.rows().every(function() {
            if (this.data().id == id) originalValue = this.data()[field];
        });

        if (field === 'aksi_claim_defect' && originalValue) {
            let badgeClass = originalValue === 'Repair' ? 'bg-primary' : 'bg-danger';
            let textClass = originalValue === 'Repair' ? 'primary' : 'danger';
            $element.html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(originalValue)}</span>`);
        } else if (field === 'tanggal_pengambilan') {
            // 🔥 PERBAIKAN: Kalo originalValue null/empty, tampilkan "-"
            if (!originalValue || originalValue === '' || originalValue === null) {
                $element.text('-');
            } else {
                $element.text(formatDate(originalValue) || '-');
            }
        } else {
            $element.text(originalValue || '-');
        }

        $element.removeClass('editing saving');
        $element.find('.saving-indicator').remove();
    }

    function formatDateForServer(dateStr) {
        if (!dateStr) return '';
        if (typeof dateStr === 'string' && dateStr.match(/^\d{4}-\d{2}-\d{2}/)) return dateStr;
        if (typeof dateStr === 'string' && dateStr.includes('/')) {
            let parts = dateStr.split('/');
            if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        return dateStr;
    }

    function cancelDetailEdit(field, id, $element) {
        if (!$element || !$element.length) $element = $(`.detail-editable[data-field="${field}"][data-id="${id}"]`);
        let table = $('#reportTable').DataTable();
        let originalValue = null;
        table.rows().every(function() {
            if (this.data().id == id) originalValue = this.data()[field];
        });
        if (field === 'aksi_claim_defect' && originalValue) {
            let badgeClass = originalValue === 'Repair' ? 'bg-primary' : 'bg-danger';
            let textClass = originalValue === 'Repair' ? 'primary' : 'danger';
            $element.html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(originalValue)}</span>`);
        } else if (field === 'tanggal_pengambilan') {
            $element.text(formatDate(originalValue) || '-');
        } else $element.text(originalValue || '-');
        $element.removeClass('editing saving');
        $element.find('.saving-indicator').remove();
    }

    function updateDetailDisplay(field, value, $element) {
        if (field === 'aksi_claim_defect' && value) {
            let badgeClass = value === 'Repair' ? 'bg-primary' : 'bg-danger';
            let textClass = value === 'Repair' ? 'primary' : 'danger';
            $element.html(`<span class="badge ${badgeClass} bg-opacity-10 text-${textClass} px-3 py-2 rounded-pill">${escapeHtml(value)}</span>`);
        } else if (field === 'tanggal_pengambilan') $element.text(formatDate(value) || '-');
        else $element.text(value || '-');
        $element.removeClass('editing');
    }

    function updateTableRow(id, field, value) {
        let scrollTop = $(window).scrollTop();
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

    $(document).on('mousedown', '.detail-edit-input, .detail-edit-select', function(e) {
        e.stopPropagation();
    });

    function updateStatusBadge($badge, isOk) {
        if (isOk) $badge.removeClass('bg-danger bg-opacity-10 text-danger').addClass('bg-success bg-opacity-10 text-success').html('<i class="ti ti-check-circle me-1"></i>OK');
        else $badge.removeClass('bg-success bg-opacity-10 text-success').addClass('bg-danger bg-opacity-10 text-danger').html('<i class="ti ti-x-circle me-1"></i>NG');
    }

    function saveStatusEdit(id, status) {
        $.ajax({
            url: 'UpdateDefectReportController.php?action=updateStatus',
            type: 'POST',
            data: {
                id: id,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateTableRow(id, 'status', status);
                    showToast('Status Updated', `Status changed to ${status == 1 ? 'OK' : 'NG'}`, 'success', 1500);
                } else {
                    Swal.fire('Error', response.message || 'Gagal update status', 'error');
                    let $toggle = $('#detailStatusToggle');
                    $toggle.prop('checked', status != 1);
                    updateStatusBadge($('#detailStatusBadge'), status != 1);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal update status', 'error');
                let $toggle = $('#detailStatusToggle');
                $toggle.prop('checked', status != 1);
                updateStatusBadge($('#detailStatusBadge'), status != 1);
            }
        });
    }

    $(document).on('change', '.status-toggle', function() {
        let $toggle = $(this);
        let id = $toggle.data('id');
        let newStatus = $toggle.is(':checked') ? 1 : 0;
        Swal.fire({
            title: 'Konfirmasi',
            text: `Ubah status menjadi ${newStatus == 1 ? 'OK' : 'NG'}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal',
            confirmButtonColor: newStatus == 1 ? '#28a745' : '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                $toggle.data('status', newStatus);
                $.ajax({
                    url: 'UpdateDefectReportController.php?action=updateStatus',
                    type: 'POST',
                    data: {
                        id: id,
                        status: newStatus
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            updateTableRow(id, 'status', newStatus);
                            let $row = $toggle.closest('tr');
                            let $badge = $row.find('.badge:not(.status-toggle)');
                            let statusText = newStatus == 1 ? 'OK' : 'NG';
                            let statusClass = newStatus == 1 ? 'success' : 'danger';
                            $badge.removeClass('bg-success bg-danger bg-opacity-10 text-success text-danger').addClass(`bg-${statusClass} bg-opacity-10 text-${statusClass}`).text(statusText);
                            showToast('Status Updated', `Status changed to ${statusText}`, 'success', 1500);
                        } else {
                            $toggle.prop('checked', newStatus != 1);
                            Swal.fire('Error', response.message || 'Gagal update status', 'error');
                        }
                    },
                    error: function() {
                        $toggle.prop('checked', newStatus != 1);
                        Swal.fire('Error', 'Gagal update status', 'error');
                    }
                });
            } else $toggle.prop('checked', !$toggle.is(':checked'));
        });
    });

    $('#btnExportExcel').on('click', function() {
        exportExcel();
    });

    function exportExcel() {
        let params = [];
        if (activeTab === 'tanggalPartNo' && filters.tanggalPartNo.aktif) {
            if (filters.tanggalPartNo.tanggal) params.push('tanggal=' + encodeURIComponent(filters.tanggalPartNo.tanggal));
            if (filters.tanggalPartNo.partnos.length > 0) params.push('partno=' + encodeURIComponent(filters.tanggalPartNo.partnos.join(',')));
        } else if (activeTab === 'lot' && filters.lot.aktif) {
            if (filters.lot.lotNos.length > 0) params.push('lot_nos=' + encodeURIComponent(filters.lot.lotNos.join(',')));
        } else if (activeTab === 'customer' && filters.customer.aktif) {
            if (filters.customer.customers.length > 0) params.push('customers=' + encodeURIComponent(filters.customer.customers.join(',')));
            if (filters.customer.tanggalAwal) params.push('tanggal_awal=' + filters.customer.tanggalAwal);
            if (filters.customer.tanggalAkhir) params.push('tanggal_akhir=' + filters.customer.tanggalAkhir);
        } else if (activeTab === 'partNoRange' && filters.partNoRange.aktif) {
            if (filters.partNoRange.partnos.length > 0) params.push('partnos=' + encodeURIComponent(filters.partNoRange.partnos.join(',')));
            if (filters.partNoRange.tanggalAwal) params.push('tanggal_awal_part=' + filters.partNoRange.tanggalAwal);
            if (filters.partNoRange.tanggalAkhir) params.push('tanggal_akhir_part=' + filters.partNoRange.tanggalAkhir);
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
            html: `<div class="mb-3"><i class="ti ti-file-spreadsheet" style="font-size: 3rem; color: #198754;"></i></div><p class="mb-1">Mohon tunggu sebentar...</p><p class="text-muted small">Mengexport ${dataCount} data laporan</p>`,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            showConfirmButton: false
        });
        window.location.href = 'ExportDefectReportController.php?' + params.join('&');
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

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        let parts = dateStr.split('-');
        if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
        return dateStr;
    }

    function formatDateTime(dateTimeStr) {
        if (!dateTimeStr) return '-';
        let parts = dateTimeStr.split(' ');
        if (parts.length >= 2) return formatDate(parts[0]) + ' ' + parts[1].substring(0, 5);
        return dateTimeStr;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // IMPORT MODAL
    $('#btnImportData').on('click', function() {
        $('#importForm')[0].reset();
        $('#importProgress').hide();
        $('#previewSection').hide();
        $('#btnImportSubmit').prop('disabled', true);
        $('.progress-bar').css('width', '0%').text('0%');
        $('#previewHeader').html('<tr><th>#</th><th>Preview</th></tr>');
        $('#previewBody').html('<tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>');
        $('#importModal').modal('show');
    });

    $('#btnDownloadTemplate').on('click', function() {
        window.location.href = 'ImportDataDefectReportController.php?action=downloadTemplate';
    });

    $('#importFile').on('change', function() {
        const file = this.files[0];
        if (!file) {
            $('#btnImportSubmit').prop('disabled', true);
            $('#previewSection').hide();
            return;
        }
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
        $('#btnImportSubmit').prop('disabled', false);
        $('#previewSection').show();
        $('#previewHeader').html('<tr><th>#</th><th>Informasi File</th></tr>');
        $('#previewBody').html(`<tr><td>1</td><td><strong>Nama File:</strong> ${escapeHtml(fileName)}<br><strong>Ukuran:</strong> ${(file.size / 1024).toFixed(2)} KB<br><strong>Status:</strong> <span class="text-success">Siap diimport</span><br><small class="text-muted">Baris pertama akan diabaikan (header)</small></td></tr>`);
    });

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
        const formData = new FormData();
        formData.append('file', file);
        formData.append('skip_first_row', '1');
        $('#importProgress').show();
        $('#btnImportSubmit').prop('disabled', true);
        $('#importFile').prop('disabled', true);
        $('#btnDownloadTemplate').prop('disabled', true);
        let progressInterval = setInterval(function() {
            let currentWidth = parseInt($('.progress-bar').css('width'));
            if (currentWidth < 90) {
                let newWidth = currentWidth + 10;
                $('.progress-bar').css('width', newWidth + '%').text(newWidth + '%');
            }
        }, 500);
        $.ajax({
            url: 'ImportDataDefectReportController.php?action=import',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 300000,
            success: function(response) {
                clearInterval(progressInterval);
                $('.progress-bar').css('width', '100%').text('100%');
                setTimeout(function() {
                    $('#importProgress').hide();
                    if (response.status === 'success') {
                        let message = response.message || 'Data berhasil diimport';
                        if (response.data) message += `<div class="text-start mt-2"><strong>Detail Import:</strong><br>Total data: ${response.data.total || 0}<br>Berhasil: ${response.data.success || 0}<br>Gagal: ${response.data.failed || 0}</div>`;
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Berhasil',
                            html: message,
                            timer: 3000,
                            showConfirmButton: true
                        }).then(() => {
                            $('#importModal').modal('hide');
                            if (activeTab === 'tanggalPartNo' && filters.tanggalPartNo.aktif) loadReportsByTab('tanggalPartNo');
                            else if (activeTab === 'lot' && filters.lot.aktif) loadReportsByTab('lot');
                            else if (activeTab === 'customer' && filters.customer.aktif) loadReportsByTab('customer');
                            else if (activeTab === 'partNoRange' && filters.partNoRange.aktif) loadReportsByTab('partNoRange');
                            else showEmptyInitialState();
                        });
                    } else {
                        let errorMsg = response.message || 'Gagal mengimport data';
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
                    resetImportForm();
                }, 1000);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $('#importProgress').hide();
                let errorMsg = 'Gagal mengimport data';
                if (status === 'timeout') errorMsg = 'Import timeout. File terlalu besar atau koneksi lambat.';
                else if (xhr.responseText) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        if (response.message) errorMsg = response.message;
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

    function resetImportForm() {
        $('#importForm')[0].reset();
        $('#importProgress').hide();
        $('#previewSection').hide();
        $('#btnImportSubmit').prop('disabled', true);
        $('#importFile').prop('disabled', false);
        $('#btnDownloadTemplate').prop('disabled', false);
        $('.progress-bar').css('width', '0%').text('0%');
        $('#previewHeader').html('<tr><th>#</th><th>Preview</th></tr>');
        $('#previewBody').html('<tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>');
    }

    $('#importModal').on('hidden.bs.modal', function() {
        resetImportForm();
    });

    function showToast(title, message, type = 'success', duration = 3000) {
        $('.toast-notification').remove();
        const toastHtml = `<div class="toast-notification toast-${type}"><div class="toast-content"><div class="toast-icon"><i class="ti ti-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : type === 'warning' ? 'alert-circle' : 'circle-x'}"></i></div><div class="toast-message"><div class="toast-title">${title}</div><div class="toast-text">${message}</div></div><button class="toast-close" onclick="closeToast(this)"><i class="ti ti-x"></i></button></div></div>`;
        $('body').append(toastHtml);
        const $toast = $('.toast-notification');
        setTimeout(function() {
            closeToast($toast.find('.toast-close')[0]);
        }, duration);
    }

    function closeToast(btn) {
        const $toast = $(btn).closest('.toast-notification');
        $toast.addClass('hiding');
        setTimeout(function() {
            $toast.remove();
        }, 500);
    }

    function updateSummary(data) {}
</script>

<style>
    .btn-primary.active-quick {
        background-color: #002B4A !important;
        border-color: #002B4A !important;
        box-shadow: 0 2px 5px rgba(0, 43, 74, 0.3);
        transform: scale(0.98);
        transition: all 0.2s ease;
    }

    .btn-primary.active-quick:hover {
        background-color: #003B66 !important;
        border-color: #003B66 !important;
    }

    .toast-notification {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        min-width: 320px;
        max-width: 380px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        animation: slideInRight 0.3s ease;
        border-top: 4px solid;
    }

    .toast-notification.toast-success {
        border-top-color: #28a745;
    }

    .toast-notification.toast-info {
        border-top-color: #17a2b8;
    }

    .toast-notification.toast-warning {
        border-top-color: #ffc107;
    }

    .toast-notification.toast-error {
        border-top-color: #dc3545;
    }

    .toast-content {
        padding: 16px 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: white;
    }

    .toast-icon {
        font-size: 22px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .toast-success .toast-icon {
        color: #28a745;
        background: rgba(40, 167, 69, 0.1);
    }

    .toast-info .toast-icon {
        color: #17a2b8;
        background: rgba(23, 162, 184, 0.1);
    }

    .toast-warning .toast-icon {
        color: #ffc107;
        background: rgba(255, 193, 7, 0.1);
    }

    .toast-error .toast-icon {
        color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
    }

    .toast-message {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 15px;
        color: #1e293b;
    }

    .toast-text {
        font-size: 13px;
        color: #475569;
        line-height: 1.4;
    }

    .toast-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #94a3b8;
        padding: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .toast-close:hover {
        background: #f1f5f9;
        color: #475569;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(calc(100% + 20px));
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(calc(100% + 20px));
            opacity: 0;
        }
    }

    .toast-notification.hiding {
        animation: slideOutRight 0.3s ease forwards;
    }

    @media (max-width: 576px) {
        .toast-notification {
            left: 16px;
            right: 16px;
            min-width: auto;
            max-width: none;
            bottom: 16px;
        }
    }

    .row.g-3>[class*="col-"] {
        display: flex;
        flex-direction: column;
    }

    .d-flex.flex-column.h-100 {
        height: 100% !important;
    }

    .justify-content-end {
        justify-content: flex-end !important;
    }

    .form-label {
        height: auto;
        min-height: 24px;
        margin-bottom: 0.5rem;
        line-height: 1.5;
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

    /* Sticky header */
    #reportTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc !important;
        /* wajib ada background */
    }

    /* Frozen columns - kolom No, Tanggal, Part No, Lot No */
    /* Sticky header - DataTables scrollY sudah handle ini otomatis */
    .dataTables_scrollHead thead th {
        background: #f8fafc !important;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 2px solid #e9ecef;
    }

    /* Style kolom frozen (FixedColumns plugin yang generate class ini) */
    table.dataTable tbody td.dtfc-fixed-left,
    table.dataTable thead th.dtfc-fixed-left {
        background: #fff;
    }

    table.dataTable thead th.dtfc-fixed-left {
        background: #f8fafc !important;
    }

    /* Shadow di batas kolom frozen */
    table.dataTable tbody td.dtfc-fixed-left:last-child,
    table.dataTable thead th.dtfc-fixed-left:last-child {
        box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.15);
    }

    /* Hover tetap keliatan di kolom frozen */
    #reportTable tbody tr:hover td.dtfc-fixed-left {
        background: #f1f5ff !important;
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

    .border-top {
        border-top: 1px solid #e0e0e0 !important;
    }

    .inline-edit {
        border: 2px solid #0d6efd !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        transition: all 0.2s ease;
        min-width: 90px;
        width: 100%;
    }

    .inline-edit:focus {
        border-color: #0a58ca !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
        outline: none;
    }

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