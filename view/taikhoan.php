<?php include 'header.php'; ?>

<div class="container my-5" style="max-width: 400px;">
    <h3 class="text-center mb-4">ĐĂNG NHẬP</h3>
    
    <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
        <div class="alert alert-danger text-center">Tài khoản hoặc mật khẩu không chính xác!</div>
    <?php endif; ?>

    <!-- Action trỏ thẳng về controller/user.php -->
    <form action="../controller/user.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="btn-login" class="btn btn-danger w-100">Đăng nhập</button>
    </form>
</div>

<?php include 'footer.php'; ?>