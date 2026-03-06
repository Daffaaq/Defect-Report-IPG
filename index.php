<?php
session_start();

// Cek apakah session login sudah ada
if (isset($_SESSION['login'])) {
    // Jika sudah login, redirect ke halaman utama
    header('Location: main/dashboard/');
    exit(); // Jangan lupa keluar setelah header untuk menghentikan eksekusi lebih lanjut
} else {
    // Jika belum login, redirect ke halaman login
    header('Location: login.php');
    exit(); // Keluar setelah header
}
