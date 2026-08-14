<?php
$noreg = isset($_SESSION['noreg']) ? htmlspecialchars($_SESSION['noreg']) : 'default';
$ext   = isset($_SESSION['ext']) ? htmlspecialchars($_SESSION['ext']) : 'jpg';

// Ambil IP user
$user_ip = $_SERVER['REMOTE_ADDR'];

// Deteksi apakah user berasal dari jaringan lokal
// Sesuaikan dengan range IP kantormu (5, 6, 7)
if (strpos($user_ip, '192.168.5.') === 0 || 
    strpos($user_ip, '192.168.6.') === 0 || 
    strpos($user_ip, '192.168.7.') === 0 || 
    $user_ip == '127.0.0.1') {
    // User dari lokal
    $base_url = "http://192.168.5.7:8080/indoprima_gemilang";
} else {
    // User dari internet
    $base_url = "http://122.144.4.194:31985/indoprima_gemilang";
}

$foto = $base_url . "/public/img/" . $noreg . "." . $ext;

// Add fallback image if photo doesn't exist
$default_foto = $base_url . "/public/img/default.jpg";
?>
<div class="body-wrapper">
    <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>

            <div class="d-block d-lg-none">
                <img src="../../dist/images/logos/dark-logo.svg" class="dark-logo" width="180" alt="" />
                <img src="../../dist/images/logos/light-logo.svg" class="light-logo" width="180" alt="" />
            </div>
            <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                </span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                        <i class="ti ti-align-justified fs-7"></i>
                    </a>
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="user-profile-img position-relative">
                                        <img src="<?= $foto ?>" 
                                             class="rounded-circle object-fit-cover border border-2 border-white shadow-sm" 
                                             width="40" 
                                             height="40" 
                                             alt="User avatar"
                                             onerror="this.src='<?= $default_foto ?>'; this.onerror=null;">
                                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" 
                                              style="width: 10px; height: 10px;"></span>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                                <div class="profile-dropdown position-relative" data-simplebar>
                                    <div class="py-3 px-7 pb-0">
                                        <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                    </div>
                                    <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                        <div class="position-relative">
                                            <img src="<?= $foto ?>" 
                                                 class="rounded-circle object-fit-cover border border-3 border-primary shadow-lg" 
                                                 width="90" 
                                                 height="90" 
                                                 alt="foto user"
                                                 onerror="this.src='<?= $default_foto ?>'; this.onerror=null;">
                                            <a href="#" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-1 border border-2 border-white" 
                                               style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                <i class="ti ti-camera fs-5 text-white"></i>
                                            </a>
                                        </div>

                                        <div class="ms-3">
                                            <h5 class="mb-1 fs-3 fw-semibold">
                                                <?php
                                                echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Guest';
                                                ?>
                                            </h5>

                                            <p class="mb-0 d-flex text-muted align-items-center gap-2">
                                                <i class="ti ti-id fs-4"></i>
                                                <span class="fw-medium">
                                                    <?php
                                                    echo isset($_SESSION['noreg']) ? htmlspecialchars($_SESSION['noreg']) : '-';
                                                    ?>
                                                </span>
                                            </p>
                                            
                                            <p class="mb-0 mt-1 d-flex text-muted align-items-center gap-2 small">
                                                <i class="ti ti-eye fs-4"></i>
                                                <span>
                                                    PT Indoprima Gemilang
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="message-body">
                                        <a href="../../logout.php" class="py-8 px-7 mt-8 d-flex align-items-center transition-hover">
                                            <span class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-1 p-6">
                                                <i class="ti ti-logout fs-6 text-danger"></i>
                                            </span>
                                            <div class="w-75 d-inline-block v-middle ps-3">
                                                <h6 class="mb-1 fw-semibold">Logout</h6>
                                                <span class="d-block text-muted small">Sign out from system</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
<style>
/* Additional CSS for better image rendering */
.object-fit-cover {
    object-fit: cover;
}

.transition-hover {
    transition: all 0.3s ease;
}

.transition-hover:hover {
    background-color: rgba(0, 0, 0, 0.02);
    transform: translateX(5px);
}

.user-profile-img {
    cursor: pointer;
}

.user-profile-img img {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.user-profile-img img:hover {
    transform: scale(1.05);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Optional: Add loading effect for images */
img[src*="default.jpg"] {
    opacity: 0.8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-dropdown .py-9.mx-7 {
        padding: 1.5rem 1rem !important;
    }
    
    .profile-dropdown img[width="90"] {
        width: 70px !important;
        height: 70px !important;
    }
}
</style>

<script>
// Optional: Add image error handling with retry
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[src*="/public/img/"]');
    images.forEach(img => {
        img.addEventListener('error', function() {
            if (!this.hasAttribute('data-retried')) {
                this.setAttribute('data-retried', 'true');
                // Retry after 1 second
                setTimeout(() => {
                    const currentSrc = this.src;
                    this.src = currentSrc + '?t=' + new Date().getTime();
                }, 1000);
            }
        });
    });
});
</script>