<?php
require_once '../../helper/auth.php';

isLogin();
?>

<?php
include '../layout/head.php';
?>

<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-semibold mb-0">Input Masalah</h4>
                <ol class="breadcrumb border border-info px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="index.php" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Input Masalah</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Form Input Masalah -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="formMasalah" method="POST" action="proses_input.php">
                        <div class="row">
                            <!-- Lotno -->
                            <div class="col-md-6 mb-3">
                                <label for="lotno" class="form-label fw-semibold">Lotno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lotno" name="lotno"
                                    placeholder="Masukkan Lotno (isi - jika ingin pilih partno)" required>
                                <small class="text-muted">Ketik "-" jika ingin memilih partno dari dropdown</small>
                            </div>

                            <!-- Partno (Dinamis: text atau select) -->
                            <div class="col-md-6 mb-3" id="partno_container">
                                <label for="partno" class="form-label fw-semibold">Partno <span class="text-danger">*</span></label>
                                <div id="partno_input_container">
                                    <input type="text" class="form-control" id="partno" name="partno"
                                        placeholder="Masukkan Partno" required>
                                </div>
                            </div>

                            <!-- Tanggal Ditemukan -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_ditemukan" class="form-label fw-semibold">Tanggal Ditemukan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_ditemukan"
                                    name="tanggal_ditemukan" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <!-- Nama Operator -->
                            <div class="col-md-6 mb-3">
                                <label for="operator" class="form-label fw-semibold">Nama Operator <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="operator" name="operator"
                                    placeholder="Masukkan nama operator yang menemukan" required>
                            </div>

                            <!-- Section (Dropdown) -->
                            <div class="col-md-6 mb-3">
                                <label for="section" class="form-label fw-semibold">Section <span class="text-danger">*</span></label>
                                <select class="form-select" id="section" name="section" required>
                                    <option value="">-- Pilih Section --</option>
                                    <option value="PRODUKSI">Produksi</option>
                                    <option value="QC">Quality Control (QC)</option>
                                    <option value="MAINTENANCE">Maintenance</option>
                                    <option value="WAREHOUSE">Warehouse</option>
                                    <option value="PURCHASING">Purchasing</option>
                                    <option value="PPIC">PPIC</option>
                                    <option value="ENGINEERING">Engineering</option>
                                    <option value="HRD">HRD</option>
                                    <option value="FINANCE">Finance</option>
                                    <option value="IT">IT</option>
                                </select>
                            </div>

                            <!-- Nama Masalah (Dropdown dinamis berdasarkan section) -->
                            <div class="col-md-12 mb-3">
                                <label for="nama_masalah" class="form-label fw-semibold">Nama Masalah <span class="text-danger">*</span></label>
                                <select class="form-select" id="nama_masalah" name="nama_masalah" required>
                                    <option value="">-- Pilih Masalah --</option>
                                </select>
                            </div>

                            <!-- Deskripsi Masalah (tambahan jika perlu) -->
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label fw-semibold">Deskripsi Masalah</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi"
                                    rows="3" placeholder="Deskripsi tambahan (opsional)"></textarea>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="reset" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="ti ti-send"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data partno untuk select2 (contoh data) -->
<?php
// Data partno (nanti bisa diambil dari database)
$partnoList = [
    'PART-001' => 'PART-001 - Komponen A',
    'PART-002' => 'PART-002 - Komponen B',
    'PART-003' => 'PART-003 - Komponen C',
    'PART-004' => 'PART-004 - Komponen D',
    'PART-005' => 'PART-005 - Komponen E',
    'PART-006' => 'PART-006 - Komponen F',
    'PART-007' => 'PART-007 - Komponen G',
    'PART-008' => 'PART-008 - Komponen H',
    'PART-009' => 'PART-009 - Komponen I',
    'PART-010' => 'PART-010 - Komponen J'
];
?>

<!-- Data masalah berdasarkan section -->
<?php
// Data masalah per section (bisa diambil dari database nantinya)
$masalahBySection = [
    'PRODUKSI' => [
        'Mesin mati mendadak',
        'Produk cacat',
        'Cycle time lambat',
        'Material tersangkut',
        'Setting mesin error',
        'Overheat',
        'Getaran berlebih',
        'Suara tidak normal'
    ],
    'QC' => [
        'Dimensi tidak sesuai',
        'Kekasaran permukaan',
        'Warna tidak match',
        'Kebocoran',
        'Kekerasan tidak sesuai',
        'Visual cacat',
        'Berat tidak sesuai',
        'Komposisi kimia'
    ],
    'MAINTENANCE' => [
        'Mesin rusak',
        'Komponen aus',
        'Oli bocor',
        'Bearing rusak',
        'Belt putus',
        'Motor terbakar',
        'Sensor error',
        'Electrical short'
    ],
    'WAREHOUSE' => [
        'Stok tidak cocok',
        'Material salah',
        'Kemasan rusak',
        'Label salah',
        'Expired',
        'Penyimpanan salah',
        'Stock opname',
        'Damage in transit'
    ],
    'PURCHASING' => [
        'Material telat',
        'Kualitas material',
        'Dokumen tidak lengkap',
        'Supplier error',
        'Harga tidak sesuai',
        'Quantity kurang'
    ],
    'PPIC' => [
        'Schedule molor',
        'Kapasitas tidak cukup',
        'Material shortage',
        'Over capacity',
        'Priority change',
        'Order change'
    ],
    'ENGINEERING' => [
        'Design error',
        'Drawing salah',
        'Spesifikasi tidak jelas',
        'Process flow error',
        'Tooling masalah',
        'Setup time lama'
    ],
    'HRD' => [
        'Karyawan absent',
        'Kecelakaan kerja',
        'Keterlambatan',
        'Training kurang',
        'Konflik',
        'Kesalahan administrasi'
    ],
    'FINANCE' => [
        'Pembayaran telat',
        'Budget tidak cukup',
        'Invoice error',
        'Tax masalah',
        'Cost overrun',
        'Claim tertunda'
    ],
    'IT' => [
        'Server down',
        'Aplikasi error',
        'Network lambat',
        'Printer error',
        'Data hilang',
        'Virus/malware',
        'Hardware rusak',
        'Login gagal'
    ]
];
?>

