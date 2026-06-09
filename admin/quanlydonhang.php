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

// Hàm map màu sắc cho Status
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Pending': return 'bg-warning text-dark';
        case 'Shipping': return 'bg-info text-dark';
        case 'Completed': return 'bg-success text-white';
        case 'Cancelled': return 'bg-danger text-white';
        default: return 'bg-secondary text-white';
    }
}
function getStatusLabel($status) {
    switch ($status) {
        case 'Pending': return 'Đang xử lý';
        case 'Shipping': return 'Đang giao hàng';
        case 'Completed': return 'Đã hoàn thành';
        case 'Cancelled': return 'Đã hủy';
        default: return $status;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-display-md m-0">Quản lý Đơn Hàng</h2>
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
                        <th class="ps-4">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Phương Thức TT</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?= $o['Order_ID'] ?></td>
                                <td><span class="fw-bold text-dark"><?= htmlspecialchars($o['CustomerName']) ?></span></td>
                                <td><?= htmlspecialchars($o['PaymentName'] ?? 'Không rõ') ?></td>
                                <td class="fw-bold text-primary-custom"><?= number_format($o['GrandTotal'], 0, ',', '.') ?>₫</td>
                                <td>
                                    <span class="badge <?= getStatusBadgeClass($o['Status']) ?> px-2 py-1" style="font-size: 0.85rem;">
                                        <?= getStatusLabel($o['Status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 d-flex justify-content-end align-items-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetails(<?= $o['Order_ID'] ?>)" title="Xem chi tiết">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                    </button>
                                    
                                    <form action="index.php?act=quanlydonhang" method="POST" class="d-flex align-items-center m-0">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?= $o['Order_ID'] ?>">
                                        <select name="status" class="form-select form-select-sm me-1" style="width: 140px;" <?= $o['Status'] == 'Cancelled' ? 'disabled' : '' ?>>
                                            <option value="Pending" <?= $o['Status'] == 'Pending' ? 'selected' : '' ?>>Đang xử lý</option>
                                            <option value="Shipping" <?= $o['Status'] == 'Shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                                            <option value="Completed" <?= $o['Status'] == 'Completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                            <option value="Cancelled" <?= $o['Status'] == 'Cancelled' ? 'selected' : '' ?>>Hủy đơn</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-secondary-custom" <?= $o['Status'] == 'Cancelled' ? 'disabled' : '' ?>>Lưu</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Chi tiết đơn hàng -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="orderModalTitle">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div id="orderLoading" class="text-center py-4">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <div id="orderContent" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Thông tin Khách Hàng</div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Tên:</strong> <span id="detailName"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="detailEmail"></span></p>
                                    <p class="mb-0"><strong>SĐT:</strong> <span id="detailPhone"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Thông tin Giao Hàng</div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Địa chỉ:</strong> <span id="detailAddress"></span></p>
                                    <p class="mb-0"><strong>Thành phố:</strong> <span id="detailCity"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Danh Sách Sản Phẩm</div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end pe-3">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody id="detailItemsTable">
                                    <!-- Rows injected via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
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
    
    var myModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    myModal.show();
    
    fetch('quanlydonhang.php?ajax=get_details&order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('orderLoading').style.display = 'none';
            document.getElementById('orderContent').style.display = 'block';
            
            // Render Info
            const info = data.info;
            document.getElementById('detailName').innerText = info.UserName;
            document.getElementById('detailEmail').innerText = info.Email || 'N/A';
            document.getElementById('detailPhone').innerText = info.PhoneNumber || 'N/A';
            document.getElementById('detailAddress').innerText = info.Adress || 'N/A';
            document.getElementById('detailCity').innerText = info.City || 'N/A';
            
            // Render Items
            const itemsTbody = document.getElementById('detailItemsTable');
            itemsTbody.innerHTML = '';
            data.items.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-3 d-flex align-items-center gap-3">
                        <img src="../view/image/${item.Image}" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                        <span class="fw-bold">${item.ProductName}</span>
                    </td>
                    <td class="text-center">${item.Quantity}</td>
                    <td class="text-end pe-3 fw-bold text-primary-custom">${parseInt(item.Subtotal).toLocaleString('vi-VN')}₫</td>
                `;
                itemsTbody.appendChild(tr);
            });
        })
        .catch(error => {
            document.getElementById('orderLoading').innerHTML = '<div class="alert alert-danger m-0">Lỗi khi tải chi tiết đơn hàng!</div>';
        });
}
</script>
