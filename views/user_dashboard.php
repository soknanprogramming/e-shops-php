<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$productRepo = new ProductRepository($conn);
$myProducts = $productRepo->getByOwnerId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; background-color: #f0f2f5; }
        .main-content { flex-grow: 1; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 15px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-primary { background-color: #007bff; }
        /* .btn-secondary { background-color: #6c757d; } */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #dee2e6; text-align: left; }
        th { background-color: #f8f9fa; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .action-link { margin-right: 10px; color: #007bff; text-decoration: none; }
        .action-link.delete { color: #dc3545; }
    </style>
</head>
<body>
    <?php include './assets/user_sidebar.php'; ?>
    <div class="main-content">
    <div class="container">
        <div class="header">
            <h1>My Products</h1>
            <div>
                <a href="product_create.php" class="btn btn-primary">+ Post New Product</a>
            </div>
        </div>

        <?php if (empty($myProducts)): ?>
            <p>You haven't posted any products yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myProducts as $prod): ?>
                        <tr>
                            <td>
                                <?php if($prod['main_image']): ?>
                                    <img src="../uploads/products/<?php echo htmlspecialchars($prod['main_image']); ?>" class="product-img" alt="Product">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td>$<?php echo number_format($prod['prices'], 2); ?></td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td><?php echo $prod['showed'] ? '<span style="color:green">Active</span>' : '<span style="color:red">Hidden</span>'; ?></td>
                            <td>
                                <a href="product_edit.php?id=<?php echo $prod['id']; ?>" class="action-link">Edit</a>
                                <a href="../controllers/product.php?action=delete&id=<?php echo $prod['id']; ?>" class="action-link delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>