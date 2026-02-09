<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/CategoryRepository.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    <title>Post Product</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; background-color: #f0f2f5; }
        .main-content { flex-grow: 1; padding: 20px; display: flex; justify-content: center; align-items: flex-start; }
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 500px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        .btn-secondary { display: block; width: 100%; padding: 10px; background-color: #6c757d; color: white; text-align: center; border-radius: 4px; text-decoration: none; box-sizing: border-box; margin-top: 10px; }
        .btn-secondary:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <?php include './assets/user_sidebar.php'; ?>
    <div class="main-content">
    <div class="form-container">
        <h2>Post New Product</h2>
        <form action="../controllers/product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label for="prices">Price ($)</label>
                <input type="number" name="prices" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="discounts">Discount ($) (Optional)</label>
                <input type="number" name="discounts" step="0.01" placeholder="0.00">
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" placeholder="e.g. Phnom Penh" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" rows="5" placeholder="Describe your product..." required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="create_product">Post Product</button>
        </form>
    </div>
    </div>
</body>
</html>