<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
                    <div class="search-wrap">
                        <span class="search-icon material-symbols-outlined">search</span>
                        <input type="text" class="search-input" placeholder="Nhập tên sản phẩm cần tìm..." />
                        <button class="search-btn">Tìm kiếm</button>
                    </div>
                </div>

                <div class="header-actions">
                    <button class="hdr-action-btn" title="Yêu thích">
                        <span class="hdr-action-icon">
                            <span class="material-symbols-outlined">favorite</span>
                            <span class="hdr-badge">0</span>
                        </span>
                        <span class="hdr-action-label">Yêu thích</span>
                    </button>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="hdr-action-btn border-0 bg-transparent p-0 d-flex flex-column align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
                                <span class="hdr-action-icon">
                                    <span class="material-symbols-outlined text-primary-custom">person</span>
                                </span>
                                <span class="hdr-action-label text-truncate" style="max-width: 80px;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><h6 class="dropdown-header">Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></h6></li>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="/GuitarX/admin/index.php"><span class="material-symbols-outlined align-middle fs-5 me-2">admin_panel_settings</span>Trang Quản Trị</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="#"><span class="material-symbols-outlined align-middle fs-5 me-2">receipt_long</span>Đơn hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/GuitarX/controller/user.php?act=logout"><span class="material-symbols-outlined align-middle fs-5 me-2">logout</span>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/GuitarX/index.php?act=login" class="hdr-action-btn text-decoration-none" title="Đăng nhập">
                            <span class="hdr-action-icon">
                                <span class="material-symbols-outlined">person</span>
                            </span>
                            <span class="hdr-action-label">Đăng nhập</span>
                        </a>
                    <?php endif; ?>

                    <button class="hdr-cart-btn" title="Giỏ hàng">
                        <span class="hdr-action-icon">
                            <span class="material-symbols-outlined">shopping_cart</span>
                            <span class="hdr-badge hdr-badge--red">0</span>
                        </span>
                        <div class="hdr-cart-text">
                            <span class="hdr-cart-label">Giỏ hàng</span>
                            <span class="hdr-cart-sub">0 sản phẩm</span>
                        </div>
                    </button>
                </div>

            </div></div>
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