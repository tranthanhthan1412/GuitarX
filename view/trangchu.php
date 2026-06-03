<main class="w-100">
    <!-- ===== HERO CAROUSEL ===== -->
    <section class="hero-carousel position-relative w-100 overflow-hidden bg-primary-custom" style="height: 600px;">

        <!-- ── Slide Track ── -->
        <div class="hero-track" id="heroTrack">

            <!-- Slide 1 — guitar.jpg (original) -->
            <div class="hero-slide active">
                <img alt="Hero Guitar" class="hero-slide-img" src="/GuitarX/view/image/guitar.jpg" />
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

            <!-- Slide 2 — bannerguitar1.jpg -->
            <div class="hero-slide">
                <img alt="Banner Guitar 1" class="hero-slide-img" src="/GuitarX/view/image/bannerguitar1.jpg" />
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

            <!-- Slide 3 — bannerguitar2.jpg -->
            <div class="hero-slide">
                <img alt="Banner Guitar 2" class="hero-slide-img" src="/GuitarX/view/image/bannerguitar2.jpg" />
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

        </div><!-- /hero-track -->

        <!-- ── Arrow Buttons ── -->
        <button class="hero-arrow hero-arrow--prev" id="heroPrev" aria-label="Slide trước">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="hero-arrow hero-arrow--next" id="heroNext" aria-label="Slide tiếp">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>

        <!-- ── Dot Indicators ── -->
        <div class="hero-dots" id="heroDots">
            <button class="hero-dot active" data-slide="0" aria-label="Slide 1"></button>
            <button class="hero-dot" data-slide="1" aria-label="Slide 2"></button>
            <button class="hero-dot" data-slide="2" aria-label="Slide 3"></button>
        </div>

    </section>

    <!-- ── Carousel Styles ── -->
    <style>
    .hero-carousel {
        position: relative;
    }

    /* Track holds all slides side-by-side via CSS transitions */
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

    /* Content animation */
    .hero-content {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity 0.55s ease 0.35s, transform 0.55s ease 0.35s;
    }

    .hero-slide.active .hero-content {
        opacity: 1;
        transform: translateY(0);
    }

    /* Arrow buttons */
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

    /* Dot indicators */
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

    <!-- ── Carousel Script ── -->
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

        // Pause on hover
        track.addEventListener('mouseenter', function() {
            clearInterval(timer);
        });
        track.addEventListener('mouseleave', startAuto);

        startAuto();
    })();
    </script>

    <!-- ===== FLASH SALE BANNER ===== -->
    <section class="w-100 flash-sale-pattern py-4 text-white">
        <div
            class="container-max-custom px-desktop-custom d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
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
                <button class="btn btn-light text-danger-custom fw-bold px-4 py-2 shadow-sm">SĂN SALE NGAY</button>
            </div>
        </div>
    </section>

    <!-- ===== FEATURED GUITARS ===== -->
    <section class="container-max-custom px-desktop-custom py-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end mb-4 gap-3">
            <div>
                <div class="bg-secondary-custom mb-2" style="width: 48px; height: 4px;"></div>
                <h2 class="font-headline-md text-dark text-uppercase mb-1">GUITAR NỔI BẬT</h2>
                <p class="text-muted font-body-md mb-0">Lựa chọn hoàn hảo cho mọi phong cách chơi</p>
            </div>
            <a class="text-primary-custom fw-bold text-decoration-none d-flex align-items-center gap-1 link-hover-effect"
                href="#">
                Xem tất cả nhạc cụ <span class="material-symbols-outlined transition-transform">arrow_forward</span>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <!-- Card 1: Martin -->
            <div class="col">
                <div class="product-card h-100 position-relative d-flex flex-column justify-content-between">
                    <span
                        class="position-absolute top-0 end-0 m-3 badge bg-secondary-custom text-white px-2 py-1 font-label-sm rounded-1 z-1">BEST
                        SELLER</span>
                    <div>
                        <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                            <img alt="Martin Guitar" src="/GuitarX/view/image/aucosticmartin.jpg" />
                        </div>
                        <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider">MARTIN</p>
                        <h3 class="font-body-md fw-bold text-dark mb-2">Martin D-28 Standard Dreadnought Acoustic</h3>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-secondary-custom font-headline-sm mb-0">85.000.000₫</span>
                            <span class="text-muted text-decoration-line-through small opacity-50">92.000.000₫</span>
                        </div>
                        <button class="btn btn-add-cart-custom w-100">THÊM VÀO GIỎ HÀNG</button>
                    </div>
                </div>
            </div>

            <!-- Card 2: Yamaha -->
            <div class="col">
                <div class="product-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                            <img alt="Yamaha Classic" src="/GuitarX/view/image/classicyamaha.jpg" />
                        </div>
                        <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider">YAMAHA</p>
                        <h3 class="font-body-md fw-bold text-dark mb-2">Yamaha CG192C Classical Guitar Solid Cedar</h3>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-secondary-custom font-headline-sm mb-0">14.500.000₫</span>
                        </div>
                        <button class="btn btn-add-cart-custom w-100">THÊM VÀO GIỎ HÀNG</button>
                    </div>
                </div>
            </div>

            <!-- Card 3: Gibson -->
            <div class="col">
                <div class="product-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                            <img alt="Vintage Electric" src="/GuitarX/view/image/elecvintage.jpg" />
                        </div>
                        <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider">GIBSON</p>
                        <h3 class="font-body-md fw-bold text-dark mb-2">Gibson Custom Shop '59 Les Paul Vintage</h3>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-secondary-custom font-headline-sm mb-0">165.000.000₫</span>
                        </div>
                        <button class="btn btn-add-cart-custom w-100">THÊM VÀO GIỎ HÀNG</button>
                    </div>
                </div>
            </div>

            <!-- Card 4: Washburn -->
            <div class="col">
                <div class="product-card h-100 position-relative d-flex flex-column justify-content-between">
                    <span
                        class="position-absolute top-0 end-0 m-3 badge bg-primary-custom text-white px-2 py-1 font-label-sm rounded-1 z-1">GIỚI
                        HẠN</span>
                    <div>
                        <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                            <img alt="Washburn Bella" src="/GuitarX/view/image/washburnbella.jpg" />
                        </div>
                        <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider">WASHBURN</p>
                        <h3 class="font-body-md fw-bold text-dark mb-2">Washburn Bella Tono Allure SC56V</h3>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-secondary-custom font-headline-sm mb-0">12.800.000₫</span>
                        </div>
                        <button class="btn btn-add-cart-custom w-100">THÊM VÀO GIỎ HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== OUR PARTNERS ===== -->
    <section class="bg-surface-container-low py-5">
        <div class="container-max-custom px-desktop-custom">
            <div class="text-center mb-5">
                <h2 class="font-headline-md text-dark text-uppercase">THƯƠNG HIỆU ĐỒNG HÀNH</h2>
                <p class="text-muted font-body-md mb-0">Nhà phân phối chính hãng các thương hiệu guitar hàng đầu thế
                    giới</p>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Yamaha" class="img-fluid max-h-100" src="/GuitarX/view/image/yamaha.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Fender" class="img-fluid max-h-100" src="/GuitarX/view/image/fender.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Gibson" class="img-fluid max-h-100" src="/GuitarX/view/image/gibson.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Taylor" class="img-fluid max-h-100" src="/GuitarX/view/image/taylor.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Washburn" class="img-fluid max-h-100" src="/GuitarX/view/image/washburn.jpg" />
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white border rounded d-flex align-items-center justify-content-center p-4 grayscale-img"
                        style="height: 128px;">
                        <img alt="Ibanez" class="img-fluid max-h-100" src="/GuitarX/view/image/ibanez.jpg" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== NEWSLETTER / JOIN COMMUNITY ===== -->
    <section class="container-max-custom px-desktop-custom py-5">
        <div class="bg-primary-custom rounded-3 overflow-hidden row m-0 align-items-stretch" style="min-height: 400px;">
            <div class="col-12 col-lg-6 p-5 d-flex flex-column justify-content-center text-white">
                <h2 class="font-display-lg mb-4 fw-bold">Gia nhập cộng đồng</h2>
                <p class="font-body-lg mb-4 opacity-75">Đăng ký để tham gia các lớp học chuyên sâu độc quyền, nhận mẹo
                    bảo dưỡng đàn và là người đầu tiên tiếp cận các phiên bản giới hạn.</p>
                <div class="row g-3">
                    <div class="col-12 col-sm-8">
                        <input
                            class="form-control bg-white bg-opacity-10 border-white border-opacity-25 text-white py-3 px-4 shadow-none"
                            placeholder="Nhập email của bạn" type="email" style="border-radius: 4px;" />
                    </div>
                    <div class="col-12 col-sm-4">
                        <button class="btn btn-secondary-custom w-100 h-100 py-3 fw-bold text-nowrap"
                            style="border-radius: 4px;">Đăng ký</button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 p-0 position-relative d-none d-lg-block">
                <img alt="Newsletter Image" class="position-absolute w-100 h-100 object-fit-cover"
                    src="/GuitarX/view/image/acoustic.jpg" />
            </div>
        </div>
    </section>
</main>