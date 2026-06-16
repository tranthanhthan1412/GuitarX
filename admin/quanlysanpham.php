<?php
// Xử lý logic Thêm/Sửa/Xóa ngay đầu file
require_once '../model/database.php';
require_once '../model/m_sanpham.php';

$db = (new Database())->getConnection();
$productModel = new ProductModel($db);

$message = '';
$error = '';

// Khởi tạo thư mục ảnh nếu chưa có
$targetDir = __DIR__ . "/../view/image/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Xử lý Xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    if ($productModel->deleteProduct($deleteId)) {
        $message = "Đã xóa sản phẩm thành công.";
    } else {
        $error = "Không thể xóa sản phẩm này vì đã có khách hàng mua (tồn tại trong đơn hàng).";
    }
}

// Xử lý Form Thêm/Sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['product_id'] ?? 0;
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? 0);
    $count = trim($_POST['count'] ?? 0);
    $brand = trim($_POST['brand'] ?? '');
    $category_id = trim($_POST['category_id'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    
    $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');

    $imageName = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $fileName = basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        $targetFilePath = $targetDir . $newFileName;
        
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $imageName = $newFileName;
            } else {
                $error = "Có lỗi xảy ra khi upload ảnh chính.";
            }
        } else {
            $error = "Ảnh chính chỉ chấp nhận các định dạng JPG, JPEG, PNG, GIF, WEBP.";
        }
    }

    if (empty($error)) {
        if ($action === 'add') {
            if (empty($imageName)) {
                $error = "Vui lòng chọn ảnh cho sản phẩm.";
            } else {
                if ($productModel->addProduct($name, $imageName, $desc, $price, $count, $brand, $category_id)) {
                    $newProductId = $db->lastInsertId();
                    $message = "Đã thêm sản phẩm mới thành công.";

                    if (isset($_FILES['album']) && !empty($_FILES['album']['name'][0])) {
                        foreach ($_FILES['album']['name'] as $key => $val) {
                            if ($_FILES['album']['error'][$key] == 0) {
                                $subFileName = basename($_FILES['album']['name'][$key]);
                                $subFileType = strtolower(pathinfo($subFileName, PATHINFO_EXTENSION));
                                $newSubName = time() . '_album_' . $key . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $subFileName);
                                $subTargetPath = $targetDir . $newSubName;
                                if (in_array($subFileType, $allowedTypes)) {
                                    if (move_uploaded_file($_FILES['album']['tmp_name'][$key], $subTargetPath)) {
                                        $productModel->addProductImage($newProductId, $newSubName);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $error = "Lỗi khi thêm sản phẩm vào CSDL.";
                }
            }
        } elseif ($action === 'edit') {
            if ($productModel->updateProduct($id, $name, $imageName, $desc, $price, $count, $brand, $category_id)) {
                $message = "Đã cập nhật thông tin sản phẩm thành công.";
                if (isset($_FILES['album']) && !empty($_FILES['album']['name'][0])) {
                    foreach ($_FILES['album']['name'] as $key => $val) {
                        if ($_FILES['album']['error'][$key] == 0) {
                            $subFileName = basename($_FILES['album']['name'][$key]);
                            $subFileType = strtolower(pathinfo($subFileName, PATHINFO_EXTENSION));
                            $newSubName = time() . '_album_edit_' . $key . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $subFileName);
                            $subTargetPath = $targetDir . $newSubName;
                            if (in_array($subFileType, $allowedTypes)) {
                                if (move_uploaded_file($_FILES['album']['tmp_name'][$key], $subTargetPath)) {
                                    $productModel->addProductImage($id, $newSubName);
                                }
                            }
                        }
                    }
                }
            } else {
                $error = "Lỗi khi cập nhật CSDL.";
            }
        }
    }
}

$limit = 6;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($currentPage < 1) $currentPage = 1;

$totalProducts = $productModel->countAllProducts();
$totalPages = ceil($totalProducts / $limit);

if (method_exists($productModel, 'getAllProductsAdmin')) {
    $products = $productModel->getAllProductsAdmin($currentPage, $limit);
} else {
    $products = $productModel->getAllProducts($currentPage, $limit);
}
$categories = $productModel->getAllCategories();
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
        <div class="page-title">Quản lý Sản Phẩm</div>
        <div class="page-subtitle">Tổng cộng <?= $totalProducts ?> sản phẩm trong kho</div>
    </div>
    <button class="btn-primary-admin" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetAddForm()">
        <span class="material-symbols-outlined">add</span> Thêm Sản Phẩm Mới
    </button>
</div>

<!-- Products Table -->
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="padding-left:24px;width:60px">ID</th>
                <th style="width:70px">Ảnh</th>
                <th>Tên Sản Phẩm</th>
                <th>Thương Hiệu</th>
                <th>Danh Mục</th>
                <th>Giá Bán</th>
                <th>Tồn Kho</th>
                <th style="text-align:right;padding-right:24px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td style="padding-left:24px;font-weight:700;color:var(--text-muted)">#<?= $p['Ma_SanPham'] ?></td>
                <td>
                    <img src="../view/image/<?= htmlspecialchars($p['Anh']) ?>" alt="<?= htmlspecialchars($p['TenSanPham']) ?>"
                        style="width:46px;height:46px;object-fit:cover;border-radius:10px;border:1.5px solid var(--border)">
                </td>
                <td>
                    <span style="font-weight:600;color:var(--text-primary)"><?= htmlspecialchars($p['TenSanPham']) ?></span>
                </td>
                <td style="color:var(--text-muted)"><?= htmlspecialchars($p['ThuongHieu']) ?></td>
                <td>
                    <span style="background:#f1f5f9;color:#475569;padding:3px 10px;border-radius:6px;font-size:0.78rem;font-weight:600">
                        <?= htmlspecialchars($p['TenDanhMuc'] ?? '') ?>
                    </span>
                </td>
                <td style="font-weight:700;color:#e63946"><?= number_format($p['GiaTien'], 0, ',', '.') ?>₫</td>
                <td>
                    <?php if ($p['SoLuong'] > 10): ?>
                        <span class="stock-badge stock-ok"><?= $p['SoLuong'] ?></span>
                    <?php elseif ($p['SoLuong'] > 0): ?>
                        <span class="stock-badge stock-low"><?= $p['SoLuong'] ?></span>
                    <?php else: ?>
                        <span class="stock-badge stock-out">Hết</span>
                    <?php endif; ?>
                </td>
                <td style="text-align:right;padding-right:24px">
                    <div style="display:inline-flex;gap:6px">
                        <button class="btn-icon primary" onclick='openEditModal(<?= json_encode($p) ?>)' title="Sửa">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <a href="index.php?act=quanlysanpham&delete_id=<?= $p['Ma_SanPham'] ?>"
                            class="btn-icon danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')" title="Xóa" style="text-decoration:none">
                            <span class="material-symbols-outlined">delete</span>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="admin-pagination">
        <a class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>" href="index.php?act=quanlysanpham&page=<?= $currentPage - 1 ?>">
            <span class="material-symbols-outlined" style="font-size:16px">chevron_left</span>
        </a>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="page-btn <?= $currentPage == $i ? 'active' : '' ?>" href="index.php?act=quanlysanpham&page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
        <a class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>" href="index.php?act=quanlysanpham&page=<?= $currentPage + 1 ?>">
            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Add/Edit Product -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?act=quanlysanpham&page=<?= $currentPage ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Thêm Sản Phẩm Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="product_id" id="productId" value="">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" class="form-control" name="name" id="productName" required placeholder="Nhập tên sản phẩm...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thương hiệu *</label>
                            <input type="text" class="form-control" name="brand" id="productBrand" required placeholder="VD: Yamaha">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Danh mục *</label>
                            <select class="form-select" name="category_id" id="productCategory" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach($categories as $c): ?>
                                <option value="<?= $c['Ma_DanhMuc'] ?>"><?= htmlspecialchars($c['TenDanhMuc']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá bán (VNĐ) *</label>
                            <input type="number" class="form-control" name="price" id="productPrice" min="0" required placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số lượng tồn kho *</label>
                            <input type="number" class="form-control" name="count" id="productCount" min="0" required placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hình ảnh đại diện chính *</label>
                            <input type="file" class="form-control" name="image" id="productImage" accept="image/*">
                            <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px" id="imageHelp">Để trống khi sửa để giữ ảnh cũ.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Album ảnh phụ (Chọn nhiều)</label>
                            <input type="file" class="form-control" name="album[]" id="productAlbum" accept="image/*" multiple>
                            <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px">Giữ Ctrl để chọn nhiều ảnh.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả sản phẩm</label>
                            <textarea class="form-control" name="description" id="productDesc" rows="3" placeholder="Nhập mô tả..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="gap:10px">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-primary-admin" id="btnSubmitForm">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetAddForm() {
    document.getElementById('productModalLabel').innerText = 'Thêm Sản Phẩm Mới';
    document.getElementById('formAction').value = 'add';
    document.getElementById('productId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productBrand').value = '';
    document.getElementById('productCategory').value = '';
    document.getElementById('productPrice').value = '';
    document.getElementById('productCount').value = '';
    document.getElementById('productDesc').value = '';
    document.getElementById('productImage').required = true;
    document.getElementById('productAlbum').value = '';
    document.getElementById('btnSubmitForm').innerText = 'Thêm mới';
}

function openEditModal(product) {
    document.getElementById('productModalLabel').innerText = 'Chỉnh Sửa Sản Phẩm';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('productId').value = product.Ma_SanPham;
    document.getElementById('productName').value = product.TenSanPham;
    document.getElementById('productBrand').value = product.ThuongHieu;
    document.getElementById('productCategory').value = product.Ma_DanhMuc;
    document.getElementById('productPrice').value = product.GiaTien;
    document.getElementById('productCount').value = product.SoLuong;
    document.getElementById('productDesc').value = product.MoTa;
    document.getElementById('productImage').required = false;
    document.getElementById('productAlbum').value = '';
    document.getElementById('btnSubmitForm').innerText = 'Lưu thay đổi';
    new bootstrap.Modal(document.getElementById('productModal')).show();
}
</script>