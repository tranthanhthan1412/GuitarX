<?php
/**
 * view/sansale.php — Trang Săn Sale Chớp Nhoáng (Redesigned)
 */
?>
<style>
/* ===== SALE PAGE EXCLUSIVE STYLES ===== */
.sale-page-hero {
    background: linear-gradient(135deg, #0d0d1a 0%, #1a0505 40%, #2d0a0a 70%, #1a1a2e 100%);
    min-height: 320px;
    position: relative;
    overflow: hidden;
}
.sale-page-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 20% 50%, rgba(230,57,70,0.18) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(230,57,70,0.12) 0%, transparent 40%),
        repeating-linear-gradient(45deg, rgba(255,255,255,0.015) 0, rgba(255,255,255,0.015) 1px, transparent 1px, transparent 16px);
}
.sale-hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    pointer-events: none;
}
.sale-flash-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #f6d365, #fda085);
    color: #1a1a2e;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.78rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 0.35rem 1rem;
    border-radius: 50px;
    box-shadow: 0 4px 16px rgba(253,160,133,0.45);
    animation: pulse-badge 2s ease-in-out infinite;
}
@keyframes pulse-badge {
    0%, 100% { box-shadow: 0 4px 16px rgba(253,160,133,0.45); }
    50%       { box-shadow: 0 6px 24px rgba(253,160,133,0.7); transform: scale(1.02); }
}
.sale-timer-block {
    background: rgba(0,0,0,0.4);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 0.6rem 1.1rem;
    min-width: 62px;
    text-align: center;
    backdrop-filter: blur(12px);
}
.sale-timer-num {
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}
.sale-timer-label {
    font-size: 0.58rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.55);
    margin-top: 3px;
}
.sale-timer-sep {
    font-size: 1.8rem;
    font-weight: 800;
    color: rgba(255,255,255,0.4);
    align-self: flex-start;
    margin-top: 6px;
}

/* Stats bar */
.sale-stats-bar {
    background: #fff;
    border-bottom: 1px solid #eee;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.sale-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--font-body);
    font-size: 0.85rem;
    color: #555;
}
.sale-stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
}

/* Filter pills */
.sale-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-family: var(--font-display);
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    letter-spacing: 0.04em;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}
.sale-filter-pill.active {
    background: var(--color-secondary);
    color: #fff;
    border-color: var(--color-secondary);
    box-shadow: 0 4px 12px rgba(230,57,70,0.35);
}
.sale-filter-pill:not(.active) {
    background: #fff;
    color: #555;
    border-color: #e0e0e0;
}
.sale-filter-pill:not(.active):hover {
    border-color: var(--color-secondary);
    color: var(--color-secondary);
    transform: translateY(-1px);
}

