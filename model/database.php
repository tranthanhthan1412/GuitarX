<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    public $conn;

    // Hàm kết nối CSDL và tự động kích hoạt tạo cấu trúc hệ thống
    public function getConnection() {
        $this->conn = null;
        try {
            // Bước 1: Kết nối trực tiếp tới MySQL Server trước để tạo database nếu chưa có
            $pdo = new PDO("mysql:host=" . $this->host . ";charset=utf8", $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // Bước 2: Kết nối chính thức vào Database của dự án GuitarX
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // LƯU Ý KHI DEPLOY: Đã tắt tự động tạo bảng và thêm dữ liệu mẫu để tối ưu hiệu năng
            // Nếu đây là lần chạy đầu tiên trên host, hãy bỏ comment 2 dòng dưới đây để tạo DB
            // $this->initializeAllTables();
            // $this->seedDataDataForPhase1();
            
        } catch(PDOException $exception) {
            die("Lỗi kết nối hoặc khởi tạo cơ sở dữ liệu: " . $exception->getMessage());
        }
        return $this->conn;
    }

    // Hàm thực thi chuỗi câu lệnh SQL tạo bảng theo đúng thứ tự ưu tiên ràng buộc khóa ngoại
    private function initializeAllTables() {
        $queries = [
            // 1. Bảng phân hạng khách hàng
            "CREATE TABLE IF NOT EXISTS `XepHang` (
                `Ma_XepHang` INT AUTO_INCREMENT PRIMARY KEY,
                `TenXepHang` VARCHAR(100) NOT NULL,
                `ChiTieuToiThieu` DECIMAL(15, 2) DEFAULT 0.00,
                `PhanTramGiamGia` INT DEFAULT 0,
                CONSTRAINT `CH_Rank_MinSpending` CHECK (`ChiTieuToiThieu` >= 0),
                CONSTRAINT `CH_Rank_Discount` CHECK (`PhanTramGiamGia` BETWEEN 0 AND 100)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 2. Bảng người dùng
            "CREATE TABLE IF NOT EXISTS `NguoiDung` (
                `Ma_NguoiDung` INT AUTO_INCREMENT PRIMARY KEY,
                `TenNguoiDung` VARCHAR(150) NOT NULL,
                `MatKhau` VARCHAR(255) NOT NULL,
                `SDT` VARCHAR(15) DEFAULT NULL,
                `VaiTro` VARCHAR(50) DEFAULT 'customer',
                `NgayKhoiTao` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `Ma_XepHang` INT NULL,
                CONSTRAINT `UQ_User_UserName` UNIQUE (`TenNguoiDung`),
                FOREIGN KEY (`Ma_XepHang`) REFERENCES `XepHang`(`Ma_XepHang`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 3. Bảng danh mục sản phẩm
            "CREATE TABLE IF NOT EXISTS `DanhMuc` (
                `Ma_DanhMuc` INT AUTO_INCREMENT PRIMARY KEY,
                `TenDanhMuc` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 4. Bảng sản phẩm
            "CREATE TABLE IF NOT EXISTS `SanPham` (
                `Ma_SanPham` INT AUTO_INCREMENT PRIMARY KEY,
                `TenSanPham` VARCHAR(255) NOT NULL,
                `Anh` VARCHAR(255) NOT NULL,
                `MoTa` TEXT DEFAULT NULL,
                `GiaTien` DECIMAL(15, 2) NOT NULL,
                `SoLuong` INT DEFAULT 0,
                `ThuongHieu` VARCHAR(100) DEFAULT NULL,
                `NgayNhapHang` DATE DEFAULT NULL,
                `Ma_DanhMuc` INT NULL,
                `PhanTramGiamGia` INT DEFAULT 0,
                CONSTRAINT `CH_Product_Price` CHECK (`GiaTien` >= 0),
                CONSTRAINT `CH_Product_Count` CHECK (`SoLuong` >= 0),
                CONSTRAINT `CH_Product_Discount` CHECK (`PhanTramGiamGia` BETWEEN 0 AND 100),
                FOREIGN KEY (`Ma_DanhMuc`) REFERENCES `DanhMuc`(`Ma_DanhMuc`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 5. Bảng đánh giá sản phẩm
            "CREATE TABLE IF NOT EXISTS `DanhGia` (
                `Ma_DanhGia` INT AUTO_INCREMENT PRIMARY KEY,
                `DiemDanhGia` INT NOT NULL,
                `BinhLuan` TEXT DEFAULT NULL,
                `Ma_NguoiDung` INT NOT NULL,
                `Ma_SanPham` INT NOT NULL,
                CONSTRAINT `CH_Review_Rating` CHECK (`DiemDanhGia` BETWEEN 1 AND 5),
                FOREIGN KEY (`Ma_NguoiDung`) REFERENCES `NguoiDung`(`Ma_NguoiDung`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_SanPham`) REFERENCES `SanPham`(`Ma_SanPham`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 6. Bảng giỏ hàng
            "CREATE TABLE IF NOT EXISTS `GioHang` (
                `Ma_GioHang` INT AUTO_INCREMENT PRIMARY KEY,
                `TongSoSanPham` INT DEFAULT 0,
                CONSTRAINT `CH_Cart_Total` CHECK (`TongSoSanPham` >= 0)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 7. Bảng chi tiết giỏ hàng
            "CREATE TABLE IF NOT EXISTS `ChiTietGioHang` (
                `Ma_SanPham` INT NOT NULL,
                `Ma_GioHang` INT NOT NULL,
                `GiaTien` DECIMAL(15, 2) NOT NULL,
                `SoLuong` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`Ma_SanPham`, `Ma_GioHang`),
                CONSTRAINT `CH_CartDetail_Price` CHECK (`GiaTien` >= 0),
                CONSTRAINT `CH_CartDetail_Qty` CHECK (`SoLuong` > 0),
                FOREIGN KEY (`Ma_SanPham`) REFERENCES `SanPham`(`Ma_SanPham`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_GioHang`) REFERENCES `GioHang`(`Ma_GioHang`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 8. Bảng mã giảm giá
            "CREATE TABLE IF NOT EXISTS `MaGiamGia` (
                `Ma_MaGiamGia` INT AUTO_INCREMENT PRIMARY KEY,
                `Ma` VARCHAR(50) NOT NULL,
                `GiaTriGiam` DECIMAL(15, 2) NOT NULL,
                `SoLuong` INT NOT NULL DEFAULT 0,
                `NgayHetHan` DATE DEFAULT NULL,
                CONSTRAINT `UQ_Voucher_Code` UNIQUE (`Ma`),
                CONSTRAINT `CH_Voucher_Value` CHECK (`GiaTriGiam` >= 0),
                CONSTRAINT `CH_Voucher_Qty` CHECK (`SoLuong` >= 0)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 9. Bảng phương thức thanh toán
            "CREATE TABLE IF NOT EXISTS `PhuongThucThanhToan` (
                `Ma_PhuongThuc` INT AUTO_INCREMENT PRIMARY KEY,
                `TenPhuongThuc` VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 10. Bảng đơn hàng
            "CREATE TABLE IF NOT EXISTS `DonHang` (
                `Ma_DonHang` INT AUTO_INCREMENT PRIMARY KEY,
                `NgayDatHang` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `NgayGiaoHang` DATETIME DEFAULT NULL,
                `TrangThai` VARCHAR(100) DEFAULT 'Pending',
                `Ma_NguoiDung` INT NOT NULL,
                `Ma_PhuongThuc` INT NULL,
                `Ma_MaGiamGia` INT NULL,
                CONSTRAINT `CH_Order_ShippingDate` CHECK (`NgayGiaoHang` IS NULL OR `NgayGiaoHang` >= `NgayDatHang`),
                FOREIGN KEY (`Ma_NguoiDung`) REFERENCES `NguoiDung`(`Ma_NguoiDung`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_PhuongThuc`) REFERENCES `PhuongThucThanhToan`(`Ma_PhuongThuc`) ON DELETE SET NULL,
                FOREIGN KEY (`Ma_MaGiamGia`) REFERENCES `MaGiamGia`(`Ma_MaGiamGia`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 11. Bảng chi tiết đơn hàng
            "CREATE TABLE IF NOT EXISTS `ChiTietDonHang` (
                `Ma_DonHang` INT NOT NULL,
                `Ma_SanPham` INT NOT NULL,
                `TongTien` DECIMAL(15, 2) NOT NULL,
                `SoLuong` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`Ma_DonHang`, `Ma_SanPham`),
                CONSTRAINT `CH_OrderDetail_Total` CHECK (`TongTien` >= 0),
                CONSTRAINT `CH_OrderDetail_Qty` CHECK (`SoLuong` > 0),
                FOREIGN KEY (`Ma_DonHang`) REFERENCES `DonHang`(`Ma_DonHang`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_SanPham`) REFERENCES `SanPham`(`Ma_SanPham`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 12. Bảng địa chỉ giao hàng
            "CREATE TABLE IF NOT EXISTS `DiaChiGiaoHang` (
                `Ma_DiaChiGiao` INT AUTO_INCREMENT PRIMARY KEY,
                `DiaChi` VARCHAR(255) NOT NULL,
                `ThanhPho` VARCHAR(100) NOT NULL,
                `Ma_NguoiDung` INT NOT NULL,
                FOREIGN KEY (`Ma_NguoiDung`) REFERENCES `NguoiDung`(`Ma_NguoiDung`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 13. Bảng phiếu giao hàng
            "CREATE TABLE IF NOT EXISTS `GhiChuGiaoHang` (
                `Ma_GiaoHang` INT AUTO_INCREMENT PRIMARY KEY,
                `NgayGiaoHang` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `Ma_DonHang` INT NOT NULL,
                `Ma_SanPham` INT NOT NULL,
                `Ma_DiaChiGiao` INT NOT NULL,
                FOREIGN KEY (`Ma_DonHang`) REFERENCES `DonHang`(`Ma_DonHang`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_SanPham`) REFERENCES `SanPham`(`Ma_SanPham`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_DiaChiGiao`) REFERENCES `DiaChiGiaoHang`(`Ma_DiaChiGiao`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // 14. Bảng sản phẩm yêu thích (Wishlist)
            "CREATE TABLE IF NOT EXISTS `YeuThich` (
                `Ma_NguoiDung` INT NOT NULL,
                `Ma_SanPham` INT NOT NULL,
                `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`Ma_NguoiDung`, `Ma_SanPham`),
                FOREIGN KEY (`Ma_NguoiDung`) REFERENCES `NguoiDung`(`Ma_NguoiDung`) ON DELETE CASCADE,
                FOREIGN KEY (`Ma_SanPham`) REFERENCES `SanPham`(`Ma_SanPham`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];

        // Duyệt mảng và thực thi từng câu lệnh tạo bảng
        foreach ($queries as $query) {
            $this->conn->exec($query);
        }

        // Tự động bổ sung cột PhanTramGiamGia nếu bảng SanPham đã được tạo từ trước
        try {
            $this->conn->exec("ALTER TABLE `SanPham` ADD COLUMN `PhanTramGiamGia` INT DEFAULT 0 AFTER `Ma_DanhMuc`");
        } catch (PDOException $e) {
            // Bỏ qua lỗi nếu cột PhanTramGiamGia đã tồn tại
        }

        // Tự động bổ sung cột Email nếu bảng NguoiDung đã được tạo từ trước nhưng chưa có cột này
        try {
            $this->conn->exec("ALTER TABLE `NguoiDung` ADD COLUMN `Email` VARCHAR(255) DEFAULT NULL AFTER `MatKhau`");
        } catch (PDOException $e) {
            // Bỏ qua lỗi nếu cột Email đã tồn tại
        }
    }

    // Hàm tự động nạp dữ liệu mẫu để Giai đoạn 1 hiển thị lên được giao diện luôn
    private function seedDataDataForPhase1() {
        
        // 0. Kiểm tra bảng NguoiDung rỗng thì chèn tài khoản admin mặc định
        $check_user = $this->conn->query("SELECT COUNT(*) FROM `NguoiDung`")->fetchColumn();
        if ($check_user == 0) {
            // Mật khẩu là 123456 (để thô cho tiện lúc demo, sau này có thể dùng mã hóa password_hash)
            $this->conn->exec("
                INSERT INTO `NguoiDung` (`TenNguoiDung`, `MatKhau`, `VaiTro`) VALUES
                ('admin', '123456', 'admin'),
                ('khachhang', '123456', 'customer');
            ");
        }

        // 0.5. Kiểm tra bảng PhuongThucThanhToan rỗng thì chèn phương thức thanh toán
        $check_pm = $this->conn->query("SELECT COUNT(*) FROM `PhuongThucThanhToan`")->fetchColumn();
        if ($check_pm == 0) {
            $this->conn->exec("
                INSERT INTO `PhuongThucThanhToan` (`Ma_PhuongThuc`, `TenPhuongThuc`) VALUES
                (1, 'Thanh toán khi nhận hàng (COD)'),
                (2, 'Chuyển khoản qua Ngân hàng / Momo');
            ");
        }

        // 1. Kiểm tra bảng DanhMuc rỗng thì chèn danh mục
        $check_cat = $this->conn->query("SELECT COUNT(*) FROM `DanhMuc`")->fetchColumn();
        if ($check_cat == 0) {
            $this->conn->exec("
                INSERT INTO `DanhMuc` (`Ma_DanhMuc`, `TenDanhMuc`) VALUES
                (1, 'Acoustic Guitars'),
                (2, 'Electric Guitars'),
                (3, 'Classic Guitars'),
                (4, 'Bass Guitars'),
                (5, 'Ukulele'),
                (6, 'Phụ Kiện');
            ");
        }

        // 2. Kiểm tra bảng SanPham rỗng thì chèn các sản phẩm tương ứng với ảnh thật bạn đang có
        $check_prod = $this->conn->query("SELECT COUNT(*) FROM `SanPham`")->fetchColumn();
        if ($check_prod == 0) {
            $this->conn->exec("
                INSERT INTO `SanPham` (`TenSanPham`, `Anh`, `MoTa`, `GiaTien`, `SoLuong`, `ThuongHieu`, `NgayNhapHang`, `Ma_DanhMuc`, `PhanTramGiamGia`) VALUES
                ('Đàn Guitar Acoustic Yamaha FS800', 'Yamaha.jpg', 'Dòng âm thanh chuẩn mực, thích hợp cho mọi đối tượng học bấm ngón.', 3500000.00, 12, 'Yamaha', '2026-04-10', 1, 15),
                ('Đàn Guitar Electric Ibanez GRG170DX', 'Ibanez.jpg', 'Đàn điện lý tưởng cho rock/metal với độ nhạy cao và cần đàn mượt.', 5800000.00, 8, 'Ibanez', '2026-04-12', 2, 0),
                ('Đàn Guitar Acoustic Washburn WD10S', 'Washburn.jpg', 'Mặt trước bằng gỗ thông nguyên tấm mang lại âm thanh cực ấm.', 4200000.00, 15, 'Washburn', '2026-04-15', 1, 10),
                ('Đàn Guitar Electric Fender Player Strat', 'Fender.jpg', 'Dòng đàn huyền thoại mang âm hưởng rực rỡ chuẩn California.', 18500000.00, 5, 'Fender', '2026-04-18', 2, 0),
                ('Đàn Guitar Classic Gibson Master', 'Gibson.jpg', 'Sản phẩm cao cấp dành cho giới biểu diễn chuyên nghiệp.', 24000000.00, 3, 'Gibson', '2026-04-20', 3, 5),
                ('Đàn Guitar Acoustic Taylor 114e', 'Taylor.jpg', 'Đỉnh cao guitar thùng với hệ thống thiết bị khuyếch đại âm thanh ES2.', 21000000.00, 4, 'Taylor', '2026-04-22', 1, 0),
                ('Đàn Ukulele Soprano Yamaha GL1', 'ukulele_soprano.png', 'Guitalele kết hợp hoàn hảo giữa guitar và ukulele, âm thanh trong trẻo, nhỏ gọn dễ mang theo.', 1800000.00, 20, 'Yamaha', '2026-05-01', 5, 20),
                ('Đàn Ukulele Tenor Kala KA-T', 'ukulele_tenor.png', 'Ukulele tenor cao cấp với thân gỗ mahogany nguyên khối, âm vang ấm áp và phong phú.', 2500000.00, 15, 'Kala', '2026-05-05', 5, 0),
                ('Đàn Bass Fender Player Jazz Bass', 'bass_4string.png', 'Bass 4 dây huyền thoại với pickup đôi single-coil, âm thanh linh hoạt từ jazz đến funk.', 19500000.00, 6, 'Fender', '2026-05-08', 4, 10),
                ('Đàn Bass Ibanez SR505E 5 Dây', 'bass_5string.png', 'Bass 5 dây hiện đại với hệ thống active EQ 3 band, thân gỗ mahogany, cần đàn mỏng dễ chơi.', 14500000.00, 7, 'Ibanez', '2026-05-10', 4, 0),
                ('Capo Guitar Dunlop Trigger', 'capo.png', 'Capo kẹp nhanh chất liệu nhôm cao cấp, lực kẹp đều, không làm lệch dây. Tương thích guitar acoustic và electric.', 350000.00, 50, 'Dunlop', '2026-05-12', 6, 25),
                ('Dây Đàn Guitar Acoustic D Addario EJ16', 'daydan.png', 'Bộ dây phosphor bronze sáng và ấm, gauge 12-53. Lựa chọn hàng đầu của các nghệ sĩ chuyên nghiệp toàn cầu.', 180000.00, 100, 'D''Addario', '2026-05-14', 6, 0),
                ('Máy Lên Dây Korg Pitchclip 2', 'tuner.svg', 'Tuner clip-on nhỏ gọn, màn hình LED rõ ràng, phát hiện cao độ nhanh và chính xác. Pin AAA dùng hơn 200 giờ.', 420000.00, 35, 'Korg', '2026-05-15', 6, 15),
                ('Bộ Phím Gảy Fender Premium Picks (12 cái)', 'picks.svg', 'Bộ 12 pick guitar cao cấp đa độ dày (0.5mm–1.14mm), chất liệu celluloid mềm mại, grip tốt khi chơi.', 120000.00, 80, 'Fender', '2026-05-16', 6, 0),
                ('Dây Đeo Đàn Da Bò Thật Levy s', 'strap.svg', 'Dây đeo guitar da bò nguyên miếng, mềm mại và bền bỉ. Điều chỉnh chiều dài linh hoạt, phong cách vintage.', 650000.00, 25, 'Levy''s', '2026-05-18', 6, 10),
                ('Bao Đàn Guitar Acoustic 3 Lớp', 'guitarbag.svg', 'Bao đàn chống sốc 3 lớp dày 15mm, chống nước, có ngăn phụ kiện. Dây kéo YKK chắc chắn, quai đeo êm vai.', 450000.00, 30, 'GuitarX', '2026-05-20', 6, 0);
            ");
        }
    }
}
?>