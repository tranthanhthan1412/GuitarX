<main class="w-100">
    <section class="hero-carousel position-relative w-100 overflow-hidden bg-primary-custom" style="height: 600px;">

        <div class="hero-track" id="heroTrack">

            <div class="hero-slide active">
                <img alt="Hero Guitar" class="hero-slide-img" src="<?= BASE_URL ?>view/image/guitar.jpg" />
                <div class="position-absolute inset-0 hero-gradient-overlay d-flex align-items-center w-100 h-100">
                    <div class="container-max-custom px-desktop-custom text-white w-100">
                        <div class="hero-content" style="max-width: 576px;">
                            <span
                                class="badge bg-secondary-custom text-white px-3 py-2 font-label-sm rounded-1 mb-3">SẢN
                                PHẨM MỚI</span>
                            <h1 class="font-display-lg mb-3 fw-bold">Huyền thoại Guitar WashBurn S9V</h1>
                            <p class="font-body-lg mb-4 opacity-90">Sở hữu chất âm huyền thoại kiến tạo nên những nền
                                nhạc du dương. Tuyệt tác chế tác chuẩn xác cho người sưu tầm chuyên nghiệp</p>
                            <div class="d-flex gap-3">
                                <button class="btn btn-secondary-custom px-4 py-3 font-headline-sm rounded-1 shadow">Tìm
                                    hiểu thêm</button>
                                <button class="btn btn-outline-light px-4 py-3 font-headline-sm rounded-1">Xem
                                    demo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-slide">
                <img alt="Banner Guitar 1" class="hero-slide-img" src="<?= BASE_URL ?>view/image/bannerguitar1.jpg" />
                <div class="position-absolute inset-0 hero-gradient-overlay d-flex align-items-center w-100 h-100">
                    <div class="container-max-custom px-desktop-custom text-white w-100">
                        <div class="hero-content" style="max-width: 576px;">
                            <span class="badge bg-secondary-custom text-white px-3 py-2 font-label-sm rounded-1 mb-3">BỘ
                                SƯU TẬP MỚI</span>
                            <h1 class="font-display-lg mb-3 fw-bold">Đỉnh Cao Âm Nhạc — Guitar Acoustic</h1>
                            <p class="font-body-lg mb-4 opacity-90">Trải nghiệm âm thanh ấm áp và sâu lắng với bộ sưu
                                tập guitar acoustic đẳng cấp thế giới. Dành cho những tâm hồn yêu nhạc.</p>
                            <div class="d-flex gap-3">
                                <button
                                    class="btn btn-secondary-custom px-4 py-3 font-headline-sm rounded-1 shadow">Khám
                                    phá ngay</button>
                                <button class="btn btn-outline-light px-4 py-3 font-headline-sm rounded-1">Nghe
                                    thử</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-slide">
                <img alt="Banner Guitar 2" class="hero-slide-img" src="<?= BASE_URL ?>view/image/bannerguitar2.jpg" />
                <div class="position-absolute inset-0 hero-gradient-overlay d-flex align-items-center w-100 h-100">
                    <div class="container-max-custom px-desktop-custom text-white w-100">
                        <div class="hero-content" style="max-width: 576px;">
                            <span class="badge bg-secondary-custom text-white px-3 py-2 font-label-sm rounded-1 mb-3">ƯU
                                ĐÃI ĐẶC BIỆT</span>
                            <h1 class="font-display-lg mb-3 fw-bold">Guitar Electric — Tốc Độ &amp; Phong Cách</h1>
                            <p class="font-body-lg mb-4 opacity-90">Làm chủ sân khấu với dòng guitar điện hiện đại. Chất
                                âm mạnh mẽ, thiết kế sang trọng — dành cho những nghệ sĩ đích thực.</p>
                            <div class="d-flex gap-3">
                                <button class="btn btn-secondary-custom px-4 py-3 font-headline-sm rounded-1 shadow">Mua
                                    ngay</button>
                                <button class="btn btn-outline-light px-4 py-3 font-headline-sm rounded-1">Xem
                                    thêm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><button class="hero-arrow hero-arrow--prev" id="heroPrev" aria-label="Slide trước">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="hero-arrow hero-arrow--next" id="heroNext" aria-label="Slide tiếp">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>

        <div class="hero-dots" id="heroDots">
            <button class="hero-dot active" data-slide="0" aria-label="Slide 1"></button>
            <button class="hero-dot" data-slide="1" aria-label="Slide 2"></button>
            <button class="hero-dot" data-slide="2" aria-label="Slide 3"></button>
        </div>

    </section>

    <style>
    .hero-carousel {
        position: relative;
    }
    .hero-track {
        display: flex;
        width: 100%;
        height: 100%;
        position: absolute;
        inset: 0;
        transition: transform 0.65s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .hero-slide {
        min-width: 100%;
        height: 600px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }
    .hero-slide-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 8s ease;
    }
    .hero-slide.active .hero-slide-img {
        transform: scale(1.06);
    }
    .hero-content {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity 0.55s ease 0.35s, transform 0.55s ease 0.35s;
    }
    .hero-slide.active .hero-content {
        opacity: 1;
        transform: translateY(0);
    }
    .hero-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        background: rgba(255, 255, 255, 0.12);
        border: 1.5px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(8px);
        transition: background 0.25s ease, transform 0.2s ease, border-color 0.25s ease;
    }
    .hero-arrow:hover {
        background: rgba(230, 57, 70, 0.55);
        border-color: var(--color-secondary);
        transform: translateY(-50%) scale(1.1);
    }
    .hero-arrow .material-symbols-outlined {
        font-size: 30px;
    }
    .hero-arrow--prev {
        left: 1.5rem;
    }
    .hero-arrow--next {
        right: 1.5rem;
    }
    .hero-dots {
        position: absolute;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 20;
    }
    .hero-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.6);
        background: transparent;
        cursor: pointer;
        padding: 0;
        transition: background 0.3s ease, border-color 0.3s ease, transform 0.3s ease, width 0.3s ease;
    }
    .hero-dot.active {
        background: #fff;
        border-color: #fff;
        width: 28px;
        border-radius: 5px;
    }
    </style>

    <script>
    (function() {
        const track = document.getElementById('heroTrack');
        const slides = track.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        const prevBtn = document.getElementById('heroPrev');
        const nextBtn = document.getElementById('heroNext');
        let current = 0;
        let timer;

        function goTo(index) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
            track.style.transform = `translateX(-${current * 100}%)`;
        }

        function next() {
            goTo(current + 1);
        }

        function prev() {
            goTo(current - 1);
        }

        function startAuto() {
            timer = setInterval(next, 5000);
        }

        function resetAuto() {
            clearInterval(timer);
            startAuto();
        }

        nextBtn.addEventListener('click', function() {
            next();
            resetAuto();
        });
        prevBtn.addEventListener('click', function() {
            prev();
            resetAuto();
        });
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                goTo(parseInt(dot.dataset.slide));
                resetAuto();
            });
        });

        track.addEventListener('mouseenter', function() {
            clearInterval(timer);
        });
        track.addEventListener('mouseleave', startAuto);

        startAuto();
    })();
    </script>

    <section class="w-100 flash-sale-pattern py-4 text-white">
        <div
            class="container-max-custom px-desktop-custom d-flex flex-column flex-md-row align-items-center justify-content-between gap-4 position-relative" style="z-index: 10;">
            <div class="d-flex align-items-center gap-3">
                <span class="material-symbols-outlined display-5">bolt</span>
                <div>
                    <h2 class="font-headline-md mb-0">ĐÊM HỘI SĂN SALE</h2>
                    <p class="mb-0 text-white-50 font-label-md text-uppercase tracking-wider">Ưu Đãi Đến 40% — Cho Các
                        Dòng Guitar Tuyển Chọn</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-4 flex-wrap justify-content-center">
                <div class="d-flex gap-2">
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded px-3 py-2 text-center"
                        style="backdrop-filter: blur(6px);">
                        <span class="d-block font-headline-md text-white lh-1" id="hours">02</span>
                        <span class="text-white-50 uppercase fw-bold" style="font-size: 10px;">GIỜ</span>
                    </div>
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded px-3 py-2 text-center"
                        style="backdrop-filter: blur(6px);">
                        <span class="d-block font-headline-md text-white lh-1" id="minutes">45</span>
                        <span class="text-white-50 uppercase fw-bold" style="font-size: 10px;">PHÚT</span>
                    </div>
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded px-3 py-2 text-center"
                        style="backdrop-filter: blur(6px);">
                        <span class="d-block font-headline-md text-white lh-1" id="seconds">12</span>
                        <span class="text-white-50 uppercase fw-bold" style="font-size: 10px;">GIÂY</span>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>index.php?act=sansale" class="btn btn-light text-danger-custom fw-bold px-4 py-2 shadow-sm text-decoration-none">SĂN SALE NGAY</a>
            </div>
        </div>
    </section>

    <section class="container-max-custom px-desktop-custom py-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end mb-4 gap-3">
            <div>
                <div class="bg-secondary-custom mb-2" style="width: 48px; height: 4px;"></div>
                <h2 class="font-headline-md text-dark text-uppercase mb-1">SẢN PHẨM NỔI BẬT</h2>
                <p class="text-muted font-body-md mb-0">Lựa chọn hoàn hảo cho mọi phong cách riêng</p>
            </div>
            <a class="text-primary-custom fw-bold text-decoration-none d-flex align-items-center gap-1 link-hover-effect"
                href="<?= BASE_URL ?>index.php?act=sanpham">
                Xem tất cả nhạc cụ <span class="material-symbols-outlined transition-transform">arrow_forward</span>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $prod): ?>
                    <div class="col">
                        <div class="product-card h-100 position-relative d-flex flex-column justify-content-between">
                            <?php if (isset($prod['PhanTramGiamGia']) && $prod['PhanTramGiamGia'] > 0): ?>
                                <span class="sale-badge">-<?php echo $prod['PhanTramGiamGia']; ?>%</span>
                            <?php endif; ?>
                            
                            <?php 
                            $isFav = isset($userFavorites) && in_array($prod['Ma_SanPham'], $userFavorites); 
                            $fillValue = $isFav ? 1 : 0;
                            $colorClass = $isFav ? 'text-danger' : '';
                            ?>
                            <button class="btn btn-light rounded-circle shadow-sm position-absolute p-2 z-2 d-flex align-items-center justify-content-center" style="top: 10px; left: 10px; width: 36px; height: 36px;" onclick="toggleFavorite(<?php echo $prod['Ma_SanPham']; ?>, this, event)" title="Yêu thích">
                                <span class="material-symbols-outlined <?php echo $colorClass; ?>" style="font-variation-settings: 'FILL' <?php echo $fillValue; ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size: 20px;">favorite</span>
                            </button>

                            <div>
                                <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                                    <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $prod['Ma_SanPham']; ?>">
                                        <img alt="<?php echo htmlspecialchars($prod['TenSanPham']); ?>" src="<?= BASE_URL ?>view/image/<?php echo htmlspecialchars($prod['Anh']); ?>" />
                                    </a>
                                </div>
                                <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider"><?php echo htmlspecialchars($prod['ThuongHieu']); ?></p>
                                <h3 class="font-body-md fw-bold text-dark mb-2">
                                    <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $prod['Ma_SanPham']; ?>" class="text-decoration-none text-dark link-hover-red">
                                        <?php echo htmlspecialchars($prod['TenSanPham']); ?>
                                    </a>
                                </h3>
                            </div>
                            <div>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                    <?php if (isset($prod['PhanTramGiamGia']) && $prod['PhanTramGiamGia'] > 0): 
                                        $salePrice = $prod['GiaTien'] - ($prod['GiaTien'] * $prod['PhanTramGiamGia'] / 100);
                                    ?>
                                        <span class="old-price"><?php echo number_format($prod['GiaTien'], 0, ',', '.'); ?>₫</span>
                                        <span class="new-price"><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</span>
                                    <?php else: ?>
                                        <span class="text-secondary-custom font-headline-sm mb-0"><?php echo number_format($prod['GiaTien'], 0, ',', '.'); ?>₫</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $prod['Ma_SanPham']; ?>" class="btn btn-outline-dark flex-grow-1 text-center text-decoration-none d-flex align-items-center justify-content-center fw-bold" style="font-size: 0.9rem;">
                                        CHI TIẾT
                                    </a>
                                    <form action="<?= BASE_URL ?>index.php?act=themgiohang" method="POST" class="m-0">
                                        <input type="hidden" name="product_id" value="<?php echo $prod['Ma_SanPham']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-add-cart-custom d-flex align-items-center justify-content-center" title="Thêm vào giỏ hàng" style="width: 42px; height: 100%;">
                                            <span class="material-symbols-outlined fs-5">shopping_cart</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Không tìm thấy sản phẩm nổi bật nào trong hệ thống.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <section class="bg-surface-container-low py-5">
        <div class="container-max-custom px-desktop-custom">
            <div class="text-center mb-5">
                <h2 class="font-headline-md text-dark text-uppercase">THƯƠNG HIỆU ĐỒNG HÀNH</h2>
                <p class="text-muted font-body-md mb-0">Nhà phân phối chính hãng các thương hiệu guitar hàng đầu thế giới</p>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Yamaha" class="img-fluid max-h-100" src="<?= BASE_URL ?>view/image/Yamaha.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Fender" class="img-fluid max-h-100" src="<?= BASE_URL ?>view/image/Fender.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Gibson" class="img-fluid max-h-100" src="<?= BASE_URL ?>view/image/Gibson.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Taylor" class="img-fluid max-h-100" src="<?= BASE_URL ?>view/image/Taylor.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Washburn" class="img-fluid max-h-100" src="<?= BASE_URL ?>view/image/washburn.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Ibanez" class="img-fluid max-h-100" src="<?= BASE_URL ?>view/image/ibanez.jpg" />
                    </div>
                </div>
            </div>
        </div>
    </section>

