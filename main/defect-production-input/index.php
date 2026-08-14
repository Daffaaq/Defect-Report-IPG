<?php
// index.php
require_once '../../helper/auth.php';
isLogin();
require_once '../../helper/connection.php';

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

    /* ===== SECTION SELECTOR ===== */
    .section-selector {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
        align-items: end;
    }

    .section-selector .field-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .section-selector .field-group label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0;
    }

    .kode-mesin-preview {
        padding: 11px 14px;
        background: white;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        font-size: 18px;
        font-weight: 700;
        color: #4f46e5;
        min-height: 48px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .kode-mesin-preview .label-kode {
        font-size: 12px;
        font-weight: 400;
        color: #94a3b8;
    }

    .kode-mesin-preview .badge-kode {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        padding: 4px 20px;
        border-radius: 20px;
        font-size: 16px;
    }

    /* ===== DYNAMIC TABLE ===== */
    .table-container {
        overflow-x: auto;
        margin-top: 10px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .table-container table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .table-container table thead th {
        padding: 14px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 5;
    }

    .table-container table thead th:not(:last-child) {
        border-right: 1px solid #e2e8f0;
    }

    .table-container table tbody tr {
        transition: all 0.2s ease;
    }

    .table-container table tbody tr:hover {
        background: #f8fafc;
    }

    .table-container table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #f1f5f9;
    }

    .table-container table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
    }

    .table-container table tbody td .form-control,
    .table-container table tbody td .form-select {
        padding: 8px 12px;
        font-size: 13px;
        border-radius: 8px;
        min-width: 100px;
    }

    .table-container table tbody td .form-control:focus,
    .table-container table tbody td .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .table-container table tbody td .btn-remove-row {
        background: #fee2e2;
        border: none;
        color: #ef4444;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
    }

    .table-container table tbody td .btn-remove-row:hover {
        background: #fecaca;
        transform: scale(1.05);
    }

    .table-container table tbody td .btn-remove-row:active {
        transform: scale(0.95);
    }

    .table-container table tbody td .btn-remove-row:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none !important;
    }

    .row-number {
        font-weight: 600;
        color: #94a3b8;
        font-size: 13px;
        text-align: center;
        min-width: 30px;
    }

    /* ===== AUTOCOMPLETE IN TABLE ===== */
    .autocomplete-container-inline {
        position: relative;
        min-width: 180px;
    }

    .autocomplete-container-inline .form-control {
        padding-right: 35px;
        min-width: 150px;
    }

    .autocomplete-container-inline .search-icon-inline {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        font-size: 14px;
    }

    .autocomplete-suggestions-inline {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        padding: 6px;
        min-width: 250px;
    }

    .autocomplete-suggestions-inline::-webkit-scrollbar {
        width: 6px;
    }

    .autocomplete-suggestions-inline::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .autocomplete-suggestions-inline::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .autocomplete-suggestions-inline .suggestion-item {
        padding: 10px 14px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .autocomplete-suggestions-inline .suggestion-item:not(:last-child) {
        border-bottom: 1px solid #f1f5f9;
    }

    .autocomplete-suggestions-inline .suggestion-item:hover,
    .autocomplete-suggestions-inline .suggestion-item.active {
        background: #f1f5ff;
        transform: translateX(4px);
    }

    .autocomplete-suggestions-inline .suggestion-item .pegawai-nama {
        font-weight: 600;
        color: #1e293b;
        font-size: 13px;
    }

    .autocomplete-suggestions-inline .suggestion-item .pegawai-noreg {
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    .autocomplete-suggestions-inline .no-result {
        padding: 16px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }

    .selected-pegawai-info-inline {
        margin-top: 6px;
        padding: 6px 12px;
        background: #f0fdf4;
        border-radius: 8px;
        border-left: 3px solid #22c55e;
        display: none;
        font-size: 12px;
        color: #166534;
    }

    .selected-pegawai-info-inline .info-text {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .selected-pegawai-info-inline .info-text span {
        font-weight: 500;
    }

    /* ===== TERMINAL STYLES ===== */
    .terminal-select {
        min-width: 120px;
    }
    
    .terminal-select option {
        padding: 4px 8px;
    }
    
    .terminal-loading {
        color: #6366f1;
        font-size: 12px;
        font-weight: 500;
    }
    
    .terminal-hint-inline {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 2px;
        font-weight: 400;
    }

    /* ===== ADD ROW BUTTON ===== */
    .btn-add-row {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        margin-top: 16px;
    }

    .btn-add-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
    }

    .btn-add-row:active {
        transform: scale(0.97) translateY(0);
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

    .row-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #4f46e5;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 12px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .section-selector {
            grid-template-columns: 1fr;
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

        .table-container table {
            min-width: 750px;
        }

        .btn-add-row {
            width: 100%;
            justify-content: center;
        }
    }

    /* ===== VALIDATION STATES ===== */
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

    /* ===== LOADING SPINNER ===== */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }

    /* ===== SKEMA/NO SERI HEADER ===== */
    #skemaHeader {
        transition: all 0.3s ease;
    }
