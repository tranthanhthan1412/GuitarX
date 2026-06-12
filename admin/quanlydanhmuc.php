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
    if ($categoryModel->deleteCategory($deleteId)) {
        $message = "Đã xóa danh mục thành công. (Các sản phẩm thuộc danh mục này đã được chuyển thành 'Chưa phân loại')";
    } else {
        $error = "Có lỗi xảy ra khi xóa danh mục.";
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
            if ($categoryModel->addCategory($name)) {
                $message = "Đã thêm danh mục mới thành công.";
            } else {
                $error = "Có lỗi xảy ra khi thêm danh mục.";
            }
        } elseif ($action === 'edit') {
            if ($categoryModel->updateCategory($id, $name)) {
                $message = "Đã cập nhật danh mục thành công.";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật.";
            }
        }
    }
}

// Lấy danh sách hiển thị
$categories = $categoryModel->getAllCategories();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-display-md m-0">Quản lý Danh Mục</h2>
    <button class="btn btn-primary-custom d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddModal()">
        <span class="material-symbols-outlined">add</span> Thêm Danh Mục Mới
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

<div class="card shadow-sm border-0" style="max-width: 800px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 15%">ID</th>
                        <th style="width: 60%">Tên Danh Mục</th>
                        <th class="text-end pe-4" style="width: 25%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">Chưa có danh mục nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $c): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?= $c['Ma_DanhMuc'] ?></td>
                                <td>
                                    <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($c['TenDanhMuc']) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-secondary me-2" 
                                        onclick='openEditModal(<?= $c['Ma_DanhMuc'] ?>, <?= json_encode($c['TenDanhMuc']) ?>)'
                                        title="Sửa">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                    </button>
                                    <a href="index.php?act=quanlydanhmuc&delete_id=<?= $c['Ma_DanhMuc'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không? Các sản phẩm bên trong sẽ bị mất liên kết danh mục.');"
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

<!-- Modal Add/Edit Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="index.php?act=quanlydanhmuc" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="categoryModalLabel">Thêm Danh Mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="category_id" id="categoryId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Danh Mục *</label>
                        <input type="text" class="form-control form-control-lg" name="name" id="categoryName" placeholder="Nhập tên..." required>
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
    
    var myModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    myModal.show();
}
</script>
