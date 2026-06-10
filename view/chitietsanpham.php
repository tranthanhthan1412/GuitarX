<?php
// 1. Lấy tên danh mục cho breadcrumb
$catName = isset($product['Category_ID']) ? $productModel->getCategoryName($product['Category_ID']) : 'Nhạc cụ';

// 2. TỰ ĐỘNG LẤY ALBUM ẢNH PHỤ NẾU CONTROLLER CHƯA TRUYỀN BIẾN $album
if (!isset($album) && isset($product['Product_ID'])) {
    // Kiểm tra nếu hàm getProductImages đã tồn tại trong model của mày
    if (method_exists($productModel, 'getProductImages')) {
        $album = $productModel->getProductImages($product['Product_ID']);
    } else {
        $album = []; // Tránh lỗi undefined nếu chưa viết hàm trong model
    }
}
?>

<main class="w-100 py-5 bg-light">
    <div class="container-max-custom px-desktop-custom">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/GuitarX/index.php" class="text-decoration-none text-muted">Trang
                        chủ</a></li>
                <?php if (isset($product['Category_ID'])): ?>
                <li class="breadcrumb-item">
                    <a href="/GuitarX/index.php?act=sanpham&id=<?php echo $product['Category_ID']; ?>"
                        class="text-decoration-none text-muted">
                        <?php echo htmlspecialchars($catName); ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-danger-custom text-truncate" style="max-width: 300px;"
                    aria-current="page">
                    <?php echo htmlspecialchars($product['ProductName']); ?>
                </li>
            </ol>
        </nav>

        <div class="row g-5 mb-5">
            <div class="col-12 col-md-6">
                <div class="row g-2 m-0 w-100 position-relative">

                    <?php 
                    $isFavMain = isset($userFavorites) && in_array($product['Product_ID'], $userFavorites); 
                    $fillValueMain = $isFavMain ? 1 : 0;
                    $colorClassMain = $isFavMain ? 'text-danger' : '';
                    ?>
                    <button
                        class="btn btn-light rounded-circle shadow-sm position-absolute p-2 z-3 d-flex align-items-center justify-content-center"
                        style="top: 15px; right: 15px; width: 44px; height: 44px;"
                        onclick="toggleFavorite(<?php echo $product['Product_ID']; ?>, this, event)" title="Yêu thích">
                        <span class="material-symbols-outlined <?php echo $colorClassMain; ?>"
                            style="font-variation-settings: 'FILL' <?php echo $fillValueMain; ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size: 24px;">favorite</span>
                    </button>

                    <div class="col-2 p-0 d-flex flex-column gap-2 justify-content-start">

                        <div class="thumb-gallery-wrap border rounded text-center p-1 bg-white position-relative active-thumb"
                            onclick="changeProductImage(this)">
                            <img src="/GuitarX/view/image/<?php echo htmlspecialchars($product['Image']); ?>"
                                class="img-fluid thumb-gallery-img" style="aspect-ratio: 1/1; object-fit: contain;">
                        </div>

                        <?php 
                        $count = 0;
                        if (!empty($album)): 
                            foreach ($album as $img): 
                                if ($count >= 3) break;
                        ?>
                        <div class="thumb-gallery-wrap border rounded text-center p-1 bg-white position-relative"
                            onclick="changeProductImage(this)">
                            <img src="/GuitarX/view/image/<?php echo htmlspecialchars($img['Image_Path']); ?>"
                                class="img-fluid thumb-gallery-img" style="aspect-ratio: 1/1; object-fit: contain;">
                        </div>
                        <?php 
                            $count++;
                            endforeach; 
                        endif; 
                        ?>

                        <?php for($i = $count + 1; $i < 4; $i++): ?>
                        <div class="thumb-gallery-wrap border rounded text-center p-1 bg-white d-flex align-items-center justify-content-center placeholder-thumb"
                            style="opacity: 0.35; aspect-ratio: 1/1;">
                            <span class="material-symbols-outlined text-muted" style="font-size: 18px;">image</span>
                        </div>
                        <?php endfor; ?>

                    </div>

                    <div class="col-10 pe-0">
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white p-3 d-flex align-items-center justify-content-center"
                            style="height: 450px;">
                            <div
                                class="product-detail-img-wrap w-100 h-100 overflow-hidden rounded-2 d-flex align-items-center justify-content-center">
                                <img id="primaryProductImg"
                                    src="/GuitarX/view/image/<?php echo htmlspecialchars($product['Image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['ProductName']); ?>"
                                    class="img-fluid h-100 object-fit-contain transition-all hover-zoom-detail" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-md-6 d-flex flex-column justify-content-between">
                <div>
                    <span
                        class="badge bg-primary-custom text-white px-3 py-2 font-label-sm rounded-1 mb-2 text-uppercase tracking-wider">
                        <?php echo htmlspecialchars($product['Brand'] ?? 'GuitarX'); ?>
                    </span>

                    <h1 class="font-display-lg text-dark mb-3 fw-bold" style="font-size: 2.2rem; line-height: 1.2;">
                        <?php echo htmlspecialchars($product['ProductName']); ?>
                    </h1>

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-success">check_circle</span>
                        <span class="text-muted font-body-md">Tình trạng:
                            <strong class="text-dark">
                                <?php echo ($product['Count'] > 0) ? 'Còn hàng (' . $product['Count'] . ' sản phẩm)' : 'Hết hàng'; ?>
                            </strong>
                        </span>
                    </div>

                    <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border-start border-danger-custom border-4">
                        <?php if (isset($product['DiscountPercent']) && $product['DiscountPercent'] > 0): 
                            $salePrice = $product['Price'] - ($product['Price'] * $product['DiscountPercent'] / 100);
                            $savings = $product['Price'] - $salePrice;
                        ?>
                            <div class="d-flex align-items-center mb-1 gap-2">
                                <span class="text-muted font-label-md d-block text-uppercase fw-bold">Giá gốc:</span>
                                <span class="old-price text-muted" style="text-decoration: line-through;"><?php echo number_format($product['Price'], 0, ',', '.'); ?>₫</span>
                                <span class="badge bg-secondary-custom text-white fw-bold" style="font-size: 0.85rem;">-<?php echo $product['DiscountPercent']; ?>%</span>
                            </div>
                            <span class="text-secondary-custom font-display-lg fs-1 fw-bold">
                                <?php echo number_format($salePrice, 0, ',', '.'); ?>₫
                            </span>
                            <div class="mt-2 text-success fw-bold font-label-md">
                                Tiết kiệm được: <?php echo number_format($savings, 0, ',', '.'); ?>₫
                            </div>
                        <?php else: ?>
                            <span class="text-muted font-label-md d-block text-uppercase fw-bold mb-1">Giá bán lẻ đề
                                xuất</span>
                            <span class="text-secondary-custom font-display-lg fs-1 fw-bold">
                                <?php echo number_format($product['Price'], 0, ',', '.'); ?>₫
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-headline-sm text-dark mb-2 text-uppercase fs-6 fw-bold">Tóm tắt sản phẩm:</h4>
                        <p class="text-muted font-body-md mb-0">
                            <?php echo nl2br(htmlspecialchars($product['Description'] ?? 'Đang cập nhật thông tin mô tả chi tiết cho sản phẩm này.')); ?>
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-3 shadow-sm mt-3">
                    <form action="/GuitarX/index.php?act=themgiohang" method="POST" class="d-flex flex-column gap-3">
                        <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>" />

                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted font-label-md fw-bold text-nowrap">Số lượng:</span>
                            <div class="input-group" style="width: 130px;">
                                <button class="btn btn-outline-secondary py-2" type="button"
                                    onclick="changeQty(-1)">-</button>
                                <input type="number" name="quantity" id="quantityInput"
                                    class="form-control text-center py-2 fw-bold" value="1" min="1"
                                    max="<?php echo $product['Count']; ?>" />
                                <button class="btn btn-outline-secondary py-2" type="button"
                                    onclick="changeQty(1)">+</button>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <div class="col-8">
                                <button type="submit" name="add_to_cart"
                                    class="btn btn-secondary-custom w-100 py-3 font-headline-sm rounded-2 shadow d-flex align-items-center justify-content-center gap-2">
                                    <span class="material-symbols-outlined">shopping_cart</span>
                                    THÊM VÀO GIỎ HÀNG
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="submit" name="buy_now"
                                    class="btn btn-primary-custom w-100 py-3 font-headline-sm rounded-2">
                                    MUA NGAY
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 bg-white p-5 mb-5">
            <h3 class="font-headline-sm text-dark mb-4 pb-2 border-bottom fw-bold text-uppercase">Chi tiết sản phẩm</h3>
            <div class="font-body-md text-muted" style="line-height: 1.8;">
                <p>Cảm ơn bạn đã quan tâm đến dòng sản phẩm
                    <strong><?php echo htmlspecialchars($product['ProductName']); ?></strong> được phân phối chính hãng
                    bởi <strong>GuitarX</strong>.
                </p>
                <p>Tất cả sản phẩm nhạc cụ tại cửa hàng đều được kiểm tra kỹ thuật nghiêm ngặt trước khi bàn giao, đảm
                    bảo action chuẩn, âm thanh ấm vang ổn định và đi kèm đầy đủ chế độ bảo hành chính hãng lên tới 12
                    tháng.</p>

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
                            <td><?php echo isset($product['DateImport']) ? date('d/m/Y', strtotime($product['DateImport'])) : 'Đang cập nhật'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Chính sách ưu đãi</td>
                            <td>Bảo hành 12 tháng, đổi trả 1-1 trong 7 ngày nếu lỗi từ nhà sản xuất.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="container-max-custom px-desktop-custom mb-5">
            <div class="card border-0 shadow-sm p-4" style="background: #ffffff; border-radius: 8px;">
                <h2 class="font-headline-md text-dark text-uppercase mb-4"
                    style="border-left: 4px solid #dc3545; padding-left: 10px; font-size: 1.5rem;">Khách hàng đánh giá
                </h2>

                <div class="review-list mb-4">
                    <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $rev): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark"
                                style="font-size: 1.05rem;"><?= htmlspecialchars($rev['Username']) ?></span>
                        </div>
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $rev['Rating'] ? '★' : '☆' ?>
                            <?php endfor; ?>
                        </div>
                        <p class="text-secondary m-0" style="white-space: pre-line; line-height: 1.6;">
                            <?= htmlspecialchars($rev['Comment']) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted fst-italic">Cây đàn này chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ cảm
                        nhận!</p>
                    <?php endif; ?>
                </div>

                <h3 class="font-headline-sm text-dark mb-3" style="font-size: 1.2rem;">Viết đánh giá của bạn</h3>
                <?php if (isset($_SESSION['user_id'])): ?>
                <form action="index.php?act=chitiet&id=<?= $product['Product_ID'] ?>" method="POST">
                    <input type="hidden" name="submit_review" value="1">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Đánh giá sản phẩm:</label>
                        <select class="form-select" name="rating" style="width: 160px; border-color: #ced4da;">
                            <option value="5">⭐⭐⭐⭐⭐ 5 Sao</option>
                            <option value="4">⭐⭐⭐⭐ 4 Sao</option>
                            <option value="3">⭐⭐⭐ 3 Sao</option>
                            <option value="2">⭐⭐ 2 Sao</option>
                            <option value="1">⭐ 1 Sao</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nội dung nhận xét:</label>
                        <textarea class="form-control" name="comment" rows="3"
                            placeholder="Bạn thấy âm thanh hay chất gỗ cây đàn này như thế nào? Chia sẻ ở đây nhé..."
                            required style="border-radius: 6px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger px-4 fw-bold text-white"
                        style="background-color: #dc3545; border: none; border-radius: 4px; padding: 10px 20px;">Gửi
                        đánh giá</button>
                </form>
                <?php else: ?>
                <div class="alert alert-light border text-secondary py-3"
                    style="border-radius: 6px; background-color: #f8f9fa;">
                    Bạn cần đăng nhập để viết đánh giá cho sản phẩm này.
                </div>
                <?php endif; ?>
            </div>
        </div>

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
                    <div
                        class="product-card h-100 position-relative d-flex flex-column justify-content-between shadow-sm">
                        <?php if (isset($related['DiscountPercent']) && $related['DiscountPercent'] > 0): ?>
                            <span class="sale-badge">-<?php echo $related['DiscountPercent']; ?>%</span>
                        <?php elseif ($related['Product_ID'] % 2 == 0): ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-secondary-custom text-white px-2 py-1 font-label-sm rounded-1 z-1">BEST SELLER</span>
                        <?php endif; ?>
                        <?php 
                                $isFavRelated = isset($userFavorites) && in_array($related['Product_ID'], $userFavorites); 
                                $fillValueRelated = $isFavRelated ? 1 : 0;
                                $colorClassRelated = $isFavRelated ? 'text-danger' : '';
                                ?>
                        <button
                            class="btn btn-light rounded-circle shadow-sm position-absolute p-2 z-2 d-flex align-items-center justify-content-center"
                            style="top: 10px; left: 10px; width: 36px; height: 36px;"
                            onclick="toggleFavorite(<?php echo $related['Product_ID']; ?>, this, event)"
                            title="Yêu thích">
                            <span class="material-symbols-outlined <?php echo $colorClassRelated; ?>"
                                style="font-variation-settings: 'FILL' <?php echo $fillValueRelated; ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size: 20px;">favorite</span>
                        </button>
                        <div>
                            <div class="product-img-wrapper bg-surface-container-low rounded mb-3">
                                <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $related['Product_ID']; ?>">
                                    <img alt="<?php echo htmlspecialchars($related['ProductName']); ?>"
                                        src="/GuitarX/view/image/<?php echo htmlspecialchars($related['Image']); ?>" />
                                </a>
                            </div>
                            <p class="text-muted font-label-sm text-uppercase fw-bold mb-1 tracking-wider">
                                <?php echo htmlspecialchars($related['Brand']); ?></p>
                            <h3 class="font-body-md fw-bold text-dark mb-2">
                                <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $related['Product_ID']; ?>"
                                    class="text-decoration-none text-dark link-hover-red">
                                    <?php echo htmlspecialchars($related['ProductName']); ?>
                                </a>
                            </h3>
                        </div>
                        <div>
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                <?php if (isset($related['DiscountPercent']) && $related['DiscountPercent'] > 0): 
                                    $relatedSalePrice = $related['Price'] - ($related['Price'] * $related['DiscountPercent'] / 100);
                                ?>
                                    <span class="old-price"><?php echo number_format($related['Price'], 0, ',', '.'); ?>₫</span>
                                    <span class="new-price"><?php echo number_format($relatedSalePrice, 0, ',', '.'); ?>₫</span>
                                <?php else: ?>
                                    <span class="text-secondary-custom font-headline-sm mb-0"><?php echo number_format($related['Price'], 0, ',', '.'); ?>₫</span>
                                <?php endif; ?>
                            </div>
                            <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $related['Product_ID']; ?>"
                                class="btn btn-add-cart-custom w-100 text-center text-decoration-none d-block pt-2">XEM
                                CHI TIẾT</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

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

    /* ĐỊNH DẠNG CỤM ALBUM 4 ẢNH DỌC */
    .thumb-gallery-wrap {
        transition: all 0.2s ease-in-out;
        border: 1px solid #dee2e6 !important;
        cursor: pointer;
    }

    .thumb-gallery-wrap:not(.placeholder-thumb):hover {
        border-color: #e63946 !important;
        /* Rê chuột vào hiện viền đỏ nhạt */
    }

    .active-thumb {
        border: 2px solid #e63946 !important;
        /* Ảnh đang chọn ăn viền đỏ đậm */
        box-shadow: 0 2px 5px rgba(230, 57, 70, 0.2);
    }
    </style>

    <script>
    // Hàm tăng giảm số lượng sản phẩm
    function changeQty(amount) {
        const input = document.getElementById('quantityInput');
        let val = parseInt(input.value) + amount;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 99;

        if (val < min) val = min;
        if (val > max) val = max;

        input.value = val;
    }

    // Hàm chuyển ảnh lớn khi bấm ảnh nhỏ (Album)
    function changeProductImage(wrapperElement) {
        const targetImg = wrapperElement.querySelector('.thumb-gallery-img');
        if (!targetImg) return;

        // Thay src ảnh to bằng ảnh nhỏ vừa click
        document.getElementById('primaryProductImg').src = targetImg.src;

        // Gỡ viền active cũ
        document.querySelectorAll('.thumb-gallery-wrap').forEach(box => {
            box.classList.remove('active-thumb');
        });

        // Thêm viền active mới vào ảnh vừa chọn
        wrapperElement.classList.add('active-thumb');
    }
    </script>
</main>