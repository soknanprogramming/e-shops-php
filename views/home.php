<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';

$productRepo = new ProductRepository($conn);
$products = $productRepo->getAll();
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
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
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