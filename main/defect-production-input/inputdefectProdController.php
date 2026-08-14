<?php
// inputdefectCNCController.php
require_once '../../helper/auth.php';
isLogin();
require_once '../../helper/connection.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// 1. Handle Search Pegawai
if ($action === 'search') {
    header("Content-Type: application/json");

    $search = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (empty($search) || strlen($search) < 2) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    $query = "SELECT TOP 10 Noreg, Nama, Divisi 
              FROM DB_PEGAWAI 
              WHERE Divisi = 'Plant 1' 
                AND tgl_resign IS NULL
                AND (Nama LIKE ? OR Noreg LIKE ?)
              ORDER BY Nama ASC";

    $params = array("%$search%", "%$search%");
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = [
            'Noreg' => $row['Noreg'],
            'Nama' => $row['Nama'],
            'Divisi' => $row['Divisi']
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

// 2. Handle Get Terminal
if ($action === 'getTerminal') {
    header("Content-Type: application/json");

    $lot_no = isset($_GET['lot_no']) ? trim($_GET['lot_no']) : '';
    $skema = isset($_GET['skema']) ? trim($_GET['skema']) : '';
    $section = isset($_GET['section']) ? trim($_GET['section']) : '';

    // Validasi hanya untuk CNC dan CRIMPING
    $allowedSections = ['CNC', 'CRIMPING_R2', 'CRIMPING_R4'];
    if (!in_array($section, $allowedSections)) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }

    if (empty($lot_no) || empty($skema)) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        exit;
    }

    // Query untuk mengambil terminal dari TrRPHMesin
    $query = "SELECT 
                TERMINALA,
                TERMINALB,
                LotNo,
                SeqNoInt
              FROM TrRPHMesin 
              WHERE LotNo = ? AND SeqNoInt = ?
              ORDER BY TERMINALA ASC";

    $params = array($lot_no, $skema);
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . print_r(sqlsrv_errors(), true)
        ]);
        exit;
    }

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Tambahkan TERMINALA
        if (!empty(trim($row['TERMINALA']))) {
            $data[] = [
                'terminal' => trim($row['TERMINALA']),
                'terminal_display' => trim($row['TERMINALA'])
            ];
        }
        
        // Tambahkan TERMINALB jika berbeda dengan TERMINALA
        if (!empty(trim($row['TERMINALB'])) && trim($row['TERMINALB']) !== trim($row['TERMINALA'])) {
            $data[] = [
                'terminal' => trim($row['TERMINALB']),
                'terminal_display' => trim($row['TERMINALB'])
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}

// 3. Handle Save Data
if ($action === 'save') {
    header("Content-Type: application/json");

    // Ambil data
    $section        = isset($_POST['section']) ? trim($_POST['section']) : '';
    $kode_mesin     = isset($_POST['kode_mesin']) ? trim($_POST['kode_mesin']) : '';
    
    // Data array
    $lot_no_array   = isset($_POST['lot_no']) ? $_POST['lot_no'] : [];
    $no_seri_array  = isset($_POST['no_seri']) ? $_POST['no_seri'] : [];
    $defect_array   = isset($_POST['defect']) ? $_POST['defect'] : [];
    $qty_array      = isset($_POST['qty']) ? $_POST['qty'] : [];
    $noreg_array    = isset($_POST['noreg_pembuat_ng']) ? $_POST['noreg_pembuat_ng'] : [];
    $terminal_array = isset($_POST['terminal']) ? $_POST['terminal'] : []; // Tambahan terminal
    
    $row_count = isset($_POST['row_count']) ? intval($_POST['row_count']) : 0;
    $noreg = isset($_SESSION['noreg']) ? $_SESSION['noreg'] : '';

    // Validasi
    $errors = [];
    
    if (empty($section)) {
        $errors[] = 'Section harus dipilih';
    }
    
    if (empty($kode_mesin)) {
        $errors[] = 'Kode Mesin tidak valid';
    }
    
    if (empty($noreg)) {
        $errors[] = 'User tidak terautentikasi';
    }

    if ($row_count < 1) {
        $errors[] = 'Minimal 1 data defect harus diisi';
    }

    // Cek apakah section memerlukan terminal
    $requiresTerminal = in_array($section, ['CNC', 'CRIMPING_R2', 'CRIMPING_R4']);

    // Validasi setiap baris
    for ($i = 0; $i < $row_count; $i++) {
        $row_num = $i + 1;
        
        $lot_no = isset($lot_no_array[$i]) ? trim($lot_no_array[$i]) : '';
        $no_seri = isset($no_seri_array[$i]) ? trim($no_seri_array[$i]) : '';
        $defect = isset($defect_array[$i]) ? trim($defect_array[$i]) : '';
        $qty = isset($qty_array[$i]) ? intval($qty_array[$i]) : 0;
        $terminal = isset($terminal_array[$i]) ? trim($terminal_array[$i]) : '';

        if (empty($lot_no)) {
            $errors[] = "Baris {$row_num}: Lot No harus diisi";
        }
        if (empty($no_seri)) {
            $errors[] = "Baris {$row_num}: No Seri harus diisi";
        }
        if (empty($defect)) {
            $errors[] = "Baris {$row_num}: Defect harus dipilih";
        }
        if ($qty < 1) {
            $errors[] = "Baris {$row_num}: QTY harus lebih dari 0";
        }
        
        // Validasi terminal jika diperlukan
        if ($requiresTerminal && empty($terminal)) {
            $errors[] = "Baris {$row_num}: Terminal harus dipilih untuk section {$section}";
        }
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errors)
        ]);
        exit;
    }

    // Insert data
    sqlsrv_begin_transaction($connection);

    try {
        for ($i = 0; $i < $row_count; $i++) {
            $lot_no = trim($lot_no_array[$i]);
            $no_seri = trim($no_seri_array[$i]);
            $defect = trim($defect_array[$i]);
            $qty = intval($qty_array[$i]);
            $noreg_pembuat = isset($noreg_array[$i]) ? trim($noreg_array[$i]) : null;
            $terminal = isset($terminal_array[$i]) ? trim($terminal_array[$i]) : null;

            // Query dengan tambahan kolom terminal
            $query = "INSERT INTO CNCDefect 
                      (Lot, skema, JenisDefect, Qty, CreatedAt, KodeMesin, Noreg, noreg_pembuat_ng, type, terminal) 
                      VALUES (?, ?, ?, ?, GETDATE(), ?, ?, ?, ?, ?)";

            $params = array(
                $lot_no, 
                $no_seri, 
                $defect, 
                $qty, 
                $kode_mesin,
                $noreg,
                $noreg_pembuat,
                0, // type = 0 internal defect
                $terminal // terminal yang dipilih
            );

            $stmt = sqlsrv_query($connection, $query, $params);

            if ($stmt === false) {
                throw new Exception("Gagal menyimpan data pada baris " . ($i + 1) . ": " . print_r(sqlsrv_errors(), true));
            }
        }

        sqlsrv_commit($connection);

        echo json_encode([
            'success' => true,
            'message' => "Berhasil menyimpan " . $row_count . " data defect!"
        ]);

    } catch (Exception $e) {
        sqlsrv_rollback($connection);
        
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ]);
    }

    exit;
}

