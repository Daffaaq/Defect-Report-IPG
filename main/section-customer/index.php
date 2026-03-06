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
                <h4 class="fw-semibold mb-0">Master Customer</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="../dashboard/" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Master Customer</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12 text-end">
            <!-- Tombol Import -->
            <button class="btn btn-success me-2" onclick="showImportModal()">
                <i class="ti ti-upload"></i> Import Customer
            </button>
            <button class="btn btn-info" onclick="showAddCustomerModal()">
                <i class="ti ti-plus"></i> Tambah Customer Baru
            </button>
            <button class="btn btn-outline-info ms-2" onclick="refreshData()">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat data...</p>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Customer</h5>
                    <h3 id="totalCustomer">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables Customer -->
    <div class="card">
        <div class="card-body">
            <table id="customerTable" class="table table-hover table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Customer</th>
                        <th>Nama Singkatan</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL Customer -->
<div class="modal fade" id="customerModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="customerModalTitle">Tambah Customer Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customerForm">
                <div class="modal-body">
                    <input type="hidden" id="customerAction" name="action">
                    <input type="hidden" id="customerId" name="id">

                    <!-- Nama Customer -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Customer <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control"
                            id="nama_customer"
                            name="nama_customer"
                            placeholder="Masukkan nama customer..."
                            required>
                    </div>

                    <!-- Nama Singkatan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Singkatan <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control"
                            id="nama_singkatan"
                            name="nama_singkatan"
                            placeholder="Masukkan singkatan..."
                            required>
                        <small class="text-muted">Contoh: PT ABC -> ABC</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info" id="customerSubmitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL Import Customer -->
<div class="modal fade" id="importModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import Customer dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Download Template -->
                <div class="alert alert-info mb-3">
                    <i class="ti ti-info-circle"></i>
                    Download template Excel untuk format yang benar
                </div>

                <div class="text-center mb-4">
                    <button class="btn btn-outline-info" onclick="downloadTemplate()">
                        <i class="ti ti-download"></i> Download Template
                    </button>
                </div>

                <!-- Upload Form -->
                <form id="importForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File Excel</label>
                        <input type="file"
                            class="form-control"
                            id="importFile"
                            accept=".xls,.xlsx,.csv"
                            required>
                        <small class="text-muted">
                            Format yang didukung: XLS, XLSX, CSV (Maks. 5MB)
                        </small>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress mb-3" id="importProgress" style="display: none; height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                            role="progressbar"
                            style="width: 0%"></div>
                    </div>

                    <!-- Info -->
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Data akan dicek duplikat (case insensitive)</li>
                            <li>Jika ada data duplikat atau error, semua data tidak akan diimport</li>
                            <li>Pastikan format sesuai dengan template</li>
                        </ul>
                    </div>
                </form>

                <!-- Preview Error (akan tampil jika ada error) -->
                <div id="errorPreview" style="display: none;" class="mt-3">
                    <hr>
                    <h6 class="text-danger">Detail Error:</h6>
                    <div class="alert alert-danger" style="max-height: 200px; overflow-y: auto;">
                        <ul id="errorList" class="mb-0"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="importBtn" onclick="importCustomer()">
                    <i class="ti ti-upload"></i> Import Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage">Apakah Anda yakin ingin menghapus customer ini?</p>
                <p class="text-danger mb-0" id="deleteCustomerInfo"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
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

