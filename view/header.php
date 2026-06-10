<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    // Nếu chưa khởi tạo đối tượng $userModel từ controller/index đổ ra, ta sẽ khởi tạo trực tiếp tại đây
    if (!isset($userModel)) {
        require_once "model/m_user.php";
        // Khởi tạo model và truyền biến kết nối database của hệ thống vào (thường tên là $db hoặc $conn)
        $userModel = new UserModel($db); 
    }
    // Gọi hàm tính rank đã viết sẵn trong m_user.php
    $rankInfo = $userModel->getCustomerRank($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>GuitarX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="/GuitarX/view/css/theme.css" rel="stylesheet" />
</head>

<body>

    <header class="site-header sticky-top">
        <div class="container-max-custom px-desktop-custom">
            <div class="header-inner">

                <a class="site-logo" href="/GuitarX/index.php">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44" height="44" fill="none">
                            <circle cx="22" cy="22" r="22" fill="#e63946" />
                            <ellipse cx="22" cy="29" rx="8" ry="9" fill="white" opacity="0.95" />
                            <ellipse cx="22" cy="27" rx="5.5" ry="6" fill="#e63946" opacity="0.25" />
                            <rect x="20.2" y="10" width="3.5" height="16" rx="1.75" fill="white" opacity="0.95" />
                            <rect x="18.5" y="8" width="7" height="4.5" rx="1.2" fill="white" opacity="0.9" />
                            <circle cx="22" cy="29" r="2.8" fill="#1a1a2e" opacity="0.45" />
                            <line x1="22" y1="10" x2="22" y2="37" stroke="#c9222f" stroke-width="0.7" opacity="0.5" />
                        </svg>
                    </div>
                    <div class="logo-text">
                        <div class="logo-name">
                            <span class="logo-main">Guitar</span><span class="logo-accent">X</span>
                        </div>
                        <span class="logo-tagline">Hân hạnh đồng hành</span>
                    </div>
                </a>

                <div class="header-search">
                    <form action="/GuitarX/index.php" method="GET" class="search-wrap mb-0">
                        <input type="hidden" name="act" value="timkiem">
                        <span class="search-icon material-symbols-outlined">search</span>
                        <input type="text" name="keyword" class="search-input"
                            placeholder="Nhập tên sản phẩm cần tìm..."
                            value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
                            required />
                        <button type="submit" class="search-btn">Tìm kiếm</button>
                    </form>
                </div>

                <div class="header-actions">
                    <a href="/GuitarX/index.php?act=yeuthich" class="hdr-action-btn text-decoration-none"
                        title="Yêu thích">
                        <span class="hdr-action-icon">
                            <span class="material-symbols-outlined">favorite</span>
                            <span class="hdr-badge"
                                id="hdrFavoriteCount"><?php echo isset($favoriteCount) ? $favoriteCount : 0; ?></span>
                        </span>
                        <span class="hdr-action-label">Yêu thích</span>
                    </a>

                    <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="hdr-action-btn border-0 bg-transparent p-0 d-flex flex-column align-items-center"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
                            <span class="hdr-action-icon">
                                <span class="material-symbols-outlined text-primary-custom">person</span>
                            </span>

                            <div class="d-flex align-items-center gap-1 justify-content-center">
                                <span class="hdr-action-label text-truncate"
                                    style="max-width: 80px;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>

                                <?php if (isset($rankInfo)): ?>
                                <span class="badge <?= $rankInfo['class'] ?>"
                                    style="font-size: 9px; padding: 2px 4px; line-height: 1; font-weight: bold;">
                                    <?= $rankInfo['name'] ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <h6 class="dropdown-header">Xin chào,
                                    <?php echo htmlspecialchars($_SESSION['username']); ?></h6>
                            </li>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                            <li><a class="dropdown-item" href="/GuitarX/admin/index.php"><span
                                        class="material-symbols-outlined align-middle fs-5 me-2">admin_panel_settings</span>Trang
                                    Quản Trị</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/GuitarX/index.php?act=lichsudonhang"><span
                                        class="material-symbols-outlined align-middle fs-5 me-2">receipt_long</span>Đơn
                                    hàng của tôi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger"
                                    href="/GuitarX/controller/user.php?act=logout"><span
                                        class="material-symbols-outlined align-middle fs-5 me-2">logout</span>Đăng
                                    xuất</a></li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <a href="/GuitarX/index.php?act=login" class="hdr-action-btn text-decoration-none"
                        title="Đăng nhập">
                        <span class="hdr-action-icon">
                            <span class="material-symbols-outlined">person</span>
                        </span>
                        <span class="hdr-action-label">Đăng nhập</span>
                    </a>
                    <?php endif; ?>

                    <?php
                        $cartItemCount = 0;
                        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $qty) {
                                $cartItemCount += $qty;
                            }
                        }
                    ?>
                    <a href="/GuitarX/index.php?act=giohang" class="hdr-cart-btn text-decoration-none" title="Giỏ hàng">
                        <span class="hdr-action-icon">
                            <span class="material-symbols-outlined">shopping_cart</span>
                            <span class="hdr-badge hdr-badge--red"><?php echo $cartItemCount; ?></span>
                        </span>
                        <div class="hdr-cart-text">
                            <span class="hdr-cart-label">Giỏ hàng</span>
                            <span class="hdr-cart-sub"><?php echo $cartItemCount; ?> sản phẩm</span>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <nav class="category-nav d-none d-md-block">
        <div class="container-max-custom px-desktop-custom">
            <div class="category-nav-inner">
                <button class="cat-all-btn">
                    <span class="material-symbols-outlined">menu</span>
                    Danh mục
                </button>
                <div class="cat-links">
                    <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <a class="cat-link" href="/GuitarX/index.php?act=sanpham&id=<?php echo $cat['Category_ID']; ?>">
                        <?php echo htmlspecialchars($cat['CategoryName']); ?>
                    </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <a class="cat-link" href="#">Acoustic Guitars</a>
                    <a class="cat-link" href="#">Electric Guitars</a>
                    <a class="cat-link" href="#">Classic Guitars</a>
                    <a class="cat-link" href="#">Bass Guitars</a>
                    <a class="cat-link" href="#">Ukulele</a>
                    <a class="cat-link" href="#">Phụ kiện</a>
                    <?php endif; ?>

                    <a class="cat-link cat-link--hot" href="#">Săn Sale chớp nhoáng 🔥</a>
                </div>
                <div class="cat-contact ms-auto">
                    <span class="material-symbols-outlined" style="font-size:16px;color:#e63946;">call</span>
                    <span>Hotline: <strong>1800 6868</strong></span>
                </div>
            </div>
        </div>
    </nav>

    <script>
    function toggleFavorite(productId, btnElement, event) {
        if (event) event.preventDefault();
        fetch('/GuitarX/index.php?act=toggle_favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'product_id=' + productId
            })
            .then(res => res.json())
            .then(data => {
                if (data.require_login) {
                    window.location.href = '/GuitarX/index.php?act=login';
                    return;
                }
                if (data.success) {
                    document.getElementById('hdrFavoriteCount').innerText = data.count;
                    const icon = btnElement.querySelector('.material-symbols-outlined');
                    if (data.is_added) {
                        btnElement.classList.add('active');
                        icon.style.fontVariationSettings = "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24";
                        icon.classList.add('text-danger');
                    } else {
                        btnElement.classList.remove('active');
                        icon.style.fontVariationSettings = "'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24";
                        icon.classList.remove('text-danger');
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error(err));
    }
    </script>