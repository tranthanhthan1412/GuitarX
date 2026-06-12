<main class="w-100 py-5 bg-light" style="min-height: 70vh;">
    <div class="container-max-custom px-desktop-custom">
        
        <?php if ($viewMode === 'list'): ?>
            <h1 class="font-display-md fw-bold mb-4">Lịch sử đơn hàng</h1>
            
            <?php if (empty($ordersList)): ?>
                <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 64px;">receipt_long</span>
                    <h3 class="font-headline-md text-dark mb-2">Bạn chưa có đơn hàng nào</h3>
                    <p class="text-muted font-body-md mb-4">Hãy khám phá các sản phẩm tuyệt vời của chúng tôi và đặt hàng ngay!</p>
                    <a href="/GuitarX/index.php" class="btn btn-secondary-custom px-4 py-2 font-headline-sm rounded-2">Khám phá sản phẩm</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Mã Đơn</th>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Ngày đặt</th>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Tổng tiền</th>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Thanh toán</th>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Trạng thái</th>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordersList as $order): ?>
                                    <tr>
                                        <td class="p-4 font-body-md fw-bold text-dark">#<?php echo $order['Ma_DonHang']; ?></td>
                                        <td class="font-body-md"><?php echo date('d/m/Y H:i', strtotime($order['NgayDatHang'])); ?></td>
                                        <td class="font-body-md fw-bold text-secondary-custom"><?php echo number_format($order['TotalAmount'], 0, ',', '.'); ?>₫</td>
                                        <td class="font-body-sm text-muted"><?php echo htmlspecialchars($order['TenPhuongThuc']); ?></td>
                                        <td>
                                            <?php 
                                            $statusClass = 'bg-secondary';
                                            if ($order['TrangThai'] == 'Pending') $statusClass = 'bg-warning text-dark';
                                            if ($order['TrangThai'] == 'Processing') $statusClass = 'bg-info text-dark';
                                            if ($order['TrangThai'] == 'Completed') $statusClass = 'bg-success';
                                            if ($order['TrangThai'] == 'Cancelled') $statusClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?> rounded-pill px-3 py-2 font-label-sm fw-bold">
                                                <?php echo htmlspecialchars($order['TrangThai']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="/GuitarX/index.php?act=lichsudonhang&id=<?php echo $order['Ma_DonHang']; ?>" class="btn btn-sm btn-outline-secondary rounded-2 font-label-sm fw-bold px-3">
                                                Xem chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php elseif ($viewMode === 'detail'): ?>
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="font-display-md fw-bold mb-0">Chi tiết đơn hàng <span class="text-secondary-custom">#<?php echo $orderId; ?></span></h1>
                <a href="/GuitarX/index.php?act=lichsudonhang" class="btn btn-outline-secondary font-label-sm fw-bold d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">arrow_back</span> Quay lại
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <div class="checkout-items-list">
                    <?php 
                    $grandTotal = 0;
                    foreach ($orderDetails as $item): 
                        $grandTotal += $item['Subtotal'];
                    ?>
                    <div class="d-flex gap-4 mb-4 pb-4 border-bottom align-items-center">
                        <div class="bg-surface-container-low rounded-3 d-flex align-items-center justify-content-center p-2 shadow-sm" style="width: 100px; height: 100px;">
                            <img src="/GuitarX/view/image/<?php echo htmlspecialchars($item['Anh']); ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="font-body-lg fw-bold mb-2"><?php echo htmlspecialchars($item['TenSanPham']); ?></h5>
                            <div class="d-flex align-items-center gap-4">
                                <span class="text-muted font-label-md">Đơn giá: <?php echo number_format($item['GiaTien'], 0, ',', '.'); ?>₫</span>
                                <span class="text-muted font-label-md">Số lượng: x<?php echo $item['SoLuong']; ?></span>
                                <span class="font-body-md fw-bold text-secondary-custom ms-auto"><?php echo number_format($item['Subtotal'], 0, ',', '.'); ?>₫</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-end mt-2">
                    <div style="width: 300px;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-body-md text-muted">Tổng tiền hàng:</span>
                            <span class="font-body-md fw-bold"><?php echo number_format($grandTotal, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="font-body-md text-muted">Phí vận chuyển:</span>
                            <span class="font-body-md fw-bold text-success">Miễn phí</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="font-headline-sm fw-bold">Tổng thanh toán:</span>
                            <span class="font-display-sm fw-bold text-danger fs-4"><?php echo number_format($grandTotal, 0, ',', '.'); ?>₫</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>
