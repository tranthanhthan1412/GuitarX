<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../model/database.php';
require_once '../model/m_user.php';

// Khởi tạo kết nối DB và Model
$database = new Database();
$db = $database->getConnection();
$userModel = new UserModel($db);

// Xử lý logic Đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $isAdminLogin = isset($_POST['is_admin_login']) && $_POST['is_admin_login'] == '1';

    $redirectOnFail = $isAdminLogin ? "../admin/login.php?error=" : "../index.php?act=login&error=";

    if (empty($username) || empty($password)) {
        header("Location: " . $redirectOnFail . "empty");
        exit();
    }

    // Kiểm tra đăng nhập
    $userData = $userModel->checkLogin($username, $password);

    if ($userData) {
        
        // 🛑 BƯỚC KIỂM TRA TÀI KHOẢN BỊ KHÓA (BAN)
        if ($userData['VaiTro'] === 'banned') {
            session_unset();
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_destroy();
            }
            // Đá ngược về kèm thông báo lỗi bị khóa tài khoản
            header("Location: " . $redirectOnFail . "banned");
            exit();
        }

        // Đăng nhập thành công (Không bị khóa)
        $_SESSION['user_id'] = $userData['Ma_NguoiDung'];
        $_SESSION['username'] = $userData['TenNguoiDung'];
        $_SESSION['email'] = $userData['Email'] ?? '';
        $_SESSION['role'] = $userData['VaiTro'];

        // 🟢 Tạo thông báo chào mừng
        $_SESSION['login_success'] = "Chào mừng " . $userData['TenNguoiDung'] . " đã quay trở lại!";

        // Nếu đăng nhập từ form admin mà không phải admin thì từ chối
        if ($isAdminLogin && $userData['VaiTro'] !== 'admin') {
            session_unset();
            session_destroy();
            header("Location: ../admin/login.php?error=not_admin");
            exit();
        }

        // Điều hướng dựa trên quyền
        if ($isAdminLogin) {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        // Đăng nhập thất bại
        header("Location: " . $redirectOnFail . "invalid");
        exit();
    }
}

// Xử lý logic Đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($username) || empty($password) || empty($email)) {
        header("Location: ../index.php?act=dangky&error=empty");
        exit();
    }

    if ($userModel->register($username, $password, $email, $phone)) {
        // Đăng ký xong tự động đăng nhập luôn
        $userData = $userModel->checkLogin($username, $password);
        if ($userData) {
            $_SESSION['user_id'] = $userData['Ma_NguoiDung'];
            $_SESSION['username'] = $userData['TenNguoiDung'];
            $_SESSION['email'] = $userData['Email'] ?? '';
            $_SESSION['role'] = $userData['VaiTro'];
            
            $_SESSION['login_success'] = "Đăng ký và đăng nhập thành công! Chào mừng " . $userData['TenNguoiDung'] . ".";
        }
        header("Location: ../index.php");
        exit();
    } else {
        // Đăng ký thất bại do user đã tồn tại
        header("Location: ../index.php?act=dangky&error=exists");
        exit();
    }
}

// Xử lý logic Đăng xuất
if (isset($_GET['act']) && $_GET['act'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>