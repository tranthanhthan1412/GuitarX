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
        if ($voucherModel->deleteVoucher($deleteId)) $message = "Đã xóa mã giảm giá thành công.";
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
                if ($voucherModel->addVoucher($code, $value, $qty, $expiry)) $message = "Đã thêm mã giảm giá mới thành công.";
            } elseif ($action === 'edit') {
                if ($voucherModel->updateVoucher($id, $code, $value, $qty, $expiry)) $message = "Đã cập nhật mã giảm giá thành công.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) $error = "Mã giảm giá (Code) này đã tồn tại, vui lòng chọn mã khác.";
            else $error = "Lỗi thao tác cơ sở dữ liệu: " . $e->getMessage();
        }
    }
}

$vouchers = $voucherModel->getAllVouchers();
?>

<!-- Alerts -->
<?php if ($message): ?>
<div class="admin-alert success">
    <span class="material-symbols-outlined">check_circle</span>
    <?= $message ?>
    <button class="close-btn" onclick="this.closest('.admin-alert').remove()">✕</button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert error">
    <span class="material-symbols-outlined">error</span>
    <?= $error ?>
    <button class="close-btn" onclick="this.closest('.admin-alert').remove()">✕</button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <div class="page-title">Quản lý Voucher</div>
        <div class="page-subtitle">Tổng cộng <?= count($vouchers) ?> mã giảm giá</div>
    </div>
    <button class="btn-primary-admin" onclick="openAddModal()">
        <span class="material-symbols-outlined">add</span> Thêm Mã Mới
    </button>
</div>

<!-- Voucher Table -->
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="padding-left:24px;width:60px">ID</th>
                <th>Mã Code</th>
                <th>Mức Giảm</th>
                <th>Số Lượng Tồn</th>
                <th>Ngày Hết Hạn</th>
                <th>Trạng Thái</th>
                <th style="text-align:right;padding-right:24px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vouchers)): ?>
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">
                    <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;opacity:0.3">local_offer</span>
                    Chưa có mã giảm giá nào.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($vouchers as $v): 
                $isExpired = strtotime($v['NgayHetHan']) < strtotime(date('Y-m-d'));
                $isOut = $v['SoLuong'] <= 0;
            ?>
            <tr>
                <td style="padding-left:24px;font-weight:700;color:var(--text-muted)">#<?= $v['Ma_MaGiamGia'] ?></td>
                <td>
                    <span style="background:#0f172a;color:#e63946;padding:5px 14px;border-radius:6px;font-size:0.82rem;font-weight:800;letter-spacing:2px;font-family:monospace">
                        <?= htmlspecialchars($v['Ma']) ?>
                    </span>
                </td>
                <td style="font-weight:700;color:#10b981"><?= number_format($v['GiaTriGiam'], 0, ',', '.') ?>₫</td>
                <td>
                    <span style="font-weight:700;color:<?= $isOut ? '#e63946' : 'var(--text-primary)' ?>"><?= $v['SoLuong'] ?></span>
                </td>
                <td style="color:var(--text-muted)"><?= date('d/m/Y', strtotime($v['NgayHetHan'])) ?></td>
                <td>
                    <?php if ($isExpired): ?>
                        <span class="status-badge cancelled">Hết hạn</span>
                    <?php elseif ($isOut): ?>
                        <span class="status-badge" style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">Hết lượt</span>
                    <?php else: ?>
                        <span class="status-badge active-badge">Đang hoạt động</span>
                    <?php endif; ?>
                </td>
                <td style="text-align:right;padding-right:24px">
                    <div style="display:inline-flex;gap:6px">
                        <button class="btn-icon primary" onclick='openEditModal(<?= json_encode($v) ?>)' title="Sửa">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <a href="index.php?act=quanlyvoucher&delete_id=<?= $v['Ma_MaGiamGia'] ?>"
                            class="btn-icon danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này không?')" title="Xóa" style="text-decoration:none">
                            <span class="material-symbols-outlined">delete</span>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Add/Edit Voucher -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?act=quanlyvoucher" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="voucherModalLabel">Thêm Mã Giảm Giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="voucher_id" id="voucherId" value="">
                    <div class="mb-3">
                        <label class="form-label">Mã Code (Chữ & Số) *</label>
                        <input type="text" class="form-control text-uppercase" name="code" id="voucherCode" placeholder="VD: GUITAR2026" required style="font-family:monospace;font-weight:700;letter-spacing:2px">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức Giảm Giá (VNĐ) *</label>
                        <input type="number" class="form-control" name="value" id="voucherValue" min="0" required placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số Lượng Có Sẵn *</label>
                        <input type="number" class="form-control" name="quantity" id="voucherQty" min="0" required placeholder="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày Hết Hạn *</label>
                        <input type="date" class="form-control" name="expiry" id="voucherExpiry" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-primary-admin" id="btnSubmitForm">Lưu</button>
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
    new bootstrap.Modal(document.getElementById('voucherModal')).show();
}
function openEditModal(voucher) {
    document.getElementById('voucherModalLabel').innerText = 'Chỉnh Sửa Mã Giảm Giá';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('voucherId').value = voucher.Ma_MaGiamGia;
    document.getElementById('voucherCode').value = voucher.Ma;
    document.getElementById('voucherValue').value = voucher.GiaTriGiam;
    document.getElementById('voucherQty').value = voucher.SoLuong;
    document.getElementById('voucherExpiry').value = voucher.NgayHetHan;
    document.getElementById('btnSubmitForm').innerText = 'Lưu thay đổi';
    new bootstrap.Modal(document.getElementById('voucherModal')).show();
}
</script>
