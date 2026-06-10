<?php
/**
 * admin/quanlysansale.php — Quản lý Săn Sale Chớp Nhoáng
 * Cho phép admin bật/tắt sale và chỉnh % giảm giá cho từng sản phẩm
 */
require_once '../model/database.php';
require_once '../model/m_sanpham.php';

$db = (new Database())->getConnection();
$productModel = new ProductModel($db);

$message = '';
$error = '';

// ---- Xử lý cập nhật DiscountPercent ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_discount') {
        $pid      = intval($_POST['product_id'] ?? 0);
        $discount = intval($_POST['discount'] ?? 0);
        $discount = max(0, min(100, $discount)); // Clamp 0-100

        $stmt = $db->prepare("UPDATE `PRODUCTS` SET `DiscountPercent` = :disc WHERE `Product_ID` = :id");
        $stmt->bindValue(':disc', $discount, PDO::PARAM_INT);
        $stmt->bindValue(':id',   $pid,      PDO::PARAM_INT);
        if ($stmt->execute()) {
            $message = "Đã cập nhật mức giảm giá cho sản phẩm #$pid thành $discount%.";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật.";
        }
    } elseif ($_POST['action'] === 'bulk_clear') {
        $db->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 0");
        $message = "Đã xóa toàn bộ giảm giá. Không có sản phẩm nào đang sale.";
    } elseif ($_POST['action'] === 'bulk_update') {
        $updates = $_POST['discounts'] ?? [];
        $count = 0;
        $stmt = $db->prepare("UPDATE `PRODUCTS` SET `DiscountPercent` = :disc WHERE `Product_ID` = :id");
        foreach ($updates as $pid => $disc) {
            $pid  = intval($pid);
            $disc = max(0, min(100, intval($disc)));
            $stmt->bindValue(':disc', $disc, PDO::PARAM_INT);
            $stmt->bindValue(':id',   $pid,  PDO::PARAM_INT);
            $stmt->execute();
            if ($disc > 0) $count++;
        }
        $message = "Đã cập nhật! Hiện có <strong>$count</strong> sản phẩm đang sale.";
    }
}

