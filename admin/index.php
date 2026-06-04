<?php
session_start();

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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-light">
    
    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-danger" href="index.php">GuitarX Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php?act=quanlysanpham">Sản Phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?act=quanlydanhmuc">Danh Mục</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?act=quanlydonhang">Đơn Hàng</a></li>
                </ul>
                <div class="d-flex text-white align-items-center">
                    <span class="me-3">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <a href="../controller/user.php?act=logout" class="btn btn-outline-light btn-sm">Đăng xuất</a>
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
