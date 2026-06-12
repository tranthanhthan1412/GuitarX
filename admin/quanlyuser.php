<?php
require_once '../model/database.php';
require_once '../model/m_user.php';

$db = (new Database())->getConnection();
$userModel = new UserModel($db);

$message = '';
$error = '';

// Xử lý Thay đổi Quyền
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_role') {
    $userId = intval($_POST['user_id'] ?? 0);
    $newRole = trim($_POST['role'] ?? '');
    
    // Không cho phép tự đổi quyền của chính mình (nếu đang đăng nhập)
    if ($userId === $_SESSION['user_id']) {
        $error = "Bạn không thể tự thay đổi quyền của chính mình!";
    } elseif ($userId > 0 && in_array($newRole, ['customer', 'admin', 'banned'])) {
        if ($userModel->changeUserRole($userId, $newRole)) {
            $message = "Đã cập nhật quyền của người dùng thành công.";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật.";
        }
    }
}

// Lấy danh sách users (Lưu ý: Đảm bảo hàm getAllUsers() trong m_user.php đã được LEFT JOIN với bảng customer_rank như tao chỉ lúc nãy nhé)
$users = $userModel->getAllUsers();

function getRoleBadge($role) {
    switch ($role) {
        case 'admin': return '<span class="badge bg-danger">Quản Trị Viên</span>';
        case 'banned': return '<span class="badge bg-dark">Đã Khóa</span>';
        default: return '<span class="badge bg-secondary">Khách Hàng</span>';
    }
}

// Đã xóa hàm getRankBadge cũ, giờ dùng trực tiếp class từ m_user.php
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-display-md m-0">Quản lý Người Dùng</h2>
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
                        <th>Tên Đăng Nhập</th>
                        <th>Email</th>
                        <th>Số Điện Thoại</th>
                        <th>Ngày Đăng Ký</th>
                        <th>Phân Quyền</th>
                        <th>Hạng Thành Viên</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có dữ liệu.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?= $u['Ma_NguoiDung'] ?></td>
                        <td><span class="fw-bold text-dark"><?= htmlspecialchars($u['TenNguoiDung']) ?></span></td>
                        <td><?= htmlspecialchars($u['Email'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($u['SDT'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($u['NgayKhoiTao'])) ?></td>
                        <td><?= getRoleBadge($u['VaiTro']) ?></td>

                        <td><span class="<?= htmlspecialchars($u['RankClass'] ?? 'rank-badge rank-new') ?>"><?= htmlspecialchars($u['TenXepHang'] ?? '🌱 Thành viên mới') ?></span></td>

                        <td class="text-end pe-4">
                            <form action="index.php?act=quanlyuser" method="POST"
                                class="d-flex align-items-center justify-content-end m-0">
                                <input type="hidden" name="action" value="change_role">
                                <input type="hidden" name="user_id" value="<?= $u['Ma_NguoiDung'] ?>">
                                <select name="role" class="form-select form-select-sm me-2" style="width: 130px;"
                                    <?= $u['Ma_NguoiDung'] === $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="customer" <?= $u['VaiTro'] == 'customer' ? 'selected' : '' ?>>Khách
                                        Hàng</option>
                                    <option value="admin" <?= $u['VaiTro'] == 'admin' ? 'selected' : '' ?>>Quản Trị Viên
                                    </option>
                                    <option value="banned" <?= $u['VaiTro'] == 'banned' ? 'selected' : '' ?>>Khóa (Ban)
                                    </option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-secondary-custom"
                                    <?= $u['Ma_NguoiDung'] === $_SESSION['user_id'] ? 'disabled' : '' ?>>Lưu</button>
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