<?php
class CategoryModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAllCategories() {
        $query = "SELECT * FROM `CATEGORIES`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM `CATEGORIES` WHERE `Category_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name) {
        $query = "INSERT INTO `CATEGORIES` (`CategoryName`) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function updateCategory($id, $name) {
        $query = "UPDATE `CATEGORIES` SET `CategoryName` = :name WHERE `Category_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        // Xóa danh mục có thể ảnh hưởng đến sản phẩm.
        // Trong database schema, khóa ngoại `Category_ID` ở bảng PRODUCTS có tùy chọn `ON DELETE SET NULL`.
        // Do đó khi xóa danh mục, các sản phẩm thuộc danh mục này sẽ có Category_ID = NULL.
        $query = "DELETE FROM `CATEGORIES` WHERE `Category_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
