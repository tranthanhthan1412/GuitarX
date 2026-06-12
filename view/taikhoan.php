<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Nếu đã đăng nhập thì không cho vào trang này nữa, đẩy về trang chủ hoặc admin
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: " . BASE_URL . "admin/index.php");
    } else {
        header("Location: " . BASE_URL . "index.php");
    }
    exit();
}
?>
<?php include_once 'header.php'; ?>
<div class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-sm border-0" style="max-width: 450px; width: 100%; border-radius: 12px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <span class="material-symbols-outlined text-primary-custom" style="font-size: 48px;">account_circle</span>
                <h3 class="font-headline-md fw-bold mt-2">ĐĂNG NHẬP</h3>
                <p class="text-muted font-label-sm">Vui lòng đăng nhập để tiếp tục</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <?php if($_GET['error'] == 'invalid'): ?>
                    <div class="alert alert-danger text-center font-label-sm rounded-1"><i class="material-symbols-outlined align-middle fs-6 me-1">error</i>Tài khoản hoặc mật khẩu không chính xác!</div>
                <?php elseif($_GET['error'] == 'empty'): ?>
                    <div class="alert alert-warning text-center font-label-sm rounded-1"><i class="material-symbols-outlined align-middle fs-6 me-1">warning</i>Vui lòng nhập đầy đủ thông tin!</div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Action trỏ thẳng về controller/user.php -->
            <form action="<?= BASE_URL ?>controller/user.php" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="mb-3">
                    <label class="form-label font-label-sm fw-bold">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control py-2 shadow-none" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="mb-4">
                    <label class="form-label font-label-sm fw-bold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control py-2 shadow-none" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="btn btn-secondary-custom w-100 py-2 fw-bold text-uppercase tracking-wider">Đăng nhập</button>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top">
                <p class="font-label-sm text-muted mb-0">Chưa có tài khoản? <a href="<?= BASE_URL ?>index.php?act=dangky" class="text-primary-custom text-decoration-none fw-bold">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>