<style>
/* Community Section Styling */
.community-section {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    position: relative;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
.community-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: radial-gradient(circle at 30% 50%, rgba(230, 57, 70, 0.15), transparent 60%);
    pointer-events: none;
}
.community-title {
    color: #ffffff;
    text-shadow: 0 2px 10px rgba(230, 57, 70, 0.3);
}
.community-input {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    transition: all 0.3s ease;
}
.community-input:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: var(--color-secondary, #e63946) !important;
    box-shadow: 0 0 15px rgba(230, 57, 70, 0.2) !important;
}
.community-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}
.community-btn {
    border-radius: 8px !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    background-color: var(--color-secondary, #e63946);
    color: white;
    border: none;
}
.community-btn:hover {
    background-color: #c9222f;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4);
}
.community-image-wrapper {
    position: relative;
}
.community-image-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(90deg, #1a1a1a 0%, transparent 40%);
    z-index: 2;
}
</style>

    <section class="container-max-custom px-desktop-custom py-5 mb-5">
        <div class="community-section rounded-4 overflow-hidden row m-0 align-items-stretch" style="min-height: 450px;">
            <div class="col-12 col-lg-6 p-5 p-lg-5 d-flex flex-column justify-content-center position-relative z-1">
                <div class="ps-lg-4">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-white bg-opacity-10 text-white mb-4" style="backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.1); width: max-content;">
                        <span class="material-symbols-outlined fs-5" style="color: var(--color-secondary, #e63946);">star</span>
                        <span class="font-label-sm fw-bold tracking-wider text-uppercase">Tham gia ngay</span>
                    </div>
                    
                    <h2 class="community-title font-display-lg mb-4 fw-bold">Gia nhập cộng đồng<br><span style="color: var(--color-secondary, #e63946);">GuitarX</span></h2>
                    <p class="font-body-lg mb-5 text-white opacity-75 leading-relaxed" style="max-width: 450px;">Đăng ký để tham gia các lớp học chuyên sâu độc quyền, nhận mẹo bảo dưỡng đàn và là người đầu tiên tiếp cận các phiên bản giới hạn.</p>
                    
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-sm-8">
                            <input class="form-control community-input py-3 px-4 shadow-none font-body-md" placeholder="Nhập địa chỉ email của bạn..." type="email" />
                        </div>
                        <div class="col-12 col-sm-4">
                            <button class="btn community-btn w-100 h-100 py-3 fw-bold font-label-md text-nowrap d-flex align-items-center justify-content-center gap-2">
                                ĐĂNG KÝ <span class="material-symbols-outlined fs-5">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 p-0 position-relative d-none d-lg-block">
                <!-- Overlay shadow for blending map edges with the dark theme -->
                <div class="position-absolute top-0 bottom-0 start-0 z-2" style="width: 40px; background: linear-gradient(90deg, #1a1a1a 0%, transparent 100%); pointer-events: none;"></div>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.6696584237025!2d106.67969327570377!3d10.759917089387768!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa06651894598e4e!2s290%20An%20D%C6%B0%C6%A1ng%20V%C6%B0%C6%A1ng%2C%20Ph%C6%B0%C6%A1ng%204%2C%20Qu%E1%BA%ADn%205%2C%20Th%C3%A0nh%20ph%E1%BB%91%20H%E1%BB%93%20Ch%C3%AD%20Minh!5e0!3m2!1svi!2s!4v1718342416434!5m2!1svi!2s" class="position-absolute w-100 h-100 z-1" style="border:0; filter: contrast(1.1) saturate(1.1);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
</main>