/* Sale product cards */
.sale-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #eee;
    transition: box-shadow 0.28s ease, transform 0.28s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.sale-card:hover {
    box-shadow: 0 16px 48px rgba(26,26,46,0.13);
    transform: translateY(-6px);
}
.sale-card-img-wrap {
    position: relative;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #f8f8f8;
    flex-shrink: 0;
}
.sale-card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.45s ease;
}
.sale-card:hover .sale-card-img-wrap img {
    transform: scale(1.08);
}
.sale-disc-badge {
    position: absolute;
    top: 0;
    right: 0;
    width: 58px;
    height: 58px;
    background: linear-gradient(135deg, #e63946, #c1121f);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 0 16px 0 50%;
    font-family: var(--font-display);
    font-weight: 800;
    color: #fff;
    z-index: 3;
    box-shadow: -2px 2px 10px rgba(193,18,31,0.3);
    line-height: 1;
}
.sale-disc-badge-num { font-size: 1.05rem; }
.sale-disc-badge-pct { font-size: 0.7rem; opacity: 0.9; }

.sale-fav-btn {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 3;
    background: rgba(255,255,255,0.92);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.sale-fav-btn:hover { transform: scale(1.15); box-shadow: 0 4px 14px rgba(0,0,0,0.18); }

.sale-card-body {
    padding: 1rem 1.1rem 1.1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.sale-card-brand {
    font-family: var(--font-display);
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #aaa;
    margin-bottom: 4px;
}
.sale-card-name {
    font-family: var(--font-display);
    font-size: 0.9rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 10px;
    line-height: 1.35;
    flex: 1;
}
.sale-card-name a { text-decoration: none; color: inherit; }
.sale-card-name a:hover { color: var(--color-secondary); }

.sale-price-box {
    background: linear-gradient(135deg, rgba(230,57,70,0.06), rgba(230,57,70,0.02));
    border: 1px solid rgba(230,57,70,0.15);
    border-radius: 10px;
    padding: 0.6rem 0.75rem;
    margin-bottom: 10px;
}
.sale-price-old {
    font-size: 0.78rem;
    color: #aaa;
    text-decoration: line-through;
    font-family: var(--font-body);
}
.sale-price-new {
    font-family: var(--font-display);
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--color-secondary);
    line-height: 1;
}
.sale-saving-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.72rem;
    font-weight: 700;
    color: #27ae60;
    margin-top: 4px;
}
.sale-card-btn {
    display: block;
    width: 100%;
    padding: 0.6rem;
    background: var(--color-primary);
    color: #fff;
    font-family: var(--font-display);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    text-align: center;
    text-decoration: none;
    border-radius: 10px;
    border: 2px solid var(--color-primary);
    transition: background 0.2s, color 0.2s, transform 0.15s;
}
.sale-card-btn:hover {
    background: transparent;
    color: var(--color-primary);
    transform: translateY(-1px);
}

/* Empty state */
.sale-empty-state {
    background: #fff;
    border-radius: 20px;
    padding: 4rem 2rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}
</style>

<!-- ===== HERO ===== -->
<section class="sale-page-hero d-flex align-items-center">
    <!-- Orbs -->
    <div class="sale-hero-orb" style="width:400px;height:400px;background:rgba(230,57,70,0.2);top:-150px;right:-100px;"></div>
    <div class="sale-hero-orb" style="width:280px;height:280px;background:rgba(255,140,0,0.1);bottom:-100px;left:5%;"></div>

    <div class="container-max-custom px-desktop-custom position-relative w-100">
        <div class="row align-items-center g-5">
            <!-- Left: Text -->
            <div class="col-12 col-lg-7 text-white">
                <div class="mb-3">
                    <span class="sale-flash-badge">
                        <span class="material-symbols-outlined" style="font-size:16px;">bolt</span>
                        FLASH SALE · SỐ LƯỢNG CÓ HẠN
                    </span>
                </div>
                <h1 class="fw-bold mb-3" style="font-family:var(--font-display); font-size:clamp(2.2rem,5vw,3.8rem); line-height:1.05;">
                    🔥 Săn Sale<br><span style="color:#fda085;">Chớp Nhoáng</span>
                </h1>
                <p style="font-size:1.05rem; opacity:0.8; max-width:480px; line-height:1.7;" class="mb-4">
                    Hàng trăm nhạc cụ chính hãng đang giảm giá sốc đến
                    <strong class="text-warning"><?= empty($saleProducts) ? '0' : max(array_column($saleProducts, 'PhanTramGiamGia')) ?>%</strong>.
                    Cơ hội vàng cho người yêu âm nhạc!
                </p>
                <!-- Countdown -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="font-size:0.85rem; opacity:0.65; margin-right:4px;">Kết thúc sau:</span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="sale-timer-block"><div class="sale-timer-num" id="ss-hours">05</div><div class="sale-timer-label">Giờ</div></div>
                        <span class="sale-timer-sep">:</span>
                        <div class="sale-timer-block"><div class="sale-timer-num" id="ss-minutes">23</div><div class="sale-timer-label">Phút</div></div>
                        <span class="sale-timer-sep">:</span>
                        <div class="sale-timer-block"><div class="sale-timer-num" id="ss-seconds">47</div><div class="sale-timer-label">Giây</div></div>
                    </div>
                </div>
            </div>
            <!-- Right: Big number stat -->
            <div class="col-12 col-lg-5 d-none d-lg-flex justify-content-center">
                <div class="text-center" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:24px; padding:2.5rem 3rem; backdrop-filter:blur(16px);">
                    <div style="font-family:var(--font-display); font-size:6rem; font-weight:800; line-height:1; background:linear-gradient(135deg,#e63946,#fda085); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">
                        <?= count($saleProducts) ?>
                    </div>
                    <div style="color:rgba(255,255,255,0.7); font-size:1rem; letter-spacing:0.1em; text-transform:uppercase; font-weight:600; margin-top:8px;">Sản phẩm đang Sale</div>
                    <div class="mt-3 d-flex justify-content-center gap-3">
                        <div style="text-align:center;">
                            <div style="font-family:var(--font-display); font-size:1.5rem; font-weight:800; color:#fda085;">30%</div>
                            <div style="font-size:0.7rem; color:rgba(255,255,255,0.5); text-transform:uppercase;">Giảm tối đa</div>
                        </div>
                        <div style="width:1px; background:rgba(255,255,255,0.15);"></div>
                        <div style="text-align:center;">
                            <div style="font-family:var(--font-display); font-size:1.5rem; font-weight:800; color:#6ee7b7;">Free</div>
                            <div style="font-size:0.7rem; color:rgba(255,255,255,0.5); text-transform:uppercase;">Vận chuyển</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== STATS BAR ===== -->
<div class="sale-stats-bar">
    <div class="container-max-custom px-desktop-custom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="sale-stat-item">
                    <div class="sale-stat-icon" style="background:#fff0f1;"><span class="material-symbols-outlined text-danger" style="font-size:18px;">local_fire_department</span></div>
                    <span><strong class="text-danger"><?= count($saleProducts) ?></strong> sản phẩm đang sale</span>
                </div>
                <div class="sale-stat-item">
                    <div class="sale-stat-icon" style="background:#fffbeb;"><span class="material-symbols-outlined text-warning" style="font-size:18px;">bolt</span></div>
                    <span>Cập nhật hàng ngày</span>
                </div>
                <div class="sale-stat-item">
                    <div class="sale-stat-icon" style="background:#f0fdf4;"><span class="material-symbols-outlined text-success" style="font-size:18px;">local_shipping</span></div>
                    <span>Miễn phí vận chuyển</span>
                </div>
                <div class="sale-stat-item">
                    <div class="sale-stat-icon" style="background:#eff6ff;"><span class="material-symbols-outlined text-primary" style="font-size:18px;">verified</span></div>
                    <span>Hàng chính hãng 100%</span>
                </div>
            </div>
            <!-- Sort -->
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:0.82rem; color:#888; white-space:nowrap;">Sắp xếp:</span>
                <select class="form-select form-select-sm border-0 fw-semibold" style="width:auto; background:#f5f5f5;" onchange="window.location.href=this.value">
                    <option value="<?= BASE_URL ?>index.php?act=sansale" <?= !isset($_GET['sort']) ? 'selected' : '' ?>>Giảm nhiều nhất</option>
                    <option value="<?= BASE_URL ?>index.php?act=sansale&sort=price-asc" <?= (isset($_GET['sort'])&&$_GET['sort']==='price-asc')?'selected':'' ?>>Giá tăng dần</option>
                    <option value="<?= BASE_URL ?>index.php?act=sansale&sort=price-desc" <?= (isset($_GET['sort'])&&$_GET['sort']==='price-desc')?'selected':'' ?>>Giá giảm dần</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- ===== MAIN CONTENT ===== -->
<main class="w-100 py-5" style="background:#f6f6f8; min-height:60vh;">
    <div class="container-max-custom px-desktop-custom">

        <!-- Filter pills -->
        <div class="d-flex align-items-center gap-2 flex-wrap mb-5">
            <span style="font-size:0.82rem; color:#888; font-weight:600;">Lọc theo mức giảm:</span>
            <a href="<?= BASE_URL ?>index.php?act=sansale" class="sale-filter-pill <?= !isset($_GET['discount']) ? 'active' : '' ?>">Tất cả</a>
            <a href="<?= BASE_URL ?>index.php?act=sansale&discount=10" class="sale-filter-pill <?= (isset($_GET['discount'])&&$_GET['discount']=='10')?'active':'' ?>">≥ 10% OFF</a>
            <a href="<?= BASE_URL ?>index.php?act=sansale&discount=20" class="sale-filter-pill <?= (isset($_GET['discount'])&&$_GET['discount']=='20')?'active':'' ?>">≥ 20% OFF</a>
            <a href="<?= BASE_URL ?>index.php?act=sansale&discount=30" class="sale-filter-pill <?= (isset($_GET['discount'])&&$_GET['discount']=='30')?'active':'' ?>">≥ 30% OFF</a>
        </div>

        <?php if (empty($saleProducts)): ?>
        <div class="sale-empty-state">
            <div style="font-size:5rem; margin-bottom:1rem;">😴</div>
            <h3 style="font-family:var(--font-display); color:#1a1a2e; margin-bottom:0.5rem;">Chưa có sản phẩm sale</h3>
            <p class="text-muted" style="max-width:380px; margin:0 auto 1.5rem;">Hãy quay lại sau nhé! Chúng tôi cập nhật deal mới mỗi ngày.</p>
            <a href="<?= BASE_URL ?>index.php" class="btn btn-secondary-custom px-5 py-2 rounded-pill shadow fw-bold">Về trang chủ</a>
        </div>

        <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($saleProducts as $prod):
                $salePrice     = $prod['GiaTien'] * (1 - $prod['PhanTramGiamGia'] / 100);
                $savingAmount  = $prod['GiaTien'] - $salePrice;
                $isFav         = isset($userFavorites) && in_array($prod['Ma_SanPham'], $userFavorites);
            ?>
            <div class="col">
                <div class="sale-card">
                    <!-- Image -->
                    <div class="sale-card-img-wrap">
                        <!-- Discount badge -->
                        <div class="sale-disc-badge">
                            <span class="sale-disc-badge-num">-<?= $prod['PhanTramGiamGia'] ?></span>
                            <span class="sale-disc-badge-pct">%</span>
                        </div>
                        <!-- Fav button -->
                        <button class="sale-fav-btn" onclick="toggleFavorite(<?= $prod['Ma_SanPham'] ?>, this, event)" title="Yêu thích">
                            <span class="material-symbols-outlined <?= $isFav ? 'text-danger' : '' ?>"
                                style="font-variation-settings:'FILL' <?= $isFav?1:0 ?>, 'wght' 400, 'GRAD' 0, 'opsz' 24; font-size:19px;">favorite</span>
                        </button>
                        <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?= $prod['Ma_SanPham'] ?>">
                            <img src="<?= BASE_URL ?>view/image/<?= htmlspecialchars($prod['Anh']) ?>"
                                 alt="<?= htmlspecialchars($prod['TenSanPham']) ?>" loading="lazy" />
                        </a>
                    </div>

                    <!-- Body -->
                    <div class="sale-card-body">
                        <div class="sale-card-brand"><?= htmlspecialchars($prod['ThuongHieu'] ?? '') ?></div>
                        <div class="sale-card-name">
                            <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?= $prod['Ma_SanPham'] ?>">
                                <?= htmlspecialchars($prod['TenSanPham']) ?>
                            </a>
                        </div>

                        <!-- Price box -->
                        <div class="sale-price-box">
                            <div class="sale-price-old"><?= number_format($prod['GiaTien'], 0, ',', '.') ?>₫</div>
                            <div class="sale-price-new"><?= number_format($salePrice, 0, ',', '.') ?>₫</div>
                            <div class="sale-saving-tag">
                                <span class="material-symbols-outlined" style="font-size:13px;">savings</span>
                                Tiết kiệm: <strong><?= number_format($savingAmount, 0, ',', '.') ?>₫</strong>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?= $prod['Ma_SanPham'] ?>" class="sale-card-btn">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Sale page countdown (separate IDs from homepage)
(function() {
    let h = 5, m = 23, s = 47;
    setInterval(() => {
        s--;
        if (s < 0) { s = 59; m--; }
        if (m < 0) { m = 59; h--; }
        if (h < 0) { h = 5; m = 59; s = 59; }
        const eh = document.getElementById('ss-hours');
        const em = document.getElementById('ss-minutes');
        const es = document.getElementById('ss-seconds');
        if (eh) eh.textContent = String(h).padStart(2,'0');
        if (em) em.textContent = String(m).padStart(2,'0');
        if (es) es.textContent = String(s).padStart(2,'0');
    }, 1000);
})();
</script>
