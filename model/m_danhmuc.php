<?php
class CategoryModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAllCategories() {
        $query = "SELECT * FROM `DanhMuc`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM `DanhMuc` WHERE `Ma_DanhMuc` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name) {
        $query = "INSERT INTO `DanhMuc` (`TenDanhMuc`) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function updateCategory($id, $name) {
        $query = "UPDATE `DanhMuc` SET `TenDanhMuc` = :name WHERE `Ma_DanhMuc` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        // Xóa danh mục có thể ảnh hưởng đến sản phẩm.
        // Trong database schema, khóa ngoại `Ma_DanhMuc` ở bảng SanPham có tùy chọn `ON DELETE SET NULL`.
        // Do đó khi xóa danh mục, các sản phẩm thuộc danh mục này sẽ có Ma_DanhMuc = NULL.
        $query = "DELETE FROM `DanhMuc` WHERE `Ma_DanhMuc` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
