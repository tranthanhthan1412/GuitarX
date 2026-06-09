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
    // Tìm kiếm sản phẩm theo tên
    public function searchProducts($keyword) {
        $query = "SELECT * FROM `PRODUCTS` WHERE `ProductName` LIKE :keyword ORDER BY `Product_ID` DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%" . $keyword . "%";
        $stmt->bindParam(":keyword", $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- ADMIN CRUD METHODS ---

    public function getAllCategories() {
        $query = "SELECT * FROM `CATEGORIES`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $image, $desc, $price, $count, $brand, $categoryId) {
        $query = "INSERT INTO `PRODUCTS` (`ProductName`, `Image`, `Description`, `Price`, `Count`, `Brand`, `DateImport`, `Category_ID`) 
                  VALUES (:name, :image, :desc, :price, :count, :brand, CURDATE(), :categoryId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":count", $count);
        $stmt->bindParam(":brand", $brand);
        $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProduct($id, $name, $image, $desc, $price, $count, $brand, $categoryId) {
        if (!empty($image)) {
            $query = "UPDATE `PRODUCTS` SET `ProductName` = :name, `Image` = :image, `Description` = :desc, `Price` = :price, `Count` = :count, `Brand` = :brand, `Category_ID` = :categoryId WHERE `Product_ID` = :id";
        } else {
            // Cập nhật không thay đổi ảnh
            $query = "UPDATE `PRODUCTS` SET `ProductName` = :name, `Description` = :desc, `Price` = :price, `Count` = :count, `Brand` = :brand, `Category_ID` = :categoryId WHERE `Product_ID` = :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $name);
        if (!empty($image)) {
            $stmt->bindParam(":image", $image);
        }
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":count", $count);
        $stmt->bindParam(":brand", $brand);
        $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function checkProductInOrder($productId) {
        $query = "SELECT COUNT(*) FROM `ORDER_DETAIL` WHERE `Product_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function deleteProduct($id) {
        if ($this->checkProductInOrder($id)) {
            return false; // Không xóa được do đã nằm trong hóa đơn
        }
        $query = "DELETE FROM `PRODUCTS` WHERE `Product_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
