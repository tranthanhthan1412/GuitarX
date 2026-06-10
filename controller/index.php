<?php
// controller/index.php — Front Controller

$act = isset($_GET['act']) ? $_GET['act'] : 'home';

// Khởi tạo FavoriteModel dùng chung
require_once __DIR__ . "/../model/m_favorite.php";
$favoriteModel = new FavoriteModel($db);
$userFavorites = [];
$favoriteCount = 0;
if (isset($_SESSION['user_id'])) {
    $userFavorites = $favoriteModel->getUserFavoriteIds($_SESSION['user_id']);
    $favoriteCount = count($userFavorites);
}

switch ($act) {
    // controller/index.php (Sửa lại đoạn case 'sanpham')

    // controller/index.php

    case 'sanpham':
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($db);

        $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
    
        // === XỬ LÝ PHÂN TRANG ===
        $limit = 6; // Hiện 6 cây mỗi trang
        $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($currentPage < 1) $currentPage = 1;

        if ($catId > 0) {
            // Lấy sản phẩm theo trang và đếm tổng theo danh mục
            $productsList = $productModel->getProductsByCategory($catId, $sort, $currentPage, $limit);
            $totalProducts = $productModel->countProductsByCategory($catId);
            $titleName = $productModel->getCategoryName($catId);
        } else {
            // Lấy tất cả sản phẩm theo trang và đếm tổng toàn bộ
            $productsList = $productModel->getAllProducts($sort, $currentPage, $limit);
            $totalProducts = $productModel->countAllProducts();
            $titleName = "Tất Cả Sản Phẩm";
        }

        // Tính tổng số trang cần có (Dùng hàm ceil để làm tròn lên)
        $totalPages = ceil($totalProducts / $limit);

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
    
    // Đón dữ liệu khi người dùng gửi Form đánh giá
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
        if (isset($_SESSION['user_id'])) { // Kiểm tra đăng nhập theo session của mày
            $userId = $_SESSION['user_id'];
            $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
            $comment = trim($_POST['comment'] ?? '');

            if (!empty($comment)) {
                $productModel->addReview($prodId, $userId, $rating, $comment);
                // Gửi xong ép trang load lại để cập nhật luôn dữ liệu mới sạch sẽ
                header("Location: index.php?act=chitiet&id=" . $prodId);
                exit();
            }
        }
    }

    $product = $productModel->getProductById($prodId);
    
    // Lấy danh sách đánh giá từ DB đổ ra giao diện
    $reviews = $productModel->getProductReviews($prodId);

    if (!$product) {
        header("Location: /GuitarX/index.php");
        exit();
    }

    // Phần lấy sản phẩm liên quan (giữ nguyên logic gốc của mày)
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
    
    case 'timkiem':
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($db);

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $productsList = $productModel->searchProducts($keyword);
        $titleName = "Kết quả tìm kiếm: " . htmlspecialchars($keyword);

        include_once __DIR__ . "/../view/header.php";
        $sanphamFile = __DIR__ . "/../view/sanpham.php";
        if (file_exists($sanphamFile)) {
            include_once $sanphamFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'yeuthich':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }
        $productsList = $favoriteModel->getFavoriteProducts($_SESSION['user_id']);
        $titleName = "Sản phẩm yêu thích";

        include_once __DIR__ . "/../view/header.php";
        $sanphamFile = __DIR__ . "/../view/sanpham.php";
        if (file_exists($sanphamFile)) {
            include_once $sanphamFile;
        }
        include_once __DIR__ . "/../view/footer.php";
        break;

    case 'toggle_favorite':
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.', 'require_login' => true]);
            exit();
        }
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if ($productId > 0) {
            $isAdded = $favoriteModel->toggleFavorite($_SESSION['user_id'], $productId);
            $count = $favoriteModel->getFavoriteCount($_SESSION['user_id']);
            echo json_encode(['success' => true, 'is_added' => $isAdded, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']);
        }
        exit();

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

    case 'apply_voucher':
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
            exit();
        }
        $code = trim($_POST['code'] ?? '');
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit();
        }
        require_once __DIR__ . "/../model/m_voucher.php";
        $voucherModel = new VoucherModel($db);
        $v = $voucherModel->getVoucherByCode($code);
        
        if (!$v) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại.']);
            exit();
        }
        if ($v['quantity'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
            exit();
        }
        if (!empty($v['expiry_date']) && strtotime($v['expiry_date']) < strtotime(date('Y-m-d'))) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết hạn.']);
            exit();
        }
        
        $_SESSION['applied_voucher'] = [
            'id' => $v['Vouchers_ID'],
            'code' => $v['Code'],
            'discount_value' => $v['discount_value']
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công.',
            'code' => $v['Code'],
            'discount' => $v['discount_value']
        ]);
        exit();

    case 'remove_voucher':
        header('Content-Type: application/json');
        if (isset($_SESSION['applied_voucher'])) {
            unset($_SESSION['applied_voucher']);
        }
        echo json_encode(['success' => true]);
        exit();

    case 'thanhtoan':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /GuitarX/index.php?act=login");
            exit();
        }

        require_once __DIR__ . "/../model/m_giohang.php";
        require_once __DIR__ . "/../model/m_donhang.php";
        require_once __DIR__ . "/../model/m_user.php"; // Require thêm model user để check rank
        
        $cartModel = new CartModel($db);
        $orderModel = new OrderModel($db);
        $userModel = new UserModel($db); // Khởi tạo đối tượng xử lý user
        
        $cartSession = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cartSession)) {
            header("Location: /GuitarX/index.php?act=giohang");
            exit();
        }

        $cartDetails = $cartModel->getCartDetails($cartSession);
        $totalAmount = $cartModel->calculateTotal($cartDetails); // Tổng tiền gốc của giỏ hàng
        $paymentMethods = $orderModel->getPaymentMethods();

        // === XỬ LÝ LOGIC RANK KHÁCH HÀNG & GIẢM GIÁ ===
        $subTotal = $totalAmount; // Lưu lại số tiền gốc để show ra view làm "Tạm tính"
        $discountPercent = 0;
        $discountAmount = 0;
        $rankName = "Thành viên mới";

        if (isset($_SESSION['user_id'])) {
            $userRank = $userModel->getCustomerRank($_SESSION['user_id']);
            $rankName = $userRank['name'];
            $discountPercent = $userRank['discount']; // Số % được giảm (0, 2, 5, 10)
            
            // Số tiền được giảm theo Rank
            $discountAmount = $subTotal * ($discountPercent / 100);
            
            // Cập nhật lại biến $totalAmount sau khi trừ đi phần giảm của Rank
            // Việc này giúp hàm createOrder() và hàm gửi Mail nhận đúng số tiền thực tế
            $totalAmount = $subTotal - $discountAmount;
        }
        // =============================================

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $address = trim($_POST['address'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $paymentMethodId = isset($_POST['payment_method']) ? intval($_POST['payment_method']) : 0;

            if (empty($address) || empty($city) || $paymentMethodId <= 0) {
                $error = 'Vui lòng điền đầy đủ thông tin giao hàng và chọn phương thức thanh toán.';
            } else {
                $voucherId = null;
                $voucherCode = null;
                $discountValue = 0;
                
                if (isset($_SESSION['applied_voucher'])) {
                    $voucherId = $_SESSION['applied_voucher']['id'];
                    $voucherCode = $_SESSION['applied_voucher']['code'];
                    $discountValue = $_SESSION['applied_voucher']['discount_value'];
                }

                // Nếu có dùng voucher, số tiền cuối cùng khi gửi mail hóa đơn sẽ trừ tiếp voucherValue
                $finalInvoiceAmount = $totalAmount - $discountValue;
                if ($finalInvoiceAmount < 0) $finalInvoiceAmount = 0;

                // Tạo đơn hàng (Mày truyền $totalAmount đã giảm rank vào đây)
                $orderId = $orderModel->createOrder($_SESSION['user_id'], $cartDetails, $address, $city, $paymentMethodId, $voucherId);
                
                if ($orderId) {
                    // Gửi email hóa đơn
                    require_once __DIR__ . "/../model/m_mail.php";
                    $mailService = new MailService();
                    $customerEmail = $_SESSION['email'] ?? '';
                    $customerName = $_SESSION['username'] ?? 'Khách hàng';
                    $fullAddress = $address . ', ' . $city;
                    
                    if (!empty($customerEmail)) {
                        // Truyền số tiền thực tế sau khi áp dụng cả Rank lẫn Voucher vào Mail hóa đơn
                        $mailService->sendInvoiceEmail($customerEmail, $customerName, $orderId, $cartDetails, $finalInvoiceAmount, $fullAddress, $voucherCode, $discountValue);
                    }

                    unset($_SESSION['cart']);
                    unset($_SESSION['applied_voucher']);
                    header("Location: /GuitarX/index.php?act=camon&id=" . $orderId);
                    exit();
                } else {
                    $error = 'Có lỗi xảy ra hoặc sản phẩm/voucher đã hết. Vui lòng thử lại sau.';
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