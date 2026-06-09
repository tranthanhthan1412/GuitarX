<main class="w-100 py-5 bg-light" style="min-height: 70vh;">
    <div class="container-max-custom px-desktop-custom">
        <h1 class="font-display-lg fw-bold mb-4">Giỏ hàng của bạn</h1>

        <?php if (empty($cartDetails)): ?>
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white">
                <span class="material-symbols-outlined text-muted mb-3" style="font-size: 64px;">production_quantity_limits</span>
                <h3 class="font-headline-md text-dark mb-2">Giỏ hàng trống</h3>
                <p class="text-muted font-body-md mb-4">Bạn chưa có sản phẩm nào trong giỏ hàng. Cùng mua sắm nhé!</p>
                <a href="/GuitarX/index.php" class="btn btn-secondary-custom px-4 py-2 font-headline-sm rounded-2">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Danh sách sản phẩm -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Sản phẩm</th>
                                        <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Đơn giá</th>
                                        <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Số lượng</th>
                                        <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted text-end">Thành tiền</th>
                                        <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartDetails as $item): ?>
                                        <tr>
                                            <td class="p-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-surface-container-low rounded-2 d-flex align-items-center justify-content-center p-2" style="width: 80px; height: 80px;">
                                                        <img src="/GuitarX/view/image/<?php echo htmlspecialchars($item['Image']); ?>" alt="Product" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                                    </div>
                                                    <div>
                                                        <h5 class="font-body-md fw-bold mb-1">
                                                            <a href="/GuitarX/index.php?act=chitiet&id=<?php echo $item['Product_ID']; ?>" class="text-dark text-decoration-none hover-text-danger">
                                                                <?php echo htmlspecialchars($item['ProductName']); ?>
                                                            </a>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center font-body-md fw-semibold">
                                                <?php echo number_format($item['Price'], 0, ',', '.'); ?>₫
                                            </td>
                                            <td class="text-center">
                                                <form action="/GuitarX/index.php?act=capnhatgiohang" method="POST" class="d-inline-flex align-items-center">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['Product_ID']; ?>">
                                                    <div class="input-group input-group-sm" style="width: 100px;">
                                                        <button class="btn btn-outline-secondary" type="submit" name="quantity" value="<?php echo $item['Quantity'] - 1; ?>">-</button>
                                                        <input type="number" class="form-control text-center px-1" value="<?php echo $item['Quantity']; ?>" readonly>
                                                        <button class="btn btn-outline-secondary" type="submit" name="quantity" value="<?php echo $item['Quantity'] + 1; ?>" <?php echo ($item['Quantity'] >= $item['MaxCount']) ? 'disabled' : ''; ?>>+</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-end font-body-md fw-bold text-secondary-custom">
                                                <?php echo number_format($item['Subtotal'], 0, ',', '.'); ?>₫
                                            </td>
                                            <td class="text-center">
                                                <a href="/GuitarX/index.php?act=xoagiohang&id=<?php echo $item['Product_ID']; ?>" class="btn btn-sm btn-light text-danger rounded-circle p-2" title="Xóa">
                                                    <span class="material-symbols-outlined fs-6 d-block">delete</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt đơn hàng -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 sticky-top" style="top: 100px;">
                        <h3 class="font-headline-sm fw-bold border-bottom pb-3 mb-4 text-uppercase">Tóm tắt đơn hàng</h3>
                        
                        <?php 
                        $discount = 0;
                        if (isset($_SESSION['applied_voucher'])) {
                            $discount = $_SESSION['applied_voucher']['discount_value'];
                        }
                        $finalTotal = max(0, $totalAmount - $discount);
                        ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-body-md text-muted">Tạm tính:</span>
                            <span class="font-body-md fw-bold"><?php echo number_format($totalAmount, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="font-body-md text-muted">Giảm giá:</span>
                            <span class="font-body-md fw-bold text-success">-<?php echo number_format($discount, 0, ',', '.'); ?>₫</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="font-headline-sm fw-bold">Tổng thanh toán:</span>
                            <span class="font-display-sm fw-bold text-secondary-custom fs-4"><?php echo number_format($finalTotal, 0, ',', '.'); ?>₫</span>
                        </div>

                        <a href="/GuitarX/index.php?act=thanhtoan" class="btn btn-secondary-custom w-100 py-3 font-headline-sm rounded-2 shadow-sm text-center text-decoration-none d-block">
                            TIẾN HÀNH THANH TOÁN
                        </a>
                        <div class="text-center mt-3">
                            <a href="/GuitarX/index.php" class="text-decoration-none font-label-sm text-primary-custom fw-bold">
                                <span class="material-symbols-outlined align-middle fs-6 me-1">arrow_back</span>Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<style>
.hover-text-danger { transition: color 0.2s ease; }
.hover-text-danger:hover { color: var(--color-secondary) !important; }
</style>
