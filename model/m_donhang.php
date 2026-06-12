<?php
class OrderModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getPaymentMethods() {
        $stmt = $this->db->query("SELECT * FROM `PhuongThucThanhToan`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createOrder($userId, $cartDetails, $address, $city, $paymentMethodId, $voucherId = null) {
        try {
            // Bắt đầu Transaction
            $this->db->beginTransaction();

            // 1. Lưu địa chỉ giao hàng vào bảng DiaChiGiaoHang
            $queryAddr = "INSERT INTO `DiaChiGiaoHang` (`DiaChi`, `ThanhPho`, `Ma_NguoiDung`) VALUES (:address, :city, :userId)";
            $stmtAddr = $this->db->prepare($queryAddr);
            $stmtAddr->bindParam(':address', $address);
            $stmtAddr->bindParam(':city', $city);
            $stmtAddr->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtAddr->execute();
            $shippingId = $this->db->lastInsertId();

            // 2. Lưu thông tin đơn hàng vào bảng DonHang
            $queryOrder = "INSERT INTO `DonHang` (`Ma_NguoiDung`, `Ma_PhuongThuc`, `TrangThai`, `Ma_MaGiamGia`) VALUES (:userId, :paymentId, 'Pending', :voucherId)";
            $stmtOrder = $this->db->prepare($queryOrder);
            $stmtOrder->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtOrder->bindParam(':paymentId', $paymentMethodId, PDO::PARAM_INT);
            $stmtOrder->bindParam(':voucherId', $voucherId, PDO::PARAM_INT);
            $stmtOrder->execute();
            $orderId = $this->db->lastInsertId();

            if ($voucherId !== null) {
                $queryUpdateVoucher = "UPDATE `MaGiamGia` SET `SoLuong` = `SoLuong` - 1 WHERE `Ma_MaGiamGia` = :voucherId AND `SoLuong` > 0";
                $stmtUpdateVoucher = $this->db->prepare($queryUpdateVoucher);
                $stmtUpdateVoucher->bindParam(':voucherId', $voucherId, PDO::PARAM_INT);
                $stmtUpdateVoucher->execute();

                if ($stmtUpdateVoucher->rowCount() == 0) {
                    throw new Exception("Mã giảm giá đã hết lượt sử dụng.");
                }
            }

            // 3. Xử lý từng sản phẩm trong giỏ hàng
            $queryDetail = "INSERT INTO `ChiTietDonHang` (`Ma_DonHang`, `Ma_SanPham`, `TongTien`, `SoLuong`) VALUES (:orderId, :productId, :total, :qty)";
            $stmtDetail = $this->db->prepare($queryDetail);

            $queryDelivery = "INSERT INTO `GhiChuGiaoHang` (`Ma_DonHang`, `Ma_SanPham`, `Ma_DiaChiGiao`) VALUES (:orderId, :productId, :shippingId)";
            $stmtDelivery = $this->db->prepare($queryDelivery);

            $queryUpdateStock = "UPDATE `SanPham` SET `SoLuong` = `SoLuong` - :qty WHERE `Ma_SanPham` = :productId AND `SoLuong` >= :qty";
            $stmtUpdateStock = $this->db->prepare($queryUpdateStock);

            foreach ($cartDetails as $item) {
                // Lưu Order Detail
                $stmtDetail->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmtDetail->bindParam(':productId', $item['Ma_SanPham'], PDO::PARAM_INT);
                $stmtDetail->bindParam(':total', $item['Subtotal']);
                $stmtDetail->bindParam(':qty', $item['SoLuong'], PDO::PARAM_INT);
                $stmtDetail->execute();

                // Lưu Delivery Note
                $stmtDelivery->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmtDelivery->bindParam(':productId', $item['Ma_SanPham'], PDO::PARAM_INT);
                $stmtDelivery->bindParam(':shippingId', $shippingId, PDO::PARAM_INT);
                $stmtDelivery->execute();

                // Trừ số lượng tồn kho
                $stmtUpdateStock->bindParam(':qty', $item['SoLuong'], PDO::PARAM_INT);
                $stmtUpdateStock->bindParam(':productId', $item['Ma_SanPham'], PDO::PARAM_INT);
                $stmtUpdateStock->execute();
                
                // Kiểm tra xem có trừ được không (nếu bị âm thì rollback)
                if ($stmtUpdateStock->rowCount() == 0) {
                    throw new Exception("Sản phẩm " . $item['TenSanPham'] . " không đủ số lượng trong kho.");
                }
            }

            // Hoàn tất Transaction
            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            // Hủy bỏ toàn bộ thay đổi nếu có lỗi
            $this->db->rollBack();
            error_log("Lỗi tạo đơn hàng: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách các đơn hàng của user kèm tổng tiền và phương thức thanh toán
    public function getOrdersByUserId($userId) {
        $query = "SELECT O.Ma_DonHang, O.NgayDatHang, O.TrangThai, PM.TenPhuongThuc,
                         GREATEST(0, COALESCE(SUM(OD.TongTien), 0) - COALESCE(V.GiaTriGiam, 0)) AS TotalAmount
                  FROM `DonHang` O
                  LEFT JOIN `PhuongThucThanhToan` PM ON O.Ma_PhuongThuc = PM.Ma_PhuongThuc
                  LEFT JOIN `ChiTietDonHang` OD ON O.Ma_DonHang = OD.Ma_DonHang
                  LEFT JOIN `MaGiamGia` V ON O.Ma_MaGiamGia = V.Ma_MaGiamGia
                  WHERE O.Ma_NguoiDung = :userId
                  GROUP BY O.Ma_DonHang, O.NgayDatHang, O.TrangThai, PM.TenPhuongThuc, V.GiaTriGiam
                  ORDER BY O.NgayDatHang DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết các sản phẩm trong một đơn hàng cụ thể (bảo mật bằng cách kiểm tra userId)
    public function getOrderDetails($orderId, $userId) {
        // Kiểm tra xem đơn hàng có thuộc về user này không
        $checkQuery = "SELECT COUNT(*) FROM `DonHang` WHERE `Ma_DonHang` = :orderId AND `Ma_NguoiDung` = :userId";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $checkStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() == 0) {
            return false; // Đơn hàng không tồn tại hoặc không thuộc về user này
        }

        // Lấy chi tiết
        $query = "SELECT OD.SoLuong, OD.TongTien as Subtotal, P.TenSanPham, P.Anh, P.GiaTien,
                         V.Ma as VoucherCode, V.GiaTriGiam as VoucherDiscount
                  FROM `ChiTietDonHang` OD
                  JOIN `SanPham` P ON OD.Ma_SanPham = P.Ma_SanPham
                  JOIN `DonHang` O ON OD.Ma_DonHang = O.Ma_DonHang
                  LEFT JOIN `MaGiamGia` V ON O.Ma_MaGiamGia = V.Ma_MaGiamGia
                  WHERE OD.Ma_DonHang = :orderId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- ADMIN METHODS ---
    public function getAllOrders() {
        $query = "SELECT O.Ma_DonHang, O.TrangThai, PM.TenPhuongThuc as PaymentName, U.TenNguoiDung as CustomerName,
                         GREATEST(0, (SELECT SUM(TongTien) FROM `ChiTietDonHang` WHERE Ma_DonHang = O.Ma_DonHang) - COALESCE(V.GiaTriGiam, 0)) as GrandTotal
                  FROM `DonHang` O
                  JOIN `NguoiDung` U ON O.Ma_NguoiDung = U.Ma_NguoiDung
                  LEFT JOIN `PhuongThucThanhToan` PM ON O.Ma_PhuongThuc = PM.Ma_PhuongThuc
                  LEFT JOIN `MaGiamGia` V ON O.Ma_MaGiamGia = V.Ma_MaGiamGia
                  ORDER BY O.Ma_DonHang DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminOrderDetails($orderId) {
        // Lấy chi tiết chung
        $queryOrder = "SELECT O.Ma_DonHang, O.TrangThai, U.TenNguoiDung, U.Email, U.SDT,
                               SA.DiaChi, SA.ThanhPho, V.Ma as VoucherCode, V.GiaTriGiam as VoucherDiscount
                       FROM `DonHang` O
                       JOIN `NguoiDung` U ON O.Ma_NguoiDung = U.Ma_NguoiDung
                       LEFT JOIN `GhiChuGiaoHang` DN ON O.Ma_DonHang = DN.Ma_DonHang
                       LEFT JOIN `DiaChiGiaoHang` SA ON DN.Ma_DiaChiGiao = SA.Ma_DiaChiGiao
                       LEFT JOIN `MaGiamGia` V ON O.Ma_MaGiamGia = V.Ma_MaGiamGia
                       WHERE O.Ma_DonHang = :id LIMIT 1";
        $stmtOrder = $this->db->prepare($queryOrder);
        $stmtOrder->bindParam(':id', $orderId, PDO::PARAM_INT);
        $stmtOrder->execute();
        $orderInfo = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách mặt hàng
        $queryItems = "SELECT OD.SoLuong, OD.TongTien as Subtotal, P.TenSanPham, P.Anh, P.GiaTien 
                       FROM `ChiTietDonHang` OD
                       JOIN `SanPham` P ON OD.Ma_SanPham = P.Ma_SanPham
                       WHERE OD.Ma_DonHang = :id";
        $stmtItems = $this->db->prepare($queryItems);
        $stmtItems->bindParam(':id', $orderId, PDO::PARAM_INT);
        $stmtItems->execute();
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        return ['info' => $orderInfo, 'items' => $items];
    }

    public function updateOrderStatus($orderId, $newStatus) {
        try {
            $this->db->beginTransaction();
            
            // Lấy status hiện tại
            $stmtCheck = $this->db->prepare("SELECT `TrangThai` FROM `DonHang` WHERE `Ma_DonHang` = :id FOR UPDATE");
            $stmtCheck->bindParam(':id', $orderId, PDO::PARAM_INT);
            $stmtCheck->execute();
            $currentStatus = $stmtCheck->fetchColumn();

            // Nếu đơn đã hủy thì không cho đổi trạng thái lại nữa
            if ($currentStatus === 'Cancelled') {
                $this->db->rollBack();
                return false; 
            }

            // Nếu chuyển thành Hủy, thì hoàn trả tồn kho
            if ($newStatus === 'Cancelled' && $currentStatus !== 'Cancelled') {
                // Hoàn trả tồn kho sản phẩm
                $stmtItems = $this->db->prepare("SELECT `Ma_SanPham`, `SoLuong` FROM `ChiTietDonHang` WHERE `Ma_DonHang` = :id");
                $stmtItems->bindParam(':id', $orderId, PDO::PARAM_INT);
                $stmtItems->execute();
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                $stmtUpdateStock = $this->db->prepare("UPDATE `SanPham` SET `SoLuong` = `SoLuong` + :qty WHERE `Ma_SanPham` = :productId");
                foreach ($items as $item) {
                    $stmtUpdateStock->bindParam(':qty', $item['SoLuong'], PDO::PARAM_INT);
                    $stmtUpdateStock->bindParam(':productId', $item['Ma_SanPham'], PDO::PARAM_INT);
                    $stmtUpdateStock->execute();
                }

                // Hoàn trả lượt sử dụng Voucher (nếu có)
                $stmtVoucher = $this->db->prepare("SELECT `Ma_MaGiamGia` FROM `DonHang` WHERE `Ma_DonHang` = :id");
                $stmtVoucher->bindParam(':id', $orderId, PDO::PARAM_INT);
                $stmtVoucher->execute();
                $usedVoucherId = $stmtVoucher->fetchColumn();

                if ($usedVoucherId) {
                    $stmtUpdateVoucher = $this->db->prepare("UPDATE `MaGiamGia` SET `SoLuong` = `SoLuong` + 1 WHERE `Ma_MaGiamGia` = :vid");
                    $stmtUpdateVoucher->bindParam(':vid', $usedVoucherId, PDO::PARAM_INT);
                    $stmtUpdateVoucher->execute();
                }
            }

            // Cập nhật trạng thái
            $stmtUpdate = $this->db->prepare("UPDATE `DonHang` SET `TrangThai` = :status WHERE `Ma_DonHang` = :id");
            $stmtUpdate->bindParam(':status', $newStatus);
            $stmtUpdate->bindParam(':id', $orderId, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Lỗi cập nhật trạng thái đơn hàng: " . $e->getMessage());
            return false;
        }
    }
}
?>
