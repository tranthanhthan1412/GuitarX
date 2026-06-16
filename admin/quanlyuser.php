<?php
require_once '../model/database.php';
require_once '../model/m_user.php';

$db = (new Database())->getConnection();
$userModel = new UserModel($db);

$message = '';
$error = '';

// Xử lý Khóa / Mở Khóa tài khoản
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $action = trim($_GET['action']);
    if ($userId === $_SESSION['user_id']) {
        $error = "Bạn không thể tự khóa tài khoản của chính mình!";
    } elseif ($userId > 0) {
        if ($action === 'lock') {
            if ($userModel->changeUserRole($userId, 'banned')) $message = "Đã khóa tài khoản thành công.";
            else $error = "Có lỗi xảy ra khi khóa tài khoản.";
        } elseif ($action === 'unlock') {
            if ($userModel->changeUserRole($userId, 'customer')) $message = "Đã mở khóa tài khoản thành công.";
            else $error = "Có lỗi xảy ra khi mở khóa tài khoản.";
        }
    }
}

$users = $userModel->getAllUsers();
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <div class="page-title">Quản lý Người Dùng</div>
        <div class="page-subtitle">Tổng cộng <?= count($users) ?> tài khoản trong hệ thống</div>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="padding-left:24px">ID</th>
                <th>Người Dùng</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>Ngày Đăng Ký</th>
                <th>Trạng Thái</th>
                <th>Hạng Thành Viên</th>
                <th style="text-align:right;padding-right:24px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
            <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Chưa có dữ liệu.</td></tr>
            <?php else: ?>
            <?php foreach ($users as $u): ?>
            <tr>
                <td style="padding-left:24px;font-weight:700;color:var(--text-muted)">#<?= $u['Ma_NguoiDung'] ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;flex-shrink:0">
                            <?= strtoupper(substr($u['TenNguoiDung'], 0, 1)) ?>
                        </div>
                        <span style="font-weight:600"><?= htmlspecialchars($u['TenNguoiDung']) ?></span>
                    </div>
                </td>
                <td style="color:var(--text-muted)"><?= htmlspecialchars($u['Email'] ?? 'N/A') ?></td>
                <td style="color:var(--text-muted)"><?= htmlspecialchars($u['SDT'] ?? 'N/A') ?></td>
                <td style="color:var(--text-muted);font-size:0.82rem"><?= date('d/m/Y', strtotime($u['NgayKhoiTao'])) ?></td>
                <td>
                    <?php if ($u['VaiTro'] === 'admin'): ?>
                        <span class="status-badge admin-badge">Admin</span>
                    <?php elseif ($u['VaiTro'] === 'banned'): ?>
                        <span class="status-badge banned-badge">Đã Khóa</span>
                    <?php else: ?>
                        <span class="status-badge active-badge">Hoạt Động</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="<?= htmlspecialchars($u['RankClass'] ?? 'rank-badge rank-new') ?>" style="font-size:0.82rem">
                        <?= htmlspecialchars($u['TenXepHang'] ?? '🌱 Thành viên mới') ?>
                    </span>
                </td>
                <td style="text-align:right;padding-right:24px">
                    <?php if ($u['Ma_NguoiDung'] !== $_SESSION['user_id'] && $u['VaiTro'] !== 'admin'): ?>
                        <?php if ($u['VaiTro'] === 'banned'): ?>
                        <a href="index.php?act=quanlyuser&action=unlock&user_id=<?= $u['Ma_NguoiDung'] ?>"
                            class="btn-primary-admin" style="padding:6px 12px;font-size:0.78rem;background:linear-gradient(135deg,#16a34a,#15803d);box-shadow:0 4px 12px rgba(22,163,74,0.25);text-decoration:none">
                            <span class="material-symbols-outlined" style="font-size:15px">lock_open</span>
                            Mở Khóa
                        </a>
                        <?php else: ?>
                        <button type="button"
                            class="btn-primary-admin" style="padding:6px 12px;font-size:0.78rem"
                            onclick="confirmLock(<?= $u['Ma_NguoiDung'] ?>, '<?= htmlspecialchars($u['TenNguoiDung'], ENT_QUOTES) ?>')">
                            <span class="material-symbols-outlined" style="font-size:15px">lock</span>
                            Khóa
                        </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color:var(--text-muted);font-size:0.8rem;font-style:italic">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function confirmLock(id, username) {
    Swal.fire({
        title: 'Khóa tài khoản?',
        text: `Bạn có chắc muốn khóa tài khoản "${username}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Xác nhận khóa',
        cancelButtonText: 'Hủy'
    }).then(result => {
        if (result.isConfirmed) window.location.href = `index.php?act=quanlyuser&action=lock&user_id=${id}`;
    });
}
</script>