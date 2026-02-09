<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';
require_once '../repos/CategoryRepository.php';

$categoryRepo = new CategoryRepository($conn);
$categories = $categoryRepo->getAll();

$productRepo = new ProductRepository($conn);

$filters = [
    'category_id' => $_GET['category_id'] ?? null,
    'min_price' => $_GET['min_price'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'has_discount' => isset($_GET['has_discount']) ? 1 : 0
];

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
        .category-nav a { white-space: nowrap; padding: 8px 15px; background: white; border: 1px solid #ddd; border-radius: 20px; text-decoration: none; color: #333; transition: 0.2s; }
        .category-nav a:hover, .category-nav a.active { background: #007bff; color: white; border-color: #007bff; }
        
        .filter-bar { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; flex-wrap: wrap; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filter-bar input[type="number"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100px; }
        .filter-bar button { padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .filter-bar button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
        <div class="category-nav">
            <a href="home.php" class="<?php echo !isset($_GET['category_id']) ? 'active' : ''; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="home.php?category_id=<?php echo $cat['id']; ?>" class="<?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form action="home.php" method="GET" class="filter-bar">
            <?php if(isset($_GET['category_id'])): ?>
                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($_GET['category_id']); ?>">
            <?php endif; ?>
            
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
            <button type="submit">Filter</button>
            <a href="home.php" style="margin-left: 10px; color: #6c757d; text-decoration: none;">Clear</a>
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
    </div>
</body>
</html>