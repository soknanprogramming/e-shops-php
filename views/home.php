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
    'location' => $_GET['location'] ?? null,
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --text-main: #111827;
            --text-sub: #6b7280;
            --border: #e5e7eb;
            --radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: var(--bg-body); color: var(--text-main); -webkit-font-smoothing: antialiased; }
        .container { padding: 20px; max-width: 1280px; margin: 0 auto; }
        
        /* Hero Section */
        .header-section {
            display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; padding: 20px 0;
        }
        .header-title h1 { margin: 0; font-size: 1.875rem; font-weight: 800; letter-spacing: -0.025em; color: var(--text-main); }
        .header-title p { margin: 4px 0 0; color: var(--text-sub); font-size: 1rem; }

        /* Category Nav */
        .category-nav { display: none; gap: 12px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 24px; scrollbar-width: none; }
        .category-nav::-webkit-scrollbar { display: none; }
        .category-nav a { 
            display: flex; align-items: center; gap: 8px; white-space: nowrap; padding: 10px 20px; 
            background: var(--bg-card); border: 1px solid var(--border); border-radius: 100px; 
            text-decoration: none; color: var(--text-sub); font-weight: 600; font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .category-nav a:hover, .category-nav a.active { 
            background: var(--text-main); color: white; border-color: var(--text-main); 
            box-shadow: var(--shadow-md);
        }
        .category-nav img { width: 20px; height: 20px; object-fit: cover; border-radius: 50%; }
        
        /* Filters */
        .filter-container { 
            background: var(--bg-card); padding: 15px; border-radius: var(--radius); 
            margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border); 
            display: none; flex-direction: column; gap: 15px;
        }
        .search-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
        .filter-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding-top: 12px; border-top: 1px solid var(--border); }
        .filter-group { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        
        input[type="text"], input[type="number"], select { 
            padding: 8px 12px; border: 1px solid var(--border); background-color: #f9fafb; 
            border-radius: 8px; outline: none; transition: all 0.2s; font-family: inherit; 
            width: 100%; box-sizing: border-box; font-size: 0.85rem; color: var(--text-main);
        }
        input:focus, select:focus { 
            background-color: white; border-color: var(--primary); 
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); 
        }
        
        .price-inputs { display: flex; align-items: center; gap: 6px; }
        .price-inputs input { width: 80px; }
        
        .btn-filter { 
            padding: 8px 20px; background-color: var(--text-main); color: white; border: none; 
            border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; font-size: 0.85rem; 
        }
        .btn-filter:hover { background-color: black; transform: translateY(-1px); }
        .btn-clear { 
            color: var(--text-sub); text-decoration: none; font-weight: 500; padding: 8px 14px; 
            border-radius: 8px; transition: all 0.2s; font-size: 0.85rem; 
        }
        .btn-clear:hover { background-color: #f3f4f6; color: var(--text-main); }
        
        /* Checkbox styling */
        .checkbox-wrapper { display: flex; align-items: center; gap: 6px; cursor: pointer; user-select: none; font-size: 0.85rem; color: var(--text-main); font-weight: 500; }
        .checkbox-wrapper input { width: 14px; height: 14px; accent-color: var(--text-main); cursor: pointer; }

        .btn-toggle-filter {
            background: var(--bg-card); border: 1px solid var(--border); padding: 10px 20px; 
            border-radius: 100px; cursor: pointer; font-weight: 600; color: var(--text-main);
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.2s; font-size: 0.9rem; box-shadow: var(--shadow-sm);
        }
        .btn-toggle-filter:hover { border-color: var(--text-main); transform: translateY(-1px); }

        .btn-reset-filter {
            background: transparent; border: 1px solid transparent; padding: 10px 20px; 
            border-radius: 100px; cursor: pointer; font-weight: 600; color: var(--text-sub);
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
            transition: all 0.2s; font-size: 0.9rem;
        }
        .btn-reset-filter:hover { background-color: #e5e7eb; color: var(--text-main); }

        /* Product Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px; }
        .product-card { 
            background: var(--bg-card); border: 1px solid transparent; border-radius: var(--radius); overflow: hidden; 
            transition: all 0.3s ease; position: relative; height: 100%; display: flex; flex-direction: column;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--border); }
        .product-card img { width: 100%; height: 220px; object-fit: cover; background-color: #f3f4f6; }
        .product-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .product-info h3 { margin: 0 0 8px; font-size: 1.1rem; font-weight: 700; line-height: 1.4; color: var(--text-main); }
        .price { color: var(--text-main); font-weight: 800; font-size: 1.25rem; margin-top: auto; display: block; }
        .category { 
            font-size: 0.75rem; color: var(--primary); margin-bottom: 8px; display: inline-block; 
            text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; 
        }
        .product-link { text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%; }
        
        /* Pagination */
        .pagination { margin-top: 48px; display: flex; justify-content: center; gap: 6px; }
        .pagination a { 
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border); text-decoration: none; border-radius: 8px; 
            background: var(--bg-card); color: var(--text-main); font-weight: 600; transition: all 0.2s;
        }
        .pagination a:hover, .pagination a.active { background-color: var(--text-main); color: white; border-color: var(--text-main); }
        .pagination a.disabled { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
            <button id="toggleCategoryBtn" class="btn-toggle-filter">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                Categories
            </button>
            <button id="toggleFilterBtn" class="btn-toggle-filter">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Filter Products
            </button>
            <a href="home.php" class="btn-reset-filter">Reset</a>
        </div>

        <div class="category-nav">
            <a href="home.php" class="<?php echo !isset($_GET['category_id']) ? 'active' : ''; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="home.php?category_id=<?php echo $cat['id']; ?>" class="<?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'active' : ''; ?>">
                    <?php if (!empty($cat['category_image'])): ?>
                        <img src="../uploads/categories/<?php echo htmlspecialchars($cat['category_image']); ?>" alt="">
                    <?php endif; ?>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form action="home.php" method="GET" class="filter-container" id="filterForm">
            <?php if(isset($_GET['category_id'])): ?>
                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($_GET['category_id']); ?>">
            <?php endif; ?>
            
            <div class="search-grid">
                <div class="input-group">
                    <!-- <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; display: block; color: var(--text-muted);">Product Name</label> -->
                    <input type="text" name="name" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <!-- <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; display: block; color: var(--text-muted);">Location</label> -->
                    <input type="text" name="location" placeholder="City or Province" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <!-- <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; display: block; color: var(--text-muted);">Seller</label> -->
                    <input type="text" name="seller" placeholder="Seller Name" value="<?php echo htmlspecialchars($_GET['seller'] ?? ''); ?>">
                </div>
            </div>

            <div class="filter-actions">
                <div class="filter-group">
                    <select name="sort">
                        <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                    </select>
                    
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Min $" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
                        <span style="color: var(--text-muted);">-</span>
                        <input type="number" name="max_price" placeholder="Max $" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="has_discount" value="1" <?php echo isset($_GET['has_discount']) ? 'checked' : ''; ?>>
                        <span>Discount</span>
                    </label>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="liked_only" value="1" <?php echo isset($_GET['liked_only']) ? 'checked' : ''; ?>>
                        <span>Liked</span>
                    </label>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn-filter">Search</button>
                    <a href="home.php" class="btn-clear">Reset</a>
                </div>
            </div>
        </form>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" alt="Product Image">
                        <div class="product-info">
                            <span class="category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <span class="price">$<?php echo number_format($product['prices'], 2); ?></span>
                            <?php if($product['discounts'] > 0): ?>
                                <span style="color: #dc3545; text-decoration: line-through; font-size: 0.9rem; margin-left: 5px;">
                                    $<?php echo number_format($product['prices'] + $product['discounts'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Links -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php 
                // Helper to keep existing filters in URL
                function getUrl($pageNum) {
                    $params = $_GET;
                    $params['page'] = $pageNum;
                    return 'home.php?' . http_build_query($params);
                }
                ?>

                <?php if ($page > 1): ?>
                    <a href="<?php echo getUrl($page - 1); ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?php echo getUrl($i); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo getUrl($page + 1); ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggleFilterBtn');
        const filterForm = document.getElementById('filterForm');
        const toggleCategoryBtn = document.getElementById('toggleCategoryBtn');
        const categoryNav = document.querySelector('.category-nav');

        toggleBtn.addEventListener('click', () => {
            const isHidden = getComputedStyle(filterForm).display === 'none';
            filterForm.style.display = isHidden ? 'flex' : 'none';
        });

        toggleCategoryBtn.addEventListener('click', () => {
            const isHidden = getComputedStyle(categoryNav).display === 'none';
            categoryNav.style.display = isHidden ? 'flex' : 'none';
        });
    </script>
</body>
</html>