<!-- Style tambahan -->
<style>
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .badge {
        padding: 5px 10px;
        font-size: 0.75rem;
    }

    .btn-sm {
        padding: 3px 8px;
        margin: 0 2px;
    }

    textarea {
        resize: vertical;
    }

    /* Style untuk select2 */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Script untuk dropdown dinamis -->
<script>
    $(document).ready(function() {
        // Data masalah per section
        var masalahData = <?php echo json_encode($masalahBySection); ?>;

        // Data partno untuk select2
        var partnoData = <?php echo json_encode($partnoList); ?>;

        // Fungsi untuk mengubah tampilan Partno berdasarkan nilai Lotno
        function updatePartnoField() {
            var lotnoValue = $('#lotno').val().trim();
            var $container = $('#partno_container');

            if (lotnoValue === '-') {
                // Jika Lotno adalah "-", tampilkan dropdown select2
                var selectHtml = '<select class="form-select" id="partno" name="partno" required style="width: 100%">';
                selectHtml += '<option value="">-- Pilih Partno --</option>';

                $.each(partnoData, function(key, value) {
                    selectHtml += '<option value="' + key + '">' + value + '</option>';
                });

                selectHtml += '</select>';

                $('#partno_input_container').html(selectHtml);

                // Inisialisasi select2
                $('#partno').select2({
                    placeholder: '-- Pilih Partno --',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                // Jika Lotno bukan "-", tampilkan text input biasa
                var inputHtml = '<input type="text" class="form-control" id="partno" name="partno" ' +
                    'placeholder="Masukkan Partno" required value="' +
                    (lotnoValue !== '-' ? lotnoValue : '') + '">';

                $('#partno_input_container').html(inputHtml);

                // Jika ada nilai di lotno (bukan -), isikan ke partno
                if (lotnoValue !== '' && lotnoValue !== '-') {
                    $('#partno').val(lotnoValue);
                }
            }
        }

        // Event listener untuk perubahan Lotno
        $('#lotno').on('input', function() {
            updatePartnoField();
        });

        // Trigger saat halaman dimuat
        updatePartnoField();

        // Ketika section berubah
        $('#section').on('change', function() {
            var selectedSection = $(this).val();
            var $masalahDropdown = $('#nama_masalah');

            // Kosongkan dropdown masalah
            $masalahDropdown.empty();
            $masalahDropdown.append('<option value="">-- Pilih Masalah --</option>');

            if (selectedSection && masalahData[selectedSection]) {
                // Tambahkan opsi masalah sesuai section
                $.each(masalahData[selectedSection], function(index, masalah) {
                    $masalahDropdown.append('<option value="' + masalah + '">' + masalah + '</option>');
                });
            }
        });

        // Trigger change saat halaman load jika ada section terpilih
        if ($('#section').val()) {
            $('#section').trigger('change');
        }

        // Validasi form sebelum submit
        $('#formMasalah').on('submit', function(e) {
            // Validasi Lotno
            if ($('#lotno').val().trim() === '') {
                alert('Lotno harus diisi!');
                $('#lotno').focus();
                return false;
            }

            // Validasi Partno
            var partnoValue = $('#partno').val();
            if (!partnoValue || partnoValue.trim() === '') {
                alert('Partno harus diisi!');
                $('#partno').focus();
                return false;
            }

            // Validasi Tanggal
            if ($('#tanggal_ditemukan').val() === '') {
                alert('Tanggal ditemukan harus diisi!');
                $('#tanggal_ditemukan').focus();
                return false;
            }

            // Validasi Operator
            if ($('#operator').val().trim() === '') {
                alert('Nama operator harus diisi!');
                $('#operator').focus();
                return false;
            }

            // Validasi Section
            if ($('#section').val() === '') {
                alert('Section harus dipilih!');
                $('#section').focus();
                return false;
            }

            // Validasi Nama Masalah
            if ($('#nama_masalah').val() === '') {
                alert('Nama masalah harus dipilih!');
                $('#nama_masalah').focus();
                return false;
            }

            return true;
        });

        // Konfirmasi reset
        $('button[type="reset"]').on('click', function(e) {
            if (!confirm('Yakin ingin mereset form?')) {
                e.preventDefault();
            }
        });
    });
</script>