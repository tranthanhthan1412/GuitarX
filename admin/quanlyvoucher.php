<?php
require_once '../model/database.php';
require_once '../model/m_voucher.php';

$db = (new Database())->getConnection();
$voucherModel = new VoucherModel($db);

$message = '';
$error = '';

// Xử lý Xóa Voucher
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    try {
        if ($voucherModel->deleteVoucher($deleteId)) {
            $message = "Đã xóa mã giảm giá thành công.";
        }
    } catch (Exception $e) {
        $error = "Không thể xóa mã giảm giá này vì nó đã được sử dụng trong hóa đơn.";
    }
}

// Xử lý Thêm / Sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['voucher_id'] ?? 0;
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $value = trim($_POST['value'] ?? 0);
    $qty = trim($_POST['quantity'] ?? 0);
    $expiry = trim($_POST['expiry'] ?? '');

    if (empty($code) || empty($expiry)) {
        $error = "Mã Voucher và Ngày hết hạn không được để trống.";
    } else {
        try {
            if ($action === 'add') {
                if ($voucherModel->addVoucher($code, $value, $qty, $expiry)) {
                    $message = "Đã thêm mã giảm giá mới thành công.";
                }
            } elseif ($action === 'edit') {
                if ($voucherModel->updateVoucher($id, $code, $value, $qty, $expiry)) {
                    $message = "Đã cập nhật mã giảm giá thành công.";
                }
            }
        } catch (PDOException $e) {
            // Lỗi trùng lặp mã Code (UNIQUE constraint)
            if ($e->getCode() == 23000) {
                $error = "Mã giảm giá (Code) này đã tồn tại, vui lòng chọn mã khác.";
            } else {
                $error = "Lỗi thao tác cơ sở dữ liệu: " . $e->getMessage();
            }
        }
    }
}

// Lấy danh sách hiển thị
$vouchers = $voucherModel->getAllVouchers();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-display-md m-0">Quản lý Voucher</h2>
    <button class="btn btn-primary-custom d-flex align-items-center gap-2" onclick="openAddModal()">
        <span class="material-symbols-outlined">add</span> Thêm Mã Mới
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Mã Code</th>
                        <th>Mức Giảm</th>
                        <th>Số Lượng Tồn</th>
                        <th>Ngày Hết Hạn</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vouchers)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có mã giảm giá nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($vouchers as $v): ?>
                            <?php 
                                $isExpired = strtotime($v['expiry_date']) < strtotime(date('Y-m-d'));
                                $isOut = $v['quantity'] <= 0;
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?= $v['Vouchers_ID'] ?></td>
                                <td>
                                    <span class="badge bg-dark px-3 py-2 fs-6 text-uppercase" style="letter-spacing: 2px;">
                                        <?= htmlspecialchars($v['Code']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-primary-custom"><?= number_format($v['discount_value'], 0, ',', '.') ?>₫</td>
                                <td><?= $v['quantity'] ?></td>
                                <td><?= date('d/m/Y', strtotime($v['expiry_date'])) ?></td>
                                <td>
                                    <?php if ($isExpired): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Hết hạn</span>
                                    <?php elseif ($isOut): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Hết lượt</span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Đang hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-secondary me-2" 
                                        onclick='openEditModal(<?= json_encode($v) ?>)'
                                        title="Sửa">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                    </button>
                                    <a href="index.php?act=quanlyvoucher&delete_id=<?= $v['Vouchers_ID'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này không?');"
                                       title="Xóa">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Voucher -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="index.php?act=quanlyvoucher" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="voucherModalLabel">Thêm Mã Giảm Giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="voucher_id" id="voucherId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã Code (Chữ & Số) *</label>
                        <input type="text" class="form-control text-uppercase" name="code" id="voucherCode" placeholder="VD: GUITAR2026" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mức Giảm Giá (VNĐ) *</label>
                        <input type="number" class="form-control" name="value" id="voucherValue" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Số Lượng Có Sẵn *</label>
                        <input type="number" class="form-control" name="quantity" id="voucherQty" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ngày Hết Hạn *</label>
                        <input type="date" class="form-control" name="expiry" id="voucherExpiry" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary-custom px-4" id="btnSubmitForm">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('voucherModalLabel').innerText = 'Thêm Mã Giảm Giá Mới';
    document.getElementById('formAction').value = 'add';
    document.getElementById('voucherId').value = '';
    document.getElementById('voucherCode').value = '';
    document.getElementById('voucherValue').value = '';
    document.getElementById('voucherQty').value = '';
    document.getElementById('voucherExpiry').value = '';
    document.getElementById('btnSubmitForm').innerText = 'Thêm mới';
    
    var myModal = new bootstrap.Modal(document.getElementById('voucherModal'));
    myModal.show();
}

function openEditModal(voucher) {
    document.getElementById('voucherModalLabel').innerText = 'Chỉnh Sửa Mã Giảm Giá';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('voucherId').value = voucher.Vouchers_ID;
    document.getElementById('voucherCode').value = voucher.Code;
    document.getElementById('voucherValue').value = voucher.discount_value;
    document.getElementById('voucherQty').value = voucher.quantity;
    document.getElementById('voucherExpiry').value = voucher.expiry_date;
    document.getElementById('btnSubmitForm').innerText = 'Lưu thay đổi';
    
    var myModal = new bootstrap.Modal(document.getElementById('voucherModal'));
    myModal.show();
}
</script>
