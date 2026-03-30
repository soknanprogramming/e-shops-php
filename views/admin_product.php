<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';

// 1. Auth Check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$productRepo = new ProductRepository($conn);
$products = $productRepo->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body { font-family: sans-serif; display: flex; }
        .main-content { flex-grow: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 4px; font-size: 0.9rem; }
        .btn-green { background-color: #28a745; }
        .btn-red { background-color: #dc3545; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 0.8rem; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-secondary { background-color: #6c757d; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Product Management</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Owner</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><img src="../uploads/products/<?php echo htmlspecialchars($product['main_image'] ?? 'default.png'); ?>" class="product-img"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['owner_name']); ?></td>
                    <td>$<?php echo htmlspecialchars($product['prices']); ?></td>
                    <td>
                        <?php if ($product['showed']): ?>
                            <span class="badge bg-success">Visible</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Hidden</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="../controllers/product.php?action=toggle_visibility&id=<?php echo $product['id']; ?>&status=<?php echo $product['showed'] ? '0' : '1'; ?>" 
                           class="btn <?php echo $product['showed'] ? 'btn-red' : 'btn-green'; ?>">
                            <?php echo $product['showed'] ? 'Hide' : 'Show'; ?>
                        </a>
                        <a href="../controllers/product.php?action=delete&id=<?php echo $product['id']; ?>" 
                           class="btn btn-red" 
                           onclick="return confirm('Are you sure you want to delete this product permanently?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
