<?php
require_once '../../helper/auth.php';
isLogin();
include '../layout/head.php';
include '../layout/sidebar.php';
include '../layout/header.php';
?>

<style>
    /* ===== GLOBAL & CARD ===== */
    .form-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 30px 35px;
        margin-top: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f2f5;
        transition: all 0.3s ease;
    }
    
    .form-card:hover {
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.08);
    }

    /* ===== FORM GROUP ===== */
    .form-group {
        margin-bottom: 22px;
        position: relative;
    }
    
    .form-group label {
        font-weight: 600;
        font-size: 13px;
        color: #1e293b;
        display: block;
        margin-bottom: 6px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    
    .form-group label .required {
        color: #ef4444;
        margin-left: 2px;
        font-weight: 700;
    }

    /* ===== INPUT & SELECT ===== */
    .form-control, .form-select {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
        box-sizing: border-box;
        background: #fafbfc;
        transition: all 0.25s ease;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .form-control:hover, .form-select:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        background: #ffffff;
    }
    
    .form-control::placeholder {
        color: #94a3b8;
        font-weight: 400;
        font-size: 13px;
    }
    
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
        cursor: pointer;
    }

    /* ===== LOTNO HERO ===== */
    .lotno-hero {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 28px;
        border: 1.5px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .lotno-hero:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
    }

    .lotno-hero label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .lotno-hero .form-control-lg {
        font-size: 18px;
        padding: 14px 18px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }

    .lotno-hero .form-control-lg:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .lotno-info-badge {
        font-size: 12px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-height: 24px;
    }

    .badge-info-custom {
        background: #eef2ff;
        color: #4f46e5;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    /* ===== CLAIM OPTIONS ===== */
    .claim-option {
        display: block;
        cursor: pointer;
    }

    .claim-option input {
        display: none;
    }

    .claim-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 16px;
        text-align: center;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: white;
        min-height: 60px;
    }

    .claim-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .repair-card {
        color: #0a58ca;
        border-color: #bbd4fb;
    }

    .repair-card:hover {
        background: #f0f7ff;
    }

    .scrap-card {
        color: #b91c1c;
        border-color: #f8cfcf;
    }

    .scrap-card:hover {
        background: #fff5f5;
    }

    .claim-option input:checked + .claim-card {
        border-width: 2.5px;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .claim-option input[value="Repair"]:checked + .claim-card {
        border-color: #0d6efd;
        background: #e7f1ff;
    }

    .claim-option input[value="Scrap"]:checked + .claim-card {
        border-color: #dc3545;
        background: #feecec;
    }

    /* ===== DYNAMIC ROW ===== */
    .section-defect-panel {
        background: #f8fafc;
        border-radius: 14px;
        padding: 20px;
        margin-top: 8px;
        border: 1px solid #e2e8f0;
    }

    .section-defect-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .section-defect-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 13px;
        color: #1e293b;
    }

    .dynamic-row {
        margin-bottom: 14px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .dynamic-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-badge {
        display: inline-block;
        background: #eef2ff;
        color: #4f46e5;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
    }

    /* ===== ICON BUTTONS ===== */
    .icon-action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        transition: all 0.2s ease;
        background: transparent;
        border: none;
        color: #94a3b8;
    }

    .icon-action-btn:hover {
        background: rgba(0, 0, 0, 0.05);
        transform: scale(1.05);
    }

    .icon-action-btn:active {
        transform: scale(0.95);
    }

    .icon-add {
        color: #6366f1;
    }

    .icon-add:hover {
        color: #4f46e5;
        background: rgba(99, 102, 241, 0.1);
    }

    .icon-remove {
        color: #ef4444;
    }

    .icon-remove:hover {
        color: #dc2626;
        background: rgba(239, 68, 68, 0.1);
    }

    .action-buttons-group {
        display: flex;
        gap: 6px;
        align-items: center;
        justify-content: flex-start;
        min-height: 44px;
    }

    /* ===== BUTTONS ===== */
    .btn-group-action {
        display: flex;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1.5px solid #f1f5f9;
    }
    
    .btn-group-action button {
        padding: 12px 28px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        letter-spacing: 0.3px;
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        flex: 1;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }
    
    .btn-submit:active {
        transform: scale(0.97) translateY(0);
    }
    
    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }
    
    .btn-reset-custom {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-reset-custom:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }
    
    .btn-reset-custom:active {
        transform: scale(0.97) translateY(0);
    }

    /* ===== VALIDATION ===== */
    .form-control.is-valid, .form-select.is-valid {
        border-color: #22c55e;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2322c55e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 18px;
        padding-right: 40px;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23ef4444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cline x1='15' y1='9' x2='9' y2='15'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 18px;
        padding-right: 40px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }

        .lotno-hero {
            padding: 16px 18px;
        }

        .btn-group-action {
            flex-direction: column;
        }
        
        .btn-group-action button {
            flex: none;
            padding: 14px;
        }

        .action-buttons-group {
            margin-top: 8px;
            width: 100%;
            justify-content: flex-start;
        }

        .icon-action-btn {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }
    }

    @media (max-width: 576px) {
        .lotno-hero .form-control-lg {
            font-size: 16px;
            padding: 12px 14px;
        }

        .claim-card {
            font-size: 13px;
            padding: 14px 12px;
            min-height: 50px;
        }
    }

    /* ===== SELECT2 OVERRIDE ===== */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 10px !important;
        min-height: 48px !important;
        border: 1.5px solid #e2e8f0 !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 10px 14px !important;
        font-size: 14px !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }

    /* ===== LOADING SPINNER ===== */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }

    .loading-spinner-small {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #e2e8f0;
        border-top: 2px solid #6366f1;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .auto-filled {
        background-color: #eef2ff !important;
        border-color: #6366f1 !important;
    }

    /* ===== BREADCRUMB ===== */
    .breadcrumb-custom {
        background: white;
        border-radius: 40px;
        padding: 6px 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 0;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="fw-semibold mb-0">📋 Input Claim Defect</h4>
                <ol class="breadcrumb breadcrumb-custom">
                    <li class="breadcrumb-item"><a href="../dashboard/index.php" class="text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-info">Input Claim Defect</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form id="defectForm" method="POST">
            
            <!-- LOTNO SECTION -->
            <div class="lotno-hero">
                <div class="row align-items-end">
                    <div class="col-md-8 col-12">
                        <label class="d-flex align-items-center gap-2">
                            <i class="ti ti-barcode fs-5"></i> LOT NUMBER <span class="required">*</span>
                            <span class="badge-info-custom">Scan / ketik manual</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" id="lotno" name="lotno"
                            placeholder="Masukkan Lot No — tekan Enter atau klik di luar field" autocomplete="off">
                        <div class="lotno-info-badge" id="lotnoInfo"></div>
                    </div>
                    <div class="col-md-4 col-12 mt-3 mt-md-0">
                        <div class="text-md-end">
                            <small class="text-muted d-block">🔍 Tekan Enter atau klik di luar field untuk cek</small>
                            <small class="text-muted d-block">💡 Ketik '-' untuk mode manual</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM DETAIL -->
            <div class="row g-4">
                <!-- Customer -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>🏢 Customer <span class="required">*</span></label>
                        <div id="customerContainer">
                            <select class="form-select" id="customer" name="nama_customer" required>
                                <option value="">-- Pilih Customer --</option>
                            </select>
                        </div>
                        <small class="text-muted" id="customerInfo"></small>
                    </div>
                </div>

                <!-- Tanggal Ditemukan -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>📅 Tanggal Ditemukan <span class="required">*</span></label>
                        <input type="date" class="form-control" id="tanggal_ditemukan" name="tanggal_ditemukan" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <!-- Part No -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>🔧 Part No <span class="required">*</span></label>
                        <div id="partnoContainer"></div>
                        <small class="text-muted" id="partnoInfo"></small>
                    </div>
                </div>

                <!-- Nama Operator -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>👤 Nama Operator <span class="required">*</span></label>
                        <input type="text" class="form-control" id="nama_operator" name="nama_operator" 
                               placeholder="Operator yang menemukan defect" required>
                    </div>
                </div>

                <!-- Group -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>📌 Group <span class="required">*</span></label>
                        <input type="text" class="form-control" id="nama_group" name="nama_group" 
                               placeholder="Nama group defect" required>
                    </div>
                </div>

                <!-- Qty Defect -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>🔢 Qty Defect <span class="required">*</span></label>
                        <input type="number" class="form-control" id="qty" name="qty" 
                               placeholder="Jumlah" min="1" required>
                    </div>
                </div>

                <!-- Shift -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label>🕒 Shift <span class="required">*</span></label>
                        <select class="form-select" id="shift" name="shift" required>
                            <option value="">-- Pilih Shift --</option>
                            <option value="1">Shift 1</option>
                            <option value="2">Shift 2</option>
                        </select>
                        <small class="text-muted">Pilih shift kerja (1 atau 2)</small>
                    </div>
                </div>

                <!-- Dynamic Section & Defect -->
                <div class="col-12">
                    <div class="form-group">
                        <label>
                            🏭 Section & ⚠️ Nama Defect <span class="required">*</span>
                            <span class="section-badge">Multiple</span>
                        </label>
                        <div class="section-defect-panel">
                            <div id="sectionDefectContainer">
                                <div class="dynamic-row" data-index="0">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-5">
                                            <label class="text-muted small mb-1 d-block">🏭 Section</label>
                                            <select class="form-select section-select" name="sections[]" required>
                                                <option value="">-- Pilih Section --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="text-muted small mb-1 d-block">⚠️ Nama Defect</label>
                                            <select class="form-select defect-select" name="defects[]" required>
                                                <option value="">-- Pilih Defect --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="action-buttons-group">
                                                <button type="button" class="icon-action-btn icon-add btn-add-row-inline" title="Tambah Baris">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                                <button type="button" class="icon-action-btn icon-remove btn-remove-row" style="display: none;" title="Hapus Baris">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-3">
                                <i class="ti ti-info-circle me-1"></i>
                                Data akan disimpan dalam format yang dipisahkan koma
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Masalah -->
                <div class="col-12">
                    <div class="form-group">
                        <label>📝 Deskripsi Masalah <span class="required">*</span></label>
                        <textarea class="form-control" id="deskripsi_masalah" name="deskripsi_masalah" 
                                  rows="4" placeholder="Jelaskan detail masalah yang ditemukan..." required></textarea>
                    </div>
                </div>

                <!-- Aksi Claim Defect -->
                <div class="col-12">
                    <div class="form-group">
                        <label class="d-block mb-3">⚡ Aksi Claim Defect <span class="required">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="claim-option w-100">
                                    <input type="radio" name="aksi_claim_defect" value="Repair" required>
                                    <div class="claim-card repair-card">
                                        <i class="ti ti-tool fs-4"></i> <span>Repair</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="claim-option w-100">
                                    <input type="radio" name="aksi_claim_defect" value="Scrap">
                                    <div class="claim-card scrap-card">
                                        <i class="ti ti-trash fs-4"></i> <span>Scrap</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="btn-group-action">
                <button type="button" class="btn-reset-custom" onclick="resetFormManual()">
                    <i class="ti ti-refresh"></i> Reset
                </button>
                <button type="button" class="btn-submit" id="submitBtn" onclick="showConfirmModal()">
                    <i class="ti ti-send"></i> Submit Claim
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL Konfirmasi -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white;">
                <h5 class="modal-title"><i class="ti ti-check-circle me-2"></i> Konfirmasi Claim Defect</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-semibold">Periksa kembali data di bawah sebelum menyimpan:</p>
                <div class="border rounded-3 p-3 bg-light" id="previewData"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSubmitBtn" 
                        style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                    Ya, Simpan Claim
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<script src="../../assets/local/select2.min.js"></script>
<script src="../../assets/local/sweetalert2@11.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const form = document.getElementById('defectForm');
    const lotnoInput = document.getElementById('lotno');
    const lotnoInfo = document.getElementById('lotnoInfo');
    const customerContainer = document.getElementById('customerContainer');
    const partnoContainer = document.getElementById('partnoContainer');
    const partnoInfo = document.getElementById('partnoInfo');
    const customerInfo = document.getElementById('customerInfo');
    const sectionDefectContainer = document.getElementById('sectionDefectContainer');
    const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // State
    let isManualMode = false;
    let isProcessing = false;
    let rowCounter = 1;
    let sectionsData = [];
    let partnoSelectInitialized = false;
    let customerSelectInitialized = false;
    const baseUrl = 'DefectReportController.php';

    // ============================================
    // INITIALIZATION
    // ============================================
    
    function init() {
        loadSectionsData();
        renderNormalMode();
        setupEventListeners();
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    function setupEventListeners() {
        // Lotno Enter & Blur
        lotnoInput.addEventListener('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                handleLotnoInput(this.value.trim());
            }
        });

        lotnoInput.addEventListener('blur', function() {
            const val = this.value.trim();
            if (val === '-') {
                enableManualMode();
            } else if (val !== '') {
                handleLotnoInput(val);
            } else {
                resetToNormalMode();
            }
        });

        // Auto submit untuk 10 karakter
        lotnoInput.addEventListener('input', function() {
            const val = this.value.trim();
            if (val.length === 10 && val !== '-') {
                setTimeout(() => {
                    const currentVal = lotnoInput.value.trim();
                    if (currentVal.length === 10) {
                        handleLotnoInput(currentVal);
                    }
                }, 100);
            }
        });

        // Dynamic row events
        document.addEventListener('click', function(e) {
            const target = e.target.closest('.btn-add-row-inline');
            if (target) {
                addNewRow();
            }
        });

        document.addEventListener('click', function(e) {
            const target = e.target.closest('.btn-remove-row');
            if (target) {
                const row = target.closest('.dynamic-row');
                const rowCount = sectionDefectContainer.querySelectorAll('.dynamic-row').length;
                if (rowCount > 1) {
                    row.remove();
                    updateRemoveButtons();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: '⚠️ Minimal 1 Baris',
                        text: 'Form harus memiliki minimal 1 baris data',
                        confirmButtonColor: '#6366f1'
                    });
                }
            }
        });

        // Confirm submit
        confirmSubmitBtn.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmSubmitModal'));
            modal.hide();
            setTimeout(submitForm, 300);
        });
    }

    // ============================================
    // LOTNO HANDLING
    // ============================================
    
    function handleLotnoInput(lotno) {
        if (isProcessing) return;
        
        if (lotno === '-') {
            enableManualMode();
            return;
        }

        isProcessing = true;
        isManualMode = false;
        renderNormalMode();

        const cleanLotNo = lotno.split('.')[0];
        lotnoInfo.innerHTML = '<span class="loading-spinner-small"></span><span class="text-info">Mencari data Lot No...</span>';
        partnoInfo.textContent = '';
        customerInfo.textContent = '';

        fetch(`${baseUrl}?action=getPartNoByLotNo&lotno=${encodeURIComponent(cleanLotNo)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data) {
                    const partno = data.data;
                    document.getElementById('partno').value = partno;
                    partnoInfo.innerHTML = `<span class="text-success"><i class="ti ti-check"></i> Part No: ${escapeHtml(partno)}</span>`;
                    lotnoInfo.innerHTML = `<span class="text-success"><i class="ti ti-check"></i> Lot No: ${escapeHtml(lotno)} ditemukan</span>`;
                    getCustomerByPartNo(partno);
                } else {
                    // Lot no tidak ditemukan
                    document.getElementById('partno').value = '';
                    partnoInfo.innerHTML = '<span class="text-warning"><i class="ti ti-alert"></i> Lot No tidak ditemukan, beralih ke mode manual</span>';
                    lotnoInfo.innerHTML = `<span class="text-warning"><i class="ti ti-alert"></i> Lot No: ${escapeHtml(lotno)} tidak ditemukan</span>`;
                    enableManualMode();
                    isProcessing = false;
                }
            })
            .catch(() => {
                partnoInfo.innerHTML = '<span class="text-danger"><i class="ti ti-x"></i> Gagal mencari Lot No</span>';
                lotnoInfo.innerHTML = '<span class="text-danger"><i class="ti ti-x"></i> Error saat memproses Lot No</span>';
                enableManualMode();
                isProcessing = false;
            });
    }

    function getCustomerByPartNo(partno) {
        fetch(`${baseUrl}?action=getCustomerByPartNo&partno=${encodeURIComponent(partno)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data) {
                    setCustomerValue(data.data);
                } else {
                    customerInfo.innerHTML = '<span class="text-warning"><i class="ti ti-alert"></i> Customer tidak ditemukan, silakan pilih manual</span>';
                }
                isProcessing = false;
            })
            .catch(() => {
                customerInfo.innerHTML = '<span class="text-danger"><i class="ti ti-x"></i> Gagal mengambil data customer</span>';
                isProcessing = false;
            });
    }

    function setCustomerValue(customerValue) {
        const select = document.getElementById('customer');
        let optionExists = false;

        Array.from(select.options).forEach(opt => {
            if (opt.value === customerValue) optionExists = true;
        });

        if (optionExists) {
            select.value = customerValue;
            $(select).trigger('change');
            customerInfo.innerHTML = `<span class="text-success"><i class="ti ti-check"></i> Customer: ${escapeHtml(customerValue)}</span>`;
        } else {
            const option = document.createElement('option');
            option.value = customerValue;
            option.textContent = customerValue;
            select.appendChild(option);
            select.value = customerValue;
            $(select).trigger('change');
            customerInfo.innerHTML = `<span class="text-success"><i class="ti ti-plus-circle"></i> Customer baru: ${escapeHtml(customerValue)}</span>`;
        }
    }

    // ============================================
    // MODE: NORMAL / MANUAL
    // ============================================
    
    function renderNormalMode() {
        // Part No: input text readonly
        partnoContainer.innerHTML = `
            <input type="text" class="form-control" id="partno" name="partno" 
                   value="" placeholder="Part No akan terisi otomatis" readonly required>
        `;

        // Customer: select2
        customerContainer.innerHTML = `
            <select class="form-select" id="customer" name="nama_customer" required>
                <option value="">-- Pilih Customer --</option>
            </select>
        `;

        $('#customer').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        loadCustomers();
        partnoSelectInitialized = false;
        customerSelectInitialized = false;
    }

    function renderManualMode() {
        // Part No: dropdown
        fetch(`${baseUrl}?action=getPartNo`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    let html = '<select class="form-select" id="partnoSelect" name="partno" required>';
                    html += '<option value="">-- Pilih Part No --</option>';
                    data.data.forEach(p => {
                        html += `<option value="${escapeHtml(p)}">${escapeHtml(p)}</option>`;
                    });
                    html += '</select>';
                    partnoContainer.innerHTML = html;
                    $('#partnoSelect').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '-- Pilih Part No --'
                    });
                    partnoSelectInitialized = true;
                } else {
                    partnoContainer.innerHTML = `
                        <input type="text" class="form-control" id="partno" name="partno" 
                               placeholder="Input Part No manual" required>
                    `;
                    partnoSelectInitialized = false;
                }
            });

        // Customer: select2
        customerContainer.innerHTML = `
            <select class="form-select" id="customer" name="nama_customer" required>
                <option value="">-- Pilih Customer --</option>
            </select>
        `;

        $('#customer').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        loadCustomers();
        customerSelectInitialized = false;
    }

    function enableManualMode() {
        if (isManualMode) return;
        isManualMode = true;
        renderManualMode();
        lotnoInfo.innerHTML = '<span class="text-info"><i class="ti ti-info-circle"></i> Mode manual: pilih Part No dan Customer dari daftar</span>';
    }

    function resetToNormalMode() {
        renderNormalMode();
        isManualMode = false;
        lotnoInfo.innerHTML = '';
        partnoInfo.innerHTML = '';
        customerInfo.innerHTML = '';
        lotnoInput.value = '';
        isProcessing = false;
    }

    // ============================================
    // LOAD DATA
    // ============================================
    
    function loadSectionsData() {
        fetch(`${baseUrl}?action=getSections`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    sectionsData = data.data;
                    populateSections();
                }
            })
            .catch(() => console.error('Gagal memuat data section'));
    }

    function populateSections() {
        document.querySelectorAll('.section-select').forEach(select => {
            const currentVal = select.value;
            select.innerHTML = '<option value="">-- Pilih Section --</option>';
            sectionsData.forEach(section => {
                select.innerHTML += `<option value="${escapeHtml(section)}">${escapeHtml(section)}</option>`;
            });
            if (currentVal) select.value = currentVal;
            initSelect2ForRow(select);
        });
    }

    function initSelect2ForRow(sectionSelect) {
        const row = sectionSelect.closest('.dynamic-row');
        const defectSelect = row.querySelector('.defect-select');

        // Section Select2
        if (!$(sectionSelect).data('select2-initialized')) {
            $(sectionSelect).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Section --'
            });
            $(sectionSelect).data('select2-initialized', true);
        }

        // Defect Select2
        if (!$(defectSelect).data('select2-initialized')) {
            $(defectSelect).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Defect --'
            });
            $(defectSelect).data('select2-initialized', true);
        }

        // Event change section
        $(sectionSelect).off('change.section').on('change.section', function() {
            const section = this.value;
            const defectSelect = this.closest('.row').querySelector('.defect-select');
            if (section) {
                loadDefectsForSection(defectSelect, section);
            } else {
                defectSelect.innerHTML = '<option value="">-- Pilih Defect --</option>';
                $(defectSelect).trigger('change.select2');
            }
        });
    }

    function loadDefectsForSection(defectSelect, section) {
        if (!section) {
            defectSelect.innerHTML = '<option value="">-- Pilih Defect --</option>';
            $(defectSelect).trigger('change.select2');
            return;
        }

        fetch(`${baseUrl}?action=getDefectsBySection&section=${encodeURIComponent(section)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    defectSelect.innerHTML = '<option value="">-- Pilih Defect --</option>';
                    data.data.forEach(defect => {
                        defectSelect.innerHTML += `<option value="${escapeHtml(defect)}">${escapeHtml(defect)}</option>`;
                    });
                    $(defectSelect).trigger('change.select2');
                }
            });
    }

    function loadCustomers() {
        fetch(`${baseUrl}?action=getCustomers`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const select = document.getElementById('customer');
                    select.innerHTML = '<option value="">-- Pilih Customer --</option>';
                    data.data.forEach(customer => {
                        select.innerHTML += `<option value="${escapeHtml(customer)}">${escapeHtml(customer)}</option>`;
                    });
                    $(select).trigger('change');
                }
            });
    }

    // ============================================
    // DYNAMIC ROWS
    // ============================================
    
    function addNewRow() {
        const newIndex = rowCounter++;
        const newRow = document.createElement('div');
        newRow.className = 'dynamic-row';
        newRow.dataset.index = newIndex;
        newRow.innerHTML = `
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="text-muted small mb-1 d-block">🏭 Section</label>
                    <select class="form-select section-select" name="sections[]" required>
                        <option value="">-- Pilih Section --</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="text-muted small mb-1 d-block">⚠️ Nama Defect</label>
                    <select class="form-select defect-select" name="defects[]" required>
                        <option value="">-- Pilih Defect --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="action-buttons-group">
                        <button type="button" class="icon-action-btn icon-add btn-add-row-inline" title="Tambah Baris">
                            <i class="ti ti-plus"></i>
                        </button>
                        <button type="button" class="icon-action-btn icon-remove btn-remove-row" title="Hapus Baris">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Hapus tombol plus dari baris terakhir
        const lastRow = sectionDefectContainer.lastElementChild;
        if (lastRow) {
            const lastPlusBtn = lastRow.querySelector('.btn-add-row-inline');
            if (lastPlusBtn) lastPlusBtn.remove();
        }

        // Tambahkan baris baru
        sectionDefectContainer.appendChild(newRow);

        // Populate sections
        const sectionSelect = newRow.querySelector('.section-select');
        sectionSelect.innerHTML = '<option value="">-- Pilih Section --</option>';
        sectionsData.forEach(section => {
            sectionSelect.innerHTML += `<option value="${escapeHtml(section)}">${escapeHtml(section)}</option>`;
        });

        // Init Select2
        initSelect2ForRow(sectionSelect);
        updateRemoveButtons();

        // Scroll ke baris baru
        newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function updateRemoveButtons() {
        const rows = sectionDefectContainer.querySelectorAll('.dynamic-row');
        const rowCount = rows.length;

        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.btn-remove-row');
            if (rowCount === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'inline-flex';
            }

            // Plus button hanya di baris terakhir
            const plusBtn = row.querySelector('.btn-add-row-inline');
            if (index === rowCount - 1) {
                if (!plusBtn) {
                    const actionsGroup = row.querySelector('.action-buttons-group');
                    const addBtn = document.createElement('button');
                    addBtn.type = 'button';
                    addBtn.className = 'icon-action-btn icon-add btn-add-row-inline';
                    addBtn.title = 'Tambah Baris';
                    addBtn.innerHTML = '<i class="ti ti-plus"></i>';
                    actionsGroup.prepend(addBtn);
                }
            } else {
                if (plusBtn) plusBtn.remove();
            }
        });
    }

    // ============================================
    // SUBMIT & VALIDATION
    // ============================================
    
    function validateForm() {
        const errors = [];
        const partnoValue = partnoSelectInitialized ? 
            document.getElementById('partnoSelect')?.value : 
            document.getElementById('partno')?.value;

        // Validate dynamic rows
        const sections = [];
        const defects = [];
        document.querySelectorAll('.section-select').forEach(select => {
            const val = select.value;
            if (!val) errors.push('Section harus dipilih di semua baris');
            else sections.push(val);
        });
        document.querySelectorAll('.defect-select').forEach(select => {
            const val = select.value;
            if (!val) errors.push('Defect harus dipilih di semua baris');
            else defects.push(val);
        });

        if (sections.length !== defects.length || sections.length === 0) {
            errors.push('Minimal 1 pasangan Section dan Defect harus diisi');
        }

        if (!document.getElementById('customer').value) errors.push('Customer harus dipilih');
        if (!lotnoInput.value.trim()) errors.push('Lot No harus diisi');
        if (!partnoValue || partnoValue.trim() === '') errors.push('Part No harus diisi');
        if (!document.getElementById('tanggal_ditemukan').value) errors.push('Tanggal ditemukan harus diisi');
        if (!document.getElementById('nama_operator').value.trim()) errors.push('Nama operator harus diisi');
        if (!document.getElementById('nama_group').value.trim()) errors.push('Group defect harus diisi');
        if (!document.getElementById('qty').value) errors.push('Qty harus diisi');
        if (!document.getElementById('shift').value) errors.push('Shift harus dipilih');
        if (!document.getElementById('deskripsi_masalah').value.trim()) errors.push('Deskripsi masalah harus diisi');
        if (!document.querySelector('input[name="aksi_claim_defect"]:checked')) errors.push('Aksi Claim Defect harus dipilih');

        return errors;
    }

    function showConfirmModal() {
        const errors = validateForm();
        
        if (errors.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: '⚠️ Validasi Gagal',
                html: errors.join('<br>'),
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        const partnoValue = partnoSelectInitialized ? 
            document.getElementById('partnoSelect')?.value : 
            document.getElementById('partno')?.value;

        const sections = [];
        const defects = [];
        document.querySelectorAll('.section-select').forEach(s => {
            if (s.value) sections.push(s.value);
        });
        document.querySelectorAll('.defect-select').forEach(d => {
            if (d.value) defects.push(d.value);
        });

        const aksiClaim = document.querySelector('input[name="aksi_claim_defect"]:checked').value;

        const previewHtml = `
            <table class="table table-sm table-borderless mb-0">
                <tr><td width="40%"><strong>Customer:</strong></td><td>${escapeHtml(document.getElementById('customer').options[document.getElementById('customer').selectedIndex]?.text || '')}</td></tr>
                <tr><td width="40%"><strong>Tanggal Ditemukan:</strong></td><td>${escapeHtml(document.getElementById('tanggal_ditemukan').value)}</td></tr>
                <tr><td width="40%"><strong>Lot No:</strong></td><td>${escapeHtml(lotnoInput.value)}</td></tr>
                <tr><td width="40%"><strong>Part No:</strong></td><td>${escapeHtml(partnoValue)}</td></tr>
                <tr><td width="40%"><strong>Nama Operator:</strong></td><td>${escapeHtml(document.getElementById('nama_operator').value)}</td></tr>
                <tr><td width="40%"><strong>Section:</strong></td><td>${escapeHtml(sections.join(', '))}</td></tr>
                <tr><td width="40%"><strong>Nama Defect:</strong></td><td>${escapeHtml(defects.join(', '))}</td></tr>
                <tr><td width="40%"><strong>Group:</strong></td><td>${escapeHtml(document.getElementById('nama_group').value)}</td></tr>
                <tr><td width="40%"><strong>Qty:</strong></td><td>${escapeHtml(document.getElementById('qty').value)}</td></tr>
                <tr><td width="40%"><strong>Shift:</strong></td><td>${escapeHtml(document.getElementById('shift').options[document.getElementById('shift').selectedIndex]?.text || '')}</td></tr>
                <tr><td width="40%"><strong>Deskripsi:</strong></td><td>${escapeHtml(document.getElementById('deskripsi_masalah').value)}</td></tr>
                <tr><td width="40%"><strong>Aksi Claim:</strong></td><td>${escapeHtml(aksiClaim)}</td></tr>
            </table>
        `;

        document.getElementById('previewData').innerHTML = previewHtml;
        new bootstrap.Modal(document.getElementById('confirmSubmitModal')).show();
    }

    function submitForm() {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...';
        submitBtn.disabled = true;

        const partnoValue = partnoSelectInitialized ? 
            document.getElementById('partnoSelect')?.value : 
            document.getElementById('partno')?.value;

        const formData = new FormData();
        formData.append('action', 'insert');
        formData.append('lotno', lotnoInput.value.trim());
        formData.append('partno', partnoValue);
        formData.append('tanggal_ditemukan', document.getElementById('tanggal_ditemukan').value);
        formData.append('nama_operator', document.getElementById('nama_operator').value.trim());
        formData.append('nama_group', document.getElementById('nama_group').value.trim());
        formData.append('qty', document.getElementById('qty').value);
        formData.append('deskripsi_masalah', document.getElementById('deskripsi_masalah').value.trim());
        formData.append('nama_customer', document.getElementById('customer').value);
        formData.append('aksi_claim_defect', document.querySelector('input[name="aksi_claim_defect"]:checked').value);
        formData.append('shift', document.getElementById('shift').value);

        // Sections & Defects as array
        document.querySelectorAll('.section-select').forEach(select => {
            if (select.value) formData.append('sections[]', select.value);
        });
        document.querySelectorAll('.defect-select').forEach(select => {
            if (select.value) formData.append('defects[]', select.value);
        });

        fetch(baseUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Reset form otomatis tanpa konfirmasi
                resetFormAuto();
                
                // Tampilkan notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: '✅ Berhasil!',
                    text: data.message || 'Data claim berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                const errorMsg = data.message || data.errors?.join('<br>') || 'Gagal menyimpan data';
                Swal.fire({
                    icon: 'error',
                    title: '❌ Gagal!',
                    html: errorMsg,
                    confirmButtonColor: '#6366f1'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: '❌ Error!',
                text: 'Terjadi kesalahan saat menyimpan data',
                confirmButtonColor: '#6366f1'
            });
        })
        .finally(() => {
            submitBtn.innerHTML = '<i class="ti ti-send"></i> Submit Claim';
            submitBtn.disabled = false;
        });
    }

    // ============================================
    // RESET FORM
    // ============================================
    
    // Reset otomatis (tanpa konfirmasi) - digunakan setelah submit sukses
    function resetFormAuto() {
        form.reset();
        renderNormalMode();
        isManualMode = false;
        partnoSelectInitialized = false;
        partnoInfo.innerHTML = '';
        lotnoInfo.innerHTML = '';
        customerInfo.innerHTML = '';
        document.getElementById('tanggal_ditemukan').value = new Date().toISOString().slice(0, 10);
        document.querySelectorAll('input[name="aksi_claim_defect"]').forEach(el => el.checked = false);
        document.getElementById('shift').value = '';
        isProcessing = false;

        // Reset dynamic rows
        sectionDefectContainer.innerHTML = `
            <div class="dynamic-row" data-index="0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <label class="text-muted small mb-1 d-block">🏭 Section</label>
                        <select class="form-select section-select" name="sections[]" required>
                            <option value="">-- Pilih Section --</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="text-muted small mb-1 d-block">⚠️ Nama Defect</label>
                        <select class="form-select defect-select" name="defects[]" required>
                            <option value="">-- Pilih Defect --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="action-buttons-group">
                            <button type="button" class="icon-action-btn icon-add btn-add-row-inline" title="Tambah Baris">
                                <i class="ti ti-plus"></i>
                            </button>
                            <button type="button" class="icon-action-btn icon-remove btn-remove-row" style="display: none;" title="Hapus Baris">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const firstSectionSelect = document.querySelector('.section-select');
        firstSectionSelect.innerHTML = '<option value="">-- Pilih Section --</option>';
        sectionsData.forEach(section => {
            firstSectionSelect.innerHTML += `<option value="${escapeHtml(section)}">${escapeHtml(section)}</option>`;
        });
        initSelect2ForRow(firstSectionSelect);
        rowCounter = 1;
    }

    // Reset manual (dengan konfirmasi) - digunakan untuk tombol Reset
    function resetFormManual() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua data yang belum disimpan akan hilang',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6366f1',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                resetFormAuto();
                Swal.fire({
                    icon: 'success',
                    title: 'Form Direset',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    // ============================================
    // UTILITY
    // ============================================
    
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Make functions global
    window.resetFormManual = resetFormManual;
    window.showConfirmModal = showConfirmModal;

    // ============================================
    // START
    // ============================================
    
    init();
});
</script>