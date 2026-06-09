<?php
class ProductModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Lấy tất cả sản phẩm
    public function getAllProducts() {
        $query = "SELECT * FROM `PRODUCTS` ORDER BY `Product_ID` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo danh mục
    public function getProductsByCategory($categoryId) {
        $query = "SELECT * FROM `PRODUCTS` WHERE `Category_ID` = :categoryId ORDER BY `Product_ID` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tên danh mục dựa vào ID
    public function getCategoryName($categoryId) {
        $query = "SELECT `CategoryName` FROM `CATEGORIES` WHERE `Category_ID` = :categoryId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['CategoryName'] : null;
    }

    // Lấy chi tiết một sản phẩm dựa vào ID
    public function getProductById($productId) {
        $query = "SELECT * FROM `PRODUCTS` WHERE `Product_ID` = :productId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
