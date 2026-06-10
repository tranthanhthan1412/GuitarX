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
    
    // Upload Ảnh
    $imageName = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $fileName = basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        $targetFilePath = $targetDir . $newFileName;
        
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $imageName = $newFileName;
            } else {
                $error = "Có lỗi xảy ra khi upload ảnh.";
            }
        } else {
            $error = "Chỉ chấp nhận các định dạng JPG, JPEG, PNG, GIF, WEBP.";
        }
    }

    if (empty($error)) {
        if ($action === 'add') {
            if (empty($imageName)) {
                $error = "Vui lòng chọn ảnh cho sản phẩm.";
            } else {
                if ($productModel->addProduct($name, $imageName, $desc, $price, $count, $brand, $category_id)) {
                    $message = "Đã thêm sản phẩm mới thành công.";
                } else {
                    $error = "Lỗi khi thêm sản phẩm vào CSDL.";
                }
            }
        } elseif ($action === 'edit') {
            // Nếu để trống ảnh khi sửa, hàm updateProduct của mày sẽ tự giữ lại ảnh cũ
            if ($productModel->updateProduct($id, $name, $imageName, $desc, $price, $count, $brand, $category_id)) {
                $message = "Đã cập nhật thông tin sản phẩm thành công.";
            } else {
                $error = "Lỗi khi cập nhật CSDL.";
            }
        }
    }
}

// === CÀI ĐẶT LOGIC PHÂN TRANG CHO ADMIN ===
$limit = 6; // Hiện 6 cây mỗi trang
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($currentPage < 1) $currentPage = 1;

// Đếm tổng số sản phẩm hiện có
$totalProducts = $productModel->countAllProducts();
$totalPages = ceil($totalProducts / $limit);

// Lấy sản phẩm theo trang (Gọi hàm phân trang dành riêng cho Admin)
if (method_exists($productModel, 'getAllProductsAdmin')) {
    $products = $productModel->getAllProductsAdmin($currentPage, $limit);
} else {
    // Phương án dự phòng nếu mày chưa kịp sửa file Model ở bước trước
    $products = $productModel->getAllProducts($currentPage, $limit); 
}

$categories = $productModel->getAllCategories();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-display-md m-0">Quản lý Sản Phẩm</h2>
    <button class="btn btn-primary-custom d-flex align-items-center gap-2" data-bs-toggle="modal"
        data-bs-target="#productModal">
        <span class="material-symbols-outlined">add</span> Thêm Sản Phẩm Mới
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

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Hình Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Thương Hiệu</th>
                        <th>Danh Mục</th>
                        <th>Giá Bán</th>
                        <th>Tồn Kho</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?= $p['Product_ID'] ?></td>
                        <td>
                            <img src="../view/image/<?= $p['Image'] ?>" alt="<?= htmlspecialchars($p['ProductName']) ?>"
                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        </td>
                        <td>
                            <span class="fw-bold text-dark d-block"><?= htmlspecialchars($p['ProductName']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($p['Brand']) ?></td>
                        <td><?= htmlspecialchars($p['CategoryName'] ?? '') ?></td>
                        <td class="fw-bold text-primary-custom"><?= number_format($p['Price'], 0, ',', '.') ?>₫</td>
                        <td>
                            <?php if ($p['Count'] > 10): ?>
                            <span
                                class="badge bg-success bg-opacity-10 text-success border border-success"><?= $p['Count'] ?></span>
                            <?php elseif ($p['Count'] > 0): ?>
                            <span
                                class="badge bg-warning bg-opacity-10 text-warning border border-warning"><?= $p['Count'] ?></span>
                            <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Hết hàng</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                onclick='openEditModal(<?= json_encode($p) ?>)' title="Sửa">
                                <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                            </button>
                            <a href="index.php?act=quanlysanpham&delete_id=<?= $p['Product_ID'] ?>"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" title="Xóa">
                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (isset($totalPages) && $totalPages > 1): ?>
<div class="d-flex justify-content-center mt-4 mb-4">
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm m-0">

            <?php $prevDisabled = ($currentPage <= 1) ? 'disabled' : ''; ?>
            <li class="page-item <?php echo $prevDisabled; ?>">
                <a class="page-link border-0 bg-light text-dark rounded-3 me-2 px-3"
                    href="index.php?act=quanlysanpham&page=<?= $currentPage - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): 
                $itemActive = ($currentPage == $i) ? 'active' : '';
                $linkClass = ($currentPage == $i) ? 'bg-dark text-white' : 'bg-light text-muted';
            ?>
            <li class="page-item <?php echo $itemActive; ?>">
                <a class="page-link border-0 mx-1 rounded-3 fw-bold <?php echo $linkClass; ?>"
                    href="index.php?act=quanlysanpham&page=<?= $i ?>"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>

            <?php $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : ''; ?>
            <li class="page-item <?php echo $nextDisabled; ?>">
                <a class="page-link border-0 bg-light text-dark rounded-3 ms-2 px-3"
                    href="index.php?act=quanlysanpham&page=<?= $currentPage + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>

        </ul>
    </nav>
</div>
<?php endif; ?>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?act=quanlysanpham&page=<?= $currentPage ?>" method="POST"
                enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="productModalLabel">Thêm Sản Phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="product_id" id="productId" value="">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Tên sản phẩm *</label>
                            <input type="text" class="form-control" name="name" id="productName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thương hiệu *</label>
                            <input type="text" class="form-control" name="brand" id="productBrand" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Danh mục *</label>
                            <select class="form-select" name="category_id" id="productCategory" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach($categories as $c): ?>
                                <option value="<?= $c['Category_ID'] ?>"><?= $c['CategoryName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Giá bán (VNĐ) *</label>
                            <input type="number" class="form-control" name="price" id="productPrice" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Số lượng tồn kho *</label>
                            <input type="number" class="form-control" name="count" id="productCount" min="0" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            <input type="file" class="form-control" name="image" id="productImage" accept="image/*">
                            <small class="text-muted" id="imageHelp">Chọn ảnh tải lên. Nếu đang sửa, để trống để giữ ảnh
                                cũ.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Mô tả sản phẩm</label>
                            <textarea class="form-control" name="description" id="productDesc" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary-custom" id="btnSubmitForm">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mở modal Thêm mới (Sửa lại bộ bắt sự kiện khớp với ID modal mới)
document.querySelector('[data-bs-target="#productModal"]').addEventListener('click', function() {
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
    document.getElementById('btnSubmitForm').innerText = 'Thêm mới';
});

// Mở modal Chỉnh sửa
function openEditModal(product) {
    document.getElementById('productModalLabel').innerText = 'Chỉnh Sửa Sản Phẩm';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('productId').value = product.Product_ID;
    document.getElementById('productName').value = product.ProductName;
    document.getElementById('productBrand').value = product.Brand;
    document.getElementById('productCategory').value = product.Category_ID;
    document.getElementById('productPrice').value = product.Price;
    document.getElementById('productCount').value = product.Count;
    document.getElementById('productDesc').value = product.Description;
    document.getElementById('productImage').required = false;
    document.getElementById('btnSubmitForm').innerText = 'Lưu thay đổi';

    var myModal = new bootstrap.Modal(document.getElementById('productModal'));
    myModal.show();
}
</script>