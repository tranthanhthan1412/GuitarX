<main class="w-100 py-5 bg-light">
    <div class="container-max-custom px-desktop-custom">
        <!-- Breadcrumb / Tiêu đề danh mục -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php"
                            class="text-decoration-none text-muted">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-danger-custom" aria-current="page">
                        <?php echo htmlspecialchars($titleName); ?></li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-secondary-custom" style="width: 8px; height: 32px;"></div>
                <h1 class="font-headline-md text-uppercase mb-0 fw-bold"><?php echo htmlspecialchars($titleName); ?>
                </h1>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sidebar: Danh mục sản phẩm (Chỉ hiển thị trên desktop) -->
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
                    <h3 class="font-headline-sm text-dark mb-3 pb-2 border-bottom fw-bold text-uppercase">Danh mục</h3>
                    <div class="d-flex flex-column gap-2">
                        <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                        <?php 
                                    $activeClass = (isset($_GET['id']) && $_GET['id'] == $cat['Ma_DanhMuc']) ? 'fw-bold text-danger-custom' : 'text-dark';
                                ?>
                        <a href="<?= BASE_URL ?>index.php?act=sanpham&id=<?php echo $cat['Ma_DanhMuc']; ?>"
                            class="text-decoration-none py-2 px-1 hover-sidebar transition-all d-flex align-items-center justify-content-between <?php echo $activeClass; ?>">
                            <span><?php echo htmlspecialchars($cat['TenDanhMuc']); ?></span>
                            <span class="material-symbols-outlined fs-5">chevron_right</span>
                        </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h3 class="font-headline-sm text-dark mb-3 pb-2 border-bottom fw-bold text-uppercase">Ưu đãi nổi bật
                    </h3>
                    <div class="position-relative overflow-hidden rounded-2">
                        <img src="<?= BASE_URL ?>view/image/acoustic.jpg" class="img-fluid rounded-2 hover-zoom"
                            alt="Promotion" />
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-75 text-white">
                            <p class="font-label-sm text-uppercase text-warning mb-1">🔥 Hot Sale</p>
                            <h4 class="font-headline-sm mb-0 fs-6">Giảm tới 40% phụ kiện</h4>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content: Danh sách sản phẩm -->
            <section class="col-12 col-lg-9">
                <!-- Bộ lọc & Sắp xếp -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm">
                    <p class="text-muted mb-0 font-body-md">
                        Tìm thấy <strong class="text-dark"><?php echo count($productsList); ?></strong> sản phẩm
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted font-label-md text-nowrap">Sắp xếp:</span>
                        <select class="form-select form-select-sm border-0 bg-light fw-semibold text-dark"
                            style="width: auto;" id="sortProduct" onchange="changeSort(this)">
                            <option value="new" <?= isset($_GET['sort']) && $_GET['sort'] == 'new' ? 'selected' : '' ?>>
                                Mới nhất</option>
                            <option value="price-asc"
                                <?= isset($_GET['sort']) && $_GET['sort'] == 'price-asc' ? 'selected' : '' ?>>Giá tăng
                                dần</option>
                            <option value="price-desc"
                                <?= isset($_GET['sort']) && $_GET['sort'] == 'price-desc' ? 'selected' : '' ?>>Giá giảm
                                dần</option>
                        </select>

                        <script>
                        function changeSort(selectElement) {
                            let sortValue = selectElement.value;

                            // Lấy id danh mục hiện tại trên URL (nếu có)
                            const urlParams = new URLSearchParams(window.location.search);
                            let catId = urlParams.get('id');

                            // Tạo đường dẫn mới quay về index.php chính
                            let newUrl = "index.php?act=sanpham&sort=" + sortValue;

                            // Nếu đang ở trong một danh mục, đính kèm lại id danh mục để lọc không bị nhảy ra ngoài
                            if (catId) {
                                newUrl += "&id=" + catId;
                            }

                            window.location.href = newUrl;
                        }
                        </script>
                    </div>
                </div>

                <!-- Lưới sản phẩm -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php if (!empty($productsList)): ?>
                    <?php foreach ($productsList as $prod): ?>
                    <div class="col">
                        <div
                            class="product-card h-100 position-relative d-flex flex-column justify-content-between shadow-sm">
                            <?php if (isset($prod['PhanTramGiamGia']) && $prod['PhanTramGiamGia'] > 0): ?>
                            <span class="sale-badge">-<?php echo $prod['PhanTramGiamGia']; ?>%</span>
                            <?php elseif ($prod['Ma_SanPham'] % 2 == 0): ?>
                            <span
                                class="position-absolute top-0 end-0 m-3 badge bg-secondary-custom text-white px-2 py-1 font-label-sm rounded-1 z-1">BEST
                                SELLER</span>
                            <?php endif; ?>

                            <?php 
                                    $isFav = isset($userFavorites) && in_array($prod['Ma_SanPham'], $userFavorites); 
                                    $fillValue = $isFav ? 1 : 0;
                                    $colorClass = $isFav ? 'text-danger' : '';
                                    ?>
                            <button
                                class="btn btn-light rounded-circle shadow-sm position-absolute p-2 z-2 d-flex align-items-center justify-content-center"
                                style="top: 10px; left: 10px; width: 36px; height: 36px;"
                                onclick="toggleFavorite(<?php echo $prod['Ma_SanPham']; ?>, this, event)"
                                title="Yêu thích">
                                <span class="material-symbols-outlined <?php echo $colorClass; ?>"
                                    style="font-variation-settings: 'FILL' <?php echo $fillValue; ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size: 20px;">favorite</span>
                            </button>

                            <div>
                                <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                                    <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $prod['Ma_SanPham']; ?>">
                                        <img alt="<?php echo htmlspecialchars($prod['TenSanPham']); ?>"
                                            src="<?= BASE_URL ?>view/image/<?php echo htmlspecialchars($prod['Anh']); ?>" />
                                    </a>
                                </div>
                                <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider">
                                    <?php echo htmlspecialchars($prod['ThuongHieu']); ?></p>
                                <h3 class="font-body-md fw-bold text-dark mb-2">
                                    <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $prod['Ma_SanPham']; ?>"
                                        class="text-decoration-none text-dark link-hover-red">
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
                                <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $prod['Ma_SanPham']; ?>"
                                    class="btn btn-add-cart-custom w-100 text-center text-decoration-none d-block pt-2">XEM
                                    CHI TIẾT</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm w-100">
                        <span class="material-symbols-outlined display-1 text-muted mb-3">inventory_2</span>
                        <p class="text-muted font-body-lg">Không tìm thấy sản phẩm nào trong danh mục này.</p>
                        <a href="<?= BASE_URL ?>index.php?act=sanpham"
                            class="btn btn-secondary-custom px-4 py-2 mt-2 font-headline-sm rounded-1 shadow">Xem tất cả
                            sản phẩm</a>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <div class="d-flex justify-content-center align-items-center w-100 mt-4 mb-2">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm m-0 d-flex justify-content-center align-items-center">

                                <?php 
                $prevDisabled = ($currentPage <= 1) ? 'disabled' : ''; 
                // Giữ lại id danh mục nếu có để phân trang không bị mất lọc
                $catParam = isset($_GET['id']) ? '&id=' . intval($_GET['id']) : '';
            ?>
                                <li class="page-item <?= $prevDisabled ?>">
                                    <a class="page-link border-0 bg-light text-dark rounded-3 me-2 px-3 d-flex align-items-center justify-content-center"
                                        href="index.php?act=sanpham&page=<?= $currentPage - 1 ?><?= $catParam ?>"
                                        aria-label="Previous" style="height: 36px;">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $totalPages; $i++): 
                $itemActive = ($currentPage == $i) ? 'active' : '';
                $linkClass = ($currentPage == $i) ? 'bg-dark text-white' : 'bg-light text-muted';
            ?>
                                <li class="page-item <?= $itemActive ?>">
                                    <a class="page-link border-0 mx-1 rounded-3 fw-bold <?= $linkClass ?>"
                                        href="index.php?act=sanpham&page=<?= $i ?><?= $catParam ?>"
                                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php endfor; ?>

                                <?php $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : ''; ?>
                                <li class="page-item <?= $nextDisabled ?>">
                                    <a class="page-link border-0 bg-light text-dark rounded-3 ms-2 px-3 d-flex align-items-center justify-content-center"
                                        href="index.php?act=sanpham&page=<?= $currentPage + 1 ?><?= $catParam ?>"
                                        aria-label="Next" style="height: 36px;">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>

                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Một vài CSS bổ sung trực tiếp cho sidebar và các hiệu ứng nhỏ -->
    <style>
    .hover-sidebar {
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .hover-sidebar:hover {
        background-color: var(--color-surface-low);
        color: var(--color-secondary) !important;
        padding-left: 8px !important;
    }

    .hover-zoom {
        transition: transform 0.5s ease;
    }

    .hover-zoom:hover {
        transform: scale(1.08);
    }

    .link-hover-red:hover {
        color: var(--color-secondary) !important;
    }
    </style>
</main>