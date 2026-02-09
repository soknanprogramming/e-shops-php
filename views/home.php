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
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .product-card { background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 150px; object-fit: cover; }
        .product-info { padding: 15px; }
        .product-info h3 { margin: 0 0 10px; font-size: 1.1rem; }
        .price { color: #28a745; font-weight: bold; font-size: 1.1rem; }
        .category { font-size: 0.85rem; color: #6c757d; }
        .product-link { text-decoration: none; color: inherit; display: block; height: 100%; }
        .product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.2); transition: box-shadow 0.2s; }
        .category-nav { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 15px; margin-bottom: 20px; }
        .category-nav a { display: flex; align-items: center; gap: 8px; white-space: nowrap; padding: 8px 15px; background: white; border: 1px solid #ddd; border-radius: 20px; text-decoration: none; color: #333; transition: 0.2s; }
        .category-nav a:hover, .category-nav a.active { background: #007bff; color: white; border-color: #007bff; }
        .category-nav img { width: 25px; height: 25px; object-fit: cover; border-radius: 50%; }
        
        .filter-container { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .search-row { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .filter-row { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .filter-container input[type="number"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100px; }
        .filter-container input[type="text"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 180px; }
        .filter-container button { padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .filter-container button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
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

        <form action="home.php" method="GET" class="filter-container">
            <?php if(isset($_GET['category_id'])): ?>
                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($_GET['category_id']); ?>">
            <?php endif; ?>
            
            <div class="search-row">
                <div>
                    <input type="text" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                </div>
                <div>
                    <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
                </div>
                <div>
                    <input type="text" name="seller" placeholder="Seller Name" value="<?php echo htmlspecialchars($_GET['seller'] ?? ''); ?>">
                </div>
            </div>

            <div class="filter-row">
                <div>
                    <label>Sort By:</label>
                    <select name="sort" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                    </select>
                </div>
                <div>
                    <label>Price:</label>
                    <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
                    -
                    <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                </div>
                <div>
                    <input type="checkbox" id="has_discount" name="has_discount" value="1" <?php echo isset($_GET['has_discount']) ? 'checked' : ''; ?>>
                    <label for="has_discount">Discount</label>
                </div>
                <?php if(isset($_SESSION['user_id'])): ?>
                <div>
                    <input type="checkbox" id="liked_only" name="liked_only" value="1" <?php echo isset($_GET['liked_only']) ? 'checked' : ''; ?>>
                    <label for="liked_only">Liked</label>
                </div>
                <?php endif; ?>
                <button type="submit">Filter</button>
                <a href="home.php" style="margin-left: 10px; color: #6c757d; text-decoration: none;">Clear</a>
            </div>
        </form>

        <h1>Latest Products</h1>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" alt="Product Image">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <p class="price">$<?php echo htmlspecialchars($product['prices']); ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Links -->
        <?php if ($totalPages > 1): ?>
            <div style="margin-top: 30px; display: flex; justify-content: center; gap: 5px;">
                <?php 
                // Helper to keep existing filters in URL
                function getUrl($pageNum) {
                    $params = $_GET;
                    $params['page'] = $pageNum;
                    return 'home.php?' . http_build_query($params);
                }
                ?>

                <?php if ($page > 1): ?>
                    <a href="<?php echo getUrl($page - 1); ?>" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px; background: white;">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?php echo getUrl($i); ?>" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px; <?php echo $i == $page ? 'background: #007bff; color: white;' : 'background: white; color: #333;'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo getUrl($page + 1); ?>" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px; background: white;">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>