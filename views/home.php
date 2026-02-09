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
        .navbar { background-color: #343a40; padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .product-card { background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 150px; object-fit: cover; }
        .product-info { padding: 15px; }
        .product-info h3 { margin: 0 0 10px; font-size: 1.1rem; }
        .price { color: #28a745; font-weight: bold; font-size: 1.1rem; }
        .category { font-size: 0.85rem; color: #6c757d; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">MyShop</div>
        <div class="links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="product_create.php" style="background-color: #28a745; padding: 5px 10px; border-radius: 4px;">Post Product</a>
                <a href="user_dashboard.php">My Dashboard</a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h1>Latest Products</h1>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" alt="Product Image">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="price">$<?php echo htmlspecialchars($product['prices']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>