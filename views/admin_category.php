<?php
session_start();
require_once '../configs/connect.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// 3. Fetch Categories
$categories = [];
try {
    $stmt = $conn->prepare("SELECT * FROM category ORDER BY id DESC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Categories</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        /* .sidebar { width: 250px; background-color: #343a40; color: white; flex-shrink: 0; }
        .sidebar-header { padding: 20px; background-color: #212529; }
        .sidebar-header h3 { margin: 0; font-size: 1.2rem; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: block; padding: 15px 20px; color: #adb5bd; text-decoration: none; border-bottom: 1px solid #454d55; }
        .sidebar-menu li a:hover { background-color: #495057; color: white; }

        /* Main Content Styles */
        /* .main-content { flex-grow: 1; padding: 20px; background-color: #f8f9fa; } */ */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
        th { background-color: #e9ecef; }
        .btn-add { background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Category Management</h1>
            <a href="#" class="btn-add">Add New Category</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat['id']); ?></td>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo htmlspecialchars($cat['category_image']); ?></td>
                            <td>
                                <a href="#">Edit</a> | 
                                <a href="#" style="color:red;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>