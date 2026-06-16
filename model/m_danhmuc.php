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
        // 1. Kiểm tra xem có sản phẩm nào đang thuộc danh mục này không
        $checkQuery = "SELECT COUNT(*) AS total FROM `SanPham` WHERE `Ma_DanhMuc` = :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Nếu số lượng sản phẩm lớn hơn 0 thì trả về false, chặn không cho xóa
        if ($result && $result['total'] > 0) {
            return false;
        }

        // 2. Nếu không còn sản phẩm nào thì tiến hành xóa bình thường
        $query = "DELETE FROM `DanhMuc` WHERE `Ma_DanhMuc` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>