<?php
// index.php
require_once '../../helper/auth.php';
isLogin();
require_once '../../helper/connection.php';

// Sembunyikan error display di production
// error_reporting(0);

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
    .form-control,
    .form-select {
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

    .form-control:hover,
    .form-select:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }

    .form-control:focus,
    .form-select:focus {
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

    /* ===== SECTION OPTIONS ===== */
    .section-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .section-option {
        position: relative;
    }

    .section-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        cursor: pointer;
        z-index: 2;
    }

    .section-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin: 0;
        padding: 12px 10px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.25s ease;
        min-height: 44px;
        cursor: pointer;
        letter-spacing: 0.3px;
        position: relative;
        z-index: 1;
        font-family: 'Inter', sans-serif;
    }

    .section-option label:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .section-option input[type="radio"]:checked+label {
        border-color: #6366f1;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        color: #4f46e5;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
        transform: translateY(-2px);
    }

    .section-option input[type="radio"]:checked+label::after {
        content: '✓';
        position: absolute;
        top: -8px;
        right: -8px;
        background: #6366f1;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
    }

    /* ===== BUTTONS ===== */
    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1.5px solid #f1f5f9;
    }

    .btn-group button {
        flex: 1;
        padding: 13px 24px;
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

    .btn-save {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }

    .btn-save:active {
        transform: scale(0.97) translateY(0);
    }

    .btn-save:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    .btn-reset {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-reset:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .btn-reset:active {
        transform: scale(0.97) translateY(0);
    }

    /* ===== AUTOCOMPLETE ===== */
    .autocomplete-container {
        position: relative;
    }

    .autocomplete-container .form-control {
        padding-right: 40px;
    }

    .autocomplete-container .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        font-size: 16px;
    }

    .autocomplete-suggestions {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        max-height: 280px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        padding: 6px;
    }

    .autocomplete-suggestions::-webkit-scrollbar {
        width: 6px;
    }

    .autocomplete-suggestions::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .autocomplete-suggestions::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .autocomplete-suggestions .suggestion-item {
        padding: 10px 14px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .autocomplete-suggestions .suggestion-item:not(:last-child) {
        border-bottom: 1px solid #f1f5f9;
    }

    .autocomplete-suggestions .suggestion-item:hover,
    .autocomplete-suggestions .suggestion-item.active {
        background: #f1f5ff;
        transform: translateX(4px);
    }

    .autocomplete-suggestions .suggestion-item .pegawai-nama {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    .autocomplete-suggestions .suggestion-item .pegawai-noreg {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    .autocomplete-suggestions .no-result {
        padding: 16px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }

    .selected-pegawai-info {
        margin-top: 10px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 10px;
        border-left: 4px solid #6366f1;
        display: none;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .selected-pegawai-info .info-label {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .selected-pegawai-info .info-value {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    /* ===== TERMINAL STYLES ===== */
    #terminal_group {
        transition: all 0.3s ease;
    }

    #terminal_group .form-select option {
        padding: 8px;
    }

    .terminal-loading {
        color: #6366f1;
        font-size: 13px;
        font-weight: 500;
        padding: 4px 0;
    }

    .terminal-hint {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
        font-weight: 400;
    }

    /* ===== VALIDATION STATES ===== */
    .form-control.is-valid {
        border-color: #22c55e;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2322c55e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 18px;
        padding-right: 40px;
    }

    .form-control.is-invalid {
        border-color: #ef4444;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23ef4444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cline x1='15' y1='9' x2='9' y2='15'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 18px;
        padding-right: 40px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .section-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .form-card {
            padding: 20px;
            border-radius: 12px;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group button {
            flex: none;
            padding: 14px;
        }

        .section-options {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .section-option label {
            font-size: 11px;
            padding: 10px 8px;
            min-height: 38px;
        }

        .autocomplete-suggestions .suggestion-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
    }

    @media (max-width: 400px) {
        .section-options {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid">
    <div class="card bg-light-info shadow-none">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Input Defect QC</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Defect QC</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form id="defectForm" method="POST" action="inputdefectQCController.php">
            <!-- Section -->
            <div class="form-group">
                <label>Section <span class="required">*</span></label>
                <div class="section-options">
                    <?php
                    $sections = [
                        "CNC" => "CNC",
                        "CRIMPING_R2" => "CRIMPING R2",
                        "CRIMPING_R4" => "CRIMPING R4",
                        "JOINT_R2" => "JOINT R2",
                        "JOINT_R4" => "JOINT R4",
                        "ASSY" => "ASSY"
                    ];
                    foreach ($sections as $val => $label): ?>
                        <div class="section-option">
                            <input type="radio" name="section" id="section_<?= $val ?>" value="<?= htmlspecialchars($val) ?>" required>
                            <label for="section_<?= $val ?>"><?= htmlspecialchars($label) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Nama Pembuat NG -->
            <div class="form-group">
                <label>Nama Pembuat NG <span class="required">*</span></label>
                <div class="autocomplete-container">
                    <input type="text"
                        class="form-control"
                        id="pegawaiSearch"
                        placeholder="Cari nama pegawai..."
                        autocomplete="off"
                        required>
                    <span class="search-icon">🔍</span>
                    <div class="autocomplete-suggestions" id="suggestions"></div>
                </div>
                <input type="hidden" name="noreg_pembuat_ng" id="noreg">
                <div class="selected-pegawai-info" id="selectedInfo">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 4px;">
                        <div>
                            <span class="info-label">Noreg</span>
                            <div class="info-value" id="selectedNoreg">-</div>
                        </div>
                        <div>
                            <span class="info-label">Nama</span>
                            <div class="info-value" id="selectedNama">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Mesin -->
            <div class="form-group">
                <label>No Mesin <span class="required">*</span></label>
                <input type="number" class="form-control" name="no_mesin" id="noMesin"
                    placeholder="Contoh: 101" required>
            </div>

            <!-- Lot No -->
            <div class="form-group">
                <label>Lot No <span class="required">*</span></label>
                <input type="text" class="form-control" name="lot_no" id="lotNo"
                    placeholder="Contoh: CT26090012" required>
            </div>

            <!-- SKEMA / NO SERI (Dinamis) -->
            <div class="form-group" id="skemaGroup">
                <label id="skemaLabel">Skema <span class="required">*</span></label>
                <input type="text" class="form-control" name="skema" id="skema"
                    placeholder="Contoh: 1" required>
            </div>

            <!-- TERMINAL -->
            <div class="form-group" id="terminal_group" style="display: none;">
                <label>Terminal <span class="required" id="terminalRequired" style="display: none;">*</span></label>
                <select class="form-select" name="terminal" id="terminal" disabled>
                    <option value="">Pilih Terminal</option>
                </select>
                <div class="terminal-hint" id="terminalHint">
                    <span id="terminalStatus">Pilih section CNC atau CRIMPING</span>
                </div>
            </div>

            <!-- QTY -->
            <div class="form-group">
                <label>QTY <span class="required">*</span></label>
                <input type="number" class="form-control" name="qty" id="qty"
                    placeholder="Jumlah qty" min="1" required>
            </div>

            <!-- Defect -->
            <div class="form-group">
                <label>Defect <span class="required">*</span></label>
                <select class="form-select" name="defect" id="defect" required>
                    <option value="">Pilih jenis defect...</option>
                    <?php
                    $defects = [
                        "Cut Core",
                        "Mis Stripping",
                        "Wire Lecet",
                        "Dimensi Wire NG",
                        "Tembaga Merosot / Maju",
                        "Tembaga Tidak Rata",
                        "Terminal Bend Down / Up",
                        "Terminal Deformasi",
                        "Terminal Tidak Tercrimping",
                        "Terminal Twist",
                        "Tabs NG",
                        "Barel NG",
                        "Bellmouth NG",
                        "CH NG",
                        "IH NG",
                        "Fraying Core",
                        "Mis Wire Seal",
                        "Wire Seal NG",
                        "Posisi Seal NG",
                        "Celup NG",
                        "NG Sambungan Wire"
                    ];

                    foreach ($defects as $defect): ?>
                        <option value="<?= htmlspecialchars($defect) ?>"><?= htmlspecialchars($defect) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tombol -->
            <div class="btn-group">
                <button type="reset" class="btn-reset">↺ Reset Form</button>
                <button type="submit" class="btn-save" id="btnSave">✓ Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('defectForm');
        const pegawaiSearch = document.getElementById('pegawaiSearch');
        const suggestionsDiv = document.getElementById('suggestions');
        const noregHidden = document.getElementById('noreg');
        const selectedInfo = document.getElementById('selectedInfo');
        const selectedNoreg = document.getElementById('selectedNoreg');
        const selectedNama = document.getElementById('selectedNama');
        const btnSave = document.getElementById('btnSave');

        // Terminal elements
        const terminalGroup = document.getElementById('terminal_group');
        const terminalSelect = document.getElementById('terminal');
        const terminalStatus = document.getElementById('terminalStatus');
        const terminalRequired = document.getElementById('terminalRequired');

        // SKEMA elements
        const skemaGroup = document.getElementById('skemaGroup');
        const skemaLabel = document.getElementById('skemaLabel');
        const skemaInput = document.getElementById('skema');

        let selectedPegawai = null;
        let debounceTimer = null;

        // Fungsi untuk mengecek apakah section memerlukan terminal
        function requiresTerminal(section) {
            const terminalSections = ['CNC', 'CRIMPING_R2', 'CRIMPING_R4'];
            return terminalSections.includes(section);
        }

        // Fungsi untuk update label SKEMA
        function updateSkemaLabel(section) {
            if (section === 'ASSY') {
                skemaLabel.innerHTML = 'No Seri <span class="required">*</span>';
                skemaInput.placeholder = 'Contoh: SERI-001';
            } else {
                skemaLabel.innerHTML = 'Skema <span class="required">*</span>';
                skemaInput.placeholder = 'Contoh: 1';
            }
        }

        function fetchDefects(section) {
            const defectSelect = document.getElementById('defect');

            // Tampilkan loading
            defectSelect.innerHTML = '<option value="">Loading defects...</option>';
            defectSelect.disabled = true;

            fetch('inputdefectQCController.php?action=listDefect&section=' + encodeURIComponent(section))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        defectSelect.innerHTML = '<option value="">Pilih jenis defect...</option>';

                        if (data.data && data.data.length > 0) {
                            data.data.forEach(function(defect) {
                                const option = document.createElement('option');
                                option.value = defect;
                                option.textContent = defect;
                                defectSelect.appendChild(option);
                            });
                            defectSelect.disabled = false;
                        } else {
                            defectSelect.innerHTML = '<option value="">Tidak ada defect tersedia</option>';
                            defectSelect.disabled = true;
                        }
                    } else {
                        defectSelect.innerHTML = '<option value="">Error loading defects</option>';
                        defectSelect.disabled = true;
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    defectSelect.innerHTML = '<option value="">Error</option>';
                    defectSelect.disabled = true;
                    console.error('Error:', error);
                });
        }

        // Fungsi untuk mengambil terminal (langsung tanpa menunggu lot/skema)
        function fetchTerminal(section) {
            if (!section || !requiresTerminal(section)) {
                hideTerminal();
                return;
            }

            const lot_no = document.getElementById('lotNo').value.trim();
            const skema = document.getElementById('skema').value.trim();

            // Tampilkan terminal group dulu
            terminalGroup.style.display = 'block';
            terminalSelect.disabled = true;
            terminalSelect.innerHTML = '<option value="">Loading...</option>';
            terminalStatus.innerHTML = '⏳ Memuat data terminal...';
            terminalRequired.style.display = 'inline';

            // Jika lot atau skema kosong, tampilkan pesan
            if (!lot_no || !skema) {
                terminalSelect.innerHTML = '<option value="">Pilih Terminal</option>';
                terminalSelect.disabled = true;
                terminalStatus.innerHTML = '⚠️ Isi Lot No dan ' + (section === 'ASSY' ? 'No Seri' : 'Skema') + ' terlebih dahulu';
                terminalStatus.style.color = '#f59e0b';
                return;
            }

            // AJAX request
            fetch('inputdefectQCController.php?action=getTerminal&lot_no=' + encodeURIComponent(lot_no) + '&skema=' + encodeURIComponent(skema) + '&section=' + encodeURIComponent(section))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        terminalSelect.innerHTML = '';

                        if (data.data && data.data.length > 0) {
                            terminalSelect.innerHTML = '<option value="">Pilih Terminal</option>';

                            data.data.forEach(function(item) {
                                const option = document.createElement('option');
                                option.value = item.terminal;
                                option.textContent = item.terminal_display || item.terminal;
                                terminalSelect.appendChild(option);
                            });

                            terminalSelect.disabled = false;
                            terminalStatus.innerHTML = '✅ ' + data.data.length + ' terminal tersedia';
                            terminalStatus.style.color = '#22c55e';
                        } else {
                            terminalSelect.innerHTML = '<option value="">Tidak ada terminal tersedia</option>';
                            terminalSelect.disabled = true;
                            terminalStatus.innerHTML = '⚠️ Tidak ada terminal untuk Lot dan ' + (section === 'ASSY' ? 'No Seri' : 'Skema') + ' ini';
                            terminalStatus.style.color = '#f59e0b';
                        }
                    } else {
                        terminalSelect.innerHTML = '<option value="">Error loading terminal</option>';
                        terminalSelect.disabled = true;
                        terminalStatus.innerHTML = '❌ Gagal mengambil data terminal';
                        terminalStatus.style.color = '#ef4444';
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    terminalSelect.innerHTML = '<option value="">Error</option>';
                    terminalSelect.disabled = true;
                    terminalStatus.innerHTML = '❌ Terjadi kesalahan koneksi';
                    terminalStatus.style.color = '#ef4444';
                    console.error('Error:', error);
                });
        }

        function hideTerminal() {
            terminalGroup.style.display = 'none';
            terminalSelect.disabled = true;
            terminalSelect.value = '';
            terminalRequired.style.display = 'none';
            terminalStatus.innerHTML = 'Pilih section CNC atau CRIMPING';
            terminalStatus.style.color = '#94a3b8';
        }

        // Search Pegawai
        function searchPegawai(query) {
            if (query.length < 2) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                fetch('inputdefectQCController.php?action=search&q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySuggestions(data.data);
                        } else {
                            console.error(data.message);
                            suggestionsDiv.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        suggestionsDiv.style.display = 'none';
                    });
            }, 300);
        }

        function displaySuggestions(data) {
            suggestionsDiv.innerHTML = '';

            if (!data || data.length === 0) {
                suggestionsDiv.innerHTML = '<div class="no-result">😕 Pegawai tidak ditemukan</div>';
                suggestionsDiv.style.display = 'block';
                return;
            }

            data.forEach((pegawai, index) => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.dataset.index = index;
                div.innerHTML = `
                <span class="pegawai-nama">${escapeHtml(pegawai.Nama)}</span>
                <span class="pegawai-noreg">#${escapeHtml(pegawai.Noreg)}</span>
            `;

                div.addEventListener('click', function() {
                    selectPegawai(pegawai);
                });

                div.addEventListener('mouseenter', function() {
                    document.querySelectorAll('.suggestion-item').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                });

                suggestionsDiv.appendChild(div);
            });

            suggestionsDiv.style.display = 'block';
        }

        function selectPegawai(pegawai) {
            selectedPegawai = pegawai;
            pegawaiSearch.value = pegawai.Nama;
            noregHidden.value = pegawai.Noreg;

            selectedNoreg.textContent = pegawai.Noreg;
            selectedNama.textContent = pegawai.Nama;
            selectedInfo.style.display = 'block';

            suggestionsDiv.style.display = 'none';
            pegawaiSearch.classList.remove('is-invalid');
            pegawaiSearch.classList.add('is-valid');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event Listeners untuk Section
        document.querySelectorAll('input[name="section"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const section = this.value;

                // Update label SKEMA
                updateSkemaLabel(section);

                // Fetch terminal langsung saat section dipilih
                fetchTerminal(section);

                // Fetch defect berdasarkan section
                fetchDefects(section);
            });
        });

        // Event Listeners untuk Lot dan Skema (refresh terminal jika sudah muncul)
        document.getElementById('lotNo').addEventListener('input', function() {
            const section = document.querySelector('input[name="section"]:checked');
            if (section && requiresTerminal(section.value)) {
                fetchTerminal(section.value);
            }
        });

        document.getElementById('skema').addEventListener('input', function() {
            const section = document.querySelector('input[name="section"]:checked');
            if (section && requiresTerminal(section.value)) {
                fetchTerminal(section.value);
            }
        });

        // Event Listeners untuk Pegawai Search
        pegawaiSearch.addEventListener('input', function() {
            const query = this.value.trim();

            if (selectedPegawai && query !== selectedPegawai.Nama) {
                selectedPegawai = null;
                noregHidden.value = '';
                selectedInfo.style.display = 'none';
                this.classList.remove('is-valid');
            }

            if (query.length >= 2) {
                searchPegawai(query);
            } else {
                suggestionsDiv.style.display = 'none';
            }
        });

        pegawaiSearch.addEventListener('keydown', function(e) {
            const items = suggestionsDiv.querySelectorAll('.suggestion-item');
            let currentIndex = -1;

            items.forEach((item, index) => {
                if (item.classList.contains('active')) {
                    currentIndex = index;
                }
            });

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length > 0) {
                    const newIndex = (currentIndex + 1) % items.length;
                    items.forEach(el => el.classList.remove('active'));
                    items[newIndex].classList.add('active');
                    items[newIndex].scrollIntoView({
                        block: 'nearest'
                    });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length > 0) {
                    const newIndex = (currentIndex - 1 + items.length) % items.length;
                    items.forEach(el => el.classList.remove('active'));
                    items[newIndex].classList.add('active');
                    items[newIndex].scrollIntoView({
                        block: 'nearest'
                    });
                }
            } else if (e.key === 'Enter') {
                const activeItem = suggestionsDiv.querySelector('.suggestion-item.active');
                if (activeItem) {
                    e.preventDefault();
                    activeItem.click();
                }
            } else if (e.key === 'Escape') {
                suggestionsDiv.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.autocomplete-container')) {
                suggestionsDiv.style.display = 'none';
            }
        });

        // Submit Form via AJAX dengan SweetAlert
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const section = form.querySelector('input[name="section"]:checked');
            const noreg = noregHidden.value.trim();
            const noMesin = document.getElementById('noMesin').value.trim();
            const lotNo = document.getElementById('lotNo').value.trim();
            const skema = document.getElementById('skema').value.trim();
            const qty = document.getElementById('qty').value.trim();
            const defect = document.getElementById('defect').value;
            const terminal = document.getElementById('terminal').value;

            let errors = [];

            if (!section) errors.push('• Section harus dipilih');
            if (!noreg) errors.push('• Pembuat NG harus dipilih dari daftar');
            if (!noMesin) errors.push('• No Mesin harus diisi');
            if (!lotNo) errors.push('• Lot No harus diisi');
            if (!skema) errors.push('• ' + (section && section.value === 'ASSY' ? 'No Seri' : 'Skema') + ' harus diisi');
            if (!qty || parseInt(qty) < 1) errors.push('• QTY harus diisi angka positif');
            if (!defect) errors.push('• Defect harus dipilih');

            // Validasi terminal jika section CNC atau CRIMPING
            if (section && requiresTerminal(section.value)) {
                if (!terminal) {
                    errors.push('• Terminal harus dipilih untuk section ' + section.value);
                }
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: '⚠️ Validasi Gagal',
                    html: errors.join('<br>'),
                    confirmButtonColor: '#6366f1',
                    confirmButtonText: 'Perbaiki'
                });
                return;
            }

            // Konfirmasi sebelum menyimpan
            Swal.fire({
                title: 'Konfirmasi Data',
                text: 'Apakah Anda yakin ingin menyimpan data ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    saveData();
                }
            });
        });

        function saveData() {
            // Disable button
            btnSave.textContent = '⏳ Menyimpan...';
            btnSave.disabled = true;

            const formData = new FormData(form);
            formData.append('action', 'save');

            fetch('inputdefectQCController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '✅ Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });

                        // Reset form
                        form.reset();
                        pegawaiSearch.value = '';
                        noregHidden.value = '';
                        selectedPegawai = null;
                        selectedInfo.style.display = 'none';
                        pegawaiSearch.classList.remove('is-valid');
                        suggestionsDiv.style.display = 'none';

                        // Reset terminal
                        hideTerminal();
                        terminalSelect.innerHTML = '<option value="">Pilih Terminal</option>';
                        terminalSelect.disabled = true;

                        // Reset label SKEMA ke default
                        skemaLabel.innerHTML = 'Skema <span class="required">*</span>';
                        skemaInput.placeholder = 'Contoh: 1';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '❌ Gagal!',
                            html: data.message,
                            confirmButtonColor: '#6366f1'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ Error!',
                        text: 'Terjadi kesalahan: ' + error,
                        confirmButtonColor: '#6366f1'
                    });
                })
                .finally(() => {
                    btnSave.textContent = '✓ Simpan Data';
                    btnSave.disabled = false;
                });
        }
    });
</script>