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
        body { font-family: sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; padding-top: 50px; }
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #6c757d; }
    </style>
</head>
<body>
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
                <label for="category_id">Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="create_product">Post Product</button>
        </form>
        <a href="home.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>