</style>

<div class="container-fluid">
    <div class="card bg-light-info shadow-none">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Input Internal Defect Production</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Internal Defect</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form id="defectForm" method="POST" action="inputdefectProdController.php">
            
            <!-- Section Selector -->
            <div class="section-selector">
                <div class="field-group">
                    <label>Section <span class="required">*</span></label>
                    <select class="form-select" name="section" id="section" required>
                        <option value="">Pilih Section...</option>
                        <option value="CNC">CNC</option>
                        <option value="CRIMPING_R2">CRIMPING R2</option>
                        <option value="CRIMPING_R4">CRIMPING R4</option>
                        <option value="ASSY">ASSY</option>
                        <option value="JOINT_R2">JOINT R2</option>
                        <option value="JOINT_R4">JOINT R4</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Kode Mesin (Otomatis)</label>
                    <div class="kode-mesin-preview">
                        <span class="label-kode">Kode:</span>
                        <span class="badge-kode" id="kodeMesinPreview">-</span>
                    </div>
                    <input type="hidden" name="kode_mesin" id="kodeMesinHidden">
                </div>
            </div>

            <!-- Dynamic Table -->
            <div class="form-group">
                <label>
                    Data Defect 
                    <span class="required">*</span>
                    <span class="row-count-badge" id="rowCountBadge">0 Baris</span>
                </label>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th style="min-width: 130px;">Lot No</th>
                                <th style="min-width: 130px;" id="skemaHeader">Skema</th>
                                <th style="min-width: 130px;" id="terminalHeader">Terminal</th>
                                <th style="min-width: 150px;">Defect</th>
                                <th style="min-width: 90px;">QTY</th>
                                <th style="min-width: 200px;">Pembuat NG</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Row akan ditambahkan via JS -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn-add-row" id="addRowBtn">
                    <span>+</span> Tambah Baris
                </button>
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
    const tableBody = document.getElementById('tableBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const rowCountBadge = document.getElementById('rowCountBadge');
    const btnSave = document.getElementById('btnSave');
    const sectionSelect = document.getElementById('section');
    const kodeMesinPreview = document.getElementById('kodeMesinPreview');
    const kodeMesinHidden = document.getElementById('kodeMesinHidden');
    const terminalHeader = document.getElementById('terminalHeader');
    const skemaHeader = document.getElementById('skemaHeader');
    
    let rowCounter = 0;
    let terminalCache = {};

    // Mapping Section ke KodeMesin
    const kodeMesinMap = {
        'CNC': 'CSM',
        'CRIMPING_R2': 'R2CRP',
        'CRIMPING_R4': 'R4CRP',
        'ASSY': 'ASSY',
        'JOINT_R2': 'R2JNT',
        'JOINT_R4': 'R4JNT'
    };

    // Section yang memerlukan terminal
    const terminalSections = ['CNC', 'CRIMPING_R2', 'CRIMPING_R4'];

    // Daftar defect options
    const defectOptions = [
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

    // ===== FUNGSI UPDATE HEADER =====
    function updateSkemaHeader() {
        const section = sectionSelect.value;
        if (section === 'ASSY') {
            skemaHeader.textContent = 'No Seri';
        } else {
            skemaHeader.textContent = 'Skema';
        }
    }

    // ===== FUNGSI TERMINAL =====
    function requiresTerminal(section) {
        return terminalSections.includes(section);
    }

    // Fungsi untuk update visibility terminal di semua baris
    function updateTerminalVisibility() {
        const section = sectionSelect.value;
        const showTerminal = requiresTerminal(section);
        
        // Update header
        if (terminalHeader) {
            terminalHeader.style.display = showTerminal ? '' : 'none';
        }
        
        // Update semua baris
        tableBody.querySelectorAll('tr').forEach(function(row) {
            const terminalCell = row.querySelector('.terminal-cell');
            if (terminalCell) {
                terminalCell.style.display = showTerminal ? '' : 'none';
            }
            
            const terminalSelect = row.querySelector('select[name="terminal[]"]');
            if (terminalSelect) {
                if (!showTerminal) {
                    terminalSelect.disabled = true;
                    terminalSelect.value = '';
                    terminalSelect.innerHTML = '<option value="">Pilih Terminal</option>';
                } else {
                    // Cek apakah lot dan skema sudah diisi
                    const lotInput = row.querySelector('input[name="lot_no[]"]');
                    const skemaInput = row.querySelector('input[name="no_seri[]"]');
                    if (lotInput && skemaInput && lotInput.value.trim() && skemaInput.value.trim()) {
                        terminalSelect.disabled = false;
                    } else {
                        terminalSelect.disabled = true;
                        terminalSelect.innerHTML = '<option value="">Pilih Terminal</option>';
                    }
                }
            }
            
            // Update status text
            const statusEl = row.querySelector('.terminal-status');
            if (statusEl) {
                if (!showTerminal) {
                    statusEl.textContent = '';
                    statusEl.style.display = 'none';
                } else {
                    statusEl.style.display = 'block';
                    const lotInput = row.querySelector('input[name="lot_no[]"]');
                    const skemaInput = row.querySelector('input[name="no_seri[]"]');
                    if (lotInput && skemaInput && lotInput.value.trim() && skemaInput.value.trim()) {
                        statusEl.textContent = '⏳ Memuat terminal...';
                        statusEl.style.color = '#6366f1';
                    } else {
                        statusEl.textContent = 'Isi Lot No dan ' + (section === 'ASSY' ? 'No Seri' : 'Skema');
                        statusEl.style.color = '#94a3b8';
                    }
                }
            }
        });
    }

    function fetchTerminal(lot_no, skema, section, selectElement, statusElement) {
        // Validasi
        if (!requiresTerminal(section) || !lot_no || !skema) {
            if (selectElement) {
                selectElement.innerHTML = '<option value="">Pilih Terminal</option>';
                selectElement.disabled = true;
            }
            if (statusElement) {
                statusElement.textContent = 'Isi Lot No dan ' + (section === 'ASSY' ? 'No Seri' : 'Skema');
                statusElement.style.color = '#94a3b8';
            }
            return;
        }

        // Cek cache
        const cacheKey = lot_no + '|' + skema + '|' + section;
        if (terminalCache[cacheKey]) {
            populateTerminalSelect(selectElement, terminalCache[cacheKey], statusElement);
            return;
        }

        // Loading
        if (selectElement) {
            selectElement.innerHTML = '<option value="">Loading...</option>';
            selectElement.disabled = true;
        }
        if (statusElement) {
            statusElement.textContent = '⏳ Memuat terminal...';
            statusElement.style.color = '#6366f1';
        }

        // AJAX request
        fetch('inputdefectProdController.php?action=getTerminal&lot_no=' + encodeURIComponent(lot_no) + 
              '&skema=' + encodeURIComponent(skema) + 
              '&section=' + encodeURIComponent(section))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simpan ke cache
                    terminalCache[cacheKey] = data.data;
                    populateTerminalSelect(selectElement, data.data, statusElement);
                } else {
                    if (selectElement) {
                        selectElement.innerHTML = '<option value="">Error</option>';
                        selectElement.disabled = true;
                    }
                    if (statusElement) {
                        statusElement.textContent = '❌ Gagal memuat terminal';
                        statusElement.style.color = '#ef4444';
                    }
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                if (selectElement) {
                    selectElement.innerHTML = '<option value="">Error</option>';
                    selectElement.disabled = true;
                }
                if (statusElement) {
                    statusElement.textContent = '❌ Koneksi error';
                    statusElement.style.color = '#ef4444';
                }
                console.error('Error:', error);
            });
    }

    function populateTerminalSelect(selectElement, data, statusElement) {
        if (!selectElement) return;
        
        selectElement.innerHTML = '';
        
        if (data && data.length > 0) {
            selectElement.innerHTML = '<option value="">Pilih Terminal</option>';
            data.forEach(function(item) {
                const option = document.createElement('option');
                option.value = item.terminal;
                option.textContent = item.terminal_display;
                selectElement.appendChild(option);
            });
            selectElement.disabled = false;
            if (statusElement) {
                statusElement.textContent = '✅ ' + data.length + ' terminal tersedia';
                statusElement.style.color = '#22c55e';
            }
        } else {
            selectElement.innerHTML = '<option value="">Tidak ada terminal</option>';
            selectElement.disabled = true;
            if (statusElement) {
                statusElement.textContent = '⚠️ Tidak ada terminal';
                statusElement.style.color = '#f59e0b';
            }
        }
    }

    // ===== LOCAL STORAGE =====
    function saveToLocalStorage() {
        const rows = tableBody.querySelectorAll('tr');
        const data = [];
        
        rows.forEach(row => {
            const rowData = {
                lot_no: row.querySelector('input[name="lot_no[]"]')?.value || '',
                no_seri: row.querySelector('input[name="no_seri[]"]')?.value || '',
                terminal: row.querySelector('select[name="terminal[]"]')?.value || '',
                defect: row.querySelector('select[name="defect[]"]')?.value || '',
                qty: row.querySelector('input[name="qty[]"]')?.value || '',
                noreg: row.querySelector('input[type="hidden"][name^="noreg_pembuat_ng"]')?.value || '',
                nama_pegawai: row.querySelector('.pegawai-search')?.value || ''
            };
            data.push(rowData);
        });

        localStorage.setItem('defectFormData', JSON.stringify(data));
        localStorage.setItem('defectSection', sectionSelect.value);
    }

    function loadFromLocalStorage() {
        const savedData = localStorage.getItem('defectFormData');
        const savedSection = localStorage.getItem('defectSection');
        
        if (savedSection) {
            sectionSelect.value = savedSection;
            generateKodeMesin();
            updateSkemaHeader();
        }

        if (savedData) {
            const data = JSON.parse(savedData);
            
            tableBody.innerHTML = '';
            rowCounter = 0;
            
            data.forEach(rowData => {
                const tr = createRow();
                
                tr.querySelector('input[name="lot_no[]"]').value = rowData.lot_no || '';
                tr.querySelector('input[name="no_seri[]"]').value = rowData.no_seri || '';
                tr.querySelector('select[name="defect[]"]').value = rowData.defect || '';
                tr.querySelector('input[name="qty[]"]').value = rowData.qty || '';
                
                // Set terminal jika ada
                if (rowData.terminal) {
                    const terminalSelect = tr.querySelector('select[name="terminal[]"]');
                    // Tunggu terminal load
                    setTimeout(() => {
                        const section = sectionSelect.value;
                        const lot_no = rowData.lot_no || '';
                        const no_seri = rowData.no_seri || '';
                        if (lot_no && no_seri && requiresTerminal(section)) {
                            const statusEl = tr.querySelector('.terminal-status');
                            fetchTerminal(lot_no, no_seri, section, terminalSelect, statusEl);
                            // Set value setelah load
                            setTimeout(() => {
                                terminalSelect.value = rowData.terminal;
                            }, 500);
                        }
                    }, 100);
                }
                
                if (rowData.noreg && rowData.nama_pegawai) {
                    const searchInput = tr.querySelector('.pegawai-search');
                    const noregHidden = tr.querySelector('input[type="hidden"][name^="noreg_pembuat_ng"]');
                    const infoDiv = tr.querySelector('.selected-pegawai-info-inline');
                    const infoNoreg = tr.querySelector('.info-noreg');
                    const infoNama = tr.querySelector('.info-nama');
                    
                    searchInput.value = rowData.nama_pegawai;
                    noregHidden.value = rowData.noreg;
                    if (infoNoreg) infoNoreg.textContent = 'Noreg: ' + rowData.noreg;
                    if (infoNama) infoNama.textContent = 'Nama: ' + rowData.nama_pegawai;
                    infoDiv.style.display = 'block';
                    searchInput.classList.add('is-valid');
                }
                
                tableBody.appendChild(tr);
            });
            
            updateRowNumbers();
            updateRowCount();
            
            // Update visibility setelah load
            setTimeout(function() {
                updateTerminalVisibility();
                updateSkemaHeader();
            }, 50);
            
            return true;
        }
        return false;
    }

    function clearLocalStorage() {
        localStorage.removeItem('defectFormData');
        localStorage.removeItem('defectSection');
    }

    // ===== GENERATE KODE MESIN =====
    function generateKodeMesin() {
        const section = sectionSelect.value;
        if (section && kodeMesinMap[section]) {
            const kode = kodeMesinMap[section];
            kodeMesinPreview.textContent = kode;
            kodeMesinHidden.value = kode;
        } else {
            kodeMesinPreview.textContent = '-';
            kodeMesinHidden.value = '';
        }
        saveToLocalStorage();
    }

    // ===== CREATE ROW =====
    function createRow() {
        rowCounter++;
        const rowId = rowCounter;
        const tr = document.createElement('tr');
        tr.dataset.rowId = rowId;
        
        const section = sectionSelect.value;
        const showTerminal = requiresTerminal(section);
        const skemaLabel = section === 'ASSY' ? 'No Seri' : 'Skema';
        
        tr.innerHTML = `
            <td class="row-number">${rowCounter}</td>
            <td>
                <input type="text" class="form-control" name="lot_no[]" 
                       placeholder="CT26090012" required>
            </td>
            <td>
                <input type="text" class="form-control" name="no_seri[]" 
                       placeholder="${skemaLabel}" required>
            </td>
            <td class="terminal-cell" style="${showTerminal ? '' : 'display:none;'}">
                <select class="form-select terminal-select" name="terminal[]" disabled>
                    <option value="">Pilih Terminal</option>
                </select>
                <div class="terminal-hint-inline terminal-status" style="color:#94a3b8;">
                    Isi Lot No dan ${skemaLabel}
                </div>
            </td>
            <td>
                <select class="form-select" name="defect[]" required>
                    <option value="">Pilih defect...</option>
                    ${defectOptions.map(d => `<option value="${d}">${d}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="qty[]" 
                       placeholder="Jumlah" min="1" required>
            </td>
            <td>
                <div class="autocomplete-container-inline">
                    <input type="text" 
                        class="form-control pegawai-search" 
                        placeholder="Cari pegawai..." 
                        autocomplete="off">
                    <span class="search-icon-inline">🔍</span>
                    <div class="autocomplete-suggestions-inline suggestions-${rowId}"></div>
                </div>
                <input type="hidden" name="noreg_pembuat_ng[]" class="noreg-hidden">
                <div class="selected-pegawai-info-inline">
                    <div class="info-text">
                        <span class="info-noreg">-</span>
                        <span class="info-nama">-</span>
                    </div>
                </div>
            </td>
            <td>
                <button type="button" class="btn-remove-row" title="Hapus baris">✕</button>
            </td>
        `;

        // Setup autocomplete
        setupAutocomplete(rowId, tr);

        // Setup terminal events
        const lotInput = tr.querySelector('input[name="lot_no[]"]');
        const skemaInput = tr.querySelector('input[name="no_seri[]"]');
        const terminalSelect = tr.querySelector('select[name="terminal[]"]');
        const statusEl = tr.querySelector('.terminal-status');

        function checkTerminal() {
            const lot_no = lotInput.value.trim();
            const no_seri = skemaInput.value.trim();
            const section = sectionSelect.value;
            
            if (requiresTerminal(section) && lot_no && no_seri) {
                fetchTerminal(lot_no, no_seri, section, terminalSelect, statusEl);
            } else if (requiresTerminal(section)) {
                terminalSelect.innerHTML = '<option value="">Pilih Terminal</option>';
                terminalSelect.disabled = true;
                if (statusEl) {
                    const label = section === 'ASSY' ? 'No Seri' : 'Skema';
                    statusEl.textContent = 'Isi Lot No dan ' + label;
                    statusEl.style.color = '#94a3b8';
                }
            }
        }

        lotInput.addEventListener('input', checkTerminal);
        skemaInput.addEventListener('input', checkTerminal);
        lotInput.addEventListener('change', checkTerminal);
        skemaInput.addEventListener('change', checkTerminal);

        // Event untuk tombol hapus
        const removeBtn = tr.querySelector('.btn-remove-row');
        removeBtn.addEventListener('click', function() {
            if (tableBody.children.length > 1) {
                tr.remove();
                updateRowNumbers();
                updateRowCount();
                saveToLocalStorage();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: '⚠️ Minimal 1 Baris',
                    text: 'Form harus memiliki minimal 1 baris data',
                    confirmButtonColor: '#6366f1'
                });
            }
        });

        // Auto save
        tr.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('change', saveToLocalStorage);
            input.addEventListener('input', saveToLocalStorage);
        });

        return tr;
    }

    // ===== SETUP AUTOCOMPLETE =====
    function setupAutocomplete(rowId, tr) {
        const searchInput = tr.querySelector('.pegawai-search');
        const suggestionsDiv = tr.querySelector(`.suggestions-${rowId}`);
        const noregHidden = tr.querySelector('.noreg-hidden');
        const infoDiv = tr.querySelector('.selected-pegawai-info-inline');
        const infoNoreg = tr.querySelector('.info-noreg');
        const infoNama = tr.querySelector('.info-nama');
        
        let selectedPegawai = null;
        let searchTimeout = null;

        function searchPegawai(query) {
            if (query.length < 2) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                suggestionsDiv.innerHTML = '<div class="no-result">⏳ Mencari...</div>';
                suggestionsDiv.style.display = 'block';

                fetch('inputdefectProdController.php?action=search&q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySuggestions(data.data);
                        } else {
                            console.error(data.message);
                            suggestionsDiv.innerHTML = '<div class="no-result">❌ Error: ' + data.message + '</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        suggestionsDiv.innerHTML = '<div class="no-result">❌ Gagal terhubung ke server</div>';
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
                    suggestionsDiv.querySelectorAll('.suggestion-item').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                });
                
                suggestionsDiv.appendChild(div);
            });
            
            suggestionsDiv.style.display = 'block';
        }

        function selectPegawai(pegawai) {
            selectedPegawai = pegawai;
            searchInput.value = pegawai.Nama;
            noregHidden.value = pegawai.Noreg;
            
            if (infoNoreg) infoNoreg.textContent = 'Noreg: ' + pegawai.Noreg;
            if (infoNama) infoNama.textContent = 'Nama: ' + pegawai.Nama;
            infoDiv.style.display = 'block';
            
            suggestionsDiv.style.display = 'none';
            searchInput.classList.remove('is-invalid');
            searchInput.classList.add('is-valid');
            
            saveToLocalStorage();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (selectedPegawai && query !== selectedPegawai.Nama) {
                selectedPegawai = null;
                noregHidden.value = '';
                infoDiv.style.display = 'none';
                this.classList.remove('is-valid');
                saveToLocalStorage();
            }
            
            if (query.length >= 2) {
                searchPegawai(query);
            } else {
                suggestionsDiv.style.display = 'none';
            }
        });

        searchInput.addEventListener('keydown', function(e) {
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
                    items[newIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length > 0) {
                    const newIndex = (currentIndex - 1 + items.length) % items.length;
                    items.forEach(el => el.classList.remove('active'));
                    items[newIndex].classList.add('active');
                    items[newIndex].scrollIntoView({ block: 'nearest' });
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
            if (!e.target.closest('.autocomplete-container-inline')) {
                suggestionsDiv.style.display = 'none';
            }
        });
    }

    // ===== UTILITY FUNCTIONS =====
    function addRow() {
        const newRow = createRow();
        tableBody.appendChild(newRow);
        updateRowCount();
        saveToLocalStorage();
        
        // Update visibility untuk baris baru
        updateTerminalVisibility();
        
        newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        const firstInput = newRow.querySelector('input');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 300);
        }
    }

    function updateRowNumbers() {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) {
                numCell.textContent = index + 1;
            }
            row.dataset.rowId = index + 1;
        });
    }

    function updateRowCount() {
        const count = tableBody.children.length;
        rowCountBadge.textContent = `${count} Baris`;
    }

    // ===== VALIDATION =====
    function validateForm() {
        const section = sectionSelect.value;
        const kodeMesin = kodeMesinHidden.value;
        const requiresTerm = requiresTerminal(section);
        let errors = [];

        if (!section) {
            errors.push('Section harus dipilih');
        }

        if (!kodeMesin) {
            errors.push('Kode Mesin tidak valid');
        }

        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach((row, index) => {
            const rowNum = index + 1;
            
            const lotInput = row.querySelector('input[name="lot_no[]"]');
            const skemaInput = row.querySelector('input[name="no_seri[]"]');
            const terminalSelect = row.querySelector('select[name="terminal[]"]');
            const defectSelect = row.querySelector('select[name="defect[]"]');
            const qtyInput = row.querySelector('input[name="qty[]"]');
            
            if (!lotInput.value.trim()) {
                lotInput.classList.add('is-invalid');
                errors.push(`Baris ${rowNum}: Lot No kosong`);
            } else {
                lotInput.classList.remove('is-invalid');
            }
            
            if (!skemaInput.value.trim()) {
                skemaInput.classList.add('is-invalid');
                const label = section === 'ASSY' ? 'No Seri' : 'Skema';
                errors.push(`Baris ${rowNum}: ${label} kosong`);
            } else {
                skemaInput.classList.remove('is-invalid');
            }
            
            if (requiresTerm && !terminalSelect.value) {
                terminalSelect.classList.add('is-invalid');
                errors.push(`Baris ${rowNum}: Terminal harus dipilih`);
            } else {
                terminalSelect.classList.remove('is-invalid');
            }
            
            if (!defectSelect.value) {
                defectSelect.classList.add('is-invalid');
                errors.push(`Baris ${rowNum}: Defect belum dipilih`);
            } else {
                defectSelect.classList.remove('is-invalid');
            }
            
            if (!qtyInput.value || parseInt(qtyInput.value) < 1) {
                qtyInput.classList.add('is-invalid');
                errors.push(`Baris ${rowNum}: QTY harus lebih dari 0`);
            } else {
                qtyInput.classList.remove('is-invalid');
            }
        });

        if (errors.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: '⚠️ Validasi Gagal',
                html: errors.slice(0, 5).join('<br>') + (errors.length > 5 ? `<br><em>... dan ${errors.length - 5} error lainnya</em>` : ''),
                confirmButtonColor: '#6366f1'
            });
            return false;
        }

        return true;
    }

    // ===== SUBMIT FORM =====
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) return;

        const rowCount = tableBody.children.length;
        Swal.fire({
            title: 'Konfirmasi Data',
            html: `Apakah Anda yakin ingin menyimpan <strong>${rowCount}</strong> data defect?`,
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
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
        btnSave.disabled = true;

        const formData = new FormData(form);
        formData.append('action', 'save');
        formData.append('row_count', tableBody.children.length);

        fetch('inputdefectProdController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearLocalStorage();
                
                Swal.fire({
                    icon: 'success',
                    title: '✅ Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
                
                tableBody.innerHTML = '';
                rowCounter = 0;
                addRow();
                sectionSelect.value = '';
                generateKodeMesin();
                updateSkemaHeader();
                // Reset terminal cache
                terminalCache = {};
                // Update visibility
                updateTerminalVisibility();
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
            btnSave.innerHTML = '✓ Simpan Data';
            btnSave.disabled = false;
        });
    }

    // ===== EVENT LISTENERS =====
    addRowBtn.addEventListener('click', addRow);

    // Event untuk section change
    sectionSelect.addEventListener('change', function() {
        generateKodeMesin();
        updateSkemaHeader();
        saveToLocalStorage();
        updateTerminalVisibility();
    });

    form.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        e.preventDefault();
        
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
                clearLocalStorage();
                terminalCache = {};
                tableBody.innerHTML = '';
                rowCounter = 0;
                addRow();
                sectionSelect.value = '';
                generateKodeMesin();
                updateSkemaHeader();
                updateTerminalVisibility();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Form Direset',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    // ===== INIT =====
    const hasLoaded = loadFromLocalStorage();
    if (!hasLoaded) {
        addRow();
        generateKodeMesin();
        updateSkemaHeader();
    }
    // Update visibility saat pertama load
    setTimeout(function() {
        updateTerminalVisibility();
        updateSkemaHeader();
    }, 100);
});
</script>W