// 4. list Defect (GET) berdasarkan section
if ($action === 'listDefect') {
    header("Content-Type: application/json");

    $section = isset($_GET['section']) ? trim($_GET['section']) : '';

    if (empty($section)) {
        echo json_encode([
            'success' => false,
            'message' => 'Section tidak boleh kosong'
        ]);
        exit;
    }

    // Defect lists berdasarkan section
    $defects = [];

    switch ($section) {
        case 'CNC':
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
            break;

        case 'CRIMPING_R2':
        case 'CRIMPING_R4':
            $defects = [
                "Double acc",
                "Missing cover",
                "Missing sleave",
                "Missing wire seal",
                "Missing VT",
                "Salah VT",
                "Sleave terbalik",
                "Salah wire seal",
                "Salah sleave",
                "Kurang skema",
                "wire lecet",
                "Wire seal sobek",
                "Vinyl terjepit",
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
            break;

        case 'JOINT_R2':
        case 'JOINT_R4':
            $defects = [
                "Kurang skema",
                "Missing terminal",
                "Wire lecet",
                "Fraying core",
                "Core wire mundur",
                "Vinyl terjepit",
                "Raychem tembus",
                "Termofit mundur",
                "Termofit kurang matang",
                "Missing termofit"
            ];
            break;

        case 'ASSY':
            // Query dari database untuk ASSY
            $query = "SELECT DISTINCT nama_defect 
                      FROM defect_table 
                      ORDER BY nama_defect ASC";

            $stmt = sqlsrv_query($connection, $query);

            if ($stmt === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . print_r(sqlsrv_errors(), true)
                ]);
                exit;
            }

            $defects = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $defects[] = $row['nama_defect'];
            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Section tidak valid'
            ]);
            exit;
    }

    // Jika tidak ada defect yang ditemukan
    if (empty($defects)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'Tidak ada defect untuk section ini'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $defects
    ]);
    exit;
}

header('Location: index.php');
exit;
?>