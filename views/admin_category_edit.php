<?php
session_start();
require_once '../configs/connect.php';

// 1. Check Auth
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// 2. Fetch Category Data
if (!isset($_GET['id'])) {
    header("Location: admin_category.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM category WHERE id = :id");
$stmt->execute([':id' => $id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: admin_category.php?error=Category not found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <style>
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 500px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 10px; }
        .current-img { margin-top: 5px; border-radius: 4px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Edit Category</h1>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="../controllers/category.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo $category['category_image']; ?>">
                
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="image">Change Image (Optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <br>
                    <img src="../uploads/categories/<?php echo htmlspecialchars($category['category_image']); ?>" alt="Current Image" width="100" class="current-img">
                </div>
                <button type="submit" name="update_category">Update Category</button>
            </form>
        </div>
    </div>
</body>
</html>