<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';
require_once '../repos/CategoryRepository.php';

$categoryRepo = new CategoryRepository($conn);
$categories = $categoryRepo->getAll();

$productRepo = new ProductRepository($conn);

// Pagination Logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Products per page

$filters = [
    'category_id' => $_GET['category_id'] ?? null,
    'min_price' => $_GET['min_price'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'has_discount' => isset($_GET['has_discount']) ? 1 : 0,
    'name' => $_GET['name'] ?? null,
    'seller' => $_GET['seller'] ?? null,
    'sort' => $_GET['sort'] ?? 'newest',
    'liked_only' => isset($_GET['liked_only']) ? 1 : 0,
    'limit' => $limit,
    'offset' => ($page - 1) * $limit
];

if ($filters['liked_only'] && isset($_SESSION['user_id'])) {
    $filters['liked_by_user_id'] = $_SESSION['user_id'];
}

$totalProducts = $productRepo->countSearch($filters);
$totalPages = ceil($totalProducts / $limit);
$products = $productRepo->search($filters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Scholarly Forest Editorial Palette */
            --primary: #1a3325; /* Deep Forest Green */
            --primary-container: #2a5038;
            --primary-light: rgba(26, 51, 37, 0.05);
            --secondary: #9d7c39; /* Sacred Gold */
            --secondary-light: rgba(157, 124, 57, 0.1);
            --secondary-container: #fef3d5;
            --on-secondary-container: #201b09;
            --tertiary: #7e000a; /* Academic Red */
            --success: #10b981;
            --bg-body: #faf7f2; /* Soft Cream Foundation */
            --surface: #ffffff;
            --surface-low: #fef9f3;
            --surface-container-low: #fef3d5;
            --surface-container-highest: #ede2c5;
            --on-surface: #201b09;
            --on-surface-variant: #6b6355;
            --outline: rgba(74, 69, 56, 0.12);
            --outline-strong: rgba(74, 69, 56, 0.25);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 2px 8px rgba(32, 27, 9, 0.06);
            --shadow-md: 0 4px 16px rgba(32, 27, 9, 0.08);
            --shadow-lg: 0 8px 32px rgba(32, 27, 9, 0.12);
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
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
        }

        /* Main Content Container */
        .main-content {
            max-width: 1440px;
            margin: 0 auto;
            padding: 1.25rem 6rem;
        }

        @media (max-width: 1200px) {
            .main-content { padding: 1.25rem 3rem; }
        }

        @media (max-width: 992px) {
            .main-content { padding: 1rem 2rem; }
        }

        @media (max-width: 576px) {
            .main-content { padding: 1rem; }
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1rem;
        }

        .page-header h1 {
            font-family: var(--font-headline);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 0.5rem;
            color: var(--primary);
        }

        .page-header p {
            font-size: 1rem;
            color: var(--on-surface-variant);
            margin: 0;
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.5rem; }
            .page-header p { font-size: 0.875rem; }
        }

        /* Categories Section */
        .categories-section {
            margin-bottom: 1rem;
        }

        .categories-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 4px 0 12px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .categories-scroll::-webkit-scrollbar { display: none; }

        .category-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            padding: 8px 16px;
            background: var(--surface);
            border: 1.5px solid var(--outline);
            border-radius: 9999px;
            text-decoration: none;
            color: var(--on-surface-variant);
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .category-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .category-chip.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        /* Toolbar Section (Filters & Sort) */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .results-count {
            font-size: 0.875rem;
            color: var(--on-surface-variant);
            font-weight: 500;
        }

        .filter-toggle-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--surface);
            border: 1.5px solid var(--outline-strong);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--on-surface);
            transition: all 0.2s;
        }

        .filter-toggle-btn:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .filter-toggle-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* Advanced Filters Panel */
        .advanced-filters-panel {
            background: var(--surface);
            border: 1.5px solid var(--outline);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1rem;
            display: none;
        }

        .advanced-filters-panel.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
            margin-bottom: 0.5rem;
        }

        .filter-group input {
            width: 100%;
            padding: 10px 12px;
            background: var(--bg-body);
            border: 1.5px solid var(--outline);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            color: var(--on-surface);
            outline: none;
            transition: border-color 0.2s;
        }

        .filter-group input:focus {
            border-color: var(--primary);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn-apply-filters {
            padding: 10px 24px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-apply-filters:hover {
            background: var(--primary-container);
        }

        .btn-reset-filters {
            padding: 10px 16px;
            background: transparent;
            color: var(--on-surface-variant);
            border: 1.5px solid var(--outline-strong);
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-reset-filters:hover {
            border-color: var(--on-surface-variant);
            color: var(--on-surface);
        }

        .filter-checkboxes {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--on-surface);
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            accent-color: var(--secondary);
            width: 16px;
            height: 16px;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 992px) {
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem; }
        }

        @media (max-width: 576px) {
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; }
        }

        .product-card {
            background: var(--surface);
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1.5px solid var(--outline);
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--outline-strong);
        }

        .product-image-wrapper {
            position: relative;
            width: 100%;
            padding-top: 75%;
            background: var(--surface-low);
            overflow: hidden;
        }

        .product-image-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-image-wrapper img {
            transform: scale(1.05);
        }

        .discount-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--tertiary);
            color: #fff;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            z-index: 2;
        }

        .product-info {
            padding: 1rem 1.25rem 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .product-category {
            font-size: 0.7rem;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .product-name {
            margin: 0;
            font-family: var(--font-headline);
            font-size: 1.125rem;
            font-weight: 700;
            line-height: 1.3;
            color: var(--on-surface);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-location {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            color: var(--on-surface-variant);
            margin-top: 0.25rem;
        }

        .product-location svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .product-price-row {
            margin-top: auto;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
            padding-top: 0.75rem;
        }

        .price-current {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.25rem;
            line-height: 1;
        }

        .price-old {
            color: var(--on-surface-variant);
            text-decoration: line-through;
            font-size: 0.875rem;
            opacity: 0.7;
        }

        .product-link {
            text-decoration: none;
            color: inherit;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            color: var(--on-surface-variant);
            opacity: 0.4;
        }

        .empty-state h3 {
            font-family: var(--font-headline);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--on-surface);
            margin: 0 0 0.5rem;
        }

        .empty-state p {
            font-size: 1rem;
            color: var(--on-surface-variant);
            margin: 0 0 1.5rem;
        }

        .empty-state .btn-clear {
            display: inline-block;
            padding: 12px 24px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .empty-state .btn-clear:hover {
            background: var(--primary-container);
        }

        /* Pagination */
        .pagination {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .pagination a {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
            border: 1.5px solid var(--outline-strong);
            text-decoration: none;
            border-radius: var(--radius-sm);
            background: var(--surface);
            color: var(--on-surface);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination a:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
        }

        .pagination a.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .pagination a.disabled {
            opacity: 0.3;
            pointer-events: none;
        }

        @media (max-width: 576px) {
            .pagination { gap: 4px; }
            .pagination a { min-width: 36px; height: 36px; padding: 0 8px; font-size: 0.75rem; }
        }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="main-content">
        <!-- Page Header -->
        <header class="page-header">
            <h1><?php echo isset($_GET['category_id']) ? 'Browse Products' : 'Discover Products'; ?></h1>
        </header>

        <!-- Categories -->
        <nav class="categories-section">
            <div class="categories-scroll">
                <?php
                function getCategoryUrl($catId = null) {
                    $params = $_GET;
                    if ($catId === null) {
                        unset($params['category_id']);
                    } else {
                        $params['category_id'] = $catId;
                    }
                    unset($params['page']);
                    return 'home.php?' . http_build_query($params);
                }
                ?>
                <a href="<?php echo getCategoryUrl(); ?>" class="category-chip <?php echo !isset($_GET['category_id']) ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?php echo getCategoryUrl($cat['id']); ?>" class="category-chip <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="toolbar-left">
                <span class="results-count"><?php echo $totalProducts; ?> results</span>
                <button class="filter-toggle-btn" id="filterToggle">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                </button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="filter-toggle-btn <?php echo isset($_GET['liked_only']) ? 'active' : ''; ?>" id="likedBtn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        Saved
                    </button>
                <?php endif; ?>
            </div>
            <div class="toolbar-right">
                <button class="filter-toggle-btn" id="sortBtn" data-sort="<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'oldest' : 'newest'; ?>">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    <span id="sortLabel"><?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'Oldest' : 'Newest'; ?></span>
                </button>
            </div>
        </div>

        <!-- Advanced Filters Panel -->
        <div class="advanced-filters-panel" id="advancedFilters">
            <form action="home.php" method="GET" class="filters-form">
                <?php
                if(isset($_GET['name'])) echo '<input type="hidden" name="name" value="'.htmlspecialchars($_GET['name']).'">';
                if(isset($_GET['category_id'])) echo '<input type="hidden" name="category_id" value="'.htmlspecialchars($_GET['category_id']).'">';
                ?>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Min Price ($)</label>
                        <input type="number" name="min_price" placeholder="0" step="0.01" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
                    </div>
                    <div class="filter-group">
                        <label>Max Price ($)</label>
                        <input type="number" name="max_price" placeholder="No limit" step="0.01" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-apply-filters">Apply Filters</button>
                    <a href="home.php" class="btn-reset-filters">Reset All</a>
                </div>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or search terms to find what you're looking for.</p>
                    <a href="home.php" class="btn-clear">Clear All Filters</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                            <div class="product-image-wrapper">
                                <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" alt="Product Image" loading="lazy">
                                <?php if($product['discounts'] > 0): ?>
                                    <span class="discount-badge">-<?php echo round(($product['discounts'] / ($product['prices'] + $product['discounts'])) * 100); ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <?php if (!empty($product['location'])): ?>
                                <div class="product-location">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($product['location']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="product-price-row">
                                    <span class="price-current">$<?php echo number_format($product['prices'], 2); ?></span>
                                    <?php if($product['discounts'] > 0): ?>
                                        <span class="price-old">$<?php echo number_format($product['prices'] + $product['discounts'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                function getUrl($pageNum) {
                    $params = $_GET;
                    $params['page'] = $pageNum;
                    return 'home.php?' . http_build_query($params);
                }
                ?>

                <?php if ($page > 1): ?>
                    <a href="<?php echo getUrl($page - 1); ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php
                // Smart pagination - show limited pages around current
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                if ($startPage > 1): ?>
                    <a href="<?php echo getUrl(1); ?>">1</a>
                    <?php if ($startPage > 2): ?>
                        <a class="disabled">...</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="<?php echo getUrl($i); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <a class="disabled">...</a>
                    <?php endif; ?>
                    <a href="<?php echo getUrl($totalPages); ?>"><?php echo $totalPages; ?></a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo getUrl($page + 1); ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Filter toggle
        document.getElementById('filterToggle').addEventListener('click', function() {
            const panel = document.getElementById('advancedFilters');
            panel.classList.toggle('show');
            this.classList.toggle('active');
        });

        // Toggle sort
        const sortBtn = document.getElementById('sortBtn');
        if (sortBtn) {
            sortBtn.addEventListener('click', function() {
                const params = new URLSearchParams(window.location.search);
                const current = this.dataset.sort;
                const next = current === 'newest' ? 'oldest' : 'newest';
                this.dataset.sort = next;
                document.getElementById('sortLabel').textContent = next === 'newest' ? 'Newest' : 'Oldest';
                params.set('sort', next);
                params.delete('page');
                window.location.href = 'home.php?' + params.toString();
            });
        }

        // Show filters if any are active
        (function() {
            const hasFilters = <?php echo (isset($_GET['min_price']) || isset($_GET['max_price'])) ? 'true' : 'false'; ?>;
            if (hasFilters) {
                document.getElementById('advancedFilters').classList.add('show');
                document.getElementById('filterToggle').classList.add('active');
            }
        })();

        // Toggle liked filter
        const likedBtn = document.getElementById('likedBtn');
        if (likedBtn) {
            likedBtn.addEventListener('click', function() {
                const params = new URLSearchParams(window.location.search);
                if (params.has('liked_only')) {
                    params.delete('liked_only');
                } else {
                    params.set('liked_only', '1');
                }
                params.delete('page');
                window.location.href = 'home.php?' + params.toString();
            });
        }
    </script>
</body>
</html>