// ---- Lấy danh sách sản phẩm + tổng sản phẩm sale ----
$allProducts = $db->query("
    SELECT p.*, c.CategoryName 
    FROM `PRODUCTS` p 
    LEFT JOIN `CATEGORIES` c ON p.Category_ID = c.Category_ID 
    ORDER BY p.DiscountPercent DESC, p.Product_ID ASC
")->fetchAll(PDO::FETCH_ASSOC);

$saleCount  = count(array_filter($allProducts, fn($p) => $p['DiscountPercent'] > 0));
$totalCount = count($allProducts);

// Filter view
$viewMode = $_GET['view'] ?? 'all'; // all | sale | nosale
$filteredProducts = match($viewMode) {
    'sale'   => array_filter($allProducts, fn($p) => $p['DiscountPercent'] > 0),
    'nosale' => array_filter($allProducts, fn($p) => $p['DiscountPercent'] == 0),
    default  => $allProducts,
};
?>

<!-- Stats cards -->
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h2 class="font-display-md m-0 d-flex align-items-center gap-2">
            <span class="material-symbols-outlined text-danger">local_fire_department</span>
            Quản lý Săn Sale Chớp Nhoáng
        </h2>
        <p class="text-muted mb-0 mt-1" style="font-size:0.85rem;">Bật/tắt và điều chỉnh mức giảm giá cho từng sản phẩm</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/GuitarX/index.php?act=sansale" target="_blank"
           class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
            <span class="material-symbols-outlined" style="font-size:16px;">open_in_new</span> Xem trang Sale
        </a>
        <form method="POST" action="index.php?act=quanlySanSale" onsubmit="return confirm('Xóa toàn bộ giảm giá?')">
            <input type="hidden" name="action" value="bulk_clear">
            <button type="submit" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                <span class="material-symbols-outlined" style="font-size:16px;">clear_all</span> Xóa tất cả sale
            </button>
        </form>
    </div>
</div>

<!-- Stat cards row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e63946 !important;">
            <div class="card-body py-3 px-3">
                <div style="font-size:2rem; font-weight:800; color:#e63946; font-family:var(--font-display);"><?= $saleCount ?></div>
                <div class="text-muted" style="font-size:0.8rem;">Đang Sale 🔥</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important;">
            <div class="card-body py-3 px-3">
                <div style="font-size:2rem; font-weight:800; color:#6c757d; font-family:var(--font-display);"><?= $totalCount - $saleCount ?></div>
                <div class="text-muted" style="font-size:0.8rem;">Không sale</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #1a1a2e !important;">
            <div class="card-body py-3 px-3">
                <div style="font-size:2rem; font-weight:800; color:#1a1a2e; font-family:var(--font-display);"><?= $totalCount ?></div>
                <div class="text-muted" style="font-size:0.8rem;">Tổng sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f6ad55 !important;">
            <div class="card-body py-3 px-3">
                <?php $maxDisc = $totalCount > 0 ? max(array_column($allProducts, 'DiscountPercent')) : 0; ?>
                <div style="font-size:2rem; font-weight:800; color:#f6ad55; font-family:var(--font-display);"><?= $maxDisc ?>%</div>
                <div class="text-muted" style="font-size:0.8rem;">Giảm tối đa</div>
            </div>
        </div>
    </div>
</div>

<!-- Alert messages -->
<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <span class="material-symbols-outlined align-middle me-1">check_circle</span><?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <?= $error ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter tabs + Bulk form -->
<form method="POST" action="index.php?act=quanlySanSale">
    <input type="hidden" name="action" value="bulk_update">

    <div class="card shadow-sm border-0">
        <!-- Card header: filter tabs -->
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
            <div class="d-flex gap-2">
                <a href="index.php?act=quanlySanSale&view=all"
                   class="btn btn-sm rounded-pill <?= $viewMode==='all' ? 'btn-dark' : 'btn-outline-secondary' ?>">
                    Tất cả (<?= $totalCount ?>)
                </a>
                <a href="index.php?act=quanlySanSale&view=sale"
                   class="btn btn-sm rounded-pill <?= $viewMode==='sale' ? 'btn-danger' : 'btn-outline-danger' ?>">
                    🔥 Đang Sale (<?= $saleCount ?>)
                </a>
                <a href="index.php?act=quanlySanSale&view=nosale"
                   class="btn btn-sm rounded-pill <?= $viewMode==='nosale' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                    Chưa Sale (<?= $totalCount - $saleCount ?>)
                </a>
            </div>
            <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined" style="font-size:17px;">save</span> Lưu tất cả thay đổi
            </button>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:50px;">ID</th>
                            <th style="width:60px;">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th style="width:130px;">Giá gốc</th>
                            <th style="width:120px;" class="text-center">
                                <span class="text-danger fw-bold">% Giảm giá</span>
                            </th>
                            <th style="width:140px;">Giá sau giảm</th>
                            <th class="text-center" style="width:90px;">Trạng thái</th>
                            <th class="pe-4 text-end" style="width:80px;">Nhanh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filteredProducts as $p):
                            $disc       = intval($p['DiscountPercent']);
                            $salePrice  = $p['Price'] * (1 - $disc / 100);
                        ?>
                        <tr class="<?= $disc > 0 ? 'table-warning' : '' ?>" style="<?= $disc > 0 ? 'background:rgba(254,243,199,0.5) !important;' : '' ?>">
                            <td class="ps-4 fw-bold text-muted">#<?= $p['Product_ID'] ?></td>
                            <td>
                                <img src="../view/image/<?= htmlspecialchars($p['Image']) ?>"
                                     alt="" style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #eee;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size:0.88rem; max-width:240px;"><?= htmlspecialchars($p['ProductName']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($p['Brand'] ?? '') ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['CategoryName'] ?? 'N/A') ?></span></td>
                            <td class="fw-bold" style="font-size:0.88rem;"><?= number_format($p['Price'], 0, ',', '.') ?>₫</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <input type="range" class="form-range discount-range"
                                           name="discounts[<?= $p['Product_ID'] ?>]"
                                           id="range_<?= $p['Product_ID'] ?>"
                                           min="0" max="80" step="5"
                                           value="<?= $disc ?>"
                                           oninput="updateDiscount(<?= $p['Product_ID'] ?>)"
                                           style="width:70px; accent-color:#e63946;">
                                    <span id="val_<?= $p['Product_ID'] ?>" class="fw-bold <?= $disc > 0 ? 'text-danger' : 'text-muted' ?>"
                                          style="min-width:36px; font-size:0.9rem; font-family:var(--font-display);">
                                        <?= $disc ?>%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php if ($disc > 0): ?>
                                <span class="fw-bold" style="color:#e63946; font-size:0.88rem;" id="sale_price_<?= $p['Product_ID'] ?>">
                                    <?= number_format($salePrice, 0, ',', '.') ?>₫
                                </span>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:0.85rem;" id="sale_price_<?= $p['Product_ID'] ?>">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" id="status_<?= $p['Product_ID'] ?>">
                                <?php if ($disc > 0): ?>
                                <span class="badge bg-danger">🔥 Sale -<?= $disc ?>%</span>
                                <?php else: ?>
                                <span class="badge bg-light text-muted border">Thường</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <!-- Quick set buttons -->
                                <div class="d-flex gap-1 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                            onclick="setDiscount(<?= $p['Product_ID'] ?>, <?= $p['Price'] ?>, 0)"
                                            title="Tắt sale" style="font-size:11px; min-width:28px;">✕</button>
                                    <button type="button" class="btn btn-outline-warning btn-sm px-1 py-0"
                                            onclick="setDiscount(<?= $p['Product_ID'] ?>, <?= $p['Price'] ?>, 10)"
                                            title="10%" style="font-size:11px;">10</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-1 py-0"
                                            onclick="setDiscount(<?= $p['Product_ID'] ?>, <?= $p['Price'] ?>, 20)"
                                            title="20%" style="font-size:11px;">20</button>
                                    <button type="button" class="btn btn-danger btn-sm px-1 py-0"
                                            onclick="setDiscount(<?= $p['Product_ID'] ?>, <?= $p['Price'] ?>, 30)"
                                            title="30%" style="font-size:11px;">30</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer save button -->
        <div class="card-footer bg-white border-top d-flex justify-content-end py-3">
            <button type="submit" class="btn btn-danger d-flex align-items-center gap-2 shadow px-4">
                <span class="material-symbols-outlined">save</span> Lưu tất cả thay đổi
            </button>
        </div>
    </div>
