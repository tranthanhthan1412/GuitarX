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

    public function createOrder($userId, $cartDetails, $address, $city, $paymentMethodId) {
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
            $queryOrder = "INSERT INTO `ORDERS` (`User_ID`, `PayMent_ID`, `Status`) VALUES (:userId, :paymentId, 'Pending')";
            $stmtOrder = $this->db->prepare($queryOrder);
            $stmtOrder->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtOrder->bindParam(':paymentId', $paymentMethodId, PDO::PARAM_INT);
            $stmtOrder->execute();
            $orderId = $this->db->lastInsertId();

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
                         COALESCE(SUM(OD.Total), 0) AS TotalAmount
                  FROM `ORDERS` O
                  LEFT JOIN `PAYMENT_METHOD` PM ON O.PayMent_ID = PM.PayMent_ID
                  LEFT JOIN `ORDER_DETAIL` OD ON O.Order_ID = OD.Order_ID
                  WHERE O.User_ID = :userId
                  GROUP BY O.Order_ID, O.Order_Date, O.Status, PM.MethodName
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
        $query = "SELECT OD.Quantity, OD.Total as Subtotal, P.ProductName, P.Image, P.Price 
                  FROM `ORDER_DETAIL` OD
                  JOIN `PRODUCTS` P ON OD.Product_ID = P.Product_ID
                  WHERE OD.Order_ID = :orderId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
