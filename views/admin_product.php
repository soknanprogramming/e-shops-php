<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';

// 1. Auth Check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$productRepo = new ProductRepository($conn);

// Filters
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'id_desc';

$sortMap = [
    'id_desc' => 'newest',
    'id_asc' => 'oldest',
    'name_asc' => 'name_asc',
    'name_desc' => 'name_desc',
    'price_asc' => 'price_asc',
    'price_desc' => 'price_desc',
    'owner_asc' => 'owner_asc',
    'owner_desc' => 'owner_desc',
    'status_asc' => 'status_asc',
    'status_desc' => 'status_desc',
];

$filters = [
    'include_hidden' => true,
    'name' => $_GET['name'] ?? null,
    'seller' => $_GET['seller'] ?? null,
    'sort' => $sortMap[$orderBy] ?? 'newest',
];

$products = $productRepo->search($filters);

// Manual filter for status
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $status = (int)$_GET['status'];
    $products = array_filter($products, function($p) use ($status) {
        return $p['showed'] == $status;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
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
            width: 100%;
        }

        @media (max-width: 992px) { .main-content { padding: 1.25rem 2rem; } }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .main-content { padding: 1rem; }
        }

        @media (max-width: 480px) {
            .main-content { padding: 0.75rem; }
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .page-header h1 {
            font-family: var(--font-headline);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        .page-header .count-badge {
            font-size: 0.8rem;
            color: var(--on-surface-variant);
            font-weight: 600;
            background: var(--surface);
            border: 1px solid var(--outline);
            padding: 6px 14px;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.25rem; }
            .page-header .count-badge { font-size: 0.75rem; padding: 5px 12px; }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            .page-header .count-badge {
                align-self: flex-start;
            }
        }

        /* Filter Bar */
        .filter-bar {
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-bar .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
            min-width: 150px;
        }

        .filter-bar label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
        }

        .filter-bar input,
        .filter-bar select {
            padding: 8px 10px;
            border: 1.5px solid var(--outline);
            border-radius: var(--radius-sm);
            font-size: 0.825rem;
            font-family: var(--font-body);
            color: var(--on-surface);
            background: var(--bg-body);
            outline: none;
            transition: border-color 0.2s;
            width: 100%;
        }

        .filter-bar input:focus,
        .filter-bar select:focus {
            border-color: var(--primary);
        }

        .btn-filter {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .btn-filter:hover { background: var(--primary-container); }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: transparent;
            color: var(--on-surface-variant);
            border: 1.5px solid var(--outline-strong);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-reset:hover { border-color: var(--on-surface-variant); color: var(--on-surface); }

        @media (max-width: 768px) {
            .filter-bar {
                padding: 0.75rem 1rem;
            }
            .filter-bar .filter-group {
                min-width: 120px;
            }
            .filter-bar input,
            .filter-bar select {
                font-size: 0.775rem;
                padding: 6px 8px;
            }
            .btn-filter,
            .btn-reset {
                font-size: 0.7rem;
                padding: 6px 14px;
            }
        }

        @media (max-width: 480px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-bar .filter-group {
                min-width: 100%;
            }
            .filter-bar > div:last-child {
                display: flex;
                gap: 0.5rem;
                margin-top: 0.5rem;
            }
        }

        /* Table Card */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-md);
            overflow: hidden;
        }

        .table-responsive { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            background: var(--bg-body);
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
            border-bottom: 1px solid var(--outline);
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--outline);
            font-size: 0.85rem;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover { background: var(--primary-light); }

        .product-img {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            background: var(--bg-body);
        }

        .product-name {
            text-decoration: none;
            color: var(--on-surface);
            font-weight: 600;
            transition: color 0.2s;
        }

        .product-name:hover { color: var(--primary); }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-visible { background: rgba(40, 167, 69, 0.12); color: #28a745; }
        .badge-hidden { background: rgba(108, 117, 125, 0.12); color: #6c757d; }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-show { background: rgba(40, 167, 69, 0.12); color: #28a745; }
        .btn-show:hover { background: rgba(40, 167, 69, 0.2); }
        .btn-hide { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
        .btn-hide:hover { background: rgba(220, 53, 69, 0.2); }

        @media (max-width: 768px) {
            .action-btn {
                padding: 5px 10px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .action-btn {
                padding: 4px 8px;
                font-size: 0.65rem;
                gap: 3px;
            }
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--on-surface-variant);
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            opacity: 0.3;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            margin: 0;
            font-size: 0.875rem;
        }

        /* Table responsive */
        @media (max-width: 768px) {
            th, td {
                padding: 10px 12px;
                font-size: 0.8rem;
            }
            .product-img {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            th, td {
                padding: 8px 10px;
                font-size: 0.75rem;
            }
            .product-img {
                width: 36px;
                height: 36px;
            }
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            color: #fff;
            font-weight: 600;
            font-size: 0.825rem;
            z-index: 9999;
            animation: toastIn 0.3s ease, toastOut 0.3s ease 2.7s forwards;
        }

        .toast-success { background: var(--primary); }
        .toast-error { background: var(--tertiary); }

        @keyframes toastIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes toastOut { from { opacity: 1; } to { opacity: 0; transform: translateY(10px); } }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Product Management</h1>
            <span class="count-badge"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></span>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <label>Product Name</label>
                <input type="text" name="name" placeholder="Search name..." value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>" form="filterForm">
            </div>
            <div class="filter-group">
                <label>Seller</label>
                <input type="text" name="seller" placeholder="Seller name..." value="<?php echo htmlspecialchars($_GET['seller'] ?? ''); ?>" form="filterForm">
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status" form="filterForm">
                    <option value="">All Status</option>
                    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : ''; ?>>Visible</option>
                    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : ''; ?>>Hidden</option>
                </select>
            </div>
            <form id="filterForm" action="" method="GET" style="display:contents;">
                <button type="submit" class="btn-filter">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>
                <a href="admin_product.php" class="btn-reset">Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'id_asc') ? 'id_desc' : 'id_asc'; ?><?php echo !empty($_GET['name']) ? '&name=' . urlencode($_GET['name']) : ''; ?><?php echo !empty($_GET['seller']) ? '&seller=' . urlencode($_GET['seller']) : ''; ?><?php echo !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>"
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    ID
                                    <?php if (strpos($orderBy, 'id') !== false): ?>
                                        <span><?php echo $orderBy === 'id_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Image</th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'name_asc') ? 'name_desc' : 'name_asc'; ?><?php echo !empty($_GET['name']) ? '&name=' . urlencode($_GET['name']) : ''; ?><?php echo !empty($_GET['seller']) ? '&seller=' . urlencode($_GET['seller']) : ''; ?><?php echo !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>"
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Name
                                    <?php if (strpos($orderBy, 'name') !== false): ?>
                                        <span><?php echo $orderBy === 'name_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'owner_asc') ? 'owner_desc' : 'owner_asc'; ?><?php echo !empty($_GET['name']) ? '&name=' . urlencode($_GET['name']) : ''; ?><?php echo !empty($_GET['seller']) ? '&seller=' . urlencode($_GET['seller']) : ''; ?><?php echo !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>"
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Owner
                                    <?php if (strpos($orderBy, 'owner') !== false): ?>
                                        <span><?php echo $orderBy === 'owner_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'price_asc') ? 'price_desc' : 'price_asc'; ?><?php echo !empty($_GET['name']) ? '&name=' . urlencode($_GET['name']) : ''; ?><?php echo !empty($_GET['seller']) ? '&seller=' . urlencode($_GET['seller']) : ''; ?><?php echo !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>"
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Price
                                    <?php if (strpos($orderBy, 'price') !== false): ?>
                                        <span><?php echo $orderBy === 'price_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?order=<?php echo ($orderBy === 'status_asc') ? 'status_desc' : 'status_asc'; ?><?php echo !empty($_GET['name']) ? '&name=' . urlencode($_GET['name']) : ''; ?><?php echo !empty($_GET['seller']) ? '&seller=' . urlencode($_GET['seller']) : ''; ?><?php echo !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>"
                                   style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 4px;">
                                    Status
                                    <?php if (strpos($orderBy, 'status') !== false): ?>
                                        <span><?php echo $orderBy === 'status_asc' ? '↑' : '↓'; ?></span>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <p>No products found matching your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                        <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image'] ?? 'default.png'); ?>" class="product-img">
                                    </a>
                                </td>
                                <td>
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-name">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($product['owner_name']); ?></td>
                                <td>$<?php echo number_format($product['prices'], 2); ?></td>
                                <td>
                                    <?php if ($product['showed']): ?>
                                        <span class="badge badge-visible">Visible</span>
                                    <?php else: ?>
                                        <span class="badge badge-hidden">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="../controllers/product.php?action=toggle_visibility&id=<?php echo $product['id']; ?>&status=<?php echo $product['showed'] ? '0' : '1'; ?>"
                                        class="action-btn <?php echo $product['showed'] ? 'btn-hide' : 'btn-show'; ?>">
                                            <?php echo $product['showed'] ? 'Hide' : 'Show'; ?>
                                        </a>
                                        <a href="../controllers/product.php?action=delete&id=<?php echo $product['id']; ?>"
                                        class="action-btn btn-hide"
                                        onclick="return confirm('Are you sure you want to delete this product permanently?')">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
        <div class="toast <?php echo isset($_GET['success']) ? 'toast-success' : 'toast-error'; ?>">
            <?php echo htmlspecialchars($_GET['success'] ?? $_GET['error'] ?? ''); ?>
        </div>
    <?php endif; ?>
</body>
</html>