</form>

<script>
const prices = {
    <?php foreach ($allProducts as $p): ?>
    <?= $p['Product_ID'] ?>: <?= $p['Price'] ?>,
    <?php endforeach; ?>
};

function updateDiscount(pid) {
    const rangeEl     = document.getElementById('range_' + pid);
    const valEl       = document.getElementById('val_' + pid);
    const priceEl     = document.getElementById('sale_price_' + pid);
    const statusEl    = document.getElementById('status_' + pid);
    const disc        = parseInt(rangeEl.value);
    const origPrice   = prices[pid] || 0;
    const salePrice   = origPrice * (1 - disc / 100);

    valEl.textContent = disc + '%';
    valEl.className   = disc > 0 ? 'fw-bold text-danger' : 'fw-bold text-muted';
    valEl.style.minWidth = '36px';
    valEl.style.fontSize = '0.9rem';

    if (disc > 0) {
        priceEl.textContent  = formatVND(salePrice) + '₫';
        priceEl.style.color  = '#e63946';
        priceEl.className    = 'fw-bold';
        statusEl.innerHTML   = `<span class="badge bg-danger">🔥 Sale -${disc}%</span>`;
    } else {
        priceEl.textContent  = '—';
        priceEl.style.color  = '';
        priceEl.className    = 'text-muted';
        statusEl.innerHTML   = '<span class="badge bg-light text-muted border">Thường</span>';
    }
}

function setDiscount(pid, origPrice, disc) {
    const rangeEl = document.getElementById('range_' + pid);
    rangeEl.value = disc;
    updateDiscount(pid);
}

function formatVND(n) {
    return Math.round(n).toLocaleString('vi-VN');
}
</script>
