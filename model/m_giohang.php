<?php
class CartModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Lấy thông tin chi tiết các sản phẩm trong giỏ hàng (từ mảng $_SESSION['cart'])
    public function getCartDetails($cartSession) {
        $cartDetails = [];
        if (empty($cartSession)) {
            return $cartDetails;
        }

        foreach ($cartSession as $productId => $quantity) {
            $query = "SELECT `Product_ID`, `ProductName`, `Image`, `Price`, `Count`, `DiscountPercent` 
                      FROM `PRODUCTS` WHERE `Product_ID` = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Đảm bảo số lượng không vượt quá số lượng tồn kho
                $actualQty = min((int)$quantity, (int)$product['Count']);
                
                // Tính giá đã giảm
                $discountPercent = isset($product['DiscountPercent']) ? (int)$product['DiscountPercent'] : 0;
                $actualPrice = $product['Price'] - ($product['Price'] * $discountPercent / 100);

                $cartDetails[] = [
                    'Product_ID' => $product['Product_ID'],
                    'ProductName' => $product['ProductName'],
                    'Image' => $product['Image'],
                    'OriginalPrice' => $product['Price'],
                    'Price' => $actualPrice,
                    'Quantity' => $actualQty,
                    'Subtotal' => $actualPrice * $actualQty,
                    'MaxCount' => $product['Count']
                ];
            }
        }

        return $cartDetails;
    }

    // Tính tổng số tiền giỏ hàng
    public function calculateTotal($cartDetails) {
        $total = 0;
        foreach ($cartDetails as $item) {
            $total += $item['Subtotal'];
        }
        return $total;
    }

    // Tính tổng số lượng sản phẩm trong giỏ hàng (để hiển thị badge trên header)
    public function getTotalItemCount($cartSession) {
        $count = 0;
        if (!empty($cartSession)) {
            foreach ($cartSession as $qty) {
                $count += $qty;
            }
        }
        return $count;
    }
}
?>
