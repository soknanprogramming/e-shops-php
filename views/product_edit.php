<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/CategoryRepository.php';
require_once '../repos/ProductRepository.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$productId = $_GET['id'];
$productRepo = new ProductRepository($conn);
$product = $productRepo->getById($productId);

// Check if product exists and belongs to user
if (!$product || $product['owner_id'] != $_SESSION['user_id']) {
    header("Location: user_dashboard.php?error=Unauthorized access");
    exit();
}

// Fetch categories for dropdown
$catRepo = new CategoryRepository($conn);
$categories = $catRepo->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; background-color: #f0f2f5; }
        .main-content { flex-grow: 1; padding: 20px; display: flex; justify-content: center; align-items: flex-start; }
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 500px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        .current-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin-top: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <?php include './assets/user_sidebar.php'; ?>
    <div class="main-content">
    <div class="form-container">
        <h2>Edit Product</h2>
        <form action="../controllers/product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="prices">Price ($)</label>
                <input type="number" name="prices" step="0.01" value="<?php echo htmlspecialchars($product['prices']); ?>" required>
            </div>
            <div class="form-group">
                <label for="discounts">Discount ($) (Optional)</label>
                <input type="number" name="discounts" step="0.01" value="<?php echo htmlspecialchars($product['discounts']); ?>" placeholder="0.00">
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($product['location']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Main Image</label>
                <input type="file" name="image" accept="image/*">
                <?php if($product['main_image']): ?>
                    <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" class="current-img">
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Additional Images</label>
                <?php for($i=1; $i<=5; $i++): ?>
                    <div style="margin-bottom: 10px;">
                        <input type="file" name="image<?php echo $i; ?>" accept="image/*">
                        <?php if(!empty($product['image'.$i])): ?>
                            <img src="../uploads/products/<?php echo htmlspecialchars($product['image'.$i]); ?>" class="current-img">
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
            
            <button type="submit" name="update_product">Update Product</button>
        </form>
    </div>
    </div>
</body>
</html>