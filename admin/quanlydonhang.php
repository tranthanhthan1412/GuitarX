<?php
require_once '../model/database.php';
require_once '../model/m_donhang.php';

$db = (new Database())->getConnection();
$orderModel = new OrderModel($db);

$message = '';
$error = '';

// Xử lý Cập nhật Trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = intval($_POST['order_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');
    if ($orderId > 0 && !empty($newStatus)) {
        if ($orderModel->updateOrderStatus($orderId, $newStatus)) {
            $message = "Đã cập nhật trạng thái đơn hàng #$orderId thành công.";
        } else {
            $error = "Cập nhật thất bại. (Đơn hàng đã Hủy không thể phục hồi).";
        }
    }
}

// Xử lý AJAX Lấy Chi tiết đơn hàng
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_details' && isset($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);
    $details = $orderModel->getAdminOrderDetails($orderId);
    header('Content-Type: application/json');
    echo json_encode($details);
    exit();
}

$orders = $orderModel->getAllOrders();

function getStatusInfo($status) {
    $map = [
        'Pending'   => ['class' => 'pending',   'label' => 'Đang xử lý'],
        'Shipping'  => ['class' => 'shipping',  'label' => 'Đang giao hàng'],
        'Completed' => ['class' => 'completed', 'label' => 'Hoàn thành'],
        'Cancelled' => ['class' => 'cancelled', 'label' => 'Đã hủy'],
    ];
    return $map[$status] ?? ['class' => 'pending', 'label' => $status];
}
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
        <div class="page-title">Quản lý Đơn Hàng</div>
        <div class="page-subtitle">Theo dõi và cập nhật trạng thái đơn hàng</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <?php
        $countPending = array_filter($orders, fn($o) => $o['TrangThai'] === 'Pending');
        if (count($countPending) > 0):
        ?>
        <div style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;padding:8px 14px;border-radius:8px;font-size:0.82rem;font-weight:600;display:flex;align-items:center;gap:6px">
            <span class="material-symbols-outlined" style="font-size:16px">pending_actions</span>
            <?= count($countPending) ?> đơn cần xử lý
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="padding-left:24px">Mã Đơn</th>
                <th>Khách Hàng</th>
                <th>Phương Thức TT</th>
                <th>Tổng Tiền</th>
                <th>Trạng Thái</th>
                <th style="text-align:right;padding-right:24px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">
                    <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;opacity:0.3">receipt_long</span>
                    Chưa có đơn hàng nào.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($orders as $o): ?>
            <?php $si = getStatusInfo($o['TrangThai']); $locked = in_array($o['TrangThai'], ['Completed','Cancelled']); ?>
            <tr>
                <td style="padding-left:24px">
                    <span style="font-weight:700;color:#e63946">#<?= $o['Ma_DonHang'] ?></span>
                </td>
                <td style="font-weight:600"><?= htmlspecialchars($o['CustomerName']) ?></td>
                <td style="color:var(--text-muted)"><?= htmlspecialchars($o['PaymentName'] ?? 'Không rõ') ?></td>
                <td style="font-weight:700"><?= number_format($o['GrandTotal'], 0, ',', '.') ?>₫</td>
                <td><span class="status-badge <?= $si['class'] ?>"><?= $si['label'] ?></span></td>
                <td style="padding-right:24px">
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                        <button class="btn-icon primary" onclick="viewOrderDetails(<?= $o['Ma_DonHang'] ?>)" title="Xem chi tiết">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                        <form action="index.php?act=quanlydonhang" method="POST" style="display:flex;align-items:center;gap:6px;margin:0">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?= $o['Ma_DonHang'] ?>">
                            <select name="status" class="form-select form-select-sm" style="width:140px;border-radius:7px;font-size:0.82rem" <?= $locked ? 'disabled' : '' ?>>
                                <option value="Pending"   <?= $o['TrangThai'] == 'Pending'   ? 'selected' : '' ?>>Đang xử lý</option>
                                <option value="Shipping"  <?= $o['TrangThai'] == 'Shipping'  ? 'selected' : '' ?>>Đang giao hàng</option>
                                <option value="Completed" <?= $o['TrangThai'] == 'Completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="Cancelled" <?= $o['TrangThai'] == 'Cancelled' ? 'selected' : '' ?>>Hủy đơn</option>
                            </select>
                            <button type="submit" class="btn-primary-admin" style="padding:6px 12px;font-size:0.8rem" <?= $locked ? 'disabled style="opacity:0.5;cursor:not-allowed"' : '' ?>>
                                Lưu
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalTitle">Chi tiết Đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#f8fafc">
                <div id="orderLoading" class="text-center py-5">
                    <div class="spinner-border" style="color:#e63946" role="status"></div>
                    <div style="color:var(--text-muted);margin-top:12px;font-size:0.85rem">Đang tải dữ liệu...</div>
                </div>
                <div id="orderContent" style="display:none">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div style="background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden">
                                <div style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);border-bottom:1px solid var(--border)">Thông tin Khách Hàng</div>
                                <div style="padding:16px">
                                    <div style="margin-bottom:8px"><strong>Tên:</strong> <span id="detailName"></span></div>
                                    <div style="margin-bottom:8px"><strong>Email:</strong> <span id="detailEmail"></span></div>
                                    <div><strong>SĐT:</strong> <span id="detailPhone"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden">
                                <div style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);border-bottom:1px solid var(--border)">Thông tin Giao Hàng</div>
                                <div style="padding:16px">
                                    <div style="margin-bottom:8px"><strong>Địa chỉ:</strong> <span id="detailAddress"></span></div>
                                    <div><strong>Thành phố:</strong> <span id="detailCity"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden">
                        <div style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);border-bottom:1px solid var(--border)">Danh Sách Sản Phẩm</div>
                        <table class="admin-table">
                            <thead><tr>
                                <th style="padding-left:20px">Sản phẩm</th>
                                <th style="text-align:center">Số lượng</th>
                                <th style="text-align:right;padding-right:20px">Thành tiền</th>
                            </tr></thead>
                            <tbody id="detailItemsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    document.getElementById('orderModalTitle').innerText = 'Chi tiết Đơn hàng #' + orderId;
    document.getElementById('orderLoading').style.display = 'block';
    document.getElementById('orderContent').style.display = 'none';
    new bootstrap.Modal(document.getElementById('orderDetailModal')).show();

    fetch('quanlydonhang.php?ajax=get_details&order_id=' + orderId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('orderLoading').style.display = 'none';
            document.getElementById('orderContent').style.display = 'block';
            const info = data.info;
            document.getElementById('detailName').innerText = info.TenNguoiDung;
            document.getElementById('detailEmail').innerText = info.Email || 'N/A';
            document.getElementById('detailPhone').innerText = info.SDT || 'N/A';
            document.getElementById('detailAddress').innerText = info.DiaChi || 'N/A';
            document.getElementById('detailCity').innerText = info.ThanhPho || 'N/A';
            const tbody = document.getElementById('detailItemsTable');
            tbody.innerHTML = '';
            data.items.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding-left:20px;display:flex;align-items:center;gap:12px;padding-top:14px;padding-bottom:14px">
                        <img src="../view/image/${item.Anh}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0">
                        <span style="font-weight:600">${item.TenSanPham}</span>
                    </td>
                    <td style="text-align:center;font-weight:600">${item.SoLuong}</td>
                    <td style="text-align:right;padding-right:20px;font-weight:700;color:#e63946">${parseInt(item.Subtotal).toLocaleString('vi-VN')}₫</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(() => {
            document.getElementById('orderLoading').innerHTML = '<div class="admin-alert error" style="margin:0"><span class="material-symbols-outlined">error</span>Lỗi khi tải chi tiết đơn hàng!</div>';
        });
}
</script>