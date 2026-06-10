<?php
class ProductModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Lấy tất cả sản phẩm
    public function getAllProducts($sortType = 'new', $page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM `PRODUCTS`";
    
        switch ($sortType) {
            case 'price-asc': $query .= " ORDER BY `Price` ASC"; break;
            case 'price-desc': $query .= " ORDER BY `Price` DESC"; break;
            case 'new': default: $query .= " ORDER BY `Product_ID` DESC"; break;
        }
    
        // Thêm LIMIT và OFFSET vào cuối câu SQL
        $query .= " LIMIT :offset, :limit";
    
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 2. Hàm đếm tổng số sản phẩm của toàn bộ cửa hàng
    public function countAllProducts() {
        $query = "SELECT COUNT(*) as total FROM `PRODUCTS`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // 3. Sửa hàm getProductsByCategory hỗ trợ phân trang và sắp xếp
    public function getProductsByCategory($catId, $sortType = 'new', $page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM `PRODUCTS` WHERE `Category_ID` = :catId";
    
        switch ($sortType) {
            case 'price-asc': $query .= " ORDER BY `Price` ASC"; break;
            case 'price-desc': $query .= " ORDER BY `Price` DESC"; break;
            case 'new': default: $query .= " ORDER BY `Product_ID` DESC"; break;
        }
    
        $query .= " LIMIT :offset, :limit";
    
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':catId', (int)$catId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Hàm đếm tổng số sản phẩm thuộc một danh mục cụ thể
    public function countProductsByCategory($catId) {
        $query = "SELECT COUNT(*) as total FROM `PRODUCTS` WHERE `Category_ID` = :catId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':catId', (int)$catId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
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

        // model/m_sanpham.php

// Hàm lấy sản phẩm phân trang dành riêng cho Admin (Sắp xếp theo ID mới nhất lên đầu)
public function getAllProductsAdmin($page = 1, $limit = 6) {
    $offset = ($page - 1) * $limit;
    
    // Admin thì cứ ID mới nhất xếp lên đầu cho dễ quản lý bro nhé
    $query = "SELECT * FROM `PRODUCTS` ORDER BY `Product_ID` DESC LIMIT :offset, :limit";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    public function getProductImages($productId) {
    $sql = "SELECT * FROM `product_images` WHERE `Product_ID` = ? ORDER BY `Image_ID` ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Hàm thêm một ảnh phụ vào album
public function addProductImage($productId, $imagePath) {
    $sql = "INSERT INTO `product_images` (`Product_ID`, `Image_Path`) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$productId, $imagePath]);
}

    // Hàm lấy danh sách tất cả đánh giá của một sản phẩm dựa trên cấu trúc bảng của mày
public function getProductReviews($productId) {
    $sql = "SELECT r.*, u.Username 
            FROM `REVIEW` r 
            JOIN `USER` u ON r.User_ID = u.User_ID 
            WHERE r.Product_ID = ? 
            ORDER BY r.Review_ID DESC"; // Sắp xếp theo ID mới nhất lên đầu do không có cột ngày
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm thêm một đánh giá mới vào bảng REVIEW
public function addReview($productId, $userId, $rating, $comment) {
    $sql = "INSERT INTO `REVIEW` (`Product_ID`, `User_ID`, `Rating`, `Comment`) VALUES (?, ?, ?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$productId, $userId, $rating, $comment]);
}
}
?>