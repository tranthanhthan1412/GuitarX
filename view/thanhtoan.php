<main class="w-100 py-5 bg-light" style="min-height: 70vh;">
    <div class="container-max-custom px-desktop-custom">
        <h1 class="font-display-lg fw-bold mb-4">Thanh toán đơn hàng</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger font-body-md"><i class="material-symbols-outlined align-middle fs-5 me-1">error</i><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="/GuitarX/index.php?act=thanhtoan" method="POST">
            <div class="row g-4">
                <!-- Cột thông tin giao hàng & thanh toán -->
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h3 class="font-headline-sm fw-bold mb-4 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-secondary-custom">local_shipping</span>
                            Thông tin giao hàng
                        </h3>
                        
                        <div class="mb-3">
                            <label class="form-label font-label-sm fw-bold">Họ và tên người nhận</label>
                            <input type="text" class="form-control py-2 shadow-none" value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-label-sm fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control py-2 shadow-none" placeholder="Số nhà, Tên đường, Phường/Xã..." required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-label-sm fw-bold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control py-2 shadow-none" placeholder="Ví dụ: TP. Hồ Chí Minh" required>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h3 class="font-headline-sm fw-bold mb-4 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-secondary-custom">payments</span>
                            Phương thức thanh toán
                        </h3>
                        
                        <?php foreach ($paymentMethods as $index => $pm): ?>
                        <div class="form-check mb-3 p-3 border rounded-2 <?php echo $index === 0 ? 'border-secondary-custom bg-surface-container-low' : ''; ?>">
                            <input class="form-check-input ms-1 mt-1" type="radio" name="payment_method" id="pm_<?php echo $pm['PayMent_ID']; ?>" value="<?php echo $pm['PayMent_ID']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label font-body-md fw-bold ms-2" for="pm_<?php echo $pm['PayMent_ID']; ?>">
                                <?php echo htmlspecialchars($pm['MethodName']); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cột tóm tắt đơn hàng -->
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 sticky-top" style="top: 100px;">
                        <h3 class="font-headline-sm fw-bold border-bottom pb-3 mb-4 text-uppercase">Đơn hàng của bạn</h3>
                        
                        <div class="checkout-items-list mb-4" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($cartDetails as $item): ?>
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                <div class="bg-light rounded-2 d-flex align-items-center justify-content-center p-1" style="width: 64px; height: 64px;">
                                    <img src="/GuitarX/view/image/<?php echo htmlspecialchars($item['Image']); ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="font-body-sm fw-bold mb-1"><?php echo htmlspecialchars($item['ProductName']); ?></h6>
                                    <div class="text-muted font-label-sm mb-1">Số lượng: <?php echo $item['Quantity']; ?></div>
                                    <div class="font-body-sm fw-bold text-secondary-custom"><?php echo number_format($item['Subtotal'], 0, ',', '.'); ?>₫</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-body-md text-muted">Tạm tính:</span>
                            <span class="font-body-md fw-bold"><?php echo number_format($totalAmount, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="font-body-md text-muted">Phí giao hàng:</span>
                            <span class="font-body-md fw-bold text-success">Miễn phí</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="font-headline-sm fw-bold">Tổng cộng:</span>
                            <span class="font-display-sm fw-bold text-danger fs-4"><?php echo number_format($totalAmount, 0, ',', '.'); ?>₫</span>
                        </div>

                        <button type="submit" class="btn btn-secondary-custom w-100 py-3 font-headline-sm rounded-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">check_circle</span>
                            ĐẶT HÀNG NGAY
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
