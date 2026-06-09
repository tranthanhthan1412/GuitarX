<?php
class OrderModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getPaymentMethods() {
        $stmt = $this->db->query("SELECT * FROM `PAYMENT_METHOD`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createOrder($userId, $cartDetails, $address, $city, $paymentMethodId, $voucherId = null) {
        try {
            // Bắt đầu Transaction
            $this->db->beginTransaction();

            // 1. Lưu địa chỉ giao hàng vào bảng SHIPPING_ADDRESS
            $queryAddr = "INSERT INTO `SHIPPING_ADDRESS` (`Adress`, `City`, `User_ID`) VALUES (:address, :city, :userId)";
            $stmtAddr = $this->db->prepare($queryAddr);
            $stmtAddr->bindParam(':address', $address);
            $stmtAddr->bindParam(':city', $city);
            $stmtAddr->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtAddr->execute();
            $shippingId = $this->db->lastInsertId();

            // 2. Lưu thông tin đơn hàng vào bảng ORDERS
            $queryOrder = "INSERT INTO `ORDERS` (`User_ID`, `PayMent_ID`, `Status`, `Vouchers_ID`) VALUES (:userId, :paymentId, 'Pending', :voucherId)";
            $stmtOrder = $this->db->prepare($queryOrder);
            $stmtOrder->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtOrder->bindParam(':paymentId', $paymentMethodId, PDO::PARAM_INT);
            $stmtOrder->bindParam(':voucherId', $voucherId, PDO::PARAM_INT);
            $stmtOrder->execute();
            $orderId = $this->db->lastInsertId();

            if ($voucherId !== null) {
                $queryUpdateVoucher = "UPDATE `VOUCHERS` SET `quantity` = `quantity` - 1 WHERE `Vouchers_ID` = :voucherId AND `quantity` > 0";
                $stmtUpdateVoucher = $this->db->prepare($queryUpdateVoucher);
                $stmtUpdateVoucher->bindParam(':voucherId', $voucherId, PDO::PARAM_INT);
                $stmtUpdateVoucher->execute();

                if ($stmtUpdateVoucher->rowCount() == 0) {
                    throw new Exception("Mã giảm giá đã hết lượt sử dụng.");
                }
            }

            // 3. Xử lý từng sản phẩm trong giỏ hàng
            $queryDetail = "INSERT INTO `ORDER_DETAIL` (`Order_ID`, `Product_ID`, `Total`, `Quantity`) VALUES (:orderId, :productId, :total, :qty)";
            $stmtDetail = $this->db->prepare($queryDetail);

            $queryDelivery = "INSERT INTO `DELIVERY_NOTE` (`Order_ID`, `Product_ID`, `ShippingAddress_ID`) VALUES (:orderId, :productId, :shippingId)";
            $stmtDelivery = $this->db->prepare($queryDelivery);

            $queryUpdateStock = "UPDATE `PRODUCTS` SET `Count` = `Count` - :qty WHERE `Product_ID` = :productId AND `Count` >= :qty";
            $stmtUpdateStock = $this->db->prepare($queryUpdateStock);

            foreach ($cartDetails as $item) {
                // Lưu Order Detail
                $stmtDetail->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmtDetail->bindParam(':productId', $item['Product_ID'], PDO::PARAM_INT);
                $stmtDetail->bindParam(':total', $item['Subtotal']);
                $stmtDetail->bindParam(':qty', $item['Quantity'], PDO::PARAM_INT);
                $stmtDetail->execute();

                // Lưu Delivery Note
                $stmtDelivery->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmtDelivery->bindParam(':productId', $item['Product_ID'], PDO::PARAM_INT);
                $stmtDelivery->bindParam(':shippingId', $shippingId, PDO::PARAM_INT);
                $stmtDelivery->execute();

                // Trừ số lượng tồn kho
                $stmtUpdateStock->bindParam(':qty', $item['Quantity'], PDO::PARAM_INT);
                $stmtUpdateStock->bindParam(':productId', $item['Product_ID'], PDO::PARAM_INT);
                $stmtUpdateStock->execute();
                
                // Kiểm tra xem có trừ được không (nếu bị âm thì rollback)
                if ($stmtUpdateStock->rowCount() == 0) {
                    throw new Exception("Sản phẩm " . $item['ProductName'] . " không đủ số lượng trong kho.");
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
        $query = "SELECT O.Order_ID, O.Order_Date, O.Status, PM.MethodName,
                         GREATEST(0, COALESCE(SUM(OD.Total), 0) - COALESCE(V.discount_value, 0)) AS TotalAmount
                  FROM `ORDERS` O
                  LEFT JOIN `PAYMENT_METHOD` PM ON O.PayMent_ID = PM.PayMent_ID
                  LEFT JOIN `ORDER_DETAIL` OD ON O.Order_ID = OD.Order_ID
                  LEFT JOIN `VOUCHERS` V ON O.Vouchers_ID = V.Vouchers_ID
                  WHERE O.User_ID = :userId
                  GROUP BY O.Order_ID, O.Order_Date, O.Status, PM.MethodName, V.discount_value
                  ORDER BY O.Order_Date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết các sản phẩm trong một đơn hàng cụ thể (bảo mật bằng cách kiểm tra userId)
    public function getOrderDetails($orderId, $userId) {
        // Kiểm tra xem đơn hàng có thuộc về user này không
        $checkQuery = "SELECT COUNT(*) FROM `ORDERS` WHERE `Order_ID` = :orderId AND `User_ID` = :userId";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $checkStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() == 0) {
            return false; // Đơn hàng không tồn tại hoặc không thuộc về user này
        }

        // Lấy chi tiết
        $query = "SELECT OD.Quantity, OD.Total as Subtotal, P.ProductName, P.Image, P.Price,
                         V.Code as VoucherCode, V.discount_value as VoucherDiscount
                  FROM `ORDER_DETAIL` OD
                  JOIN `PRODUCTS` P ON OD.Product_ID = P.Product_ID
                  JOIN `ORDERS` O ON OD.Order_ID = O.Order_ID
                  LEFT JOIN `VOUCHERS` V ON O.Vouchers_ID = V.Vouchers_ID
                  WHERE OD.Order_ID = :orderId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- ADMIN METHODS ---
    public function getAllOrders() {
        $query = "SELECT O.Order_ID, O.Status, PM.MethodName as PaymentName, U.UserName as CustomerName,
                         GREATEST(0, (SELECT SUM(Total) FROM `ORDER_DETAIL` WHERE Order_ID = O.Order_ID) - COALESCE(V.discount_value, 0)) as GrandTotal
                  FROM `ORDERS` O
                  JOIN `USER` U ON O.User_ID = U.User_ID
                  LEFT JOIN `PAYMENT_METHOD` PM ON O.PayMent_ID = PM.PayMent_ID
                  LEFT JOIN `VOUCHERS` V ON O.Vouchers_ID = V.Vouchers_ID
                  ORDER BY O.Order_ID DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminOrderDetails($orderId) {
        // Lấy chi tiết chung
        $queryOrder = "SELECT O.Order_ID, O.Status, U.UserName, U.Email, U.PhoneNumber,
                               SA.Adress, SA.City, V.Code as VoucherCode, V.discount_value as VoucherDiscount
                       FROM `ORDERS` O
                       JOIN `USER` U ON O.User_ID = U.User_ID
                       LEFT JOIN `DELIVERY_NOTE` DN ON O.Order_ID = DN.Order_ID
                       LEFT JOIN `SHIPPING_ADDRESS` SA ON DN.ShippingAddress_ID = SA.ShippingAddress_ID
                       LEFT JOIN `VOUCHERS` V ON O.Vouchers_ID = V.Vouchers_ID
                       WHERE O.Order_ID = :id LIMIT 1";
        $stmtOrder = $this->db->prepare($queryOrder);
        $stmtOrder->bindParam(':id', $orderId, PDO::PARAM_INT);
        $stmtOrder->execute();
        $orderInfo = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách mặt hàng
        $queryItems = "SELECT OD.Quantity, OD.Total as Subtotal, P.ProductName, P.Image, P.Price 
                       FROM `ORDER_DETAIL` OD
                       JOIN `PRODUCTS` P ON OD.Product_ID = P.Product_ID
                       WHERE OD.Order_ID = :id";
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
            $stmtCheck = $this->db->prepare("SELECT `Status` FROM `ORDERS` WHERE `Order_ID` = :id FOR UPDATE");
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
                $stmtItems = $this->db->prepare("SELECT `Product_ID`, `Quantity` FROM `ORDER_DETAIL` WHERE `Order_ID` = :id");
                $stmtItems->bindParam(':id', $orderId, PDO::PARAM_INT);
                $stmtItems->execute();
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                $stmtUpdateStock = $this->db->prepare("UPDATE `PRODUCTS` SET `Count` = `Count` + :qty WHERE `Product_ID` = :productId");
                foreach ($items as $item) {
                    $stmtUpdateStock->bindParam(':qty', $item['Quantity'], PDO::PARAM_INT);
                    $stmtUpdateStock->bindParam(':productId', $item['Product_ID'], PDO::PARAM_INT);
                    $stmtUpdateStock->execute();
                }

                // Hoàn trả lượt sử dụng Voucher (nếu có)
                $stmtVoucher = $this->db->prepare("SELECT `Vouchers_ID` FROM `ORDERS` WHERE `Order_ID` = :id");
                $stmtVoucher->bindParam(':id', $orderId, PDO::PARAM_INT);
                $stmtVoucher->execute();
                $usedVoucherId = $stmtVoucher->fetchColumn();

                if ($usedVoucherId) {
                    $stmtUpdateVoucher = $this->db->prepare("UPDATE `VOUCHERS` SET `quantity` = `quantity` + 1 WHERE `Vouchers_ID` = :vid");
                    $stmtUpdateVoucher->bindParam(':vid', $usedVoucherId, PDO::PARAM_INT);
                    $stmtUpdateVoucher->execute();
                }
            }

            // Cập nhật trạng thái
            $stmtUpdate = $this->db->prepare("UPDATE `ORDERS` SET `Status` = :status WHERE `Order_ID` = :id");
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
