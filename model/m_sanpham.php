<?php
class ProductModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Lấy tất cả sản phẩm
    public function getAllProducts($sortType = 'new', $page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM `SanPham`";
    
        switch ($sortType) {
            case 'price-asc': $query .= " ORDER BY `GiaTien` ASC"; break;
            case 'price-desc': $query .= " ORDER BY `GiaTien` DESC"; break;
            case 'new': default: $query .= " ORDER BY `Ma_SanPham` DESC"; break;
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
        $query = "SELECT COUNT(*) as total FROM `SanPham`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // 3. Sửa hàm getProductsByCategory hỗ trợ phân trang và sắp xếp
    public function getProductsByCategory($catId, $sortType = 'new', $page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM `SanPham` WHERE `Ma_DanhMuc` = :catId";
    
        switch ($sortType) {
            case 'price-asc': $query .= " ORDER BY `GiaTien` ASC"; break;
            case 'price-desc': $query .= " ORDER BY `GiaTien` DESC"; break;
            case 'new': default: $query .= " ORDER BY `Ma_SanPham` DESC"; break;
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
        $query = "SELECT COUNT(*) as total FROM `SanPham` WHERE `Ma_DanhMuc` = :catId";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':catId', (int)$catId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Lấy tên danh mục dựa vào ID
    public function getCategoryName($categoryId) {
        $query = "SELECT `TenDanhMuc` FROM `DanhMuc` WHERE `Ma_DanhMuc` = :categoryId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['TenDanhMuc'] : null;
    }

    // Lấy chi tiết một sản phẩm dựa vào ID
    public function getProductById($productId) {
        $query = "SELECT * FROM `SanPham` WHERE `Ma_SanPham` = :productId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Tìm kiếm sản phẩm theo tên
    public function searchProducts($keyword) {
        $query = "SELECT * FROM `SanPham` WHERE `TenSanPham` LIKE :keyword ORDER BY `Ma_SanPham` DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%" . $keyword . "%";
        $stmt->bindParam(":keyword", $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- ADMIN CRUD METHODS ---

    public function getAllCategories() {
        $query = "SELECT * FROM `DanhMuc`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $image, $desc, $price, $count, $brand, $categoryId, $discountPercent = 0) {
        $query = "INSERT INTO `SanPham` (`TenSanPham`, `Anh`, `MoTa`, `GiaTien`, `SoLuong`, `ThuongHieu`, `NgayNhapHang`, `Ma_DanhMuc`, `PhanTramGiamGia`) 
                  VALUES (:name, :image, :desc, :price, :count, :brand, CURDATE(), :categoryId, :discountPercent)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":count", $count);
        $stmt->bindParam(":brand", $brand);
        $stmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(":discountPercent", $discountPercent, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProduct($id, $name, $image, $desc, $price, $count, $brand, $categoryId, $discountPercent = 0) {
        if (!empty($image)) {
            $query = "UPDATE `SanPham` SET `TenSanPham` = :name, `Anh` = :image, `MoTa` = :desc, `GiaTien` = :price, `SoLuong` = :count, `ThuongHieu` = :brand, `Ma_DanhMuc` = :categoryId, `PhanTramGiamGia` = :discountPercent WHERE `Ma_SanPham` = :id";
        } else {
            // Cập nhật không thay đổi ảnh
            $query = "UPDATE `SanPham` SET `TenSanPham` = :name, `MoTa` = :desc, `GiaTien` = :price, `SoLuong` = :count, `ThuongHieu` = :brand, `Ma_DanhMuc` = :categoryId, `PhanTramGiamGia` = :discountPercent WHERE `Ma_SanPham` = :id";
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
        $stmt->bindParam(":discountPercent", $discountPercent, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function checkProductInOrder($productId) {
        $query = "SELECT COUNT(*) FROM `ChiTietDonHang` WHERE `Ma_SanPham` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function deleteProduct($id) {
        if ($this->checkProductInOrder($id)) {
            return false; // Không xóa được do đã nằm trong hóa đơn
        }
        $query = "DELETE FROM `SanPham` WHERE `Ma_SanPham` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

        // model/m_sanpham.php

// Hàm lấy sản phẩm phân trang dành riêng cho Admin (Sắp xếp theo ID mới nhất lên đầu)
public function getAllProductsAdmin($page = 1, $limit = 6) {
    $offset = ($page - 1) * $limit;
    
    // Admin thì cứ ID mới nhất xếp lên đầu cho dễ quản lý bro nhé
    $query = "SELECT * FROM `SanPham` ORDER BY `Ma_SanPham` DESC LIMIT :offset, :limit";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    public function getProductImages($productId) {
        try {
            $sql = "SELECT * FROM `product_images` WHERE `Ma_SanPham` = ? ORDER BY `Image_ID` ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Hàm thêm một ảnh phụ vào album
    public function addProductImage($productId, $imagePath) {
        try {
            $sql = "INSERT INTO `product_images` (`Ma_SanPham`, `Image_Path`) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$productId, $imagePath]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Hàm lấy danh sách tất cả đánh giá của một sản phẩm dựa trên cấu trúc bảng của mày
    public function getProductReviews($productId) {
        $sql = "SELECT r.*, u.TenNguoiDung as Username 
                FROM `DanhGia` r 
                JOIN `NguoiDung` u ON r.Ma_NguoiDung = u.Ma_NguoiDung 
                WHERE r.Ma_SanPham = ? 
                ORDER BY r.Ma_DanhGia DESC"; // Sắp xếp theo ID mới nhất lên đầu do không có cột ngày
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Hàm thêm một đánh giá mới vào bảng DanhGia
public function addReview($productId, $userId, $rating, $comment) {
    $sql = "INSERT INTO `DanhGia` (`Ma_SanPham`, `Ma_NguoiDung`, `DiemDanhGia`, `BinhLuan`) VALUES (?, ?, ?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$productId, $userId, $rating, $comment]);
}

// Lấy tất cả sản phẩm đang sale (PhanTramGiamGia > 0)
public function getSaleProducts($sortType = 'discount', $minDiscount = 0) {
    $query = "SELECT * FROM `SanPham` WHERE `PhanTramGiamGia` > :minDiscount";
    switch ($sortType) {
        case 'price-asc':  $query .= " ORDER BY `GiaTien` ASC"; break;
        case 'price-desc': $query .= " ORDER BY `GiaTien` DESC"; break;
        default: $query .= " ORDER BY `PhanTramGiamGia` DESC, `Ma_SanPham` DESC"; break;
    }
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':minDiscount', (int)$minDiscount, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>