<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/UserRepository.php';
require_once '../repos/ProductRepository.php';
require_once '../repos/CategoryRepository.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

$userRepo = new UserRepository($conn);
$pendingRequests = $userRepo->getPendingRequests();
$pendingCount = count($pendingRequests);

// Fetch stats
$productRepo = new ProductRepository($conn);
$catRepo = new CategoryRepository($conn);

$stmtTotalUsers = $conn->prepare("SELECT COUNT(*) as total FROM User");
$stmtTotalUsers->execute();
$totalUsers = $stmtTotalUsers->fetch()['total'];

$stmtTotalProducts = $conn->prepare("SELECT COUNT(*) as total FROM Product");
$stmtTotalProducts->execute();
$totalProducts = $stmtTotalProducts->fetch()['total'];

$stmtTotalCategories = $conn->prepare("SELECT COUNT(*) as total FROM category");
$stmtTotalCategories->execute();
$totalCategories = $stmtTotalCategories->fetch()['total'];

$stmtActiveProducts = $conn->prepare("SELECT COUNT(*) as total FROM Product WHERE showed = 1");
$stmtActiveProducts->execute();
$activeProducts = $stmtActiveProducts->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="../icon/e-commerce-logo.png" sizes="any" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3325;
            --primary-container: #2a5038;
            --primary-light: rgba(26, 51, 37, 0.05);
            --secondary: #9d7c39;
            --secondary-light: rgba(157, 124, 57, 0.1);
            --tertiary: #7e000a;
            --bg-body: #faf7f2;
            --surface: #ffffff;
            --on-surface: #201b09;
            --on-surface-variant: #6b6355;
            --outline: rgba(74, 69, 56, 0.12);
            --outline-strong: rgba(74, 69, 56, 0.25);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --font-headline: 'Manrope', sans-serif;
            --font-body: 'Public Sans', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            margin: 0;
            padding: 0;
            background-color: var(--bg-body);
            color: var(--on-surface);
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            padding: 1.5rem 3rem;
            max-width: calc(100vw - 240px);
        }

        @media (max-width: 992px) { .main-content { padding: 1.25rem 2rem; } }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .admin-sidebar {
                width: 100% !important;
                min-height: auto !important;
                flex-direction: row !important;
                overflow-x: auto;
            }
            .sidebar-menu { display: flex; padding: 0.5rem !important; gap: 4px; flex-grow: 1; }
            .sidebar-menu li a { white-space: nowrap; flex-shrink: 0; padding: 8px 12px !important; font-size: 0.75rem !important; }
            .sidebar-menu li a .badge-count { display: none; }
            .sidebar-menu li a svg { display: none; }
            .sidebar-brand { display: none; }
            .sidebar-footer { display: none; }
            .main-content { max-width: 100%; padding: 1rem; }
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-family: var(--font-headline);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 0.25rem;
        }

        .page-header p {
            font-size: 0.875rem;
            color: var(--on-surface-variant);
            margin: 0;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-container) 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .welcome-card h2 {
            font-family: var(--font-headline);
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0 0 0.5rem;
        }

        .welcome-card p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .stats-grid { grid-template-columns: 1fr; } }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg { width: 24px; height: 24px; }

        .stat-icon.users { background: rgba(26, 51, 37, 0.1); color: var(--primary); }
        .stat-icon.products { background: rgba(157, 124, 57, 0.1); color: var(--secondary); }
        .stat-icon.categories { background: rgba(126, 0, 10, 0.1); color: var(--tertiary); }
        .stat-icon.active { background: rgba(40, 167, 69, 0.1); color: #28a745; }

        .stat-info .stat-value {
            font-family: var(--font-headline);
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            line-height: 1;
        }

        .stat-info .stat-label {
            font-size: 0.75rem;
            color: var(--on-surface-variant);
            font-weight: 600;
            margin: 0.25rem 0 0;
        }

        /* Pending Alert */
        .pending-alert {
            background: var(--secondary-light);
            border: 1px solid rgba(157, 124, 57, 0.2);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .pending-alert-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pending-alert svg {
            width: 28px;
            height: 28px;
            color: var(--secondary);
            flex-shrink: 0;
        }

        .pending-alert h3 {
            font-family: var(--font-headline);
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 0.125rem;
            color: var(--on-surface);
        }

        .pending-alert p {
            margin: 0;
            font-size: 0.825rem;
            color: var(--on-surface-variant);
        }

        .btn-review {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: var(--secondary);
            color: #fff;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            transition: opacity 0.2s;
            white-space: nowrap;
        }

        .btn-review:hover { opacity: 0.85; }
        .btn-review svg { width: 16px; height: 16px; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h2>Admin Control Panel</h2>
            <p>Manage users, products, and categories from this central dashboard.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?php echo $totalUsers; ?></p>
                    <p class="stat-label">Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon products">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?php echo $totalProducts; ?></p>
                    <p class="stat-label">Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon categories">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?php echo $totalCategories; ?></p>
                    <p class="stat-label">Categories</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?php echo $activeProducts; ?></p>
                    <p class="stat-label">Active Products</p>
                </div>
            </div>
        </div>

        <!-- Pending Requests Alert -->
        <?php if ($pendingCount > 0): ?>
            <div class="pending-alert">
                <div class="pending-alert-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h3><?php echo $pendingCount; ?> Pending Request<?php echo $pendingCount > 1 ? 's' : ''; ?></h3>
                        <p>Users are waiting for posting permission approval.</p>
                    </div>
                </div>
                <a href="admin_user.php?filter=requesting" class="btn-review">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Review
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
