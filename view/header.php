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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="<?= BASE_URL ?>view/css/theme.css" rel="stylesheet" />
    <style>
    /* Sửa lỗi tràn màn hình trên mobile */
    html,
    body {
        width: 100%;
        overflow-x: hidden;
    }

    /* 1. ĐỊNH DẠNG TRÊN PC */
    .category-nav {
        background-color: #ffffff !important;
        border-bottom: 1px solid #eee;
    }

    .cat-all-btn {
        background-color: #e63946 !important;
        color: #ffffff !important;
        border: none;
        padding: 10px 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cat-link {
        color: #1a1a2e !important;
        font-weight: 500;
        font-size: 14px;
        padding: 15px 12px !important;
        text-transform: uppercase;
    }

    .cat-link:hover {
        color: #e63946 !important;
    }

    .cat-link--hot {
        color: #e63946 !important;
        font-weight: 600;
    }

    /* 2. ĐỊNH DẠNG DI ĐỘNG (DƯỚI 768px) */
    @media (max-width: 767.98px) {
        .header-inner {
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 12px 0;
            gap: 10px;
        }

        .mobile-menu-btn {
            background: none;
            border: none;
            color: #fff;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .site-logo {
            flex-grow: 1;
            justify-content: center;
        }

        .header-search {
            width: 100% !important;
            margin-top: 5px;
        }

        .header-actions {
            width: auto;
            gap: 10px !important;
        }

        .hdr-cart-text,
        .hdr-action-label,
        .hdr-badge {
            display: none !important;
        }

        .hdr-cart-btn {
            padding: 0.4rem 0.6rem;
            margin-left: 0;
        }

        .hdr-action-icon .material-symbols-outlined {
            font-size: 22px;
        }

        /* Offcanvas Menu Mobile */
        .offcanvas-custom {
            background-color: #1a1a2e !important;
            color: #fff;
        }

        .offcanvas-custom .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cat-links-mobile .cat-link {
            color: #ffffff !important;
            padding: 15px 20px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            text-align: left;
            font-size: 14px;
            display: block;
        }

        .cat-contact-mobile {
            padding: 15px 20px;
            color: #ffffff !important;
            font-size: 14px;
            background-color: rgba(255, 255, 255, 0.03);
            margin-top: auto;
        }
    }
    </style>
</head>

<body>

    <header class="site-header sticky-top">
        <div class="container-max-custom px-desktop-custom">
            <div class="header-inner">

                <button class="mobile-menu-btn d-md-none order-1" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileMenuOffcanvas" aria-controls="mobileMenuOffcanvas">
                    <span class="material-symbols-outlined" style="font-size: 28px;">menu</span>
                </button>

                <a class="site-logo order-2 mx-auto mx-md-0 text-decoration-none" href="<?= BASE_URL ?>index.php">
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

                <div class="header-search order-4 order-md-2">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="search-wrap mb-0">
                        <input type="hidden" name="act" value="timkiem">
                        <span class="search-icon material-symbols-outlined">search</span>
                        <input type="text" name="keyword" class="search-input"
                            placeholder="Nhập tên sản phẩm cần tìm..."
                            value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
                            required />
                        <button type="submit" class="search-btn">Tìm kiếm</button>
                    </form>
                </div>

                <div class="header-actions order-3 d-flex align-items-center gap-2 gap-md-3">
                    <a href="<?= BASE_URL ?>index.php?act=yeuthich" class="hdr-action-btn text-decoration-none"
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
                                <span class="material-symbols-outlined">person</span>
                            </span>
                            <span class="hdr-action-label text-truncate" style="max-width: 80px;">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <div class="px-3 py-2">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="material-symbols-outlined text-muted"
                                            style="font-size:18px;">person</span>
                                        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                                    </div>
                                    <?php if (isset($rankInfo)): ?>
                                    <span class="<?= htmlspecialchars($rankInfo['class']) ?>">
                                        <?= htmlspecialchars($rankInfo['name']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider m-0">
                            </li>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/index.php"><span
                                        class="material-symbols-outlined align-middle fs-5 me-2">admin_panel_settings</span>Trang
                                    Quản Trị</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?act=lichsudonhang"><span
                                        class="material-symbols-outlined align-middle fs-5 me-2">receipt_long</span>Đơn
                                    hàng của tôi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger"
                                    href="<?= BASE_URL ?>controller/user.php?act=logout"><span
                                        class="material-symbols-outlined align-middle fs-5 me-2">logout</span>Đăng
                                    xuất</a></li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <a href="<?= BASE_URL ?>index.php?act=login" class="hdr-action-btn text-decoration-none"
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
                    <a href="<?= BASE_URL ?>index.php?act=giohang" class="hdr-cart-btn text-decoration-none"
                        title="Giỏ hàng">
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

    <nav class="category-nav p-0">
        <div class="container-max-custom px-desktop-custom">

            <div class="d-none d-md-flex align-items-center w-100 category-nav-inner">
                <button class="cat-all-btn">
                    <span class="material-symbols-outlined">menu</span>
                    Danh mục
                </button>
                <div class="cat-links">
                    <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <a class="cat-link text-decoration-none"
                        href="<?= BASE_URL ?>index.php?act=sanpham&id=<?php echo $cat['Ma_DanhMuc']; ?>">
                        <?php echo htmlspecialchars($cat['TenDanhMuc']); ?>
                    </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <a class="cat-link text-decoration-none" href="#">Acoustic Guitars</a>
                    <a class="cat-link text-decoration-none" href="#">Electric Guitars</a>
                    <a class="cat-link text-decoration-none" href="#">Classic Guitars</a>
                    <a class="cat-link text-decoration-none" href="#">Bass Guitars</a>
                    <a class="cat-link text-decoration-none" href="#">Ukulele</a>
                    <a class="cat-link text-decoration-none" href="#">Phụ kiện</a>
                    <?php endif; ?>

                    <a class="cat-link cat-link--hot text-decoration-none"
                        href="<?= BASE_URL ?>index.php?act=sansale">Săn Sale chớp nhoáng 🔥</a>
                </div>
                <div class="cat-contact ms-auto text-dark d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size:16px;color:#e63946;">call</span>
                    <span>Hotline: <strong>1800 6868</strong></span>
                </div>
            </div>

            <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="mobileMenuOffcanvas"
                aria-labelledby="mobileMenuOffcanvasLabel">
                <div class="offcanvas-header py-3">
                    <div class="logo-name">
                        <span class="logo-main" style="font-size: 1.2rem;">Guitar</span><span class="logo-accent"
                            style="font-size: 1.2rem;">X</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-body p-0">
                    <div class="cat-links-mobile">
                        <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                        <a class="cat-link text-decoration-none d-block"
                            href="<?= BASE_URL ?>index.php?act=sanpham&id=<?php echo $cat['Ma_DanhMuc']; ?>">
                            <?php echo htmlspecialchars($cat['TenDanhMuc']); ?>
                        </a>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <a class="cat-link text-decoration-none d-block" href="#">ACOUSTIC GUITARS</a>
                        <a class="cat-link text-decoration-none d-block" href="#">ELECTRIC GUITARS</a>
                        <a class="cat-link text-decoration-none d-block" href="#">CLASSIC GUITARS</a>
                        <a class="cat-link text-decoration-none d-block" href="#">BASS GUITARS</a>
                        <a class="cat-link text-decoration-none d-block" href="#">UKULELE</a>
                        <a class="cat-link text-decoration-none d-block" href="#">PHỤ KIỆN</a>
                        <?php endif; ?>

                        <a class="cat-link text-decoration-none d-block fw-bold text-warning mt-2"
                            href="<?= BASE_URL ?>index.php?act=sansale">SĂN SALE CHỚP NHOÁNG 🔥</a>
                    </div>
                </div>

                <div class="cat-contact-mobile d-flex align-items-center gap-3 mt-auto p-3 flex-shrink-0"
                    style="border-top: 1px solid rgba(255,255,255,0.05); background-color: rgba(0,0,0,0.2);">
                    <span class="material-symbols-outlined" style="font-size:32px;color:#e63946;">support_agent</span>
                    <div class="d-flex flex-column">
                        <span style="font-size: 12px; opacity: 0.7; font-weight: 500;">Hotline hỗ trợ (24/7):</span>
                        <strong style="font-size: 18px; color: #fff;">1800 6868</strong>
                    </div>
                </div>
            </div>

        </div>
    </nav>

    <script>
    function toggleFavorite(productId, btnElement, event) {
        if (event) event.preventDefault();
        fetch('<?= BASE_URL ?>index.php?act=toggle_favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'product_id=' + productId
            })
            .then(res => res.json())
            .then(data => {
                if (data.require_login) {
                    window.location.href = '<?= BASE_URL ?>index.php?act=login';
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

    <?php if (isset($_SESSION['login_success'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Đăng nhập thành công!',
            text: '<?php echo $_SESSION['login_success']; ?>',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true,
            timerProgressBar: true
        });
    });
    </script>
    <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>