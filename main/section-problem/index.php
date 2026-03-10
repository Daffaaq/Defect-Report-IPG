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
                <h4 class="fw-semibold mb-0">Master Defect</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="../dashboard/index.php" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Master Defect</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <label class="form-label fw-semibold">Filter Section</label>
                    <select class="form-select" id="filterSection" onchange="filterBySection()">
                        <option value="">Semua Section</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-8 text-end">
            <button class="btn btn-info" onclick="showAddDefectModal()">
                <i class="ti ti-plus"></i> Tambah Defect Baru
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Total Defect
                        <span id="filterContext" class="small">(Semua Section)</span>
                    </h5>
                    <h3 id="totalDefect">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Total Section
                        <span id="sectionContext" class="small">(Unique)</span>
                    </h5>
                    <h3 id="totalSection">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables Defect -->
    <div class="card">
        <div class="card-body">
            <table id="defectTable" class="table table-hover table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Section</th>
                        <th>Nama Defect</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="defectTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL Defect -->
<div class="modal fade" id="defectModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="defectModalTitle">Tambah Defect Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="defectForm">
                <div class="modal-body">
                    <input type="hidden" id="defectAction" name="action">
                    <input type="hidden" id="defectId" name="id">
                    <input type="hidden" id="finalNamaSection" name="nama_section">

                    <!-- Opsi Pemilihan Section -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilihan Section</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="sectionOption"
                                    id="selectExistingSection" value="existing" checked onclick="toggleSectionInput()">
                                <label class="form-check-label fw-semibold" for="selectExistingSection">
                                    <i class="ti ti-list text-info me-1"></i> Pilih Section yang Sudah Ada
                                </label>
                                <div class="ms-4 mt-2" id="existingSectionContainer">
                                    <select class="form-select" id="existingSection" name="existing_section">
                                        <option value="">-- Pilih Section --</option>
                                    </select>
                                    <small class="text-muted">Pilih dari daftar section yang sudah tersedia</small>
                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sectionOption"
                                    id="addNewSection" value="new" onclick="toggleSectionInput()">
                                <label class="form-check-label fw-semibold" for="addNewSection">
                                    <i class="ti ti-plus text-success me-1"></i> Tambah Section Baru
                                </label>
                                <div class="ms-4 mt-2" id="newSectionContainer" style="display: none;">
                                    <input type="text"
                                        class="form-control"
                                        id="newSection"
                                        name="new_section"
                                        placeholder="Contoh: Produksi, Quality Control">
                                    <small class="text-muted">Masukkan nama section baru</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Multiple Defect Input -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">Daftar Defect</label>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="addDefectRow()" id="addDefectBtn">
                                <i class="ti ti-plus"></i> Tambah Defect
                            </button>
                        </div>

                        <div id="defectRowsContainer">
                            <!-- Defect rows will be added here -->
                            <div class="defect-row mb-2">
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control"
                                        name="nama_defect[]"
                                        placeholder="Masukkan nama defect..."
                                        required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeDefectRow(this)">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <small class="text-muted">
                            <i class="ti ti-info-circle"></i> Anda bisa menambahkan multiple defect sekaligus
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info" id="defectSubmitBtn">Simpan</button>
                </div>
            </form>
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
                <p id="deleteMessage">Apakah Anda yakin ingin menghapus defect ini?</p>
                <p class="text-danger mb-0" id="deleteDefectInfo"></p>
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
    let defectTable;
    let allDefects = [];
    let currentDeleteId = null;
    let baseUrl = 'SectionProblemController.php';
    let allSections = [];

    $(document).ready(function() {
        initializeTable();
        loadDefects();
        loadSectionsForFilter();
    });

    function initializeTable() {
        defectTable = $('#defectTable').DataTable({
            columns: [{
                    data: null,
                    width: '60px',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'nama_section',
                    render: function(data) {
                        return `<span class="badge bg-info">${escapeHtml(data)}</span>`;
                    }
                },
                {
                    data: 'nama_defect',
                    render: function(data) {
                        return escapeHtml(data).replace(/\n/g, '<br>');
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
                        <button class="btn btn-sm btn-warning me-1" onclick="editDefect(${row.id})">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDeleteDefect(${row.id}, '${escapeHtml(row.nama_section)}', '${escapeHtml(row.nama_defect).substring(0, 30)}')">
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
                let currentData = defectTable.rows({
                    filter: 'applied'
                }).data().toArray();
                updateSummary(currentData);
            }
        });
    }

    function addDefectRow() {
        let container = $('#defectRowsContainer');
        let newRow = `
            <div class="defect-row mb-2">
                <div class="input-group">
                    <input type="text" 
                        class="form-control" 
                        name="nama_defect[]"
                        placeholder="Masukkan nama defect..." 
                        required>
                    <button type="button" class="btn btn-outline-danger" onclick="removeDefectRow(this)">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.append(newRow);
    }

    function removeDefectRow(button) {
        if ($('.defect-row').length > 1) {
            $(button).closest('.defect-row').remove();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 defect', 'info');
        }
    }

    function loadDefects() {
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
                    allDefects = response.data;
                    defectTable.clear().rows.add(allDefects).draw();
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

    function loadSectionsForFilter() {
        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: {
                action: 'getSections'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    allSections = response.data;

                    // Populate filter dropdown
                    let filterSelect = $('#filterSection');
                    filterSelect.find('option:not(:first)').remove();

                    allSections.forEach(section => {
                        filterSelect.append(`<option value="${escapeHtml(section)}">${escapeHtml(section)}</option>`);
                    });

                    // Populate existing section dropdown di modal
                    let existingSelect = $('#existingSection');
                    existingSelect.find('option:not(:first)').remove();

                    allSections.forEach(section => {
                        existingSelect.append(`<option value="${escapeHtml(section)}">${escapeHtml(section)}</option>`);
                    });
                }
            }
        });
    }

    function toggleSectionInput() {
        let selectExisting = $('#selectExistingSection').is(':checked');

        if (selectExisting) {
            // Pilih section yang ada - tampilkan dan aktifkan dropdown
            $('#existingSectionContainer').show();
            $('#newSectionContainer').hide();
            $('#existingSection').prop('disabled', false);
            $('#newSection').prop('disabled', true).val('');

            // Kembalikan nilai yang tersimpan jika ada
            let lastSelectedSection = $('#existingSection').data('last-value');
            if (lastSelectedSection) {
                $('#existingSection').val(lastSelectedSection);
            }
        } else {
            // Tambah section baru - simpan dulu nilai dropdown sebelum menyembunyikan
            let currentSectionValue = $('#existingSection').val();
            if (currentSectionValue) {
                $('#existingSection').data('last-value', currentSectionValue);
            }

            $('#existingSectionContainer').hide();
            $('#newSectionContainer').show();
            $('#existingSection').prop('disabled', true).val('');
            $('#newSection').prop('disabled', false);

            // Fokus ke input section baru
            setTimeout(() => {
                $('#newSection').focus();
            }, 100);
        }
    }

    function filterBySection() {
        let selectedSection = $('#filterSection').val();
        let filteredData;
        let contextText = '(Semua Section)';

        if (!selectedSection) {
            filteredData = allDefects;
        } else {
            filteredData = allDefects.filter(item => item.nama_section === selectedSection);
            contextText = `(Section: ${escapeHtml(selectedSection)})`;
        }

        $('#filterContext').text(contextText);
        defectTable.clear().rows.add(filteredData).draw();
        updateSummary(filteredData);
    }

    function updateSummary(data = allDefects) {
        let totalDefect = data.length;
        let uniqueSections = [...new Set(data.map(item => item.nama_section))];

        $('#totalDefect').text(totalDefect);
        $('#totalSection').text(uniqueSections.length);
    }

    // ==========================
    // CRUD OPERATIONS
    // ==========================

    function showAddDefectModal() {
        resetModal();

        $('#defectModalTitle').text('Tambah Defect Baru');
        $('#defectAction').val('insert');
        $('#defectId').val('');
        $('#defectSubmitBtn').text('Simpan');

        // Set default ke pilih section yang ada
        $('#selectExistingSection').prop('checked', true);
        toggleSectionInput();

        // Tampilkan tombol tambah defect
        $('#addDefectBtn').show();

        $('#defectModal').modal('show');
    }

    function editDefect(id) {
        let defect = allDefects.find(d => d.id === id);

        if (defect) {
            resetModal();

            $('#defectModalTitle').text('Edit Defect');
            $('#defectAction').val('update');
            $('#defectId').val(defect.id);
            $('#defectSubmitBtn').text('Update');

            // Set nilai section yang sudah ada
            $('#selectExistingSection').prop('checked', true);
            toggleSectionInput();

            // Pilih section yang sesuai di dropdown
            setTimeout(() => {
                $('#existingSection').val(defect.nama_section);
            }, 500);

            // Isi defect
            $('#defectRowsContainer').empty();
            let editRow = `
                <div class="defect-row mb-2">
                    <div class="input-group">
                        <input type="text" 
                            class="form-control" 
                            name="nama_defect[]"
                            value="${escapeHtml(defect.nama_defect)}"
                            placeholder="Masukkan nama defect..." 
                            required>
                        <button type="button" class="btn btn-outline-danger" onclick="removeDefectRow(this)">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#defectRowsContainer').append(editRow);

            // Sembunyikan tombol tambah defect saat edit (karena hanya 1 defect)
            $('#addDefectBtn').hide();

            $('#defectModal').modal('show');
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
                        let defect = response.data;

                        resetModal();

                        $('#defectModalTitle').text('Edit Defect');
                        $('#defectAction').val('update');
                        $('#defectId').val(defect.id);
                        $('#defectSubmitBtn').text('Update');

                        // Set nilai section yang sudah ada
                        $('#selectExistingSection').prop('checked', true);
                        toggleSectionInput();

                        // Pilih section yang sesuai di dropdown
                        setTimeout(() => {
                            $('#existingSection').val(defect.nama_section);
                        }, 500);

                        // Isi defect
                        $('#defectRowsContainer').empty();
                        let editRow = `
                            <div class="defect-row mb-2">
                                <div class="input-group">
                                    <input type="text" 
                                        class="form-control" 
                                        name="nama_defect[]"
                                        value="${escapeHtml(defect.nama_defect)}"
                                        placeholder="Masukkan nama defect..." 
                                        required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeDefectRow(this)">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        $('#defectRowsContainer').append(editRow);

                        // Sembunyikan tombol tambah defect saat edit
                        $('#addDefectBtn').hide();

                        $('#defectModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message || 'Data tidak ditemukan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data defect', 'error');
                }
            });
        }
    }

    $('#defectForm').on('submit', function(e) {
        e.preventDefault();

        // Validasi input berdasarkan pilihan radio
        let selectExisting = $('#selectExistingSection').is(':checked');
        let namaSection = '';
        let errorMsg = '';

        if (selectExisting) {
            namaSection = $('#existingSection').val();
            if (!namaSection) {
                errorMsg = 'Silakan pilih section dari daftar yang tersedia';
            }
        } else {
            namaSection = $('#newSection').val().trim();
            if (!namaSection) {
                errorMsg = 'Silakan isi nama section baru';
            }
        }

        if (errorMsg) {
            Swal.fire('Error', errorMsg, 'error');
            return;
        }

        // Validasi defect rows
        let defectRows = $('.defect-row');
        let hasDefect = false;
        let emptyFields = [];

        defectRows.each(function(index) {
            let defectValue = $(this).find('input').val().trim();
            if (defectValue) {
                hasDefect = true;
            } else {
                emptyFields.push(index + 1);
            }
        });

        if (!hasDefect) {
            Swal.fire('Error', 'Minimal harus mengisi 1 defect', 'error');
            return;
        }

        if (emptyFields.length > 0) {
            Swal.fire('Error', `Baris ke-${emptyFields.join(', ')} masih kosong`, 'error');
            return;
        }

        // Set nilai final nama_section ke hidden input
        $('#finalNamaSection').val(namaSection);

        $('#defectSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        let formData = new FormData(this);

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

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
                    $('#defectModal').modal('hide');

                    let message = response.message;
                    if (response.total_inserted > 1) {
                        message = `Berhasil menambahkan ${response.total_inserted} defect`;
                    }

                    if (response.final_section_format) {
                        message += `\n\nFormat section: ${response.final_section_format}`;
                    }

                    Swal.fire('Sukses', message, 'success');
                    loadDefects();
                    loadSectionsForFilter();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();

                let errorMsg = 'Gagal menyimpan data';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#defectSubmitBtn').prop('disabled', false).text(
                    $('#defectAction').val() === 'insert' ? 'Simpan' : 'Update'
                );
            }
        });
    });

    // ==========================
    // DELETE HANDLER
    // ==========================

    function confirmDeleteDefect(id, sectionName, defectPreview) {
        currentDeleteId = id;
        $('#deleteMessage').text(`Apakah Anda yakin ingin menghapus defect berikut?`);
        $('#deleteDefectInfo').html(`
            <strong>Section:</strong> ${escapeHtml(sectionName)}<br>
            <strong>Defect:</strong> ${escapeHtml(defectPreview)}...
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
                    loadDefects();
                    loadSectionsForFilter();
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
        $('#defectForm')[0].reset();
        $('#existingSection').prop('disabled', false).removeData('last-value');
        $('#newSection').prop('disabled', true);
        $('#existingSectionContainer').show();
        $('#newSectionContainer').hide();
        $('#finalNamaSection').val('');

        // Reset defect rows ke 1 row
        $('#defectRowsContainer').empty();
        let defaultRow = `
        <div class="defect-row mb-2">
            <div class="input-group">
                <input type="text" 
                    class="form-control" 
                    name="nama_defect[]"
                    placeholder="Masukkan nama defect..." 
                    required>
                <button type="button" class="btn btn-outline-danger" onclick="removeDefectRow(this)">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </div>
    `;
        $('#defectRowsContainer').append(defaultRow);

        // Tampilkan kembali tombol tambah defect
        $('#addDefectBtn').show();
    }

    function refreshData() {
        loadDefects();
        loadSectionsForFilter();
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
    $('#defectModal').on('hidden.bs.modal', function() {
        resetModal();
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
    #defectTable {
        font-size: 0.9rem;
    }

    #defectTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    /* Hover effect */
    #defectTable tbody tr:hover {
        background-color: rgba(19, 184, 234, 0.05);
    }

    /* Card styling */
    .card.bg-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
    }

    .card.bg-success {
        background: linear-gradient(45deg, #1cc88a, #169b6b);
    }

    .card .card-title {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .card h3 {
        margin-bottom: 0;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #defectTable {
            font-size: 0.8rem;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        .card h3 {
            font-size: 1.5rem;
        }
    }
</style>