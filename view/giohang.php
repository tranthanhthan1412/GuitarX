<main class="w-100 py-5 bg-light" style="min-height: 70vh;">
    <div class="container-max-custom px-desktop-custom">
        <h1 class="font-display-lg fw-bold mb-4">Giỏ hàng của bạn</h1>

        <?php if (empty($cartDetails)): ?>
        <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white">
            <span class="material-symbols-outlined text-muted mb-3"
                style="font-size: 64px;">production_quantity_limits</span>
            <h3 class="font-headline-md text-dark mb-2">Giỏ hàng trống</h3>
            <p class="text-muted font-body-md mb-4">Bạn chưa có sản phẩm nào trong giỏ hàng. Cùng mua sắm nhé!</p>
            <a href="<?= BASE_URL ?>index.php"
                class="btn btn-secondary-custom px-4 py-2 font-headline-sm rounded-2">Tiếp tục mua sắm</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted">Sản phẩm
                                    </th>
                                    <th scope="col"
                                        class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Đơn giá
                                    </th>
                                    <th scope="col"
                                        class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Số lượng
                                    </th>
                                    <th scope="col" class="py-3 px-4 font-label-sm text-uppercase text-muted text-end">
                                        Thành tiền</th>
                                    <th scope="col"
                                        class="py-3 px-4 font-label-sm text-uppercase text-muted text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartDetails as $item): ?>
                                <tr>
                                    <td class="p-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-surface-container-low rounded-2 d-flex align-items-center justify-content-center p-2"
                                                style="width: 80px; height: 80px;">
                                                <img src="<?= BASE_URL ?>view/image/<?php echo htmlspecialchars($item['Anh']); ?>"
                                                    alt="Product" class="img-fluid"
                                                    style="max-height: 100%; object-fit: contain;">
                                            </div>
                                            <div>
                                                <h5 class="font-body-md fw-bold mb-1">
                                                    <a href="<?= BASE_URL ?>index.php?act=chitiet&id=<?php echo $item['Ma_SanPham']; ?>"
                                                        class="text-dark text-decoration-none hover-text-danger">
                                                        <?php echo htmlspecialchars($item['TenSanPham']); ?>
                                                    </a>
                                                </h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center font-body-md fw-semibold">
                                        <?php echo number_format($item['GiaTien'], 0, ',', '.'); ?>₫
                                    </td>
                                    <td class="text-center">
                                        <form action="<?= BASE_URL ?>index.php?act=capnhatgiohang" method="POST"
                                            class="d-inline-flex align-items-center" novalidate>
                                            <input type="hidden" name="product_id"
                                                value="<?php echo $item['Ma_SanPham']; ?>">
                                            <div class="input-group input-group-sm" style="width: 110px;">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="handleButtonClick(this, 'down')">-</button>

                                                <input type="number" name="quantity"
                                                    class="form-control text-center px-1 quantity-input"
                                                    value="<?php echo $item['SoLuong']; ?>" min="1"
                                                    data-old-val="<?php echo $item['SoLuong']; ?>"
                                                    data-max="<?php echo $item['MaxCount']; ?>"
                                                    onchange="validateQuantity(this)">

                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="handleButtonClick(this, 'up')">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-end font-body-md fw-bold text-secondary-custom">
                                        <?php echo number_format($item['Subtotal'], 0, ',', '.'); ?>₫
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>index.php?act=xoagiohang&id=<?php echo $item['Ma_SanPham']; ?>"
                                            class="btn btn-sm btn-light text-danger rounded-circle p-2" title="Xóa">
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

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 sticky-top" style="top: 100px;">
                    <h3 class="font-headline-sm fw-bold border-bottom pb-3 mb-4 text-uppercase">Tóm tắt đơn hàng</h3>

                    <?php 
                        $discount = 0;
                        if (isset($_SESSION['applied_voucher'])) {
                            $discount = $_SESSION['applied_voucher']['GiaTriGiam'];
                        }
                        $finalTotal = max(0, $totalAmount - $discount);
                        ?>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="font-body-md text-muted">Tạm tính:</span>
                        <span
                            class="font-body-md fw-bold"><?php echo number_format($totalAmount, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="font-body-md text-muted">Giảm giá:</span>
                        <span
                            class="font-body-md fw-bold text-success">-<?php echo number_format($discount, 0, ',', '.'); ?>₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="font-headline-sm fw-bold">Tổng thanh toán:</span>
                        <span
                            class="font-display-sm fw-bold text-secondary-custom fs-4"><?php echo number_format($finalTotal, 0, ',', '.'); ?>₫</span>
                    </div>

                    <a href="<?= BASE_URL ?>index.php?act=thanhtoan"
                        class="btn btn-secondary-custom w-100 py-3 font-headline-sm rounded-2 shadow-sm text-center text-decoration-none d-block">
                        TIẾN HÀNH THANH TOÁN
                    </a>
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>index.php"
                            class="text-decoration-none font-label-sm text-primary-custom fw-bold">
                            <span class="material-symbols-outlined align-middle fs-6 me-1">arrow_back</span>Tiếp tục mua
                            sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Khởi tạo lưu giá trị hợp lệ ban đầu cho toàn bộ ô input khi trang vừa load xong
document.querySelectorAll('.quantity-input').forEach(input => {
    // Ép kiểu về int để lưu chính xác vào data bộ nhớ tạm
    input.dataset.oldVal = parseInt(input.value) || 1;

    // CHẶN SUBMIT FORM KHI ẤN ENTER VÀ CHẶN PHÍM ĐẶC BIỆT (KeyDown)
    input.addEventListener('keydown', function(e) {
        // 1. Nếu bấm Enter, chặn đứng hành vi submit mặc định làm reload trang
        if (e.key === 'Enter') {
            e.preventDefault();
            this
                .blur(); // Ép ô input mất focus để kích hoạt onchange (validateQuantity) một cách an toàn
            return;
        }

        // 2. Chặn ký tự đặc biệt dấu âm, dấu cộng, chữ e
        if (e.key === '-' || e.key === '+' || e.key === 'e' || e.key === 'E' || e.key === '.' || e
            .key === ',') {
            e.preventDefault();
        }
    });

    // XỬ LÝ KHI KHÁCH GÕ SỐ (Sự kiện 'input' chạy real-time)
    input.addEventListener('input', function() {
        let currentVal = this.value;
        let maxStock = parseInt(this.getAttribute('data-max'));
        let oldVal = parseInt(this.dataset.oldVal) || 1;

        // Nếu khách xóa trống ô input lúc đang gõ, tạm thời bỏ qua để họ gõ số tiếp
        if (currentVal === '') return;

        let intVal = parseInt(currentVal);

        // Trường hợp gõ số âm hoặc bằng 0
        if (isNaN(intVal) || intVal <= 0) {
            this.value = oldVal; // Ép giao diện về số cũ ngay lập tức
            Swal.fire({
                icon: 'error',
                title: 'Lỗi số lượng!',
                text: 'Số lượng nhập không hợp lệ. Vui lòng nhập số lượng lớn hơn 0!',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // Trường hợp gõ vượt quá số lượng kho hàng
        if (intVal > maxStock) {
            this.value = oldVal; // Ép giao diện về số cũ ngay lập tức
            Swal.fire({
                icon: 'warning',
                title: 'Vượt quá số lượng kho!',
                text: `Xin lỗi, trong kho của GuitarX chỉ còn lại ${maxStock} cây!`,
                confirmButtonColor: '#ffc107'
            });
            return;
        }

        // Nếu số gõ vào hợp lệ, cập nhật mốc bộ nhớ đệm
        this.dataset.oldVal = intVal;
    });
});

// HÀM CHỈ CHẠY KHI CLICK RA NGOÀI HOẶC SAU KHI Ô INPUT BỊ BLUR (An toàn tuyệt đối)
function validateQuantity(input) {
    let currentVal = input.value;
    let oldVal = parseInt(input.dataset.oldVal) || 1;

    // Nếu trống (do người dùng xóa hết rồi click ra ngoài)
    if (currentVal === '') {
        input.value = oldVal;
        Swal.fire({
            icon: 'error',
            title: 'Lỗi số lượng!',
            text: 'Số lượng không được để trống!',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    // Lúc này mọi giá trị bậy bạ đã bị hàm 'input' chặn đứng, giá trị gửi đi chắc chắn chuẩn
    input.form.submit();
}

// HÀM XỬ LÝ KHI BẤM NÚT CỘNG (+) HOẶC TRỪ (-)
function handleButtonClick(button, direction) {
    let input = button.parentNode.querySelector('input[type=number]');
    let currentVal = parseInt(input.value) || 1;
    let maxStock = parseInt(input.getAttribute('data-max'));

    if (direction === 'up') {
        if (currentVal >= maxStock) {
            Swal.fire({
                icon: 'warning',
                title: 'Vượt quá số lượng kho!',
                text: `Xin lỗi, trong kho của GuitarX chỉ còn lại ${maxStock} cây!`,
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        input.value = currentVal + 1;
    } else if (direction === 'down') {
        if (currentVal <= 1) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi số lượng!',
                text: 'Số lượng nhập không hợp lệ. Vui lòng nhập số lượng lớn hơn 0!',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        input.value = currentVal - 1;
    }

    // Cập nhật lại bộ nhớ đệm giá trị cũ trước khi submit form
    input.dataset.oldVal = input.value;
    input.form.submit();
}
</script>

<style>
.hover-text-danger {
    transition: color 0.2s ease;
}

.hover-text-danger:hover {
    color: var(--color-secondary) !important;
}
</style>