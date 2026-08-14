<?php
require_once 'helper/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $noreg = trim($_POST['noreg']); // Noreg operator
    $password = trim($_POST['password']);
    if (empty($noreg) || empty($password)) {
        echo json_encode(["status" => "error", "title" => "Oops...", "message" => "Silakan isi semua field!"]);
        exit();
    }

    // Cek apakah noreg operator ada di database
    $sql = "SELECT * FROM DB_PEGAWAI WHERE noreg = ?";
    $stmt = sqlsrv_query($connection, $sql, array($noreg));

    if ($stmt === false) {
        echo json_encode(["status" => "error", "title" => "Oops...", "message" => "Database error!"]);
        exit();
    }

    $rowOperator = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if (!$rowOperator) {
        echo json_encode(["status" => "error", "title" => "Oops...", "message" => "Noreg operator tidak ditemukan!"]);
        exit();
    }

    // Cek apakah supervisor memiliki akses supervisor
    if (!isSupervisor($supervisor)) {
        echo json_encode(["status" => "error", "title" => "Akses Ditolak", "message" => "Noreg supervisor tidak memiliki akses sebagai supervisor!"]);
        exit();
    }

    $_SESSION['login'] = true;
    $_SESSION['noreg'] = $rowOperator['noreg'];
    $_SESSION['nama'] = $rowOperator['nama'];
    $_SESSION['ext'] = $rowOperator['ext'];

    echo json_encode(["status" => "success", "title" => "Berhasil!", "message" => "Login berhasil, mengalihkan..."]);
    exit();
}