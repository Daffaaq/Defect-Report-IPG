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
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<!-- Date Range Picker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Data Internal Defect</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="../dashboard/index.php" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Internal Defect</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Range Tanggal</label>
                            <input type="text" class="form-control" id="dateRange" placeholder="Pilih range tanggal">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Section</label>
                            <select class="form-select" id="sectionFilter">
                                <option value="all">-- Semua Section --</option>
                                <?php
                                $sections = [
                                    'CNC' => 'CNC',
                                    'CRIMPING_R2' => 'CRIMPING R2',
                                    'CRIMPING_R4' => 'CRIMPING R4',
                                    'ASSY' => 'ASSY'
                                ];
                                foreach ($sections as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" id="applyFilter">
                                <i class="fas fa-filter"></i> Terapkan
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary w-100" id="resetFilter">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-info w-100" id="refreshData" title="Refresh Data">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
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

    <!-- DataTables -->
    <div class="card">
        <div class="card-body">
            <table id="defectTable" class="table table-hover table-striped" style="width:100%">
                <!-- Update thead di index.php -->
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Lot</th>
                        <th>Skema</th>
                        <th>Part Name</th>
                        <th>Terminal A</th>
                        <th>Terminal B</th>
                        <th>Jenis Defect</th>
                        <th>Qty</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Shift</th>
                        <th>Kode Mesin</th>
                        <th>Section</th>
                        <th>Penemu</th>
                        <th>Terminal NG</th>
                        <th>Aksi</th> <!-- Tambahkan kolom aksi -->
                    </tr>
                </thead>
                <tbody id="defectTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Scripts -->
<script src="../../assets/local/sweetalert2@11.js"></script>
<script src="../../assets/local/jquery.dataTables.min.js"></script>
<script src="../../assets/local/dataTables.bootstrap5.min.js"></script>
<script src="../../assets/local/dataTables.responsive.min.js"></script>
<script src="../../assets/local/responsive.bootstrap5.min.js"></script>
<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        let filters = {
            date_start: '',
            date_end: '',
            section: 'all'
        };

        const today = moment();
        const todayStr = today.format('YYYY-MM-DD');
        filters.date_start = todayStr;
        filters.date_end = todayStr;
        $('#dateRange').val(today.format('YYYY-MM-DD') + ' s/d ' + today.format('YYYY-MM-DD'));

        $('#dateRange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' s/d ',
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal'
            },
            autoUpdateInput: false,
            maxDate: moment(),
            opens: 'center'
        });

        $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format('YYYY-MM-DD'));
            filters.date_start = picker.startDate.format('YYYY-MM-DD');
            filters.date_end = picker.endDate.format('YYYY-MM-DD');
        });

        $('#dateRange').on('cancel.daterangepicker', function() {
            $(this).val('');
            filters.date_start = '';
            filters.date_end = '';
        });

        $('#sectionFilter').on('change', function() {
            filters.section = $(this).val();
        });

        const sectionLabels = {
            'CNC': 'CNC',
            'CRIMPING_R2': 'CRIMPING R2',
            'CRIMPING_R4': 'CRIMPING R4',
            'ASSY': 'ASSY',
            'JOINT_R2': 'JOINT R2',
            'JOINT_R4': 'JOINT R4'
        };

        // Fungsi untuk menghapus data
        function deleteData(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: 'myInternalDefectController.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                // Reload DataTable
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + error
                            });
                        }
                    });
                }
            });
        }

        // Inisialisasi DataTable
        const table = $('#defectTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            order: [
                [8, 'desc']
            ],
            language: {
                url: '../../assets/local/datatables-indonesia.json',
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)"
            },
            processing: true,
            ajax: {
                url: 'myInternalDefectController.php',
                data: function(d) {
                    d.date_start = filters.date_start;
                    d.date_end = filters.date_end;
                    d.section = filters.section;
                },
                dataSrc: function(json) {
                    if (!json.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: json.message || 'Gagal memuat data'
                        });
                        return [];
                    }
                    return json.data || [];
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memuat data: ' + error
                    });
                }
            },
            columns: [{
                    data: null,
                    render: (d, t, r, meta) => meta.row + 1
                },
                {
                    data: 'Lot',
                    render: d => `<span class="badge bg-info">${d || '-'}</span>`
                },
                {
                    data: 'Skema',
                    defaultContent: '-'
                },
                {
                    data: 'PartName',
                    defaultContent: '-'
                },
                {
                    data: 'TERMINALA',
                    render: d => `<span class="badge bg-primary">${d || '-'}</span>`
                },
                {
                    data: 'TERMINALB',
                    render: d => `<span class="badge bg-success">${d || '-'}</span>`
                },
                {
                    data: 'JenisDefect',
                    render: d => `<span class="badge bg-danger">${d || '-'}</span>`
                },
                {
                    data: 'Qty',
                    className: 'text-end fw-bold',
                    render: d => parseInt(d || 0).toLocaleString()
                },
                {
                    data: 'Tanggal',
                    defaultContent: '-'
                },
                {
                    data: 'Jam',
                    defaultContent: '-'
                },
                {
                    data: 'Shift',
                    render: function(d) {
                        let color = 'secondary';
                        if (d === 'Shift 1') color = 'primary';
                        else if (d === 'Shift 2') color = 'warning';
                        else if (d === 'Shift 3') color = 'success';
                        return `<span class="badge bg-${color}">${d || '-'}</span>`;
                    }
                },
                {
                    data: 'KodeMesin',
                    render: d => `<span class="badge bg-secondary">${d || '-'}</span>`
                },
                {
                    data: 'Section',
                    render: function(d) {
                        let color = 'secondary';
                        if (d === 'CNC') color = 'info';
                        else if (d === 'CRIMPING_R2') color = 'primary';
                        else if (d === 'CRIMPING_R4') color = 'success';
                        else if (d === 'ASSY') color = 'warning';
                        return `<span class="badge bg-${color}">${sectionLabels[d] || d || '-'}</span>`;
                    }
                },
                {
                    data: 'Noreg',
                    defaultContent: '-'
                },
                {
                    data: 'terminal',
                    defaultContent: '-'
                },
                {
                    data: 'ID',
                    render: function(data, type, row) {
                        return `<button class="btn btn-danger btn-sm btn-delete" data-id="${data}" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>`;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            columnDefs: [{
                targets: 0,
                width: '50px'
            }],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Data Defect Internal'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Data Defect Internal'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info btn-sm'
                },
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copy',
                    className: 'btn btn-secondary btn-sm'
                }
            ]
        });

        // Event listener untuk tombol delete (delegasi event)
        $('#defectTable').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            deleteData(id);
        });

        function loadData() {
            table.ajax.reload();
        }

        $('#applyFilter').on('click', function() {
            if (!filters.date_start || !filters.date_end) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih range tanggal terlebih dahulu'
                });
                return;
            }
            loadData();
            Swal.fire({
                icon: 'success',
                title: 'Filter Diterapkan',
                timer: 1500,
                showConfirmButton: false
            });
        });

        $('#resetFilter').on('click', function() {
            $('#dateRange').val('');
            filters.date_start = '';
            filters.date_end = '';
            $('#sectionFilter').val('all');
            filters.section = 'all';
            loadData();
            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                timer: 1500,
                showConfirmButton: false
            });
        });

        $('#refreshData').on('click', function() {
            loadData();
        });
    });
