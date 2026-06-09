<?php
// Lấy tên danh mục cho breadcrumb
$catName = isset($product['Category_ID']) ? $productModel->getCategoryName($product['Category_ID']) : 'Nhạc cụ';
?>
<main class="w-100 py-5 bg-light">
    <div class="container-max-custom px-desktop-custom">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/GuitarX/index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
                <?php if (isset($product['Category_ID'])): ?>
                    <li class="breadcrumb-item">
                        <a href="/GuitarX/index.php?act=sanpham&id=<?php echo $product['Category_ID']; ?>" class="text-decoration-none text-muted">
                            <?php echo htmlspecialchars($catName); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-danger-custom text-truncate" style="max-width: 300px;" aria-current="page">
                    <?php echo htmlspecialchars($product['ProductName']); ?>
                </li>
            </ol>
        </nav>

        <!-- Product Main Info -->
        <div class="row g-5 mb-5">
            <!-- Cột trái: Ảnh sản phẩm -->
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white p-3 d-flex align-items-center justify-content-center position-relative" style="min-height: 400px;">
                    <?php 
                    $isFavMain = isset($userFavorites) && in_array($product['Product_ID'], $userFavorites); 
                    $fillValueMain = $isFavMain ? 1 : 0;
                    $colorClassMain = $isFavMain ? 'text-danger' : '';
                    ?>
                    <button class="btn btn-light rounded-circle shadow-sm position-absolute p-2 z-2 d-flex align-items-center justify-content-center" style="top: 15px; left: 15px; width: 44px; height: 44px;" onclick="toggleFavorite(<?php echo $product['Product_ID']; ?>, this, event)" title="Yêu thích">
                        <span class="material-symbols-outlined <?php echo $colorClassMain; ?>" style="font-variation-settings: 'FILL' <?php echo $fillValueMain; ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size: 24px;">favorite</span>
                    </button>
                    <div class="product-detail-img-wrap w-100 overflow-hidden rounded-2">
                        <img src="/GuitarX/view/image/<?php echo htmlspecialchars($product['Image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['ProductName']); ?>" 
                             class="img-fluid w-100 object-fit-cover transition-all hover-zoom-detail" />
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thông tin chi tiết -->
            <div class="col-12 col-md-6 d-flex flex-column justify-content-between">
                <div>
                    <!-- Thương hiệu -->
                    <span class="badge bg-primary-custom text-white px-3 py-2 font-label-sm rounded-1 mb-2 text-uppercase tracking-wider">
                        <?php echo htmlspecialchars($product['Brand'] ?? 'GuitarX'); ?>
                    </span>
                    
                    <!-- Tên sản phẩm -->
                    <h1 class="font-display-lg text-dark mb-3 fw-bold" style="font-size: 2.2rem; line-height: 1.2;">
                        <?php echo htmlspecialchars($product['ProductName']); ?>
                    </h1>

                    <!-- Tình trạng kho hàng -->
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-success">check_circle</span>
                        <span class="text-muted font-body-md">Tình trạng: 
                            <strong class="text-dark">
                                <?php echo ($product['Count'] > 0) ? 'Còn hàng (' . $product['Count'] . ' sản phẩm)' : 'Hết hàng'; ?>
                            </strong>
                        </span>
                    </div>

                    <!-- Giá bán -->
                    <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border-start border-danger-custom border-4">
                        <span class="text-muted font-label-md d-block text-uppercase fw-bold mb-1">Giá bán lẻ đề xuất</span>
                        <span class="text-secondary-custom font-display-lg fs-1 fw-bold">
                            <?php echo number_format($product['Price'], 0, ',', '.'); ?>₫
                        </span>
                    </div>

                    <!-- Mô tả ngắn -->
                    <div class="mb-4">
                        <h4 class="font-headline-sm text-dark mb-2 text-uppercase fs-6 fw-bold">Tóm tắt sản phẩm:</h4>
                        <p class="text-muted font-body-md mb-0">
                            <?php echo nl2br(htmlspecialchars($product['Description'] ?? 'Đang cập nhật thông tin mô tả chi tiết cho sản phẩm này.')); ?>
                        </p>
                    </div>
                </div>

                <!-- Thao tác mua hàng -->
                <div class="bg-white p-4 rounded-3 shadow-sm mt-3">
                    <form action="/GuitarX/index.php?act=themgiohang" method="POST" class="d-flex flex-column gap-3">
                        <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>" />
                        
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted font-label-md fw-bold text-nowrap">Số lượng:</span>
                            <div class="input-group" style="width: 130px;">
                                <button class="btn btn-outline-secondary py-2" type="button" onclick="changeQty(-1)">-</button>
                                <input type="number" name="quantity" id="quantityInput" class="form-control text-center py-2 fw-bold" value="1" min="1" max="<?php echo $product['Count']; ?>" />
                                <button class="btn btn-outline-secondary py-2" type="button" onclick="changeQty(1)">+</button>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <div class="col-8">
                                <button type="submit" name="add_to_cart" class="btn btn-secondary-custom w-100 py-3 font-headline-sm rounded-2 shadow d-flex align-items-center justify-content-center gap-2">
                                    <span class="material-symbols-outlined">shopping_cart</span>
                                    THÊM VÀO GIỎ HÀNG
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="submit" name="buy_now" class="btn btn-primary-custom w-100 py-3 font-headline-sm rounded-2">
                                    MUA NGAY
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detailed Tabs (Description & Specifications) -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-5 mb-5">
            <h3 class="font-headline-sm text-dark mb-4 pb-2 border-bottom fw-bold text-uppercase">Chi tiết sản phẩm</h3>
            <div class="font-body-md text-muted" style="line-height: 1.8;">
                <p>Cảm ơn bạn đã quan tâm đến dòng sản phẩm <strong><?php echo htmlspecialchars($product['ProductName']); ?></strong> được phân phối chính hãng bởi <strong>GuitarX</strong>.</p>
                <p>Tất cả sản phẩm nhạc cụ tại cửa hàng đều được kiểm tra kỹ thuật nghiêm ngặt trước khi bàn giao, đảm bảo action chuẩn, âm thanh ấm vang ổn định và đi kèm đầy đủ chế độ bảo hành chính hãng lên tới 12 tháng.</p>
                
                <table class="table table-bordered mt-4" style="max-width: 600px;">
                    <tbody>
                        <tr>
                            <td class="fw-bold bg-light" style="width: 30%;">Thương hiệu</td>
                            <td><?php echo htmlspecialchars($product['Brand'] ?? 'Chưa xác định'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Dòng nhạc cụ</td>
                            <td><?php echo htmlspecialchars($catName); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Ngày nhập khẩu</td>
                            <td><?php echo isset($product['DateImport']) ? date('d/m/Y', strtotime($product['DateImport'])) : 'Đang cập nhật'; ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Chính sách ưu đãi</td>
                            <td>Bảo hành 12 tháng, đổi trả 1-1 trong 7 ngày nếu lỗi từ nhà sản xuất.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div>
                <div class="d-flex flex-column mb-4">
                    <div class="bg-secondary-custom mb-2" style="width: 48px; height: 4px;"></div>
                    <h2 class="font-headline-md text-dark text-uppercase mb-1">Sản phẩm tương tự</h2>
                    <p class="text-muted font-body-md mb-0">Các nhạc cụ cùng danh mục có thể bạn quan tâm</p>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="col">
                            <div class="product-card h-100 position-relative d-flex flex-column justify-content-between shadow-sm">
                                <?php 
                                $isFavRelated = isset($userFavorites) && in_array($related['Product_ID'], $userFavorites); 
                                $fillValueRelated = $isFavRelated ? 1 : 0;
                                $colorClassRelated = $isFavRelated ? 'text-danger' : '';
                                ?>
                                <button class="btn btn-light rounded-circle shadow-sm position-absolute p-2 z-2 d-flex align-items-center justify-content-center" style="top: 10px; left: 10px; width: 36px; height: 36px;" onclick="toggleFavorite(<?php echo $related['Product_ID']; ?>, this, event)" title="Yêu thích">
                                    <span class="material-symbols-outlined <?php echo $colorClassRelated; ?>" style="font-variation-settings: 'FILL' <?php echo $fillValueRelated; ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size: 20px;">favorite</span>
                                </button>
                                <div>
                                    <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                                        <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $related['Product_ID']; ?>">
                                            <img alt="<?php echo htmlspecialchars($related['ProductName']); ?>" src="/GuitarX/view/image/<?php echo htmlspecialchars($related['Image']); ?>" />
                                        </a>
                                    </div>
                                    <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider"><?php echo htmlspecialchars($related['Brand']); ?></p>
                                    <h3 class="font-body-md fw-bold text-dark mb-2">
                                        <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $related['Product_ID']; ?>" class="text-decoration-none text-dark link-hover-red">
                                            <?php echo htmlspecialchars($related['ProductName']); ?>
                                        </a>
                                    </h3>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="text-secondary-custom font-headline-sm mb-0"><?php echo number_format($related['Price'], 0, ',', '.'); ?>₫</span>
                                    </div>
                                    <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $related['Product_ID']; ?>" class="btn btn-add-cart-custom w-100 text-center text-decoration-none d-block pt-2">XEM CHI TIẾT</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts and Custom Styles -->
    <style>
    .hover-zoom-detail {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .hover-zoom-detail:hover {
        transform: scale(1.04);
    }
    .link-hover-red:hover {
        color: var(--color-secondary) !important;
    }
    </style>
    <script>
    function changeQty(amount) {
        const input = document.getElementById('quantityInput');
        let val = parseInt(input.value) + amount;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 99;
        
        if (val < min) val = min;
        if (val > max) val = max;
        
        input.value = val;
    }
    </script>
</main>
