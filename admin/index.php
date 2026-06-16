<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Middleware: Kiểm tra quyền Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Xử lý các API endpoint của Quản lý Chat (Trả về JSON, không render HTML)
if (isset($_GET['act']) && strpos($_GET['act'], 'chat_api') === 0) {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../model/database.php';
    require_once __DIR__ . '/../model/m_chat.php';
    $db = (new Database())->getConnection();
    $chatModel = new ChatModel($db);

    switch ($_GET['act']) {
        case 'chat_api_get_users':
            $users = $chatModel->getChatUsers();
            echo json_encode(['status' => 'success', 'data' => $users]);
            exit;
        case 'chat_api_get_messages':
            $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
            $messages = $chatModel->getMessages($userId);
            $chatModel->markAsRead($userId, true);
            echo json_encode(['status' => 'success', 'data' => $messages]);
            exit;
        case 'chat_api_send':
            $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $content = isset($_POST['message']) ? trim($_POST['message']) : '';
            if ($userId > 0 && $content !== '') {
                if ($chatModel->sendMessage($userId, $content, 1)) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'DB error']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            }
            exit;
    }
}

// Lấy stats cho dashboard
require_once __DIR__ . '/../model/database.php';
$db_stats = (new Database())->getConnection();
$totalOrders = $db_stats->query("SELECT COUNT(*) FROM DonHang")->fetchColumn();
$totalRevenue = $db_stats->query("SELECT COALESCE(SUM(ctd.TongTien), 0) FROM ChiTietDonHang ctd JOIN DonHang dh ON ctd.Ma_DonHang = dh.Ma_DonHang WHERE dh.TrangThai='Completed'")->fetchColumn();
$totalProducts = $db_stats->query("SELECT COUNT(*) FROM SanPham")->fetchColumn();
$totalUsers = $db_stats->query("SELECT COUNT(*) FROM NguoiDung WHERE VaiTro='customer'")->fetchColumn();
$pendingOrders = $db_stats->query("SELECT COUNT(*) FROM DonHang WHERE TrangThai='Pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — GuitarX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-text: rgba(255,255,255,0.65);
            --sidebar-active: #e63946;
            --accent: #e63946;
            --accent2: #7c3aed;
            --topbar-h: 64px;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius: 12px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            margin: 0;
        }

        /* ===== SIDEBAR ===== */
        .admin-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .sidebar-top {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none;
        }
        .sidebar-logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--accent), #c1121f);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 20px rgba(230,57,70,0.35);
            flex-shrink: 0;
        }
        .sidebar-logo-icon .material-symbols-outlined { font-size: 22px; color: #fff; }
        .sidebar-logo-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .sidebar-logo-name span { color: var(--accent); }
        .sidebar-logo-tag {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.35);
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 500;
            line-height: 1;
        }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 4px 10px 8px;
            margin-top: 8px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.2s ease;
            position: relative;
        }
        .nav-item .material-symbols-outlined { font-size: 20px; flex-shrink: 0; }
        .nav-item:hover {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(230,57,70,0.2), rgba(230,57,70,0.1));
            color: #fff;
            border: 1px solid rgba(230,57,70,0.25);
        }
        .nav-item.active .material-symbols-outlined { color: var(--accent); }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 8px; bottom: 8px;
            width: 3px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
            left: -1px;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            line-height: 1.4;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        .user-info {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            margin-bottom: 8px;
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 0.85rem;
            flex-shrink: 0;
        }
        .user-name { font-size: 0.85rem; font-weight: 600; color: #fff; }
        .user-role { font-size: 0.7rem; color: rgba(255,255,255,0.4); }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 12px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-logout:hover {
            background: rgba(230,57,70,0.15);
            color: #e63946;
        }
        .btn-logout .material-symbols-outlined { font-size: 18px; }

        /* ===== MAIN CONTENT ===== */
        .admin-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        .admin-topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px;
            position: sticky; top: 0; z-index: 50;
            gap: 16px;
        }
        .topbar-title {
            font-size: 1.15rem; font-weight: 700;
            color: var(--text-primary);
            flex: 1;
        }
        .topbar-breadcrumb {
            font-size: 0.75rem; color: var(--text-muted);
            margin-top: 2px;
        }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 14px;
            color: var(--text-muted);
            font-size: 0.85rem;
            cursor: pointer;
        }
        .topbar-search .material-symbols-outlined { font-size: 18px; }

        /* ===== CONTENT AREA ===== */
        .admin-content {
            flex: 1;
            padding: 28px;
        }

        /* ===== DASHBOARD STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 24px;
            border: 1px solid var(--border);
            display: flex; align-items: center; gap: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 4px; height: 100%;
        }
        .stat-card.orders::before { background: #3b82f6; }
        .stat-card.revenue::before { background: #10b981; }
        .stat-card.products::before { background: #f59e0b; }
        .stat-card.users::before { background: #8b5cf6; }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-card.orders .stat-icon { background: rgba(59,130,246,0.12); color: #3b82f6; }
        .stat-card.revenue .stat-icon { background: rgba(16,185,129,0.12); color: #10b981; }
        .stat-card.products .stat-icon { background: rgba(245,158,11,0.12); color: #f59e0b; }
        .stat-card.users .stat-icon { background: rgba(139,92,246,0.12); color: #8b5cf6; }
        .stat-icon .material-symbols-outlined { font-size: 26px; }

        .stat-info { flex: 1; }
        .stat-value {
            font-size: 1.75rem; font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
            margin-bottom: 4px;
        }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
        .stat-sub {
            font-size: 0.75rem; color: #e63946; font-weight: 600;
            margin-top: 4px;
        }

        /* ===== ADMIN TABLES (SHARED) ===== */
        .admin-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }
        .admin-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .admin-card-title {
            font-size: 1rem; font-weight: 700;
            color: var(--text-primary); flex: 1;
        }

        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table thead th {
            padding: 12px 16px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-muted);
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .admin-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }
        .admin-table tbody tr:last-child { border-bottom: none; }
        .admin-table tbody tr:hover { background: #f8fafc; }
        .admin-table td { padding: 14px 16px; font-size: 0.875rem; vertical-align: middle; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .page-title { font-size: 1.4rem; font-weight: 800; color: var(--text-primary); }
        .page-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-top: 2px; }

        /* ===== BUTTONS ===== */
        .btn-primary-admin {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--accent), #c1121f);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(230,57,70,0.25);
        }
        .btn-primary-admin:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(230,57,70,0.35);
            color: #fff;
        }
        .btn-primary-admin .material-symbols-outlined { font-size: 18px; }

        .btn-icon {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
        }
        .btn-icon:hover { background: var(--bg); color: var(--text-primary); }
        .btn-icon .material-symbols-outlined { font-size: 16px; }
        .btn-icon.danger:hover { background: #fef2f2; color: #e63946; border-color: #fecaca; }
        .btn-icon.primary:hover { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }
        .btn-icon.success:hover { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }

        /* ===== BADGES ===== */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem; font-weight: 600;
        }
        .status-badge.pending { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .status-badge.shipping { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .status-badge.completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .status-badge.cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-badge.active-badge { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .status-badge.banned-badge { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }
        .status-badge.admin-badge { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-badge::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: currentColor; flex-shrink: 0;
        }

        /* Stock badges */
        .stock-ok { color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; }
        .stock-low { color: #92400e; background: #fffbeb; border: 1px solid #fde68a; }
        .stock-out { color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
        .stock-badge {
            display: inline-block;
            padding: 3px 10px; border-radius: 20px;
            font-size: 0.75rem; font-weight: 700;
        }

        /* ===== ALERTS ===== */
        .admin-alert {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 0.875rem; font-weight: 500;
            margin-bottom: 20px;
            border: 1px solid;
        }
        .admin-alert.success { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
        .admin-alert.error { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .admin-alert .material-symbols-outlined { font-size: 18px; }
        .admin-alert .close-btn {
            margin-left: auto; cursor: pointer; opacity: 0.6;
            font-size: 18px; line-height: 1;
            background: none; border: none; color: inherit; padding: 0;
        }
        .admin-alert .close-btn:hover { opacity: 1; }

        /* ===== MODAL STYLING ===== */
        .modal-content { border: none; border-radius: 14px; overflow: hidden; }
        .modal-header {
            background: var(--sidebar-bg);
            color: #fff; padding: 18px 24px;
            border-bottom: none;
        }
        .modal-header .btn-close { filter: invert(1) brightness(2); }
        .modal-title { font-weight: 700; font-size: 1rem; }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); }
        .form-label { font-weight: 600; font-size: 0.82rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control, .form-select {
            border-radius: 8px; border: 1.5px solid var(--border);
            font-size: 0.9rem; padding: 10px 14px;
            font-family: 'Inter', sans-serif; color: var(--text-primary);
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent); box-shadow: 0 0 0 4px rgba(230,57,70,0.08);
        }

        /* ===== PAGINATION ===== */
        .admin-pagination { display: flex; justify-content: center; gap: 6px; padding: 20px 0 4px; }
        .page-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem; font-weight: 600;
            transition: all 0.2s;
        }
        .page-btn:hover { background: var(--bg); color: var(--text-primary); }
        .page-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }

        /* ===== DASHBOARD WELCOME ===== */
        .welcome-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1a1033 100%);
            border-radius: var(--radius);
            padding: 32px 36px;
            margin-bottom: 28px;
            display: flex; align-items: center; justify-content: space-between;
            position: relative; overflow: hidden;
        }
        .welcome-banner::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(230,57,70,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }
        .welcome-title { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .welcome-sub { color: rgba(255,255,255,0.5); font-size: 0.9rem; }
        .welcome-icon {
            width: 60px; height: 60px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1;
        }
        .welcome-icon .material-symbols-outlined { font-size: 30px; color: var(--accent); }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-top">
        <a href="index.php" class="sidebar-logo" style="text-decoration:none">
            <div class="sidebar-logo-icon">
                <span class="material-symbols-outlined">music_note</span>
            </div>
            <div>
                <div class="sidebar-logo-name">Guitar<span>X</span></div>
                <div class="sidebar-logo-tag">Admin Panel</div>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <?php $act = $_GET['act'] ?? 'dashboard'; ?>
        <div class="nav-section-label">Tổng quan</div>
        <a href="index.php" class="nav-item <?= $act === 'dashboard' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">dashboard</span>
            Dashboard
        </a>

        <div class="nav-section-label" style="margin-top:12px">Quản lý</div>
        <a href="index.php?act=quanlysanpham" class="nav-item <?= $act === 'quanlysanpham' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">inventory_2</span>
            Sản Phẩm
        </a>
        <a href="index.php?act=quanlydanhmuc" class="nav-item <?= $act === 'quanlydanhmuc' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">category</span>
            Danh Mục
        </a>
        <a href="index.php?act=quanlydonhang" class="nav-item <?= $act === 'quanlydonhang' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">receipt_long</span>
            Đơn Hàng
            <?php if ($pendingOrders > 0): ?>
                <span class="nav-badge"><?= $pendingOrders ?></span>
            <?php endif; ?>
        </a>
        <a href="index.php?act=quanlyuser" class="nav-item <?= $act === 'quanlyuser' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">group</span>
            Người Dùng
        </a>
        <a href="index.php?act=quanlyvoucher" class="nav-item <?= $act === 'quanlyvoucher' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">local_offer</span>
            Voucher
        </a>

        <div class="nav-section-label" style="margin-top:12px">Khác</div>
        <a href="index.php?act=quanlySanSale" class="nav-item <?= $act === 'quanlySanSale' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">local_fire_department</span>
            Sale Chớp Nhoáng
        </a>
        <a href="index.php?act=quanlychat" class="nav-item <?= $act === 'quanlychat' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">chat</span>
            Quản Lý Chat
        </a>
        <a href="../index.php" class="nav-item" target="_blank">
            <span class="material-symbols-outlined">open_in_new</span>
            Trang Khách Hàng
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
                <div class="user-role">Quản trị viên</div>
            </div>
        </div>
        <a href="../controller/user.php?act=logout" class="btn-logout">
            <span class="material-symbols-outlined">logout</span>
            Đăng xuất
        </a>
    </div>
</aside>

<!-- ===== MAIN AREA ===== -->
<div class="admin-main">
    <!-- TOPBAR -->
    <header class="admin-topbar">
        <button class="btn-icon d-md-none" onclick="toggleSidebar()" style="border:none;background:none;font-size:22px;">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div style="flex:1">
            <?php
            $pageTitles = [
                'dashboard'     => ['Dashboard', 'Tổng quan hệ thống'],
                'quanlysanpham' => ['Sản Phẩm', 'Quản lý kho hàng'],
                'quanlydanhmuc' => ['Danh Mục', 'Phân loại sản phẩm'],
                'quanlydonhang' => ['Đơn Hàng', 'Theo dõi & xử lý đơn hàng'],
                'quanlyuser'    => ['Người Dùng', 'Quản lý tài khoản khách hàng'],
                'quanlyvoucher' => ['Voucher', 'Mã giảm giá khuyến mãi'],
                'quanlySanSale' => ['Sale Chớp Nhoáng', 'Sản phẩm khuyến mãi đặc biệt'],
                'quanlychat'    => ['Tin Nhắn', 'Chăm sóc khách hàng'],
            ];
            $currentTitle = $pageTitles[$act] ?? ['Admin', 'GuitarX Admin Panel'];
            ?>
            <div class="topbar-title"><?= $currentTitle[0] ?></div>
            <div class="topbar-breadcrumb">GuitarX Admin / <?= $currentTitle[1] ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:0.8rem;color:var(--text-muted);"><?= date('d/m/Y H:i') ?></span>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="admin-content">
        <?php
        switch ($act) {
            case 'quanlysanpham':  include 'quanlysanpham.php';  break;
            case 'quanlydanhmuc':  include 'quanlydanhmuc.php';  break;
            case 'quanlydonhang':  include 'quanlydonhang.php';  break;
            case 'quanlyuser':     include 'quanlyuser.php';     break;
            case 'quanlyvoucher':  include 'quanlyvoucher.php';  break;
            case 'quanlySanSale':  include 'quanlysansale.php';  break;
            case 'quanlychat':     include 'quanlychat.php';     break;
            case 'dashboard':
            default:
                // Dashboard
        ?>
        <div class="welcome-banner">
            <div style="position:relative;z-index:1">
                <div class="welcome-title">Chào mừng, <?= htmlspecialchars($_SESSION['username']) ?>! 👋</div>
                <div class="welcome-sub">Đây là tổng quan hoạt động hệ thống GuitarX hôm nay.</div>
            </div>
            <div class="welcome-icon">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card orders">
                <div class="stat-icon"><span class="material-symbols-outlined">receipt_long</span></div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($totalOrders) ?></div>
                    <div class="stat-label">Tổng Đơn Hàng</div>
                    <?php if ($pendingOrders > 0): ?>
                        <div class="stat-sub"><?= $pendingOrders ?> đang chờ xử lý</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="stat-card revenue">
                <div class="stat-icon"><span class="material-symbols-outlined">payments</span></div>
                <div class="stat-info">
                    <div class="stat-value" style="font-size:1.3rem"><?= number_format($totalRevenue, 0, ',', '.') ?>₫</div>
                    <div class="stat-label">Doanh Thu (Hoàn thành)</div>
                </div>
            </div>
            <div class="stat-card products">
                <div class="stat-icon"><span class="material-symbols-outlined">inventory_2</span></div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($totalProducts) ?></div>
                    <div class="stat-label">Sản Phẩm</div>
                </div>
            </div>
            <div class="stat-card users">
                <div class="stat-icon"><span class="material-symbols-outlined">group</span></div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($totalUsers) ?></div>
                    <div class="stat-label">Khách Hàng</div>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <span class="material-symbols-outlined" style="color:var(--accent);font-size:20px">receipt_long</span>
                <span class="admin-card-title">Đơn hàng mới nhất</span>
                <a href="index.php?act=quanlydonhang" class="btn-primary-admin" style="padding:7px 14px;font-size:0.8rem">
                    Xem tất cả
                </a>
            </div>
            <?php
            $recentOrders = $db_stats->query("
                SELECT dh.Ma_DonHang, nd.TenNguoiDung,
                       GREATEST(0, COALESCE(SUM(ctd.TongTien),0) - COALESCE(v.GiaTriGiam,0)) AS TongTien,
                       dh.TrangThai, dh.NgayDatHang
                FROM DonHang dh
                JOIN NguoiDung nd ON dh.Ma_NguoiDung = nd.Ma_NguoiDung
                LEFT JOIN ChiTietDonHang ctd ON dh.Ma_DonHang = ctd.Ma_DonHang
                LEFT JOIN MaGiamGia v ON dh.Ma_MaGiamGia = v.Ma_MaGiamGia
                GROUP BY dh.Ma_DonHang, nd.TenNguoiDung, dh.TrangThai, dh.NgayDatHang, v.GiaTriGiam
                ORDER BY dh.NgayDatHang DESC LIMIT 6
            ")->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Ngày Đặt</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td style="padding-left:24px"><span style="font-weight:700;color:#e63946">#<?= $o['Ma_DonHang'] ?></span></td>
                        <td style="font-weight:600"><?= htmlspecialchars($o['TenNguoiDung']) ?></td>
                        <td style="font-weight:700;color:var(--text-primary)"><?= number_format($o['TongTien'], 0, ',', '.') ?>₫</td>
                        <td style="color:var(--text-muted)"><?= date('d/m/Y H:i', strtotime($o['NgayDatHang'])) ?></td>
                        <td>
                            <?php
                            $sc = ['Pending'=>'pending','Shipping'=>'shipping','Completed'=>'completed','Cancelled'=>'cancelled'];
                            $sl = ['Pending'=>'Đang xử lý','Shipping'=>'Đang giao','Completed'=>'Hoàn thành','Cancelled'=>'Đã hủy'];
                            $cls = $sc[$o['TrangThai']] ?? 'pending';
                            ?>
                            <span class="status-badge <?= $cls ?>"><?= $sl[$o['TrangThai']] ?? $o['TrangThai'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php break; } ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('open');
}
// Auto-dismiss alerts
document.querySelectorAll('.admin-alert .close-btn').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.admin-alert').remove());
});
</script>
</body>
</html>