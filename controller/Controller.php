<?php
// controller/Controller.php - Lớp điều hướng chuẩn OOP MVC

class MainController {
    private $db;
    private $favoriteModel;
    
    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . "/../model/m_favorite.php";
        $this->favoriteModel = new FavoriteModel($this->db);
    }
    
    // Gọi phương thức này để nhận request và điều hướng
    public function handleRequest() {
        $act = isset($_GET['act']) ? $_GET['act'] : 'home';
        
        switch ($act) {
            case 'sanpham': $this->sanpham(); break;
            case 'chitiet': $this->chitiet(); break;
            case 'timkiem': $this->timkiem(); break;
            case 'yeuthich': $this->yeuthich(); break;
            case 'toggle_favorite': $this->toggleFavorite(); break;
            case 'login': $this->login(); break;
            case 'dangky': $this->dangky(); break;
            case 'giohang': $this->giohang(); break;
            case 'themgiohang': $this->themgiohang(); break;
            case 'capnhatgiohang': $this->capnhatgiohang(); break;
            case 'xoagiohang': $this->xoagiohang(); break;
            case 'apply_voucher': $this->applyVoucher(); break;
            case 'remove_voucher': $this->removeVoucher(); break;
            case 'thanhtoan': $this->thanhtoan(); break;
            case 'camon': $this->camon(); break;
            case 'lichsudonhang': $this->lichsudonhang(); break;
            case 'sansale': $this->sansale(); break;
            case 'chat_api_get': $this->chatApiGet(); break;
            case 'chat_api_send': $this->chatApiSend(); break;
            case 'home':
            default: $this->home(); break;
        }
    }

    // Hàm tiện ích để load View kèm Header và Footer
    private function renderView($viewName, $data = []) {
        // Biến toàn cục cần cho header/footer
        global $db; 
        
        // Khởi tạo các biến dùng chung cho view
        $userFavorites = [];
        $favoriteCount = 0;
        if (isset($_SESSION['user_id'])) {
            $userFavorites = $this->favoriteModel->getUserFavoriteIds($_SESSION['user_id']);
            $favoriteCount = count($userFavorites);
        }
        
        // Lấy danh mục để hiển thị trên thanh điều hướng header
        $stmt_cat = $this->db->query("SELECT * FROM `DanhMuc` ORDER BY `Ma_DanhMuc`");
        $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

        // Xuất mảng data thành các biến độc lập để view dễ dùng (ví dụ: $titleName)
        extract($data);

        include_once __DIR__ . "/../view/header.php";
        
        $viewFile = __DIR__ . "/../view/" . $viewName . ".php";
        if (file_exists($viewFile)) {
            include_once $viewFile;
        }
        
        include_once __DIR__ . "/../view/footer.php";
    }

    // --- CÁC PHƯƠNG THỨC XỬ LÝ NGHIỆP VỤ (ACTION METHODS) --- //

    public function home() {
        // Lấy sản phẩm nổi bật cho trang chủ
        $stmt_feat = $this->db->query("SELECT * FROM `SanPham` ORDER BY `Ma_SanPham` DESC LIMIT 8");
        $featuredProducts = $stmt_feat->fetchAll(PDO::FETCH_ASSOC);

        $this->renderView('trangchu', ['featuredProducts' => $featuredProducts]);
    }

    public function sanpham() {
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($this->db);

        $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
        $limit = 6; 
        $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($currentPage < 1) $currentPage = 1;

        if ($catId > 0) {
            $productsList = $productModel->getProductsByCategory($catId, $sort, $currentPage, $limit);
            $totalProducts = $productModel->countProductsByCategory($catId);
            $titleName = $productModel->getCategoryName($catId);
        } else {
            $productsList = $productModel->getAllProducts($sort, $currentPage, $limit);
            $totalProducts = $productModel->countAllProducts();
            $titleName = "Tất Cả Sản Phẩm";
        }
        $totalPages = ceil($totalProducts / $limit);

        $this->renderView('sanpham', [
            'productsList' => $productsList,
            'titleName' => $titleName,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'catId' => $catId,
            'sort' => $sort
        ]);
    }

    public function chitiet() {
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($this->db);

        $prodId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
            if (isset($_SESSION['user_id'])) { 
                $userId = $_SESSION['user_id'];
                $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
                $comment = trim($_POST['comment'] ?? '');

                if (!empty($comment)) {
                    $productModel->addReview($prodId, $userId, $rating, $comment);
                    header("Location: index.php?act=chitiet&id=" . $prodId);
                    exit();
                }
            }
        }

        $product = $productModel->getProductById($prodId);
        $reviews = $productModel->getProductReviews($prodId);

        if (!$product) {
            header("Location: index.php");
            exit();
        }

        $relatedProducts = [];
        if (!empty($product['Ma_DanhMuc'])) {
            $allRelated = $productModel->getProductsByCategory($product['Ma_DanhMuc']);
            foreach ($allRelated as $item) {
                if ($item['Ma_SanPham'] != $prodId) {
                    $relatedProducts[] = $item;
                }
                if (count($relatedProducts) >= 4) {
                    break;
                }
            }
        }

        $this->renderView('chitietsanpham', [
            'product' => $product,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts
        ]);
    }

    public function timkiem() {
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($this->db);

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $productsList = $productModel->searchProducts($keyword);
        $titleName = "Kết quả tìm kiếm: " . htmlspecialchars($keyword);

        $this->renderView('sanpham', [
            'productsList' => $productsList,
            'titleName' => $titleName
        ]);
    }

    public function yeuthich() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
            exit();
        }
        $productsList = $this->favoriteModel->getFavoriteProducts($_SESSION['user_id']);
        $titleName = "Sản phẩm yêu thích";

        $this->renderView('sanpham', [
            'productsList' => $productsList,
            'titleName' => $titleName
        ]);
    }

    public function toggleFavorite() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.', 'require_login' => true]);
            exit();
        }
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if ($productId > 0) {
            $isAdded = $this->favoriteModel->toggleFavorite($_SESSION['user_id'], $productId);
            $count = $this->favoriteModel->getFavoriteCount($_SESSION['user_id']);
            echo json_encode(['success' => true, 'is_added' => $isAdded, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']);
        }
        exit();
    }

    public function login() {
        include_once __DIR__ . "/../view/taikhoan.php";
    }

    public function dangky() {
        include_once __DIR__ . "/../view/dangky.php";
    }

    public function giohang() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
            exit();
        }
        require_once __DIR__ . "/../model/m_giohang.php";
        $cartModel = new CartModel($this->db);
        
        $cartSession = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cartDetails = $cartModel->getCartDetails($cartSession);
        $totalAmount = $cartModel->calculateTotal($cartDetails);

        $this->renderView('giohang', [
            'cartDetails' => $cartDetails,
            'totalAmount' => $totalAmount
        ]);
    }

    public function themgiohang() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
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
        header("Location: index.php?act=giohang");
        exit();
    }

    public function capnhatgiohang() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
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
        header("Location: index.php?act=giohang");
        exit();
    }

    public function xoagiohang() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
            exit();
        }
        $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        header("Location: index.php?act=giohang");
        exit();
    }

    public function applyVoucher() {
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
        $voucherModel = new VoucherModel($this->db);
        $v = $voucherModel->getVoucherByCode($code);
        
        if (!$v) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại.']);
            exit();
        }
        if ($v['SoLuong'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
            exit();
        }
        if (!empty($v['NgayHetHan']) && strtotime($v['NgayHetHan']) < strtotime(date('Y-m-d'))) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết hạn.']);
            exit();
        }
        
        $_SESSION['applied_voucher'] = [
            'id' => $v['Ma_MaGiamGia'],
            'code' => $v['Ma'],
            'GiaTriGiam' => $v['GiaTriGiam']
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công.',
            'code' => $v['Ma'],
            'discount' => $v['GiaTriGiam']
        ]);
        exit();
    }

    public function removeVoucher() {
        header('Content-Type: application/json');
        if (isset($_SESSION['applied_voucher'])) {
            unset($_SESSION['applied_voucher']);
        }
        echo json_encode(['success' => true]);
        exit();
    }

    public function thanhtoan() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
            exit();
        }
        require_once __DIR__ . "/../model/m_giohang.php";
        require_once __DIR__ . "/../model/m_donhang.php";
        require_once __DIR__ . "/../model/m_user.php"; 
        
        $cartModel = new CartModel($this->db);
        $orderModel = new OrderModel($this->db);
        $userModel = new UserModel($this->db); 
        
        $cartSession = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cartSession)) {
            header("Location: index.php?act=giohang");
            exit();
        }

        $cartDetails = $cartModel->getCartDetails($cartSession);
        $totalAmount = $cartModel->calculateTotal($cartDetails); 
        $paymentMethods = $orderModel->getPaymentMethods();

        $subTotal = $totalAmount; 
        $discountPercent = 0;
        $discountAmount = 0;
        $rankName = "Thành viên mới";

        if (isset($_SESSION['user_id'])) {
            $userRank = $userModel->getCustomerRank($_SESSION['user_id']);
            $rankName = $userRank['name'];
            $discountPercent = $userRank['discount']; 
            $discountAmount = $subTotal * ($discountPercent / 100);
            $totalAmount = $subTotal - $discountAmount;
        }

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
                    $discountValue = $_SESSION['applied_voucher']['GiaTriGiam'];
                }

                $finalInvoiceAmount = $totalAmount - $discountValue;
                if ($finalInvoiceAmount < 0) $finalInvoiceAmount = 0;

                $orderId = $orderModel->createOrder($_SESSION['user_id'], $cartDetails, $address, $city, $paymentMethodId, $voucherId);
                
                if ($orderId) {
                    require_once __DIR__ . "/../model/m_mail.php";
                    $mailService = new MailService();
                    $customerEmail = $_SESSION['email'] ?? '';
                    $customerName = $_SESSION['username'] ?? 'Khách hàng';
                    $fullAddress = $address . ', ' . $city;
                    
                    if (!empty($customerEmail)) {
                        $mailService->sendInvoiceEmail($customerEmail, $customerName, $orderId, $cartDetails, $finalInvoiceAmount, $fullAddress, $voucherCode, $discountValue);
                    }

                    unset($_SESSION['cart']);
                    unset($_SESSION['applied_voucher']);
                    header("Location: index.php?act=camon&id=" . $orderId);
                    exit();
                } else {
                    $error = 'Có lỗi xảy ra hoặc sản phẩm/voucher đã hết. Vui lòng thử lại sau.';
                }
            }
        }

        $this->renderView('thanhtoan', [
            'cartDetails' => $cartDetails,
            'totalAmount' => $totalAmount,
            'subTotal' => $subTotal,
            'discountPercent' => $discountPercent,
            'discountAmount' => $discountAmount,
            'userRank' => isset($userRank) ? $userRank : null,
            'rankName' => $rankName,
            'paymentMethods' => $paymentMethods,
            'error' => $error
        ]);
    }

    public function camon() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
            exit();
        }
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $this->renderView('camon', ['orderId' => $orderId]);
    }

    public function lichsudonhang() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?act=login");
            exit();
        }
        require_once __DIR__ . "/../model/m_donhang.php";
        $orderModel = new OrderModel($this->db);
        
        $userId = $_SESSION['user_id'];
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($orderId > 0) {
            $orderDetails = $orderModel->getOrderDetails($orderId, $userId);
            if ($orderDetails === false) {
                header("Location: index.php?act=lichsudonhang");
                exit();
            }
            $this->renderView('lichsudonhang', ['viewMode' => 'detail', 'orderDetails' => $orderDetails]);
        } else {
            $ordersList = $orderModel->getOrdersByUserId($userId);
            $this->renderView('lichsudonhang', ['viewMode' => 'list', 'ordersList' => $ordersList]);
        }
    }

    public function sansale() {
        require_once __DIR__ . "/../model/m_sanpham.php";
        $productModel = new ProductModel($this->db);
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'discount';
        $minDiscount = isset($_GET['discount']) ? intval($_GET['discount']) : 0;
        $saleProducts = $productModel->getSaleProducts($sort, $minDiscount);

        $this->renderView('sansale', ['saleProducts' => $saleProducts]);
    }

    public function chatApiGet() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            exit;
        }
        require_once __DIR__ . "/../model/m_chat.php";
        $chatModel = new ChatModel($this->db);
        $messages = $chatModel->getMessages($_SESSION['user_id']);
        $chatModel->markAsRead($_SESSION['user_id'], false);
        echo json_encode(['status' => 'success', 'data' => $messages]);
        exit;
    }

    public function chatApiSend() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            exit;
        }
        require_once __DIR__ . "/../model/m_chat.php";
        $chatModel = new ChatModel($this->db);
        $content = isset($_POST['message']) ? trim($_POST['message']) : '';
        if ($content !== '') {
            if ($chatModel->sendMessage($_SESSION['user_id'], $content, 0)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'DB error']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Empty message']);
        }
        exit;
    }
}
?>
