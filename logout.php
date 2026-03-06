<?php
session_start();
unset($_SESSION['login']);
$_SESSION['login'] = null;

// Simpan pesan notifikasi di dalam session
$_SESSION['message'] = "Anda telah berhasil logout.";

// Redirect ke halaman login
header('Location: login.php');
exit();