</script>

<style>
    #defectTable {
        font-size: 0.9rem;
    }

    #defectTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    #defectTable tbody tr:hover {
        background-color: rgba(19, 184, 234, 0.05);
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
        white-space: nowrap;
    }

    .daterangepicker {
        z-index: 9999;
    }

    .dt-buttons {
        margin-bottom: 15px;
    }

    .dt-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    /* Tambahkan di style section */
    .btn-delete {
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-delete:active {
        transform: scale(0.95);
    }

    /* Styling untuk kolom aksi */
    #defectTable td:last-child {
        white-space: nowrap;
    }

    /* Tooltip untuk delete button */
    .btn-delete[title] {
        position: relative;
    }

    /* Styling untuk length menu */
    .dataTables_length select {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        margin: 0 0.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #defectTable {
            font-size: 0.8rem;
        }

        .dt-buttons .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .form-label {
            font-size: 0.85rem;
        }

        .row>[class*="col-"] {
            margin-bottom: 10px;
        }
    }

    /* Loading spinner */
    #loadingSpinner .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Badge section colors */
    .badge.bg-info {
        background-color: #0dcaf0 !important;
    }

    .badge.bg-primary {
        background-color: #0d6efd !important;
    }

    .badge.bg-success {
        background-color: #198754 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }
</style>