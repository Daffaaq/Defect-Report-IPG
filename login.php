<?php
require_once 'helper/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $noreg = trim($_POST['noreg']);
    $password = trim($_POST['password']);

    // Cek field kosong
    if (empty($noreg) || empty($password)) {
        $_SESSION['error_message'] = "Silakan isi semua field.";
        header("Location: login.php");
        exit();
    }

    // Cek password
    if (!in_array($password, ['insanprima', 'excited', 'harness'])) {
        $_SESSION['error_message'] = "Password salah.";
        header("Location: login.php");
        exit();
    }

    // Query pegawai
    $sql = "SELECT * FROM db_pegawai WHERE noreg = ?";
    $params = [$noreg];
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Cek noreg
    if (!$row) {
        $_SESSION['error_message'] = "Noreg tidak ditemukan.";
        header("Location: login.php");
        exit();
    }

    // Cek resign
    if (!empty($row['tgl_resign'])) {
        $_SESSION['error_message'] = "Akun tidak aktif karena karyawan sudah tidak bekerja.";
        header("Location: login.php");
        exit();
    }

    // Cek divisi
    if (!in_array($row['divisi'], ['Plant 1', 'PRIMA GO AI'])) {
        $_SESSION['error_message'] = "Akses ditolak. Sistem ini hanya dapat digunakan oleh karyawan Plant 1 dan PRIMA GO AI.";
        header("Location: login.php");
        exit();
    }

    // LOGIN BERHASIL
    $_SESSION['login'] = true;
    $_SESSION['noreg'] = $row['noreg'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['ext'] = $row['ext'];
    $_SESSION['divisi'] = $row['divisi'];
    $_SESSION['message'] = "Login berhasil!";

    header('Location: main/Dashboard/');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | IPG System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="dist/images/iconn.png" type="image/x-icon">
    <!-- Bootstrap 5 -->
    <link href="/assets/local-login/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="/assets/local-login/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="/assets/local-login/animate.min.css">
    <!-- Google Fonts -->
    <link href="/assets/local-login/font.css" rel="stylesheet">
    <script src="/assets/local-login/jquery.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Main Container dengan Background Image */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: linear-gradient(rgba(0, 30, 60, 0.85), rgba(0, 45, 90, 0.9)),
                url('assets/images/Background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }

        /* Card Utama - FIXED HEIGHT */
        .login-card {
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 70px rgba(0, 20, 40, 0.5);
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
            position: relative;
            z-index: 10;
            /* Hapus max-height dan overflow biar nggak scroll di laptop */
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel - Info Sistem */
        .info-panel {
            background: linear-gradient(145deg, #002B4A, #003B66);
            padding: 40px 35px;
            /* Kurangi padding */
            height: 100%;
            color: white;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Efek subtle light */
        .info-panel::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03) 0%, transparent 70%);
        }

        .logo-area {
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
            /* Kurangi margin */
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            padding: 10px;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .info-panel h2 {
            font-size: 1.8rem;
            /* Lebih kecil */
            font-weight: 700;
            margin-bottom: 5px;
            line-height: 1.2;
            position: relative;
            z-index: 2;
        }

        .info-panel .subtitle {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 25px;
            /* Kurangi margin */
            padding-bottom: 20px;
            /* Kurangi padding */
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 2;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            /* Kurangi margin */
            position: relative;
            z-index: 2;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            /* Kurangi jarak */
            font-size: 0.9rem;
        }

        .feature-list li i {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .system-badge {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 10px 18px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.85rem;
            margin-top: 15px;
            /* Kurangi margin */
        }

        .system-badge i {
            margin-right: 8px;
            color: #FFD700;
        }

        /* Right Panel - Form Login */
        .form-panel {
            padding: 40px 45px;
            /* Kurangi padding */
            background: white;
            display: flex;
            flex-direction: column;
        }

        .form-header {
            margin-bottom: 25px;
            /* Kurangi margin */
            text-align: left;
        }

        .form-header h3 {
            font-size: 1.8rem;
            /* Lebih kecil */
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .form-header p {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Form Elements */
        .form-group-modern {
            margin-bottom: 20px;
            /* Kurangi margin */
        }

        .form-group-modern label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #334155;
            font-size: 0.85rem;
        }

        .form-group-modern label i {
            margin-right: 6px;
            color: #002B4A;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .input-wrapper .form-control {
            width: 100%;
            padding: 14px 16px 14px 45px;
            /* Kurangi padding */
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            /* Kurangi radius */
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #f8fafc;
            font-family: 'Poppins', sans-serif;
        }

        .input-wrapper .form-control:focus {
            border-color: #002B4A;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 43, 74, 0.1);
            outline: none;
        }

        .input-wrapper .form-control:focus+i {
            color: #002B4A;
        }

        /* Login Button */
        .btn-login-modern {
            width: 100%;
            padding: 14px;
            /* Kurangi padding */
            border: none;
            border-radius: 12px;
            background: linear-gradient(145deg, #002B4A, #003F6B);
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-login-modern:hover {
            background: linear-gradient(145deg, #003B66, #004D82);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 43, 74, 0.3);
        }

        .btn-login-modern i {
            transition: transform 0.3s ease;
        }

        .btn-login-modern:hover i {
            transform: translateX(5px);
        }

        /* Alert */
        .alert-modern {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            border-left: 4px solid;
            animation: slideDown 0.4s ease;
            font-size: 0.85rem;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-modern i {
            margin-right: 10px;
            font-size: 1rem;
        }

        .alert-modern.alert-danger {
            background: #fee2e2;
            border-left-color: #dc2626;
            color: #991b1b;
        }

        .alert-modern.alert-success {
            background: #dcfce7;
            border-left-color: #16a34a;
            color: #166534;
        }

        /* Footer System Info (Non-clickable) */
        .footer-links-modern.static-info {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            font-size: 0.85rem;
            color: #64748b;
            flex-wrap: wrap;
        }

        .footer-links-modern.static-info span {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: default;
            transition: opacity 0.3s ease;
        }

        .footer-links-modern.static-info span i {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        /* System Version */
        .version-info {
            text-align: center;
            margin-top: 15px;
            color: #94a3b8;
            font-size: 0.7rem;
        }

        .version-info i {
            margin-right: 4px;
        }

        /* Shake Animation */
        .shake {
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Loading Spinner */
        .spinner-modern {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ========== RESPONSIVE UNTUK HP ========== */
        @media (max-width: 768px) {
            .login-container {
                padding: 10px;
                align-items: flex-start;
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .login-card {
                border-radius: 24px;
                margin: 0;
                max-height: 95vh;
                overflow-y: auto;
                /* Scroll cuma di HP */
            }

            /* Hide left panel di HP, tapi tampilkan sebagai header kecil */
            .col-lg-6:first-child {
                display: none;
                /* Sembunyikan left panel di HP */
            }

            /* Form panel jadi full width */
            .col-lg-6:last-child {
                width: 100%;
                flex: 0 0 auto;
            }

            .form-panel {
                padding: 30px 25px;
            }

            .form-header {
                text-align: center;
                margin-bottom: 25px;
            }

            .form-header h3 {
                font-size: 1.8rem;
                margin-bottom: 5px;
            }

            .form-header p {
                font-size: 0.9rem;
            }

            /* Form elements lebih kecil sedikit */
            .form-group-modern {
                margin-bottom: 20px;
            }

            .form-group-modern label {
                font-size: 0.85rem;
            }

            .input-wrapper .form-control {
                padding: 14px 16px 14px 42px;
                font-size: 0.9rem;
            }

            .btn-login-modern {
                padding: 14px;
                font-size: 0.95rem;
                margin-top: 15px;
            }

            /* Footer links di HP */
            .footer-links-modern {
                flex-wrap: wrap;
                gap: 15px;
                margin-top: 25px;
                padding-top: 20px;
            }

            .footer-links-modern a {
                font-size: 0.85rem;
            }

            .version-info {
                margin-top: 15px;
                font-size: 0.7rem;
            }

            /* Alert di HP */
            .alert-modern {
                padding: 12px 15px;
                font-size: 0.85rem;
                margin-bottom: 20px;
            }

            .alert-modern i {
                font-size: 1rem;
            }
        }

        /* Untuk HP yang lebih kecil (iPhone SE, dll) */
        @media (max-width: 375px) {
            .form-panel {
                padding: 25px 20px;
            }

            .form-header h3 {
                font-size: 1.6rem;
            }

            .form-header p {
                font-size: 0.85rem;
            }

            .input-wrapper .form-control {
                padding: 12px 16px 12px 40px;
            }

            .btn-login-modern {
                padding: 12px;
            }

            .footer-links-modern {
                gap: 10px;
            }

            .footer-links-modern a {
                font-size: 0.8rem;
            }
        }

        /* Landscape mode di HP */
        @media (max-width: 900px) and (orientation: landscape) {
            .login-container {
                align-items: center;
                padding: 20px;
            }

            .login-card {
                max-height: 90vh;
                overflow-y: auto;
            }

            .form-panel {
                padding: 25px;
            }
        }

        /* Untuk tablet */
        @media (min-width: 769px) and (max-width: 992px) {
            .info-panel {
                padding: 35px 25px;
            }

            .info-panel h2 {
                font-size: 1.6rem;
            }

            .form-panel {
                padding: 35px 30px;
            }

            .form-header h3 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="row g-0">
                <!-- Left Panel - Info Sistem (6 kolom) -->
                <div class="col-lg-6">
                    <div class="info-panel">
                        <div class="logo-area">
                            <div class="logo-icon">
                                <img src="assets/images/iconn.png" alt="Company Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-database\'></i>';">
                            </div>
                            <h2>IPG System</h2>
                            <div class="subtitle">
                                Indoprima Gemilang Plant 1 Management System
                            </div>
                        </div>

                        <ul class="feature-list">
                            <li>
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure & Encrypted Access</span>
                            </li>
                            <li>
                                <i class="fas fa-bolt"></i>
                                <span>High Performance Platform</span>
                            </li>
                            <li>
                                <i class="fas fa-sync"></i>
                                <span>Real-time Data Updates</span>
                            </li>
                            <li>
                                <i class="fas fa-chart-line"></i>
                                <span>Comprehensive Dashboard</span>
                            </li>
                        </ul>

                        <div class="system-badge">
                            <i class="fas fa-clock"></i> System Status: <strong>Operational 24/7</strong>
                        </div>

                        <div style="margin-top: 20px; font-size: 0.8rem; opacity: 0.7; position: relative; z-index: 2;">
                            <i class="fas fa-headset me-2"></i> IS · ext. 2026
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Login Form (6 kolom) -->
                <div class="col-lg-6">
                    <div class="form-panel">
                        <div class="form-header">
                            <h3>Welcome!</h3>
                            <p>Silakan login dengan akun Anda</p>
                        </div>


                        <form id="loginForm" action="login.php" method="POST">
                            <div class="form-group-modern">
                                <label>
                                    <i class="fas fa-id-card"></i> No Register
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="noreg" name="noreg" class="form-control" placeholder="Masukkan Noreg" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group-modern">
                                <label>
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-key"></i>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password">
                                </div>
                            </div>

                            <button type="submit" class="btn-login-modern" id="loginBtn">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>

                        <div class="footer-links-modern static-info">
                            <span><i class="fas fa-server"></i> Server Status: Online</span>
                            <span><i class="fas fa-code-branch"></i> Version: v2.0</span>
                            <span><i class="fas fa-shield-check"></i> Security: Protected</span>
                        </div>

                        <div class="version-info">
                            <i class="fas fa-database"></i> PRIMA GO AI · Internal Use
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Loading state on form submit
            $('#loginForm').on('submit', function(e) {
                let noreg = $('#noreg').val().trim();
                let password = $('#password').val().trim();

                if (noreg === "" || password === "") {
                    e.preventDefault();

                    if (noreg === "") {
                        $('#noreg').addClass('shake');
                        setTimeout(() => $('#noreg').removeClass('shake'), 400);
                    }

                    if (password === "") {
                        $('#password').addClass('shake');
                        setTimeout(() => $('#password').removeClass('shake'), 400);
                    }

                    // Tampilkan alert manual
                    $('.alert-modern').remove();
                    let alertHtml = '<div class="alert-modern alert-danger"><i class="fas fa-exclamation-circle"></i>Harap isi semua field!</div>';
                    $('.form-header').after(alertHtml);
                } else {
                    // Show loading state
                    let btn = $('#loginBtn');
                    btn.html('<span class="spinner-modern me-2"></span> Memproses...').prop('disabled', true);
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert-modern').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);

            // Input focus animation
            $('.form-control').focus(function() {
                $(this).parent().find('i').css('color', '#002B4A');
            }).blur(function() {
                $(this).parent().find('i').css('color', '#94a3b8');
            });
        });
    </script>
</body>

</html>