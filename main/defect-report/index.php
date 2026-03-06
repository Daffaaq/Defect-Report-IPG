<?php
require_once '../../helper/auth.php';
isLogin();
?>

<?php include '../layout/head.php'; ?>
<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Input Claim Defect</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="../dashboard/" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Input Claim Defect</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat data...</p>
    </div>

    <!-- Form Input Masalah -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="formClaim" method="POST">
                        <div class="row">
                            <!-- Customer -->
                            <div class="col-md-6 mb-3">
                                <label for="customer" class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer" name="nama_customer" required>
                                    <option value="">-- Pilih Customer --</option>
                                </select>
                            </div>

                            <!-- Tanggal Ditemukan -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_ditemukan" class="form-label fw-semibold">Tanggal Ditemukan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_ditemukan"
                                    name="tanggal_ditemukan" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <!-- Lotno -->
                            <div class="col-md-6 mb-3">
                                <label for="lotno" class="form-label fw-semibold">Lot No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lotno" name="lotno"
                                    placeholder="Masukkan Lot No (ketik - untuk pilih partno manual)" required>
                                <small class="text-muted" id="lotnoInfo"></small>
                            </div>

                            <!-- Partno Container (dinamis: text atau select) -->
                            <div class="col-md-6 mb-3">
                                <label for="partno" class="form-label fw-semibold">Part No <span class="text-danger">*</span></label>
                                <div id="partnoContainer">
                                    <!-- Akan diisi oleh JavaScript -->
                                </div>
                                <small class="text-muted" id="partnoInfo"></small>
                            </div>

                            <!-- Nama Operator -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_operator" class="form-label fw-semibold">Nama Operator <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_operator" name="nama_operator"
                                    placeholder="Masukkan nama operator yang menemukan" required>
                            </div>

                            <!-- Section (Dropdown dari database) -->
                            <div class="col-md-6 mb-3">
                                <label for="section" class="form-label fw-semibold">Section <span class="text-danger">*</span></label>
                                <select class="form-select" id="section" name="nama_section" required>
                                    <option value="">-- Pilih Section --</option>
                                </select>
                            </div>

                            <!-- Nama Defect (di tengah dengan offset) -->
                            <div class="col-md-6 offset-md-3 mb-3">
                                <label for="nama_defect" class="form-label fw-semibold">Nama Defect <span class="text-danger">*</span></label>
                                <select class="form-select" id="nama_defect" name="nama_defect" required>
                                    <option value="">-- Pilih Defect --</option>
                                </select>
                            </div>

                            <!-- Deskripsi Masalah -->
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi_masalah" class="form-label fw-semibold">Deskripsi Masalah <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="deskripsi_masalah" name="deskripsi_masalah"
                                    rows="4" placeholder="Jelaskan detail masalah yang ditemukan..." required></textarea>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="ti ti-refresh"></i> Reset
                            </button>
                            <button type="button" class="btn btn-info" id="submitBtn" onclick="showConfirmModal()">
                                <i class="ti ti-send"></i> Submit Claim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL Konfirmasi Submit -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Konfirmasi Submit Claim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyimpan data claim berikut?</p>
                <div class="border rounded p-3 bg-light" id="previewData">
                    <!-- Data preview akan diisi oleh JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" id="confirmSubmitBtn">Ya, Submit</button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let baseUrl = 'DefectReportController.php';
    let partnoSelectInitialized = false;

    $(document).ready(function() {
        // Initialize Select2 untuk dropdown biasa
        $('.form-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Load initial data
        loadCustomers();
        loadSections();
        loadPartnoList(); // Load daftar partno untuk dropdown

        // Set default partno container ke input text
        renderPartnoInput('', true);

        // Event listener untuk perubahan Lot No
        let lotnoTimer;
        $('#lotno').on('input', function() {
            clearTimeout(lotnoTimer);
            let lotno = $(this).val().trim();

            if (lotno === '-') {
                // Jika user mengetik "-", tampilkan dropdown partno
                renderPartnoDropdown();
                $('#lotnoInfo').html('<span class="text-info">Mode pilih partno manual</span>');
            } else if (lotno.length > 0) {
                // Jika ada input lotno, cek ke database
                $('#lotnoInfo').html('<span class="text-info">Mencari Part No...</span>');

                lotnoTimer = setTimeout(function() {
                    getPartNoByLotNo(lotno);
                }, 500);
            } else {
                // Jika kosong, reset ke input readonly
                renderPartnoInput('', true);
                $('#lotnoInfo').html('');
            }
        });

        // Event listener untuk perubahan Section
        $('#section').on('change', function() {
            let section = $(this).val();
            if (section) {
                loadDefectsBySection(section);
            } else {
                $('#nama_defect').empty().append('<option value="">-- Pilih Defect --</option>').trigger('change');
            }
        });

        // Tombol konfirmasi submit - PERBAIKAN DISINI
        $('#confirmSubmitBtn').on('click', function() {
            // Tutup modal terlebih dahulu
            $('#confirmSubmitModal').modal('hide');

            // Beri sedikit jeda sebelum submit agar modal tertutup sempurna
            setTimeout(function() {
                submitForm();
            }, 300);
        });
    });

    // ==========================
    // FUNGSI RENDER PARTNO
    // ==========================

    function renderPartnoInput(value = '', readonly = true) {
        let readonlyAttr = readonly ? 'readonly' : '';
        let html = `<input type="text" class="form-control" id="partno" name="partno" 
                    value="${escapeHtml(value)}" placeholder="Part No" required ${readonlyAttr}>`;
        $('#partnoContainer').html(html);

        // Hapus select2 instance jika ada
        if (partnoSelectInitialized) {
            $('#partnoSelect').select2('destroy');
            partnoSelectInitialized = false;
        }
    }

    function renderPartnoDropdown() {
        // Load daftar partno dan render sebagai select
        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {
                action: 'getPartNo'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let html = '<select class="form-select" id="partnoSelect" name="partno" required style="width: 100%">';
                    html += '<option value="">-- Pilih Part No --</option>';

                    response.data.forEach(function(partno) {
                        html += '<option value="' + escapeHtml(partno) + '">' + escapeHtml(partno) + '</option>';
                    });

                    html += '</select>';

                    $('#partnoContainer').html(html);

                    // Initialize select2 untuk dropdown partno
                    $('#partnoSelect').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '-- Pilih Part No --'
                    });

                    partnoSelectInitialized = true;
                } else {
                    Swal.fire('Error', 'Gagal memuat daftar partno', 'error');
                    renderPartnoInput('', false);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat daftar partno', 'error');
                renderPartnoInput('', false);
            }
        });
    }

    // ==========================
    // FUNGSI LOAD DATA
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

    function loadSections() {
        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {
                action: 'getSections'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let select = $('#section');
                    select.empty().append('<option value="">-- Pilih Section --</option>');

                    response.data.forEach(function(section) {
                        select.append('<option value="' + escapeHtml(section) + '">' + escapeHtml(section) + '</option>');
                    });

                    select.trigger('change');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data section', 'error');
            }
        });
    }

    function loadPartnoList() {
        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {
                action: 'getPartNo'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.partnoList = response.data;
                }
            },
            error: function() {
                console.error('Gagal memuat daftar partno');
            }
        });
    }

    function loadDefectsBySection(section) {
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
                    let select = $('#nama_defect');
                    select.empty().append('<option value="">-- Pilih Defect --</option>');

                    response.data.forEach(function(defect) {
                        select.append('<option value="' + escapeHtml(defect) + '">' + escapeHtml(defect) + '</option>');
                    });

                    select.trigger('change');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data defect', 'error');
            }
        });
    }

    function getPartNoByLotNo(lotno) {
        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {
                action: 'getPartNoByLotNo',
                lotno: lotno
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Tampilkan input text dengan value partno yang ditemukan
                    renderPartnoInput(response.data, true);
                    $('#partnoInfo').html('<span class="text-success">Part No ditemukan</span>');
                    $('#lotnoInfo').html('');
                } else {
                    // Lotno tidak ditemukan, beri option untuk input manual
                    renderPartnoInput('', false);
                    $('#partnoInfo').html('<span class="text-warning">Lot No tidak ditemukan. Silakan input Part No manual.</span>');
                    $('#lotnoInfo').html('');
                }
            },
            error: function() {
                renderPartnoInput('', false);
                $('#partnoInfo').html('<span class="text-danger">Gagal mencari Lot No. Silakan input Part No manual.</span>');
                $('#lotnoInfo').html('');
            }
        });
    }

    // ==========================
    // FUNGSI KONFIRMASI
    // ==========================

    function showConfirmModal() {
        // Validasi terlebih dahulu
        let errors = [];
        let partnoValue = partnoSelectInitialized ? $('#partnoSelect').val() : $('#partno').val();

        if (!$('#customer').val()) errors.push('Customer harus dipilih');
        if (!$('#lotno').val().trim()) errors.push('Lot No harus diisi');
        if (!partnoValue || partnoValue.trim() === '') errors.push('Part No harus diisi');
        if (!$('#tanggal_ditemukan').val()) errors.push('Tanggal ditemukan harus diisi');
        if (!$('#nama_operator').val().trim()) errors.push('Nama operator harus diisi');
        if (!$('#section').val()) errors.push('Section harus dipilih');
        if (!$('#nama_defect').val()) errors.push('Nama defect harus dipilih');
        if (!$('#deskripsi_masalah').val().trim()) errors.push('Deskripsi masalah harus diisi');

        if (errors.length > 0) {
            Swal.fire('Validasi Gagal', errors.join('<br>'), 'error');
            return;
        }

        // Tampilkan preview data
        let customerText = $('#customer option:selected').text();
        let sectionText = $('#section option:selected').text();
        let defectText = $('#nama_defect option:selected').text();

        let previewHtml = `
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td width="40%"><strong>Customer:</strong></td>
                    <td>${escapeHtml(customerText)}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Ditemukan:</strong></td>
                    <td>${escapeHtml($('#tanggal_ditemukan').val())}</td>
                </tr>
                <tr>
                    <td><strong>Lot No:</strong></td>
                    <td>${escapeHtml($('#lotno').val())}</td>
                </tr>
                <tr>
                    <td><strong>Part No:</strong></td>
                    <td>${escapeHtml(partnoValue)}</td>
                </tr>
                <tr>
                    <td><strong>Nama Operator:</strong></td>
                    <td>${escapeHtml($('#nama_operator').val())}</td>
                </tr>
                <tr>
                    <td><strong>Section:</strong></td>
                    <td>${escapeHtml(sectionText)}</td>
                </tr>
                <tr>
                    <td><strong>Nama Defect:</strong></td>
                    <td>${escapeHtml(defectText)}</td>
                </tr>
                <tr>
                    <td><strong>Deskripsi:</strong></td>
                    <td>${escapeHtml($('#deskripsi_masalah').val())}</td>
                </tr>
            </table>
        `;

        $('#previewData').html(previewHtml);

        // Tampilkan modal
        $('#confirmSubmitModal').modal('show');
    }

    // ==========================
    // SUBMIT FORM
    // ==========================

    function submitForm() {
        // Siapkan data
        let partnoValue = partnoSelectInitialized ? $('#partnoSelect').val() : $('#partno').val();

        let formData = new FormData();
        formData.append('action', 'insert');
        formData.append('nama_customer', $('#customer').val());
        formData.append('lotno', $('#lotno').val().trim());
        formData.append('partno', partnoValue);
        formData.append('tanggal_ditemukan', $('#tanggal_ditemukan').val());
        formData.append('nama_operator', $('#nama_operator').val().trim());
        formData.append('nama_section', $('#section').val());
        formData.append('nama_defect', $('#nama_defect').val());
        formData.append('deskripsi_masalah', $('#deskripsi_masalah').val().trim());

        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim data
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data claim berhasil disimpan',
                        showConfirmButton: true
                    }).then(() => {
                        resetForm();
                    });
                } else {
                    let errorMsg = response.message;
                    if (response.errors) {
                        errorMsg = response.errors.join('<br>');
                    }
                    Swal.fire('Error', errorMsg, 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMsg = 'Gagal menyimpan data';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    }

    // ==========================
    // UTILITY FUNCTIONS
    // ==========================

    function resetForm() {
        $('#formClaim')[0].reset();

        // Reset ke input text readonly
        renderPartnoInput('', true);

        $('#partnoInfo').html('');
        $('#lotnoInfo').html('');

        $('.form-select').val('').trigger('change');
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
</script>

<style>
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        font-weight: 500;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .card.bg-light-info {
        background-color: #e6f5fe !important;
    }

    .breadcrumb {
        background-color: white;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Preview table styling */
    #previewData table {
        font-size: 0.9rem;
    }

    #previewData table td {
        padding: 5px 0;
        border: none !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .btn {
            width: 100%;
            margin-bottom: 5px;
        }

        .d-flex.justify-content-end {
            flex-direction: column;
        }

        /* Pada layar kecil, offset tidak berlaku */
        .offset-md-3 {
            margin-left: 0 !important;
        }
    }

    /* Loading spinner */
    #loadingSpinner {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* Info text */
    small.text-muted {
        font-size: 0.8rem;
        margin-top: 4px;
        display: block;
    }
</style>