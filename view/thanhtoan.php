<main class="w-100 py-5 bg-light" style="min-height: 70vh;">
    <div class="container-max-custom px-desktop-custom">
        <h1 class="font-display-lg fw-bold mb-4">Thanh toán đơn hàng</h1>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger font-body-md"><i
                class="material-symbols-outlined align-middle fs-5 me-1">error</i><?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>index.php?act=thanhtoan" method="POST">
            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h3 class="font-headline-sm fw-bold mb-4 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-secondary-custom">local_shipping</span>
                            Thông tin giao hàng
                        </h3>

                        <div class="mb-3">
                            <label class="form-label font-label-sm fw-bold d-flex align-items-center gap-2">
                                Họ và tên người nhận
                                <?php if (isset($userRank) && !empty($userRank['name'])): ?>
                                <span class="<?php echo htmlspecialchars($userRank['class']); ?>">
                                    <?php echo htmlspecialchars($userRank['name']); ?>
                                </span>
                                <?php endif; ?>
                            </label>
                            <input type="text" class="form-control py-2 shadow-none"
                                value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-label-sm fw-bold">Địa chỉ chi tiết <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control py-2 shadow-none"
                                placeholder="Số nhà, Tên đường, Phường/Xã..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-label-sm fw-bold">Tỉnh / Thành phố <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control py-2 shadow-none"
                                placeholder="Ví dụ: TP. Hồ Chí Minh" required>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h3 class="font-headline-sm fw-bold mb-4 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-secondary-custom">payments</span>
                            Phương thức thanh toán
                        </h3>

                        <?php foreach ($paymentMethods as $index => $pm): ?>
                        <div
                            class="form-check mb-3 p-3 border rounded-2 <?php echo $index === 0 ? 'border-secondary-custom bg-surface-container-low' : ''; ?>">
                            <input class="form-check-input ms-1 mt-1" type="radio" name="payment_method"
                                id="pm_<?php echo $pm['Ma_PhuongThuc']; ?>" value="<?php echo $pm['Ma_PhuongThuc']; ?>"
                                <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label font-body-md fw-bold ms-2"
                                for="pm_<?php echo $pm['Ma_PhuongThuc']; ?>">
                                <?php echo htmlspecialchars($pm['TenPhuongThuc']); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 sticky-top" style="top: 100px;">
                        <h3 class="font-headline-sm fw-bold border-bottom pb-3 mb-4 text-uppercase">Đơn hàng của bạn
                        </h3>

                        <div class="checkout-items-list mb-4" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($cartDetails as $item): ?>
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                <div class="bg-light rounded-2 d-flex align-items-center justify-content-center p-1"
                                    style="width: 64px; height: 64px;">
                                    <img src="<?= BASE_URL ?>view/image/<?php echo htmlspecialchars($item['Anh']); ?>"
                                        class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="font-body-sm fw-bold mb-1">
                                        <?php echo htmlspecialchars($item['TenSanPham']); ?></h6>
                                    <div class="text-muted font-label-sm mb-1">Số lượng:
                                        <?php echo $item['SoLuong']; ?></div>
                                    <div class="font-body-sm fw-bold text-secondary-custom">
                                        <?php echo number_format($item['Subtotal'], 0, ',', '.'); ?>₫</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-4 pb-3 border-bottom">
                            <label class="form-label font-label-sm fw-bold text-dark">Mã giảm giá</label>
                            <div class="input-group">
                                <input type="text" id="voucherCodeInput" class="form-control shadow-none"
                                    placeholder="Nhập mã voucher..."
                                    value="<?php echo htmlspecialchars($_SESSION['applied_voucher']['code'] ?? ''); ?>"
                                    <?php echo isset($_SESSION['applied_voucher']) ? 'readonly' : ''; ?>>
                                <button class="btn btn-outline-secondary" type="button" id="btnApplyVoucher"
                                    style="display: <?php echo isset($_SESSION['applied_voucher']) ? 'none' : 'block'; ?>;">Áp
                                    dụng</button>
                                <button class="btn btn-danger" type="button" id="btnRemoveVoucher"
                                    style="display: <?php echo isset($_SESSION['applied_voucher']) ? 'block' : 'none'; ?>;">Hủy</button>
                            </div>
                            <div id="voucherFeedback" class="form-text mt-1" style="display: none;"></div>
                        </div>

                        <?php 
                        // --- LOGIC ĐỊNH DẠNG TÍNH TOÁN HIỂN THỊ TIỀN CHUẨN ---
                        // Giả sử Controller truyền qua: $subTotal (Giá gốc 200k), $totalAmount (Giá sau khi trừ tiền Rank 180k)
                        // Nếu Controller chưa tạo $subTotal, dùng tạm giá trị tính toán phòng hờ
                        $realSubTotal = isset($subTotal) ? $subTotal : ($totalAmount / (1 - (($userRank['discount'] ?? 0) / 100)));
                        $rankDiscountAmount = max(0, $realSubTotal - $totalAmount);

                        $voucherDiscount = 0;
                        $appliedVoucherCode = '';
                        if (isset($_SESSION['applied_voucher'])) {
                            $voucherDiscount = $_SESSION['applied_voucher']['GiaTriGiam'];
                            $appliedVoucherCode = $_SESSION['applied_voucher']['code'];
                        }
                        $finalTotal = max(0, $totalAmount - $voucherDiscount);
                        ?>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-body-md text-muted">Tạm tính:</span>
                            <span
                                class="font-body-md fw-bold"><?php echo number_format($realSubTotal, 0, ',', '.'); ?>₫</span>
                        </div>

                        <?php if ($rankDiscountAmount > 0): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-body-md text-muted">Ưu đãi Rank (<?php echo $userRank['name']; ?>
                                -<?php echo $userRank['discount']; ?>%):</span>
                            <span
                                class="font-body-md fw-bold text-success">-<?php echo number_format($rankDiscountAmount, 0, ',', '.'); ?>₫</span>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-3" id="discountRow"
                            style="display: <?php echo $voucherDiscount > 0 ? 'flex' : 'none'; ?> !important;">
                            <span class="font-body-md text-muted">Voucher giảm <span
                                    id="lblVoucherCode"><?php echo $appliedVoucherCode ? "($appliedVoucherCode)" : ""; ?></span>:</span>
                            <span class="font-body-md fw-bold text-danger"
                                id="lblDiscount">-<?php echo number_format($voucherDiscount, 0, ',', '.'); ?>₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="font-body-md text-muted">Phí giao hàng:</span>
                            <span class="font-body-md fw-bold text-success">Miễn phí</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="font-headline-sm fw-bold">Tổng thanh toán:</span>
                            <span class="font-display-sm fw-bold text-danger fs-4"
                                id="lblFinalTotal"><?php echo number_format($finalTotal, 0, ',', '.'); ?>₫</span>
                        </div>

                        <button type="submit"
                            class="btn btn-secondary-custom w-100 py-3 font-headline-sm rounded-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">check_circle</span>
                            ĐẶT HÀNG NGAY
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnApply = document.getElementById('btnApplyVoucher');
    const btnRemove = document.getElementById('btnRemoveVoucher');
    const txtInput = document.getElementById('voucherCodeInput');
    const feedback = document.getElementById('voucherFeedback');
    const discountRow = document.getElementById('discountRow');
    const lblVoucherCode = document.getElementById('lblVoucherCode');
    const lblDiscount = document.getElementById('lblDiscount');
    const lblFinalTotal = document.getElementById('lblFinalTotal');
    const baseTotal = <?php echo $totalAmount; ?>; // Tiền gốc tính coupon dựa trên tiền sau giảm rank

    if (btnApply) {
        btnApply.addEventListener('click', function() {
            const code = txtInput.value.trim();
            if (!code) {
                feedback.className = "form-text text-danger mt-1";
                feedback.innerText = "Vui lòng nhập mã giảm giá.";
                feedback.style.display = "block";
                return;
            }

            feedback.style.display = "none";

            fetch('<?= BASE_URL ?>index.php?act=apply_voucher', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'code=' + encodeURIComponent(code)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        feedback.className = "form-text text-success mt-1 fw-bold";
                        feedback.innerText = data.message;
                        feedback.style.display = "block";

                        txtInput.setAttribute('readonly', 'true');
                        btnApply.style.display = 'none';
                        btnRemove.style.display = 'block';

                        const discount = parseFloat(data.discount);
                        const finalTotal = Math.max(0, baseTotal - discount);

                        discountRow.style.setProperty('display', 'flex', 'important');
                        lblVoucherCode.innerText = '(' + data.code + ')';
                        lblDiscount.innerText = '-' + discount.toLocaleString('vi-VN') + '₫';
                        lblFinalTotal.innerText = finalTotal.toLocaleString('vi-VN') + '₫';
                    } else {
                        feedback.className = "form-text text-danger mt-1";
                        feedback.innerText = data.message;
                        feedback.style.display = "block";
                    }
                })
                .catch(err => {
                    console.error(err);
                    feedback.className = "form-text text-danger mt-1";
                    feedback.innerText = "Có lỗi xảy ra khi áp dụng mã giảm giá.";
                    feedback.style.display = "block";
                });
        });
    }

    if (btnRemove) {
        btnRemove.addEventListener('click', function() {
            fetch('<?= BASE_URL ?>index.php?act=remove_voucher', {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        feedback.style.display = "none";
                        txtInput.value = '';
                        txtInput.removeAttribute('readonly');
                        btnApply.style.display = 'block';
                        btnRemove.style.display = 'none';

                        discountRow.style.setProperty('display', 'none', 'important');
                        lblFinalTotal.innerText = baseTotal.toLocaleString('vi-VN') + '₫';
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        });
    }
});
</script>