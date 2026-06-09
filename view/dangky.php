<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Nếu đã đăng nhập thì không cho vào trang này nữa, đẩy về trang chủ hoặc admin
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: /GuitarX/admin/index.php");
    } else {
        header("Location: /GuitarX/index.php");
    }
    exit();
}
?>
<?php include_once 'header.php'; ?>
<div class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-sm border-0" style="max-width: 450px; width: 100%; border-radius: 12px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <span class="material-symbols-outlined text-primary-custom" style="font-size: 48px;">person_add</span>
                <h3 class="font-headline-md fw-bold mt-2">ĐĂNG KÝ</h3>
                <p class="text-muted font-label-sm">Tạo tài khoản để trải nghiệm mua sắm tốt nhất</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <?php if($_GET['error'] == 'exists'): ?>
                    <div class="alert alert-danger text-center font-label-sm rounded-1"><i class="material-symbols-outlined align-middle fs-6 me-1">error</i>Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.</div>
                <?php elseif($_GET['error'] == 'empty'): ?>
                    <div class="alert alert-warning text-center font-label-sm rounded-1"><i class="material-symbols-outlined align-middle fs-6 me-1">warning</i>Vui lòng nhập đầy đủ thông tin bắt buộc!</div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Action trỏ thẳng về controller/user.php -->
            <form action="/GuitarX/controller/user.php" method="POST">
                <input type="hidden" name="action" value="register">
                
                <div class="mb-3">
                    <label class="form-label font-label-sm fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control py-2 shadow-none" placeholder="Ví dụ: nguyenvana123" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label font-label-sm fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control py-2 shadow-none" placeholder="Ví dụ: nguyenvana@gmail.com" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label font-label-sm fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control py-2 shadow-none" placeholder="Tối thiểu 6 ký tự" required minlength="6">
                </div>

                <div class="mb-4">
                    <label class="form-label font-label-sm fw-bold">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control py-2 shadow-none" placeholder="09xx xxx xxx">
                </div>
                
                <button type="submit" class="btn btn-secondary-custom w-100 py-2 fw-bold text-uppercase tracking-wider">Tạo tài khoản</button>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top">
                <p class="font-label-sm text-muted mb-0">Đã có tài khoản? <a href="/GuitarX/index.php?act=login" class="text-primary-custom text-decoration-none fw-bold">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
