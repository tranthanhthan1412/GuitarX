<?php
require_once '../model/database.php';
require_once '../model/m_danhmuc.php';

$db = (new Database())->getConnection();
$categoryModel = new CategoryModel($db);

$message = '';
$error = '';

// Xử lý Xóa Danh Mục
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $resultDelete = $categoryModel->deleteCategory($deleteId);
    if ($resultDelete === true) {
        $message = "Đã xóa danh mục thành công.";
    } elseif ($resultDelete === false) {
        $error = "Không thể xóa! Danh mục này hiện đang có sản phẩm bên trong. Vui lòng xóa hoặc chuyển sản phẩm sang danh mục khác trước.";
    } else {
        $error = "Có lỗi xảy ra trong quá trình xử lý xóa danh mục.";
    }
}

// Xử lý Thêm / Sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['category_id'] ?? 0;
    $name = trim($_POST['name'] ?? '');
    if (empty($name)) {
        $error = "Tên danh mục không được để trống.";
    } else {
        if ($action === 'add') {
            if ($categoryModel->addCategory($name)) $message = "Đã thêm danh mục mới thành công.";
            else $error = "Có lỗi xảy ra khi thêm danh mục.";
        } elseif ($action === 'edit') {
            if ($categoryModel->updateCategory($id, $name)) $message = "Đã cập nhật danh mục thành công.";
            else $error = "Có lỗi xảy ra khi cập nhật.";
        }
    }
}

$categories = $categoryModel->getAllCategories();
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
        <div class="page-title">Quản lý Danh Mục</div>
        <div class="page-subtitle">Tổng cộng <?= count($categories) ?> danh mục sản phẩm</div>
    </div>
    <button class="btn-primary-admin" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddModal()">
        <span class="material-symbols-outlined">add</span> Thêm Danh Mục Mới
    </button>
</div>

<!-- Category Table -->
<div class="admin-card" style="max-width:700px">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="padding-left:24px;width:80px">ID</th>
                <th>Tên Danh Mục</th>
                <th style="text-align:right;padding-right:24px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
            <tr>
                <td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted)">
                    <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;opacity:0.3">category</span>
                    Chưa có danh mục nào.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td style="padding-left:24px;font-weight:700;color:var(--text-muted)">#<?= $c['Ma_DanhMuc'] ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#e63946,#c1121f);border-radius:8px;display:flex;align-items:center;justify-content:center">
                            <span class="material-symbols-outlined" style="font-size:18px;color:#fff">category</span>
                        </div>
                        <span style="font-weight:600;font-size:0.95rem"><?= htmlspecialchars($c['TenDanhMuc']) ?></span>
                    </div>
                </td>
                <td style="text-align:right;padding-right:24px">
                    <div style="display:inline-flex;gap:6px">
                        <button class="btn-icon primary" onclick='openEditModal(<?= $c["Ma_DanhMuc"] ?>, <?= json_encode($c["TenDanhMuc"]) ?>)' title="Sửa">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button class="btn-icon danger" onclick="confirmDelete(<?= $c['Ma_DanhMuc'] ?>, '<?= htmlspecialchars($c['TenDanhMuc'], ENT_QUOTES) ?>')" title="Xóa">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?act=quanlydanhmuc" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Thêm Danh Mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="category_id" id="categoryId" value="">
                    <div>
                        <label class="form-label">Tên Danh Mục *</label>
                        <input type="text" class="form-control form-control-lg" name="name" id="categoryName" placeholder="Nhập tên danh mục..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-primary-admin" id="btnSubmitForm">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('categoryModalLabel').innerText = 'Thêm Danh Mục Mới';
    document.getElementById('formAction').value = 'add';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('btnSubmitForm').innerText = 'Thêm mới';
}
function openEditModal(id, name) {
    document.getElementById('categoryModalLabel').innerText = 'Chỉnh Sửa Danh Mục';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('btnSubmitForm').innerText = 'Lưu thay đổi';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Danh mục "${name}" sẽ bị xóa vĩnh viễn!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        borderRadius: '12px'
    }).then(result => {
        if (result.isConfirmed) window.location.href = `index.php?act=quanlydanhmuc&delete_id=${id}`;
    });
}
</script>