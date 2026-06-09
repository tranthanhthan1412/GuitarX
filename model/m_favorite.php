<?php
class FavoriteModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Thêm hoặc xóa sản phẩm khỏi danh sách yêu thích
    // Trả về true nếu vừa thêm vào, false nếu vừa xóa đi
    public function toggleFavorite($userId, $productId) {
        $queryCheck = "SELECT * FROM `FAVORITES` WHERE User_ID = :userId AND Product_ID = :productId";
        $stmtCheck = $this->db->prepare($queryCheck);
        $stmtCheck->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtCheck->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Đã tồn tại -> Xóa khỏi danh sách
            $queryDelete = "DELETE FROM `FAVORITES` WHERE User_ID = :userId AND Product_ID = :productId";
            $stmtDelete = $this->db->prepare($queryDelete);
            $stmtDelete->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtDelete->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtDelete->execute();
            return false;
        } else {
            // Chưa tồn tại -> Thêm vào danh sách
            $queryInsert = "INSERT INTO `FAVORITES` (User_ID, Product_ID) VALUES (:userId, :productId)";
            $stmtInsert = $this->db->prepare($queryInsert);
            $stmtInsert->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtInsert->execute();
            return true;
        }
    }

    // Đếm tổng số lượng sản phẩm yêu thích của user
    public function getFavoriteCount($userId) {
        $query = "SELECT COUNT(*) FROM `FAVORITES` WHERE User_ID = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Lấy chi tiết các sản phẩm yêu thích (dùng hiển thị trang Wishlist)
    public function getFavoriteProducts($userId) {
        $query = "SELECT P.* 
                  FROM `PRODUCTS` P
                  JOIN `FAVORITES` F ON P.Product_ID = F.Product_ID
                  WHERE F.User_ID = :userId
                  ORDER BY F.Created_At DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy mảng ID các sản phẩm yêu thích để check nhanh ở giao diện
    public function getUserFavoriteIds($userId) {
        $query = "SELECT Product_ID FROM `FAVORITES` WHERE User_ID = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
