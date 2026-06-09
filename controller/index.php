<?php
// controller/index.php — Front Controller

$act = isset($_GET['act']) ? $_GET['act'] : 'home';

switch ($act) {
    case 'sanpham':
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($db);

        $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($catId > 0) {
            $productsList = $productModel->getProductsByCategory($catId);
            $titleName = $productModel->getCategoryName($catId);
        } else {
            $productsList = $productModel->getAllProducts();
            $titleName = "Tất Cả Sản Phẩm";
        }

        include_once __DIR__ . "/../view/header.php";
        $sanphamFile = __DIR__ . "/../view/sanpham.php";
        if (file_exists($sanphamFile)) {
            include_once $sanphamFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'chitiet':
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($db);

        $prodId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $product = $productModel->getProductById($prodId);

        if (!$product) {
            header("Location: /GuitarX/index.php");
            exit();
        }

        // Lấy các sản phẩm liên quan (cùng category, tối đa 4 sản phẩm, loại trừ sản phẩm hiện tại)
        $relatedProducts = [];
        if (!empty($product['Category_ID'])) {
            $allRelated = $productModel->getProductsByCategory($product['Category_ID']);
            foreach ($allRelated as $item) {
                if ($item['Product_ID'] != $prodId) {
                    $relatedProducts[] = $item;
                }
                if (count($relatedProducts) >= 4) {
                    break;
                }
            }
        }

        include_once __DIR__ . "/../view/header.php";
        $chitietFile = __DIR__ . "/../view/chitietsanpham.php";
        if (file_exists($chitietFile)) {
            include_once $chitietFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'login':
        // taikhoan.php đã tự gọi header và footer ở bên trong nên không cần bao bọc lại
        include_once __DIR__ . "/../view/taikhoan.php";
        break;

    case 'dangky':
        include_once __DIR__ . "/../view/dangky.php";
        break;

    case 'giohang':
        // KIỂM TRA ĐĂNG NHẬP
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        require_once __DIR__ . "/../model/m_giohang.php";
        $cartModel = new CartModel($db);
        
        $cartSession = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cartDetails = $cartModel->getCartDetails($cartSession);
        $totalAmount = $cartModel->calculateTotal($cartDetails);

        include_once __DIR__ . "/../view/header.php";
        $giohangFile = __DIR__ . "/../view/giohang.php";
        if (file_exists($giohangFile)) {
            include_once $giohangFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'themgiohang':
        // KIỂM TRA ĐĂNG NHẬP
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

            if ($productId > 0 && $quantity > 0) {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
        }
        header("Location: /GuitarX/index.php?act=giohang");
        exit();
        break;

    case 'capnhatgiohang':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

            if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$productId] = $quantity;
                } else {
                    unset($_SESSION['cart'][$productId]);
                }
            }
        }
        header("Location: /GuitarX/index.php?act=giohang");
        exit();
        break;

    case 'xoagiohang':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        header("Location: /GuitarX/index.php?act=giohang");
        exit();
        break;

    case 'thanhtoan':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        require_once __DIR__ . "/../model/m_giohang.php";
        require_once __DIR__ . "/../model/m_donhang.php";
        
        $cartModel = new CartModel($db);
        $orderModel = new OrderModel($db);
        
        $cartSession = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cartSession)) {
            header("Location: /GuitarX/index.php?act=giohang");
            exit();
        }

        $cartDetails = $cartModel->getCartDetails($cartSession);
        $totalAmount = $cartModel->calculateTotal($cartDetails);
        $paymentMethods = $orderModel->getPaymentMethods();

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $address = trim($_POST['address'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $paymentMethodId = isset($_POST['payment_method']) ? intval($_POST['payment_method']) : 0;

            if (empty($address) || empty($city) || $paymentMethodId <= 0) {
                $error = 'Vui lòng điền đầy đủ thông tin giao hàng và chọn phương thức thanh toán.';
            } else {
                $orderId = $orderModel->createOrder($_SESSION['user_id'], $cartDetails, $address, $city, $paymentMethodId);
                
                if ($orderId) {
                    unset($_SESSION['cart']);
                    header("Location: /GuitarX/index.php?act=camon&id=" . $orderId);
                    exit();
                } else {
                    $error = 'Có lỗi xảy ra hoặc sản phẩm đã hết hàng. Vui lòng thử lại sau.';
                }
            }
        }

        include_once __DIR__ . "/../view/header.php";
        $thanhtoanFile = __DIR__ . "/../view/thanhtoan.php";
        if (file_exists($thanhtoanFile)) {
            include_once $thanhtoanFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'camon':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        include_once __DIR__ . "/../view/header.php";
        $camonFile = __DIR__ . "/../view/camon.php";
        if (file_exists($camonFile)) {
            include_once $camonFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'lichsudonhang':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        require_once __DIR__ . "/../model/m_donhang.php";
        $orderModel = new OrderModel($db);
        
        $userId = $_SESSION['user_id'];
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($orderId > 0) {
            // Xem chi tiết một đơn hàng
            $orderDetails = $orderModel->getOrderDetails($orderId, $userId);
            if ($orderDetails === false) {
                // Đơn hàng không tồn tại hoặc không phải của user này
                header("Location: /GuitarX/index.php?act=lichsudonhang");
                exit();
            }
            $viewMode = 'detail';
        } else {
            // Xem danh sách đơn hàng
            $ordersList = $orderModel->getOrdersByUserId($userId);
            $viewMode = 'list';
        }

        include_once __DIR__ . "/../view/header.php";
        $lichsuFile = __DIR__ . "/../view/lichsudonhang.php";
        if (file_exists($lichsuFile)) {
            include_once $lichsuFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'home':
    default:
        include_once __DIR__ . "/../view/header.php";
        include_once __DIR__ . "/../view/trangchu.php";
        include_once __DIR__ . "/../view/footer.php";
        break;
}
?>