<script>
    let customerTable;
    let allCustomers = [];
    let currentDeleteId = null;
    let baseUrl = 'SectionCustomerController.php';
    let importBaseUrl = 'ImportCustomerController.php';

    $(document).ready(function() {
        initializeTable();
        loadCustomers();
    });

    function initializeTable() {
        customerTable = $('#customerTable').DataTable({
            columns: [{
                    data: null,
                    width: '60px',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'nama_customer',
                    render: function(data) {
                        return escapeHtml(data);
                    }
                },
                {
                    data: 'nama_singkatan',
                    render: function(data) {
                        return `<span class="badge bg-info">${escapeHtml(data)}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    width: '150px',
                    render: function(data) {
                        return formatDate(data);
                    }
                },
                {
                    data: null,
                    width: '120px',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        <button class="btn btn-sm btn-warning me-1" onclick="editCustomer(${row.id})">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDeleteCustomer(${row.id}, '${escapeHtml(row.nama_customer)}')">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                    }
                }
            ],
            order: [
                [1, 'asc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            responsive: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ],
            drawCallback: function() {
                // Update total customer
                let currentData = customerTable.rows({
                    filter: 'applied'
                }).data().toArray();
                $('#totalCustomer').text(currentData.length);
            }
        });
    }

    function loadCustomers() {
        $('#loadingSpinner').show();

        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {
                action: 'getAll'
            },
            dataType: 'json',
            success: function(response) {
                $('#loadingSpinner').hide();

                if (response.status === 'success') {
                    allCustomers = response.data;
                    customerTable.clear().rows.add(allCustomers).draw();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingSpinner').hide();
                let errorMsg = 'Gagal memuat data';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    }

    // ==========================
    // IMPORT FUNCTIONS
    // ==========================

    function showImportModal() {
        $('#importModal').modal('show');
        $('#importFile').val('');
        $('#errorPreview').hide();
        $('#errorList').empty();
    }

    function downloadTemplate() {
        window.location.href = importBaseUrl + '?action=downloadTemplate';
    }

    function importCustomer() {
        let file = $('#importFile')[0].files[0];

        if (!file) {
            Swal.fire('Error', 'Pilih file Excel terlebih dahulu', 'error');
            return;
        }

        // Validasi ekstensi file
        let allowedExtensions = ['xls', 'xlsx', 'csv'];
        let fileExtension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(fileExtension)) {
            Swal.fire('Error', 'Format file harus XLS, XLSX, atau CSV', 'error');
            return;
        }

        // Validasi ukuran file (5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire('Error', 'Ukuran file maksimal 5MB', 'error');
            return;
        }

        // Tampilkan progress bar
        $('#importProgress').show();
        $('.progress-bar').css('width', '0%');

        let formData = new FormData();
        formData.append('action', 'import');
        formData.append('file', file);

        $('#importBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Mengimport...');

        $.ajax({
            url: importBaseUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        let percent = Math.round((e.loaded / e.total) * 100);
                        $('.progress-bar').css('width', percent + '%');
                    }
                });
                return xhr;
            },
            success: function(response) {
                $('#importProgress').hide();
                $('.progress-bar').css('width', '0%');

                if (response.status === 'success') {
                    $('#importModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: true
                    }).then(() => {
                        loadCustomers();
                    });
                } else {
                    // Tampilkan error detail
                    let errorHtml = '';

                    if (response.errors && response.errors.length > 0) {
                        errorHtml += '<li><strong>Error:</strong><ul>';
                        response.errors.forEach(function(err) {
                            errorHtml += '<li>' + escapeHtml(err) + '</li>';
                        });
                        errorHtml += '</ul></li>';
                    }

                    if (response.duplicates && response.duplicates.length > 0) {
                        errorHtml += '<li><strong>Duplikat:</strong><ul>';
                        response.duplicates.forEach(function(dup) {
                            errorHtml += '<li>' + escapeHtml(dup) + '</li>';
                        });
                        errorHtml += '</ul></li>';
                    }

                    if (errorHtml) {
                        $('#errorList').html(errorHtml);
                        $('#errorPreview').show();
                    }

                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                $('#importProgress').hide();
                $('.progress-bar').css('width', '0%');

                let errorMsg = 'Gagal mengimport data';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#importBtn').prop('disabled', false).html('<i class="ti ti-upload"></i> Import Data');
            }
        });
    }

    // ==========================
    // CRUD OPERATIONS
    // ==========================

    function showAddCustomerModal() {
        resetModal();

        $('#customerModalTitle').text('Tambah Customer Baru');
        $('#customerAction').val('insert');
        $('#customerId').val('');
        $('#customerSubmitBtn').text('Simpan');

        $('#customerModal').modal('show');
    }

    function editCustomer(id) {
        let customer = allCustomers.find(c => c.id === id);

        if (customer) {
            resetModal();

            $('#customerModalTitle').text('Edit Customer');
            $('#customerAction').val('update');
            $('#customerId').val(customer.id);
            $('#customerSubmitBtn').text('Update');

            $('#nama_customer').val(customer.nama_customer);
            $('#nama_singkatan').val(customer.nama_singkatan);

            $('#customerModal').modal('show');
        } else {
            // Jika tidak ditemukan di cache, ambil dari API
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: {
                    action: 'getDetail',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        let customer = response.data;

                        resetModal();

                        $('#customerModalTitle').text('Edit Customer');
                        $('#customerAction').val('update');
                        $('#customerId').val(customer.id);
                        $('#customerSubmitBtn').text('Update');

                        $('#nama_customer').val(customer.nama_customer);
                        $('#nama_singkatan').val(customer.nama_singkatan);

                        $('#customerModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message || 'Data tidak ditemukan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data customer', 'error');
                }
            });
        }
    }

    $('#customerForm').on('submit', function(e) {
        e.preventDefault();

        // Validasi sederhana
        let nama_customer = $('#nama_customer').val().trim();
        let nama_singkatan = $('#nama_singkatan').val().trim();

        if (!nama_customer) {
            Swal.fire('Error', 'Nama Customer wajib diisi', 'error');
            return;
        }

        if (!nama_singkatan) {
            Swal.fire('Error', 'Nama Singkatan wajib diisi', 'error');
            return;
        }

        $('#customerSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        let formData = new FormData(this);

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#customerModal').modal('hide');
                    Swal.fire('Sukses', response.message, 'success');
                    loadCustomers();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'Gagal menyimpan data';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#customerSubmitBtn').prop('disabled', false).text(
                    $('#customerAction').val() === 'insert' ? 'Simpan' : 'Update'
                );
            }
        });
    });

    // ==========================
    // DELETE HANDLER
    // ==========================

    function confirmDeleteCustomer(id, customerName) {
        currentDeleteId = id;
        $('#deleteMessage').text(`Apakah Anda yakin ingin menghapus customer berikut?`);
        $('#deleteCustomerInfo').html(`
            <strong>Customer:</strong> ${escapeHtml(customerName)}
        `);
        $('#deleteModal').modal('show');
    }

    $('#confirmDeleteBtn').on('click', function() {
        if (!currentDeleteId) return;

        let formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', currentDeleteId);

        $('#confirmDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...');

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#deleteModal').modal('hide');
                    Swal.fire('Sukses', response.message, 'success');
                    loadCustomers();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'Gagal menghapus data';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#confirmDeleteBtn').prop('disabled', false).text('Hapus');
            }
        });
    });

    // ==========================
    // UTILITY FUNCTIONS
    // ==========================

    function resetModal() {
        $('#customerForm')[0].reset();
        $('#customerId').val('');
    }

    function refreshData() {
        loadCustomers();
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

    function formatDate(dateString) {
        if (!dateString) return '-';
        let date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Reset modal ketika ditutup
    $('#customerModal').on('hidden.bs.modal', function() {
        resetModal();
    });

    $('#importModal').on('hidden.bs.modal', function() {
        $('#importFile').val('');
        $('#errorPreview').hide();
        $('#errorList').empty();
    });

    $('#deleteModal').on('hidden.bs.modal', function() {
        currentDeleteId = null;
    });
</script>

<style>
    /* DataTables custom styling */
    .dataTables_wrapper {
        margin-top: 10px;
    }

    /* Badge styling */
    .badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
    }

    /* Table styling */
    #customerTable {
        font-size: 0.9rem;
    }

    #customerTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    /* Hover effect */
    #customerTable tbody tr:hover {
        background-color: rgba(19, 184, 234, 0.05);
    }

    /* Card styling */
    .card .card-title {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .card h3 {
        margin-bottom: 0;
        font-weight: 600;
    }

    /* Progress bar */
    .progress {
        border-radius: 5px;
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    /* Error preview */
    #errorPreview ul {
        padding-left: 20px;
    }

    #errorPreview li {
        margin-bottom: 3px;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #customerTable {
            font-size: 0.8rem;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        .card h3 {
            font-size: 1.5rem;
        }

        .text-end .btn {
            margin-bottom: 5px;
            width: 100%;
        }
    }
</style>