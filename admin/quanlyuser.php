<?php
require_once '../model/database.php';
require_once '../model/m_user.php';

$db = (new Database())->getConnection();
$userModel = new UserModel($db);

$message = '';
$error = '';

// Xử lý Khóa / Mở Khóa tài khoản qua phương thức GET để đồng bộ với SweetAlert2
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $action = trim($_GET['action']);

    // Không cho phép tự khóa chính mình
    if ($userId === $_SESSION['user_id']) {
        $error = "Bạn không thể tự khóa tài khoản của chính mình!";
    } elseif ($userId > 0) {
        if ($action === 'lock') {
            // Gọi hàm đổi vai trò/trạng thái thành 'banned'
            if ($userModel->changeUserRole($userId, 'banned')) {
                $message = "Đã khóa tài khoản thành công.";
            } else {
                $error = "Có lỗi xảy ra khi khóa tài khoản.";
            }
        } elseif ($action === 'unlock') {
            // Mở khóa thì trả về quyền mặc định là 'customer'
            if ($userModel->changeUserRole($userId, 'customer')) {
                $message = "Đã mở khóa tài khoản thành công.";
            } else {
                $error = "Có lỗi xảy ra khi mở khóa tài khoản.";
            }
        }
    }
}

// Lấy danh sách users
$users = $userModel->getAllUsers();

function getRoleBadge($role) {
    switch ($role) {
        case 'admin': return '<span class="badge bg-danger">Quản Trị Viên</span>';
        case 'banned': return '<span class="badge bg-dark">Đã Khóa</span>';
        default: return '<span class="badge bg-success">Hoạt Động</span>';
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                        <th>Trạng Thái</th>
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

                        <td><span
                                class="<?= htmlspecialchars($u['RankClass'] ?? 'rank-badge rank-new') ?>"><?= htmlspecialchars($u['TenXepHang'] ?? '🌱 Thành viên mới') ?></span>
                        </td>

                        <td class="text-end pe-4">
                            <?php if ($u['Ma_NguoiDung'] !== $_SESSION['user_id'] && $u['VaiTro'] !== 'admin'): ?>
                            <?php if ($u['VaiTro'] === 'banned'): ?>
                            <a href="index.php?act=quanlyuser&action=unlock&user_id=<?= $u['Ma_NguoiDung'] ?>"
                                class="btn btn-sm btn-outline-success fw-bold px-3">
                                Mở Khóa
                            </a>
                            <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold px-3"
                                onclick="confirmLock(<?= $u['Ma_NguoiDung'] ?>, '<?= htmlspecialchars($u['TenNguoiDung'], ENT_QUOTES) ?>')">
                                Khóa tài khoản
                            </button>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="text-muted small italic">Không thể tác động</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Hàm xác nhận khóa tài khoản bằng SweetAlert2
function confirmLock(id, username) {
    Swal.fire({
        title: 'Xác nhận khóa tài khoản?',
        text: `Bạn có chắc chắn muốn khóa tài khoản của "${username}" không?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xác nhận khóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // Chuyển trang thực hiện logic khóa ở Controller bên trên
            window.location.href = `index.php?act=quanlyuser&action=lock&user_id=${id}`;
        }
    });
}
</script>