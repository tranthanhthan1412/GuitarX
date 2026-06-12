<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Middleware: Kiểm tra quyền Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Nếu chưa đăng nhập hoặc không phải admin -> đẩy về trang đăng nhập của Admin
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - GuitarX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <link href="../view/css/theme.css" rel="stylesheet">
    <style>
    /* Biến thành link để nhấn vào refresh */
    .site-logo {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        cursor: pointer;
    }

    .logo-icon {
        filter: drop-shadow(0 2px 6px rgba(230, 57, 70, 0.45));
    }

    .admin-nav-links .cat-link {
        color: rgba(255, 255, 255, 0.75) !important;
        border-bottom: 3px solid transparent;
        padding: 0 1rem;
        text-decoration: none;
        font-family: var(--font-display);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .admin-nav-links .cat-link:hover,
    .admin-nav-links .cat-link.active {
        color: var(--color-secondary) !important;
        border-bottom-color: var(--color-secondary) !important;
    }
    </style>
</head>

<body class="bg-light">

    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark site-header">
        <div class="container-fluid px-4">

            <a class="site-logo" href="index.php">
                <div class="logo-icon">
                    <span class="material-symbols-outlined text-secondary-custom"
                        style="font-size: 32px;">admin_panel_settings</span>
                </div>
                <div class="logo-text">
                    <div class="logo-name">
                        <span class="logo-main">Guitar</span>
                        <span class="logo-accent">X</span>
                    </div>
                    <span class="logo-tagline">ADMIN SYSTEM</span>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="admin-nav-links navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <?php $act = $_GET['act'] ?? ''; ?>
                    <a class="nav-link cat-link <?= $act == 'quanlysanpham' ? 'active' : '' ?>"
                        href="index.php?act=quanlysanpham">Sản Phẩm</a>
                    <a class="cat-link nav-link <?= $act == 'quanlydanhmuc' ? 'active' : '' ?>"
                        href="index.php?act=quanlydanhmuc">Danh Mục</a>
                    <a class="cat-link nav-link <?= $act == 'quanlydonhang' ? 'active' : '' ?>"
                        href="index.php?act=quanlydonhang">Đơn Hàng</a>
                    <a class="cat-link nav-link <?= $act == 'quanlyuser' ? 'active' : '' ?>"
                        href="index.php?act=quanlyuser">Người Dùng</a>
                    <a class="cat-link nav-link <?= $act == 'quanlyvoucher' ? 'active' : '' ?>"
                        href="index.php?act=quanlyvoucher">Voucher</a>
                    <a class="cat-link nav-link <?= $act == 'quanlySanSale' ? 'active' : '' ?>"
                        href="index.php?act=quanlySanSale">🔥 Sale Chớp Nhoáng</a>
                </div>

                <div class="header-actions d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center text-white gap-2">
                        <span class="material-symbols-outlined">person</span>
                        <span
                            style="font-family: var(--font-display); font-size: 0.85rem;"><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                    </div>

                    <a href="../controller/user.php?act=logout" class="hdr-cart-btn text-decoration-none">
                        <span class="material-symbols-outlined">logout</span>
                        <div class="hdr-cart-text">
                            <span class="hdr-cart-label">ĐĂNG XUẤT</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php
        $act = isset($_GET['act']) ? $_GET['act'] : 'dashboard';
        switch ($act) {
            case 'quanlysanpham':
                include 'quanlysanpham.php';
                break;
            case 'quanlydanhmuc':
                include 'quanlydanhmuc.php';
                break;
            case 'quanlydonhang':
                include 'quanlydonhang.php';
                break;
            case 'quanlyuser':
                include 'quanlyuser.php';
                break;
            case 'quanlyvoucher':
                include 'quanlyvoucher.php';
                break;
            case 'quanlySanSale':
                include 'quanlysansale.php';
                break;
            case 'dashboard':
            default:
                echo "<div class='alert alert-success'>
                        <h4 class='alert-heading'>Chào mừng tới Trang Quản Trị GuitarX!</h4>
                        <p>Hệ thống phân quyền đã hoạt động thành công. Tại đây bạn có thể thêm, sửa, xóa sản phẩm và đơn hàng một cách an toàn.</p>
                      </div>";
                break;
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>