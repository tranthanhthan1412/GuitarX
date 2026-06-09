<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'model/database.php';
$database = new Database();
$db = $database->getConnection(); 

// Lấy danh mục để hiển thị trên thanh điều hướng
$stmt_cat = $db->query("SELECT * FROM `CATEGORIES` ORDER BY `Category_ID`");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Lấy sản phẩm nổi bật cho trang chủ (tối đa 8 sản phẩm)
$stmt_feat = $db->query("SELECT * FROM `PRODUCTS` ORDER BY `Product_ID` DESC LIMIT 8");
$featuredProducts = $stmt_feat->fetchAll(PDO::FETCH_ASSOC);
?>
<?php

include_once 'controller/index.php';
?>