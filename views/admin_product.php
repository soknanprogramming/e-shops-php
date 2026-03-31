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

// Filters
$filters = [
    'include_hidden' => true,
    'name' => $_GET['name'] ?? null,
    'seller' => $_GET['seller'] ?? null,
];

if (isset($_GET['status']) && $_GET['status'] !== '') {
    // If status is 1 or 0, we need a custom way to handle it because search() defaults to showed=1
    // Actually search() in ProductRepository.php only adds 'AND p.showed = 1' if include_hidden is NOT true.
    // Let's modify ProductRepository::search to handle specific showed status if requested.
}

// Since I want to filter by showed status specifically in admin, I might need to adjust search() 
// or just filter here for now if the list is small. 
// But let's use search and handle status.
$products = $productRepo->search($filters);

// Manual filter for status if needed (since search() doesn't have a specific 'showed' param yet)
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $status = (int)$_GET['status'];
    $products = array_filter($products, function($p) use ($status) {
        return $p['showed'] == $status;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body { font-family: sans-serif; display: flex; margin: 0; background-color: #f4f7f6; }
        .sidebar { width: 250px; flex-shrink: 0; }
        .main-content { flex-grow: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .filter-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .filter-form { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #666; }
        input, select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; }
        .btn-filter { background: #007bff; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .btn-reset { background: #6c757d; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-size: 0.9rem; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; font-weight: 600; color: #444; }
        tr:hover { background-color: #fcfcfc; }
        
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 0.85rem; font-weight: 600; display: inline-block; }
        .btn-green { background-color: #28a745; }
        .btn-green:hover { background-color: #218838; }
        .btn-red { background-color: #dc3545; }
        .btn-red:hover { background-color: #c82333; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; color: white; text-transform: uppercase; }
        .bg-success { background-color: #28a745; }
        .bg-secondary { background-color: #6c757d; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; background: #eee; }
        .product-name { text-decoration: none; color: #007bff; font-weight: 600; }
        .product-name:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Product Management</h1>
            <span>Total: <?php echo count($products); ?> Products</span>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="filter-card">
            <form action="" method="GET" class="filter-form">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" placeholder="Search name..." value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Seller</label>
                    <input type="text" name="seller" placeholder="Seller name..." value="<?php echo htmlspecialchars($_GET['seller'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : ''; ?>>Visible</option>
                        <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : ''; ?>>Hidden</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                <a href="admin_product.php" class="btn-reset">Reset</a>
            </form>
        </div>

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
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999; padding: 30px;">No products found matching your filters.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image'] ?? 'default.png'); ?>" class="product-img">
                            </a>
                        </td>
                        <td>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-name">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($product['owner_name']); ?></td>
                        <td>$<?php echo number_format($product['prices'], 2); ?></td>
                        <td>
                            <?php if ($product['showed']): ?>
                                <span class="badge bg-success">Visible</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="../controllers/product.php?action=toggle_visibility&id=<?php echo $product['id']; ?>&status=<?php echo $product['showed'] ? '0' : '1'; ?>" 
                                class="btn <?php echo $product['showed'] ? 'btn-red' : 'btn-green'; ?>">
                                    <?php echo $product['showed'] ? 'Hide' : 'Show'; ?>
                                </a>
                                <a href="../controllers/product.php?action=delete&id=<?php echo $product['id']; ?>" 
                                class="btn btn-red" 
                                onclick="return confirm('Are you sure you want to delete this product permanently?')">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
