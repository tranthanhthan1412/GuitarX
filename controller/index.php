<?php
// controller/index.php — Front Controller

$act = isset($_GET['act']) ? $_GET['act'] : 'home';

switch ($act) {
    case 'sanpham':
        include_once __DIR__ . "/../view/header.php";
        $sanphamFile = __DIR__ . "/../view/sanpham.php";
        if (file_exists($sanphamFile)) {
            include_once $sanphamFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'login':
        // taikhoan.php đã tự gọi header và footer ở bên trong nên không cần bao bọc lại
        include_once __DIR__ . "/../view/taikhoan.php";
        break;

    case 'home':
    default:
        include_once __DIR__ . "/../view/header.php";
        include_once __DIR__ . "/../view/trangchu.php";
        include_once __DIR__ . "/../view/footer.php";
        break;
}
?>