<?php
class FavoriteModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Thêm hoặc xóa sản phẩm khỏi danh sách yêu thích
    // Trả về true nếu vừa thêm vào, false nếu vừa xóa đi
    public function toggleFavorite($userId, $productId) {
        $queryCheck = "SELECT * FROM `YeuThich` WHERE Ma_NguoiDung = :userId AND Ma_SanPham = :productId";
        $stmtCheck = $this->db->prepare($queryCheck);
        $stmtCheck->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtCheck->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Đã tồn tại -> Xóa khỏi danh sách
            $queryDelete = "DELETE FROM `YeuThich` WHERE Ma_NguoiDung = :userId AND Ma_SanPham = :productId";
            $stmtDelete = $this->db->prepare($queryDelete);
            $stmtDelete->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtDelete->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtDelete->execute();
            return false;
        } else {
            // Chưa tồn tại -> Thêm vào danh sách
            $queryInsert = "INSERT INTO `YeuThich` (Ma_NguoiDung, Ma_SanPham) VALUES (:userId, :productId)";
            $stmtInsert = $this->db->prepare($queryInsert);
            $stmtInsert->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtInsert->execute();
            return true;
        }
    }

    // Đếm tổng số lượng sản phẩm yêu thích của user
    public function getFavoriteCount($userId) {
        $query = "SELECT COUNT(*) FROM `YeuThich` WHERE Ma_NguoiDung = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Lấy chi tiết các sản phẩm yêu thích (dùng hiển thị trang Wishlist)
    public function getFavoriteProducts($userId) {
        $query = "SELECT P.* 
                  FROM `SanPham` P
                  JOIN `YeuThich` F ON P.Ma_SanPham = F.Ma_SanPham
                  WHERE F.Ma_NguoiDung = :userId
                  ORDER BY F.NgayTao DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy mảng ID các sản phẩm yêu thích để check nhanh ở giao diện
    public function getUserFavoriteIds($userId) {
        $query = "SELECT Ma_SanPham FROM `YeuThich` WHERE Ma_NguoiDung = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
