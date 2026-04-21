<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Input Claim Defect | Sistem Claim</title>
    <!-- CSS Libraries -->
    <link href="../../assets/local/select2.min.css" rel="stylesheet" />
    <link href="../../assets/local/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .card.bg-light-info {
            background: linear-gradient(135deg, #eef9ff 0%, #e1f0fa 100%) !important;
        }

        .breadcrumb {
            background-color: white;
            border-radius: 40px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .lotno-hero {
            background: #f8fafc;
            border-radius: 24px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e2edf2;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            transition: all 0.2s;
        }

        .lotno-hero label {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1e4a6b;
        }

        .lotno-hero input {
            font-size: 1.2rem;
            padding: 0.8rem 1rem;
            border-radius: 48px;
            border: 1.5px solid #cbdde6;
            background: white;
            transition: 0.2s;
        }

        .lotno-hero input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }

        .lotno-info-badge {
            font-size: 0.85rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 12px;
            min-height: 44px;
        }

        .claim-option input {
            display: none;
        }

        .claim-card {
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 16px 12px;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: white;
        }

        .claim-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.05);
        }

        .repair-card {
            color: #0a58ca;
            border-color: #bbd4fb;
        }

        .scrap-card {
            color: #b91c1c;
            border-color: #f8cfcf;
        }

        .claim-option input:checked+.claim-card {
            border-width: 2.5px;
            background: #f0f9ff;
        }

        .claim-option input[value="Repair"]:checked+.claim-card {
            border-color: #0d6efd;
            background: #e7f1ff;
        }

        .claim-option input[value="Scrap"]:checked+.claim-card {
            border-color: #dc3545;
            background: #feecec;
        }

        @media (max-width: 768px) {
            .lotno-hero {
                padding: 1rem 1.25rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 8px;
            }

            .d-flex.justify-content-end {
                flex-direction: column;
            }
        }

        .badge-info {
            background: #e1f0fa;
            color: #0a558c;
        }

        .auto-filled {
            background-color: #e8f0fe !important;
            border-color: #0d6efd !important;
        }

        .loading-spinner-small {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0d6efd;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 6px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Dynamic row styles */
        .section-defect-panel {
            background: #f9fafb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .section-defect-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-defect-header h6 {
            margin: 0;
            font-weight: 600;
            color: #1e4a6b;
        }

        .dynamic-row {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px dashed #e5e7eb;
        }

        .dynamic-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-badge {
            display: inline-block;
            background: #e1f0fa;
            color: #0a558c;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 8px;
        }

        /* Icon-only buttons - no background, just icons */
        .icon-action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 1.2rem;
            line-height: 1;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            color: #6c757d;
        }

        .icon-action-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            transform: scale(1.05);
        }

        .icon-action-btn:active {
            transform: scale(0.95);
        }

        .icon-add {
            color: #0d6efd;
        }

        .icon-add:hover {
            color: #0a58ca;
            background: rgba(13, 110, 253, 0.1);
        }

        .icon-remove {
            color: #dc3545;
        }

        .icon-remove:hover {
            color: #b91c1c;
            background: rgba(220, 53, 69, 0.1);
        }

        .action-buttons-group {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            min-height: 44px;
        }

        @media (max-width: 768px) {
            .action-buttons-group {
                margin-top: 8px;
                width: 100%;
                justify-content: flex-start;
                min-height: auto;
            }

            .icon-action-btn {
                width: 36px;
                height: 36px;
                font-size: 1.3rem;
            }

            .dynamic-row .col-md-2 {
                margin-top: 5px;
            }
        }

        .dynamic-row .row {
            align-items: flex-start;
        }

        @media (min-width: 769px) {
            .dynamic-row .row {
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <?php include '../layout/head.php'; ?>
    <?php include '../layout/sidebar.php'; ?>
    <?php include '../layout/header.php'; ?>

    <div class="container-fluid">
        <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="fw-semibold mb-0">📋 Input Claim Defect</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded bg-white mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard/index.php" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-info">Input Claim Defect</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4 p-lg-5">
                        <form id="formClaim" method="POST">
                            <!-- LOTNO SECTION -->
                            <div class="lotno-hero">
                                <div class="row align-items-end">
                                    <div class="col-md-8 col-12">
                                        <label for="lotno" class="form-label d-flex align-items-center gap-2">
                                            <i class="ti ti-barcode fs-5"></i> LOT NUMBER <span class="text-danger">*</span>
                                            <span class="badge bg-info text-dark badge-info">Scan / ketik manual</span>
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

                            <!-- DETAIL FORM -->
                            <div class="row g-4">
                                <!-- Row 1: Customer | Tanggal Ditemukan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">🏢 Customer <span class="text-danger">*</span></label>
                                    <div id="customerContainer">
                                        <select class="form-select" id="customer" name="nama_customer" required>
                                            <option value="">-- Pilih Customer --</option>
                                        </select>
                                    </div>
                                    <small class="text-muted" id="customerInfo"></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">📅 Tanggal Ditemukan <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_ditemukan" name="tanggal_ditemukan" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>

                                <!-- Row 2: Part No | Nama Operator -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">🔧 Part No <span class="text-danger">*</span></label>
                                    <div id="partnoContainer"></div>
                                    <small class="text-muted" id="partnoInfo"></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">👤 Nama Operator <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_operator" name="nama_operator" placeholder="Operator yang menemukan defect" required>
                                </div>

                                <!-- Row 3: Group | Qty Defect -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">📌 Group <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_group" name="nama_group" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">🔢 Qty Defect <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="qty" name="qty" placeholder="Jumlah" min="1" required>
                                </div>

                                <div class="col-ms-12">
                                    <label class="form-label fw-semibold">🕒 Shift <span class="text-danger">*</span></label>
                                    <select class="form-select" id="shift" name="shift" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                    </select>
                                    <small class="text-muted">Pilih shift kerja (1 atau 2)</small>
                                </div>

                                <!-- Dynamic Section & Nama Defect -->
                                <div class="col-12">
                                    <div class="section-defect-panel">
                                        <div class="section-defect-header">
                                            <h6>
                                                <i class="ti ti-layers me-2"></i>
                                                🏭 Section & ⚠️ Nama Defect
                                                <span class="section-badge">Multiple</span>
                                            </h6>
                                        </div>

                                        <div id="sectionDefectContainer">
                                            <div class="dynamic-row" data-index="0">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-5">
                                                        <label class="form-label text-muted small mb-1">🏭 Section</label>
                                                        <select class="form-select section-select" name="sections[]" required style="width: 100%">
                                                            <option value="">-- Pilih Section --</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label text-muted small mb-1">⚠️ Nama Defect</label>
                                                        <select class="form-select defect-select" name="defects[]" required style="width: 100%">
                                                            <option value="">-- Pilih Defect --</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label text-muted small mb-1" style="visibility:hidden;"></label>
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
                                            (contoh: Section1,Section2 dan Defect1,Defect2)
                                        </small>
                                    </div>
                                </div>

                                <!-- Deskripsi Masalah (Full Width) -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">📝 Deskripsi Masalah <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="deskripsi_masalah" name="deskripsi_masalah" rows="4" placeholder="Jelaskan detail masalah yang ditemukan..." required></textarea>
                                </div>

                                <!-- Aksi Claim Defect (Repair / Scrap) -->
                                <div class="col-12 mt-2">
                                    <label class="form-label fw-semibold mb-3 d-block">⚡ Aksi Claim Defect <span class="text-danger">*</span></label>
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

                            <div class="d-flex justify-content-end gap-2 mt-5 pt-3">
                                <button type="button" class="btn btn-outline-warning px-4" onclick="resetForm()">
                                    <i class="ti ti-refresh"></i> Reset
                                </button>
                                <button type="button" class="btn btn-primary px-5" id="submitBtn" onclick="showConfirmModal()">
                                    <i class="ti ti-send"></i> Submit Claim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL Konfirmasi -->
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="ti ti-check-circle me-2"></i> Konfirmasi Claim Defect</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold">Periksa kembali data di bawah sebelum menyimpan:</p>
                    <div class="border rounded-3 p-3 bg-light" id="previewData"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="confirmSubmitBtn">Ya, Simpan Claim</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <?php include '../layout/scripts.php'; ?>
    <script src="../../assets/local/select2.min.js"></script>
    <script src="../../assets/local/sweetalert2@11.js"></script>

    <script>
        let baseUrl = 'DefectReportController.php';
        let partnoSelectInitialized = false;
        let customerSelectInitialized = false;
        let isManualMode = false;
        let isProcessing = false;
        let rowCounter = 1;

        let sectionsData = [];

        $(document).ready(function() {
            // Load data section terlebih dahulu
            loadSectionsData();

            // Set default ke mode normal (input text readonly)
            renderNormalMode();
            isManualMode = false;

            // LOTNO EVENT: ENTER ATAU BLUR
            $('#lotno').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let lotno = $(this).val().trim();
                    if (lotno === '-') {
                        enableManualMode();
                    } else if (lotno !== '') {
                        processLotNo(lotno);
                    }
                }
            });

            $('#lotno').on('blur', function() {
                let lotno = $(this).val().trim();
                if (lotno === '-') {
                    enableManualMode();
                } else if (lotno !== '') {
                    processLotNo(lotno);
                } else {
                    resetToNormalMode();
                }
            });

            // TAMBAHKAN: Auto submit untuk lot number 10 karakter
            $('#lotno').on('input', function() {
                let lotno = $(this).val().trim();

                // Jika panjang karakter mencapai 10, proses otomatis
                if (lotno.length === 10) {
                    // Beri sedikit delay untuk memastikan input selesai
                    setTimeout(function() {
                        let currentLotno = $('#lotno').val().trim();
                        if (currentLotno.length === 10 && currentLotno !== '') {
                            if (currentLotno === '-') {
                                enableManualMode();
                            } else {
                                processLotNo(currentLotno);
                            }
                        }
                    }, 100);
                }
            });

            // Event untuk tombol tambah inline (plus icon)
            $(document).on('click', '.btn-add-row-inline', function() {
                addNewRow();
            });

            $(document).on('click', '.btn-remove-row', function() {
                let rowCount = $('#sectionDefectContainer .dynamic-row').length;
                if (rowCount > 1) {
                    $(this).closest('.dynamic-row').remove();
                    updateRemoveButtons();
                }
            });

            $('#confirmSubmitBtn').on('click', function() {
                $('#confirmSubmitModal').modal('hide');
                setTimeout(function() {
                    submitForm();
                }, 300);
            });

            setTimeout(function() {
                if ($('#sectionDefectContainer .dynamic-row').length > 0) {
                    let $firstSectionSelect = $('#sectionDefectContainer .section-select:first');
                    let $firstDefectSelect = $('#sectionDefectContainer .defect-select:first');

                    // Isi data jika sectionsData sudah ada
                    if (sectionsData && sectionsData.length > 0) {
                        $firstSectionSelect.empty().append('<option value="">-- Pilih Section --</option>');
                        sectionsData.forEach(function(section) {
                            $firstSectionSelect.append('<option value="' + escapeHtml(section) + '">' + escapeHtml(section) + '</option>');
                        });
                        initializeSelect2ForRow($firstSectionSelect, $firstDefectSelect);
                    }
                }
            }, 500);
        });

        // ==========================
        // LOAD DATA SECTION & DEFECT
        // ==========================

        function loadSectionsData() {
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getSections'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        sectionsData = response.data;

                        // Isi data ke semua section select TERLEBIH DAHULU
                        $('.section-select').each(function() {
                            let $select = $(this);
                            let currentVal = $select.val();
                            $select.empty().append('<option value="">-- Pilih Section --</option>');
                            sectionsData.forEach(function(section) {
                                $select.append('<option value="' + escapeHtml(section) + '">' + escapeHtml(section) + '</option>');
                            });
                            if (currentVal) {
                                $select.val(currentVal);
                            }
                        });

                        // HANYA inisialisasi Select2 jika belum diinisialisasi
                        $('.dynamic-row').each(function() {
                            let $sectionSelect = $(this).find('.section-select');
                            let $defectSelect = $(this).find('.defect-select');

                            // Cek apakah sudah memiliki data-select2-initialized
                            if (!$sectionSelect.data('select2-initialized')) {
                                $sectionSelect.select2({
                                    theme: 'bootstrap-5',
                                    width: '100%',
                                    placeholder: '-- Pilih Section --'
                                });
                                $sectionSelect.data('select2-initialized', true);
                            }

                            if (!$defectSelect.data('select2-initialized')) {
                                $defectSelect.select2({
                                    theme: 'bootstrap-5',
                                    width: '100%',
                                    placeholder: '-- Pilih Defect --'
                                });
                                $defectSelect.data('select2-initialized', true);
                            }

                            // Hapus event handler lama, pasang yang baru
                            $sectionSelect.off('change.section').on('change.section', function() {
                                let selectedSection = $(this).val();
                                let $defectSelect = $(this).closest('.row').find('.defect-select');
                                if (selectedSection) {
                                    loadDefectsForSection($defectSelect, selectedSection);
                                } else {
                                    $defectSelect.empty().append('<option value="">-- Pilih Defect --</option>');
                                    $defectSelect.trigger('change.select2');
                                }
                            });
                        });
                    }
                },
                error: function() {
                    console.error('Gagal memuat data section');
                }
            });
        }

        function initializeSelect2ForRow($sectionSelect, $defectSelect) {
            // Hanya inisialisasi jika belum
            if (!$sectionSelect.data('select2-initialized')) {
                $sectionSelect.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '-- Pilih Section --'
                });
                $sectionSelect.data('select2-initialized', true);
            }

            if (!$defectSelect.data('select2-initialized')) {
                $defectSelect.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '-- Pilih Defect --'
                });
                $defectSelect.data('select2-initialized', true);
            }

            // Hapus event handler lama sebelum pasang yang baru
            $sectionSelect.off('change.section').on('change.section', function() {
                let selectedSection = $(this).val();
                let $defectSelect = $(this).closest('.row').find('.defect-select');
                if (selectedSection) {
                    loadDefectsForSection($defectSelect, selectedSection);
                } else {
                    $defectSelect.empty().append('<option value="">-- Pilih Defect --</option>');
                    $defectSelect.trigger('change.select2');
                }
            });
        }

        function loadDefectsForSection($defectSelect, section) {
            if (!section) {
                $defectSelect.empty().append('<option value="">-- Pilih Defect --</option>');
                $defectSelect.trigger('change.select2');
                return;
            }

            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getDefectsBySection',
                    section: section
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let currentVal = $defectSelect.val();
                        $defectSelect.empty().append('<option value="">-- Pilih Defect --</option>');
                        response.data.forEach(function(defect) {
                            $defectSelect.append('<option value="' + escapeHtml(defect) + '">' + escapeHtml(defect) + '</option>');
                        });
                        if (currentVal) {
                            $defectSelect.val(currentVal);
                        }
                        $defectSelect.trigger('change.select2');
                    }
                }
            });
        }

        // ==========================
        // FUNGSI DYNAMIC ROW
        // ==========================

        function addNewRow() {
            let newIndex = rowCounter++;
            let newRow = `
        <div class="dynamic-row" data-index="${newIndex}">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label text-muted small mb-1">🏭 Section</label>
                    <select class="form-select section-select" name="sections[]" required style="width: 100%">
                        <option value="">-- Pilih Section --</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label text-muted small mb-1">⚠️ Nama Defect</label>
                    <select class="form-select defect-select" name="defects[]" required style="width: 100%">
                        <option value="">-- Pilih Defect --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1" style="visibility:hidden;"></label>
                    <div class="action-buttons-group">
                        <button type="button" class="icon-action-btn icon-remove btn-remove-row" title="Hapus Baris">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Hapus tombol plus dari baris terakhir sebelumnya
            let lastRow = $('#sectionDefectContainer .dynamic-row:last-child');
            lastRow.find('.action-buttons-group .icon-add').remove();

            // Tambahkan baris baru
            $('#sectionDefectContainer').append(newRow);

            // Tambahkan tombol plus ke baris terakhir (baris yang baru ditambahkan)
            let newLastRow = $('#sectionDefectContainer .dynamic-row:last-child');
            newLastRow.find('.action-buttons-group').prepend(`
        <button type="button" class="icon-action-btn icon-add btn-add-row-inline" title="Tambah Baris">
            <i class="ti ti-plus"></i>
        </button>
    `);

            let $newSectionSelect = newLastRow.find('.section-select');
            let $newDefectSelect = newLastRow.find('.defect-select');

            // Isi data section
            $newSectionSelect.empty().append('<option value="">-- Pilih Section --</option>');
            if (sectionsData && sectionsData.length > 0) {
                sectionsData.forEach(function(section) {
                    $newSectionSelect.append('<option value="' + escapeHtml(section) + '">' + escapeHtml(section) + '</option>');
                });
            }

            // Inisialisasi Select2
            initializeSelect2ForRow($newSectionSelect, $newDefectSelect);

            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            let rowCount = $('#sectionDefectContainer .dynamic-row').length;

            // Update tombol hapus: sembunyikan jika hanya 1 baris
            $('.btn-remove-row').each(function() {
                if (rowCount === 1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });

            // Pastikan tombol plus hanya ada di baris terakhir
            $('.btn-add-row-inline').show();
            $('#sectionDefectContainer .dynamic-row').each(function(index) {
                if (index !== rowCount - 1) {
                    // Bukan baris terakhir, hapus tombol plus
                    $(this).find('.btn-add-row-inline').remove();
                } else {
                    // Baris terakhir, pastikan ada tombol plus
                    if ($(this).find('.btn-add-row-inline').length === 0) {
                        $(this).find('.action-buttons-group').prepend(`
                    <button type="button" class="icon-action-btn icon-add btn-add-row-inline" title="Tambah Baris">
                        <i class="ti ti-plus"></i>
                    </button>
                `);
                    }
                }
            });
        }

        function getSectionsAsString() {
            let sections = [];
            $('.section-select').each(function() {
                let val = $(this).val();
                if (val) sections.push(val);
            });
            return sections.join(',');
        }

        function getDefectsAsString() {
            let defects = [];
            $('.defect-select').each(function() {
                let val = $(this).val();
                if (val) defects.push(val);
            });
            return defects.join(',');
        }

        function validateDynamicRows() {
            let valid = true;
            let sections = [];
            let defects = [];

            $('.section-select').each(function() {
                let val = $(this).val();
                if (!val) {
                    valid = false;
                } else {
                    sections.push(val);
                }
            });

            $('.defect-select').each(function() {
                let val = $(this).val();
                if (!val) {
                    valid = false;
                } else {
                    defects.push(val);
                }
            });

            if (!valid) {
                return false;
            }

            if (sections.length !== defects.length) {
                return false;
            }

            return true;
        }

        // ==========================
        // MODE NORMAL (AUTO)
        // ==========================

        function renderNormalMode() {
            // Part No: input text readonly
            let htmlPartno = `<input type="text" class="form-control" id="partno" name="partno" 
                    value="" placeholder="Part No akan terisi otomatis" readonly required>`;
            $('#partnoContainer').html(htmlPartno);

            // Customer: select2 biasa
            let htmlCustomer = `<select class="form-select" id="customer" name="nama_customer" required>
                <option value="">-- Pilih Customer --</option>
            </select>`;
            $('#customerContainer').html(htmlCustomer);

            // Initialize select2 untuk customer
            $('#customer').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Load customers
            loadCustomers();

            partnoSelectInitialized = false;
            customerSelectInitialized = false;
        }

        // ==========================
        // MODE MANUAL (SEMUA DROPDOWN)
        // ==========================

        function renderManualMode() {
            // Part No: dropdown select2
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getPartNo'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        let htmlPartno = '<select class="form-select" id="partnoSelect" name="partno" required style="width: 100%">';
                        htmlPartno += '<option value="">-- Pilih Part No --</option>';
                        response.data.forEach(function(partno) {
                            htmlPartno += '<option value="' + escapeHtml(partno) + '">' + escapeHtml(partno) + '</option>';
                        });
                        htmlPartno += '</select>';
                        $('#partnoContainer').html(htmlPartno);

                        $('#partnoSelect').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: '-- Pilih Part No --'
                        });

                        partnoSelectInitialized = true;
                    } else {
                        let htmlPartno = `<input type="text" class="form-control" id="partno" name="partno" 
                                value="" placeholder="Input Part No manual" required>`;
                        $('#partnoContainer').html(htmlPartno);
                        partnoSelectInitialized = false;
                    }
                }
            });

            // Customer: dropdown select2 biasa
            let htmlCustomer = `<select class="form-select" id="customer" name="nama_customer" required>
                <option value="">-- Pilih Customer --</option>
            </select>`;
            $('#customerContainer').html(htmlCustomer);

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
            $('#lotnoInfo').html('<span class="text-info"><i class="ti ti-info-circle"></i> Mode manual: pilih Part No dan Customer dari daftar</span>');
            $('#partnoInfo').html('');
            $('#customerInfo').html('');
        }

        function resetToNormalMode() {
            if (!isManualMode) {
                renderNormalMode();
            } else {
                renderNormalMode();
                isManualMode = false;
            }
            $('#lotnoInfo').html('');
            $('#partnoInfo').html('');
            $('#customerInfo').html('');
            $('#lotno').val('');
            isProcessing = false;
        }

        // ==========================
        // FUNGSI UTAMA PROSES LOTNO
        // ==========================

        function processLotNo(lotno) {
            if (isProcessing) return;
            isProcessing = true;

            isManualMode = false;
            renderNormalMode();

            let cleanLotNo = lotno.split('.')[0];

            $('#lotnoInfo').html('<span class="loading-spinner-small"></span><span class="text-info">Mencari data Lot No...</span>');
            $('#partnoInfo').html('');
            $('#customerInfo').html('');

            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getPartNoByLotNo',
                    lotno: cleanLotNo
                },
                dataType: 'json',
                success: function(partnoResponse) {
                    if (partnoResponse.status === 'success' && partnoResponse.data) {
                        let partno = partnoResponse.data;
                        $('#partno').val(partno);
                        $('#partnoInfo').html('<span class="text-success"><i class="ti ti-check"></i> Part No: ' + escapeHtml(partno) + '</span>');
                        $('#lotnoInfo').html('<span class="text-success"><i class="ti ti-check"></i> Lot No: ' + escapeHtml(lotno) + ' ditemukan</span>');
                        getCustomerByPartNo(partno);
                    } else {
                        // LOT NO TIDAK DITEMUKAN - Beralih ke mode manual
                        $('#partno').val('');
                        $('#partnoInfo').html('<span class="text-warning"><i class="ti ti-alert"></i> Lot No tidak ditemukan, beralih ke mode manual</span>');
                        $('#lotnoInfo').html('<span class="text-warning"><i class="ti ti-alert"></i> Lot No: ' + escapeHtml(lotno) + ' tidak ditemukan, silahkan input manual</span>');

                        // Beralih ke mode manual
                        enableManualMode();
                        isProcessing = false;
                    }
                },
                error: function() {
                    $('#partnoInfo').html('<span class="text-danger"><i class="ti ti-x"></i> Gagal mencari Lot No</span>');
                    $('#lotnoInfo').html('<span class="text-danger"><i class="ti ti-x"></i> Error saat memproses Lot No</span>');
                    // Beralih ke mode manual jika error
                    enableManualMode();
                    isProcessing = false;
                }
            });
        }

        function getCustomerByPartNo(partno) {
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getCustomerByPartNo',
                    partno: partno
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        setCustomerValue(response.data);
                    } else {
                        $('#customerInfo').html('<span class="text-warning"><i class="ti ti-alert"></i> Customer tidak ditemukan untuk Part No ini, silakan pilih manual</span>');
                        if (!$('#customer').val()) {
                            $('#customer').val('').trigger('change');
                        }
                    }
                    isProcessing = false;
                },
                error: function() {
                    $('#customerInfo').html('<span class="text-danger"><i class="ti ti-x"></i> Gagal mengambil data customer</span>');
                    isProcessing = false;
                }
            });
        }

        function setCustomerValue(customerValue) {
            let customerSelect = $('#customer');
            let optionExists = false;

            customerSelect.find('option').each(function() {
                if ($(this).val() === customerValue) {
                    optionExists = true;
                    return false;
                }
            });

            if (optionExists) {
                customerSelect.val(customerValue).trigger('change');
                $('#customerInfo').html('<span class="text-success"><i class="ti ti-check"></i> Customer: ' + escapeHtml(customerValue) + '</span>');

                let $select2Container = $('#customer').next('.select2-container');
                $select2Container.addClass('auto-filled');
                setTimeout(function() {
                    $select2Container.removeClass('auto-filled');
                }, 1500);
            } else {
                customerSelect.append('<option value="' + escapeHtml(customerValue) + '">' + escapeHtml(customerValue) + '</option>');
                customerSelect.val(customerValue).trigger('change');
                $('#customerInfo').html('<span class="text-success"><i class="ti ti-plus-circle"></i> Customer baru: ' + escapeHtml(customerValue) + '</span>');
            }
        }

        // ==========================
        // LOAD DATA
        // ==========================

        function loadCustomers() {
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getCustomers'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let select = $('#customer');
                        select.empty().append('<option value="">-- Pilih Customer --</option>');
                        response.data.forEach(function(customer) {
                            select.append('<option value="' + escapeHtml(customer) + '">' + escapeHtml(customer) + '</option>');
                        });
                        select.trigger('change');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat data customer', 'error');
                }
            });
        }

        // ==========================
        // KONFIRMASI & SUBMIT
        // ==========================

        function showConfirmModal() {
            let errors = [];
            let partnoValue = partnoSelectInitialized ? $('#partnoSelect').val() : $('#partno').val();

            if (!validateDynamicRows()) {
                errors.push('Section dan Nama Defect harus diisi minimal 1 baris dan jumlahnya harus sama');
            }

            if (!$('#customer').val()) errors.push('Customer harus dipilih');
            if (!$('#lotno').val().trim()) errors.push('Lot No harus diisi');
            if (!partnoValue || partnoValue.trim() === '') errors.push('Part No harus diisi');
            if (!$('#tanggal_ditemukan').val()) errors.push('Tanggal ditemukan harus diisi');
            if (!$('#nama_operator').val().trim()) errors.push('Nama operator harus diisi');
            if (!$('#nama_group').val().trim()) errors.push('Group defect harus diisi');
            if (!$('#qty').val()) errors.push('Qty harus diisi');
            if (!$('#shift').val().trim()) errors.push('Shift harus diisi');
            if (!$('#deskripsi_masalah').val().trim()) errors.push('Deskripsi masalah harus diisi');
            let aksiClaim = $('input[name="aksi_claim_defect"]:checked').val();
            if (!aksiClaim) errors.push('Aksi Claim Defect harus dipilih');

            if (errors.length > 0) {
                Swal.fire('Validasi Gagal', errors.join('<br>'), 'error');
                return;
            }

            let sectionsText = getSectionsAsString();
            let defectsText = getDefectsAsString();

            let previewHtml = `
            <table class="table table-sm table-borderless mb-0">
                <tr><td width="40%"><strong>Customer:</strong></td><td>${escapeHtml($('#customer option:selected').text())}</td></tr>
                <tr><td width="40%"><strong>Tanggal Ditemukan:</strong></td><td>${escapeHtml($('#tanggal_ditemukan').val())}</td></tr>
                <tr><td width="40%"><strong>Lot No:</strong></td><td>${escapeHtml($('#lotno').val())}</td></tr>
                <tr><td width="40%"><strong>Part No:</strong></td><td>${escapeHtml(partnoValue)}</td></tr>
                <tr><td width="40%"><strong>Nama Operator:</strong></td><td>${escapeHtml($('#nama_operator').val())}</td></tr>
                <tr><td width="40%"><strong>Section:</strong></td><td>${escapeHtml(sectionsText)}</td></tr>
                <tr><td width="40%"><strong>Nama Defect:</strong></td><td>${escapeHtml(defectsText)}</td></tr>
                <tr><td width="40%"><strong>Group Defect:</strong></td><td>${escapeHtml($('#nama_group').val())}</td></tr>
                <tr><td width="40%"><strong>Qty:</strong></td><td>${escapeHtml($('#qty').val())}</td></tr>
                <tr><td width="40%"><strong>Shift:</strong></td>
<td>${escapeHtml($('#shift option:selected').text() || $('#shift').val())}</td></tr>
                <tr><td width="40%"><strong>Deskripsi:</strong></td><td>${escapeHtml($('#deskripsi_masalah').val())}</td></tr>
                <tr><td width="40%"><strong>Aksi Claim:</strong></td><td>${escapeHtml(aksiClaim)}</td></tr>
            </table>`;

            $('#previewData').html(previewHtml);
            $('#confirmSubmitModal').modal('show');
        }

        // Di fungsi submitForm()
        function submitForm() {
            let partnoValue = partnoSelectInitialized ? $('#partnoSelect').val() : $('#partno').val();

            // 🔥 KUMPULKAN DATA SEBAGAI ARRAY
            let sections = [];
            let defects = [];

            $('.section-select').each(function() {
                let val = $(this).val();
                if (val && val !== '') sections.push(val);
            });

            $('.defect-select').each(function() {
                let val = $(this).val();
                if (val && val !== '') defects.push(val);
            });

            let formData = new FormData();
            formData.append('action', 'insert');
            formData.append('lotno', $('#lotno').val().trim());
            formData.append('partno', partnoValue);
            formData.append('tanggal_ditemukan', $('#tanggal_ditemukan').val());
            formData.append('nama_operator', $('#nama_operator').val().trim());
            formData.append('nama_group', $('#nama_group').val().trim());
            formData.append('qty', $('#qty').val());
            formData.append('deskripsi_masalah', $('#deskripsi_masalah').val().trim());
            formData.append('nama_customer', $('#customer').val());
            formData.append('aksi_claim_defect', $('input[name="aksi_claim_defect"]:checked').val());
            formData.append('shift', $('#shift').val());

            // 🔥 KIRIM SEBAGAI ARRAY (bukan string comma-separated!)
            for (let i = 0; i < sections.length; i++) {
                formData.append('sections[]', sections[i]);
                formData.append('defects[]', defects[i]);
            }

            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', 'Data claim berhasil disimpan', 'success')
                            .then(() => resetForm());
                    } else {
                        let errorMsg = response.message;
                        if (response.errors) errorMsg = response.errors.join('<br>');
                        Swal.fire('Error', errorMsg, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Gagal menyimpan data';
                    try {
                        let response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                        if (response.errors) errorMsg = response.errors.join('<br>');
                    } catch (e) {}
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }

        function resetForm() {
            $('#formClaim')[0].reset();
            renderNormalMode();
            isManualMode = false;
            partnoSelectInitialized = false;
            $('#partnoInfo, #lotnoInfo, #customerInfo').html('');
            $('#tanggal_ditemukan').val(new Date().toISOString().slice(0, 10));
            $('input[name="aksi_claim_defect"]').prop('checked', false);
            $('#shift').val(''); // Reset dropdown shift
            isProcessing = false;

            // Reset dynamic rows ke 1 row
            $('#sectionDefectContainer').html(`
    <div class="dynamic-row" data-index="0">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <label class="form-label text-muted small mb-1">🏭 Section</label>
                <select class="form-select section-select" name="sections[]" required style="width: 100%">
                    <option value="">-- Pilih Section --</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label text-muted small mb-1">⚠️ Nama Defect</label>
                <select class="form-select defect-select" name="defects[]" required style="width: 100%">
                    <option value="">-- Pilih Defect --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small mb-1" style="visibility:hidden;"></label>
                <div class="action-buttons-group">
                    <button type="button" class="icon-action-btn icon-add btn-add-row-inline" title="Tambah Baris">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
`);

            let $firstSectionSelect = $('#sectionDefectContainer .section-select');
            let $firstDefectSelect = $('#sectionDefectContainer .defect-select');

            // Isi data section
            $firstSectionSelect.empty().append('<option value="">-- Pilih Section --</option>');
            if (sectionsData && sectionsData.length > 0) {
                sectionsData.forEach(function(section) {
                    $firstSectionSelect.append('<option value="' + escapeHtml(section) + '">' + escapeHtml(section) + '</option>');
                });
            }

            // Inisialisasi Select2 untuk row pertama
            initializeSelect2ForRow($firstSectionSelect, $firstDefectSelect);

            rowCounter = 1;

            // Reload sections data jika perlu
            if (!sectionsData || sectionsData.length === 0) {
                loadSectionsData();
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }
    </script>
</body>

</html>