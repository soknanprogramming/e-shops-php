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
            --secondary: #9d7c39; /* Sacred Gold */
            --secondary-container: #fef3d5;
            --on-secondary-container: #201b09;
            --tertiary: #7e000a; /* Academic Red */
            --bg-body: #fff9ee; /* Soft Cream Foundation */
            --surface: #fff9ee;
            --surface-container-low: #fef3d5;
            --surface-container-highest: #ede2c5;
            --on-surface: #201b09;
            --on-surface-variant: #4a4538;
            --outline-variant: rgba(74, 69, 56, 0.2);
            --radius-md: 8px;
            --radius-lg: 16px;
            --shadow-ambient: 0 4px 24px rgba(32, 27, 9, 0.04);
            --font-headline: 'Manrope', sans-serif;
            --font-body: 'Public Sans', sans-serif;
        }

        body { 
            font-family: var(--font-body); 
            margin: 0; 
            padding: 0; 
            background-color: var(--bg-body); 
            color: var(--on-surface); 
            -webkit-font-smoothing: antialiased; 
            line-height: 1.6;
        }

        .container { 
            padding: 40px 6rem; 
            max-width: 1440px; 
            margin: 0 auto; 
        }
        
        @media (max-width: 1200px) {
            .container { padding: 40px 2rem; }
        }

        /* Hero Section */
        .hero-section {
            margin-bottom: 4rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .hero-section h1 { 
            font-family: var(--font-headline);
            font-size: 3.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin: 0;
            color: var(--primary);
            line-height: 1.1;
        }
        .hero-section p {
            font-size: 1.25rem;
            color: var(--on-surface-variant);
            max-width: 600px;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .hero-section { margin-bottom: 2rem; text-align: center; }
            .hero-section h1 { font-size: 2.5rem; }
            .hero-section p { font-size: 1rem; margin: 0 auto; }
        }

        /* Category Nav - Editorial Style */
        .category-nav { 
            display: flex; 
            gap: 12px; 
            overflow-x: auto; 
            padding: 8px 0 24px; 
            margin-bottom: 2rem; 
            scrollbar-width: none; 
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
        }
        .category-nav::-webkit-scrollbar { display: none; }
        .category-nav a { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            white-space: nowrap; 
            padding: 10px 24px; 
            background: #ffffff; 
            border: 1px solid var(--outline-variant); 
            border-radius: 9999px; 
            text-decoration: none; 
            color: var(--on-surface-variant); 
            font-weight: 600; 
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .category-nav a:hover, .category-nav a.active { 
            background: var(--primary); 
            color: #ffffff; 
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(26, 51, 37, 0.15);
        }

        /* Product Grid */
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 3rem; 
        }
        
        @media (max-width: 992px) {
            .product-grid { gap: 1.5rem; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); }
        }
        
        @media (max-width: 576px) {
            .product-grid { grid-template-columns: 1fr; }
            .container { padding: 20px 1rem; }
        }

        .product-card { 
            background: #ffffff; 
            border-radius: var(--radius-md); 
            overflow: hidden; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            position: relative; 
            display: flex; 
            flex-direction: column;
            border: 1px solid transparent;
        }
        .product-card:hover { 
            transform: translateY(-8px); 
            box-shadow: var(--shadow-ambient);
            border-top: 2px solid var(--secondary);
        }
        .product-card img { 
            width: 100%; 
            height: 280px; 
            object-fit: cover; 
            background-color: var(--surface-container-highest); 
        }
        .product-info { 
            padding: 1.5rem; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            gap: 0.5rem;
        }
        .category-badge { 
            font-size: 0.75rem; 
            color: var(--secondary); 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            font-weight: 700; 
        }
        .product-info h3 { 
            margin: 0; 
            font-family: var(--font-headline);
            font-size: 1.375rem; 
            font-weight: 600; 
            line-height: 1.3; 
            color: var(--on-surface); 
        }
        .price-container {
            margin-top: auto;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .price { 
            color: var(--primary); 
            font-weight: 800; 
            font-size: 1.5rem; 
        }
        .old-price { 
            color: var(--tertiary); 
            text-decoration: line-through; 
            font-size: 0.875rem; 
            opacity: 0.6;
        }
        .product-link { text-decoration: none; color: inherit; height: 100%; }
        
        /* Pagination */
        .pagination { margin-top: 6rem; display: flex; justify-content: center; gap: 8px; }
        .pagination a { 
            width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--outline-variant); text-decoration: none; border-radius: 50%; 
            background: #ffffff; color: var(--on-surface); font-weight: 600; transition: all 0.3s;
        }
        .pagination a:hover, .pagination a.active { 
            background-color: var(--primary); 
            color: white; 
            border-color: var(--primary); 
            box-shadow: 0 4px 12px rgba(26, 51, 37, 0.15);
        }
        .pagination a.disabled { opacity: 0.3; pointer-events: none; }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
        <header class="hero-section">
            <h1>The Curated <br>Collection</h1>
            <p>Experience the finest selection of products, curated with scholarly precision and archival elegance.</p>
        </header>

        <nav class="category-nav">
            <?php 
            // Helper to get URL with preserved params
            function getCategoryUrl($catId = null) {
                $params = $_GET;
                if ($catId === null) {
                    unset($params['category_id']);
                } else {
                    $params['category_id'] = $catId;
                }
                unset($params['page']); // Reset page when changing category
                return 'home.php?' . http_build_query($params);
            }
            ?>
            <a href="<?php echo getCategoryUrl(); ?>" class="<?php echo !isset($_GET['category_id']) ? 'active' : ''; ?>">All Collections</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo getCategoryUrl($cat['id']); ?>" class="<?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" alt="Product Image">
                        <div class="product-info">
                            <span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="price-container">
                                <span class="price">$<?php echo number_format($product['prices'], 2); ?></span>
                                <?php if($product['discounts'] > 0): ?>
                                    <span class="old-price">
                                        $<?php echo number_format($product['prices'] + $product['discounts'], 2); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
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
</body>
</html>