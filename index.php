<?php
// Tự động start session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Gọi file cấu hình
require_once 'config/config.php';

// Khởi tạo kết nối DB
require_once 'model/database.php';
$database = new Database();
$db = $database->getConnection(); 

// Gọi Controller (Đã được chuyển sang dạng Class OOP chuẩn MVC)
require_once 'controller/Controller.php';

// Khởi tạo Controller và điều hướng Request
$controller = new MainController($db);
$controller->handleRequest();
?>