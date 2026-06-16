<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị — GuitarX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            overflow: hidden;
            background: #0f172a;
        }

        /* ===== LEFT BRANDING PANEL ===== */
        .login-brand {
            flex: 1;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 40%, #1a1033 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(230,57,70,0.18) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-glow 4s ease-in-out infinite;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(139,92,246,0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-glow 4s ease-in-out infinite reverse;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.15); opacity: 1; }
        }

        .brand-content { position: relative; z-index: 2; text-align: center; }

        .brand-logo-icon {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #e63946, #c1121f);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 20px 50px rgba(230,57,70,0.4);
            animation: logo-float 3s ease-in-out infinite;
        }
        @keyframes logo-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .brand-logo-icon .material-symbols-outlined { font-size: 48px; color: #fff; }

        .brand-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 8px;
        }
        .brand-title span { color: #e63946; }

        .brand-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.45);
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 500;
            margin-bottom: 48px;
        }

        .brand-features { display: flex; flex-direction: column; gap: 16px; }
        .brand-feature {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem; font-weight: 500;
            backdrop-filter: blur(10px);
        }
        .brand-feature .material-symbols-outlined { color: #e63946; font-size: 20px; }

        /* Floating particles */
        .particles { position: absolute; inset: 0; z-index: 1; pointer-events: none; }
        .particle {
            position: absolute;
            width: 3px; height: 3px;
            border-radius: 50%;
            background: rgba(230,57,70,0.4);
            animation: float-particle linear infinite;
        }
        @keyframes float-particle {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 0.5; }
            100% { transform: translateY(-100px) rotate(720deg); opacity: 0; }
        }

        /* ===== RIGHT FORM PANEL ===== */
        .login-form-panel {
            width: 480px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
        }

        .login-form-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: linear-gradient(180deg, #e63946 0%, #7c3aed 100%);
        }

        .form-header { text-align: center; margin-bottom: 40px; width: 100%; }
        .form-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .form-header p { color: #94a3b8; font-size: 0.9rem; }

        .form-group { width: 100%; margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .input-wrap {
            position: relative;
            display: flex; align-items: center;
        }
        .input-wrap .material-symbols-outlined {
            position: absolute; left: 14px;
            color: #94a3b8; font-size: 20px;
            transition: color 0.2s;
        }
        .input-wrap input {
            width: 100%;
            padding: 13px 14px 13px 46px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all 0.2s;
        }
        .input-wrap input:focus {
            border-color: #e63946;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(230,57,70,0.08);
        }
        .input-wrap input:focus + .material-symbols-outlined,
        .input-wrap:has(input:focus) .material-symbols-outlined { color: #e63946; }

        .alert-box {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-box.error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-box.warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .alert-box .material-symbols-outlined { font-size: 18px; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(230,57,70,0.35); }
        .btn-login:hover::after { opacity: 1; }
        .btn-login:active { transform: translateY(0); }

        .back-link {
            text-align: center; margin-top: 28px;
            color: #94a3b8; font-size: 0.85rem;
        }
        .back-link a {
            color: #e63946; text-decoration: none; font-weight: 600;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .back-link a:hover { text-decoration: underline; }

        @media (max-width: 900px) {
            .login-brand { display: none; }
            .login-form-panel { width: 100%; }
        }
    </style>
</head>
<body>

    <!-- LEFT: Branding -->
    <div class="login-brand">
        <div class="particles" id="particles"></div>
        <div class="brand-content">
            <div class="brand-logo-icon">
                <span class="material-symbols-outlined">music_note</span>
            </div>
            <h1 class="brand-title">Guitar<span>X</span></h1>
            <p class="brand-subtitle">Admin Control System</p>

            <div class="brand-features">
                <div class="brand-feature">
                    <span class="material-symbols-outlined">inventory_2</span>
                    Quản lý sản phẩm & danh mục
                </div>
                <div class="brand-feature">
                    <span class="material-symbols-outlined">receipt_long</span>
                    Theo dõi đơn hàng thời gian thực
                </div>
                <div class="brand-feature">
                    <span class="material-symbols-outlined">group</span>
                    Quản lý khách hàng & phân quyền
                </div>
                <div class="brand-feature">
                    <span class="material-symbols-outlined">local_offer</span>
                    Tạo & quản lý mã giảm giá
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="login-form-panel">
        <div class="form-header">
            <h2>Chào mừng trở lại 👋</h2>
            <p>Đăng nhập để truy cập bảng điều khiển</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <?php if($_GET['error'] == 'invalid'): ?>
                <div class="alert-box error">
                    <span class="material-symbols-outlined">error</span>
                    Tài khoản hoặc mật khẩu không chính xác!
                </div>
            <?php elseif($_GET['error'] == 'empty'): ?>
                <div class="alert-box warning">
                    <span class="material-symbols-outlined">warning</span>
                    Vui lòng nhập đầy đủ thông tin!
                </div>
            <?php elseif($_GET['error'] == 'not_admin'): ?>
                <div class="alert-box error">
                    <span class="material-symbols-outlined">gpp_bad</span>
                    Tài khoản này không có quyền quản trị!
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="../controller/user.php" method="POST" style="width:100%">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="is_admin_login" value="1">

            <div class="form-group">
                <label>Tên đăng nhập</label>
                <div class="input-wrap">
                    <span class="material-symbols-outlined">person</span>
                    <input type="text" name="username" placeholder="Nhập tên đăng nhập..." required autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-wrap">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" name="password" placeholder="Nhập mật khẩu..." required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-login">ĐĂNG NHẬP HỆ THỐNG</button>
        </form>

        <div class="back-link">
            <a href="../index.php">
                <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
                Quay lại trang khách hàng
            </a>
        </div>
    </div>

    <script>
    // Generate floating particles
    const container = document.getElementById('particles');
    for (let i = 0; i < 20; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left = Math.random() * 100 + '%';
        p.style.width = p.style.height = (Math.random() * 4 + 2) + 'px';
        p.style.animationDuration = (Math.random() * 15 + 10) + 's';
        p.style.animationDelay = (Math.random() * 10) + 's';
        p.style.opacity = Math.random() * 0.6 + 0.2;
        container.appendChild(p);
    }
    </script>
</body>
</html>
