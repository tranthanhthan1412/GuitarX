<?php
session_start();
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
        // Đăng nhập thành công
        $_SESSION['user_id'] = $userData['User_ID'];
        $_SESSION['username'] = $userData['UserName'];
        $_SESSION['email'] = $userData['Email'] ?? '';
        $_SESSION['role'] = $userData['Role'];

        // Nếu đăng nhập từ form admin mà không phải admin thì từ chối (hoặc có thể vẫn cho vào nhưng ở trang index)
        if ($isAdminLogin && $userData['Role'] !== 'admin') {
            session_unset();
            session_destroy();
            header("Location: ../admin/login.php?error=not_admin");
            exit();
        }

        // Điều hướng dựa trên quyền
        if ($userData['Role'] === 'admin') {
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
            $_SESSION['user_id'] = $userData['User_ID'];
            $_SESSION['username'] = $userData['UserName'];
            $_SESSION['email'] = $userData['Email'] ?? '';
            $_SESSION['role'] = $userData['Role'];
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
