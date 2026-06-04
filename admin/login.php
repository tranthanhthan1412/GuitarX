<?php
session_start();
// Nếu đã là admin thì đẩy thẳng vào trong
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - GuitarX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
        }
        .admin-login-header {
            background: #1a1a2e;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .admin-login-body {
            padding: 2.5rem;
            background: white;
        }
    </style>
</head>
<body>

    <div class="card admin-login-card">
        <div class="admin-login-header">
            <span class="material-symbols-outlined mb-2" style="font-size: 48px; color: #e63946;">admin_panel_settings</span>
            <h4 class="mb-0 fw-bold">GUITARX ADMIN</h4>
            <small class="text-white-50">Khu vực dành riêng cho Quản trị viên</small>
        </div>
        <div class="admin-login-body">
            
            <?php if(isset($_GET['error'])): ?>
                <?php if($_GET['error'] == 'invalid'): ?>
                    <div class="alert alert-danger text-center small py-2"><i class="material-symbols-outlined align-middle fs-6 me-1">error</i>Tài khoản hoặc mật khẩu không chính xác!</div>
                <?php elseif($_GET['error'] == 'empty'): ?>
                    <div class="alert alert-warning text-center small py-2"><i class="material-symbols-outlined align-middle fs-6 me-1">warning</i>Vui lòng nhập đủ thông tin!</div>
                <?php elseif($_GET['error'] == 'not_admin'): ?>
                    <div class="alert alert-danger text-center small py-2"><i class="material-symbols-outlined align-middle fs-6 me-1">gpp_bad</i>Tài khoản này không có quyền quản trị!</div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="../controller/user.php" method="POST">
                <input type="hidden" name="action" value="login">
                <!-- Cờ đặc biệt đánh dấu đây là form từ trang admin -->
                <input type="hidden" name="is_admin_login" value="1">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="material-symbols-outlined fs-6">person</i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0 shadow-none" placeholder="Nhập username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="material-symbols-outlined fs-6">lock</i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0 shadow-none" placeholder="Nhập mật khẩu" required>
                    </div>
                </div>
                <button type="submit" class="btn w-100 py-2 fw-bold text-white shadow-sm" style="background-color: #e63946;">ĐĂNG NHẬP HỆ THỐNG</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-muted text-decoration-none small"><i class="material-symbols-outlined align-middle fs-6 me-1">arrow_back</i>Quay lại trang khách hàng</a>
            </div>
        </div>
    </div>

</body>
</html>
