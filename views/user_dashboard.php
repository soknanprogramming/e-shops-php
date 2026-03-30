<?php
session_start();
require_once '../configs/connect.php';

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1.1 Fetch User Status (to check for pending requests)
$stmtUser = $conn->prepare("SELECT can_post, request_post_permission FROM User WHERE id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$currentUser = $stmtUser->fetch();

// 1.2 Fetch User Profile (to check for phone number)
$stmtProfile = $conn->prepare("SELECT phone1 FROM user_profile WHERE user_id = ?");
$stmtProfile->execute([$_SESSION['user_id']]);
$userProfile = $stmtProfile->fetch();

// 2. Fetch My Products
$stmt = $conn->prepare("SELECT p.*, pi.main_image FROM Product p LEFT JOIN product_image pi ON p.product_image_id = pi.id WHERE p.owner_id = ? ORDER BY p.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$myProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard</title>
    <style>
        body { font-family: sans-serif; display: flex; background-color: #f0f2f5; margin: 0; }
        .main-content { flex-grow: 1; padding: 20px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 15px; text-decoration: none; color: white; border-radius: 4px; }
        .btn-primary { background-color: #007bff; }
        .btn-disabled { background-color: #ccc; cursor: not-allowed; pointer-events: none; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .product-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 4px; }
        .alert { padding: 10px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include './assets/user_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header-actions">
            <h1>My Dashboard</h1>
            
            <!-- UI Logic for Post Button -->
            <?php if ($currentUser['can_post'] == 1): ?>
                <a href="product_create.php" class="btn btn-primary">+ Post New Product</a>
            <?php else: ?>
                <div id="request-container">
                    <?php if ($currentUser['request_post_permission'] == 1): ?>
                        <span class="btn btn-disabled" style="background-color: #ffc107; color: #000;">Request Pending...</span>
                    <?php else: ?>
                        <button id="request-btn" class="btn btn-primary" style="background-color: #17a2b8; border: none; cursor: pointer;">Request Permission</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const requestBtn = document.getElementById('request-btn');
            if (requestBtn) {
                requestBtn.addEventListener('click', function() {
                    requestBtn.disabled = true;
                    requestBtn.innerText = 'Sending...';
                    
                    fetch('../controllers/user.php?action=request_permission', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('request-container').innerHTML = '<span class="btn btn-disabled" style="background-color: #ffc107; color: #000;">Request Pending...</span>';
                        } else {
                            alert('Error: ' + data.message);
                            requestBtn.disabled = false;
                            requestBtn.innerText = 'Request Permission';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        requestBtn.disabled = false;
                        requestBtn.innerText = 'Request Permission';
                    });
                });
            }
        });
        </script>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <!-- Permission Warning -->
        <?php if ($currentUser['can_post'] != 1): ?>
            <div class="alert">
                <strong>Notice:</strong> You currently do not have permission to post products. Please contact an administrator for approval.
            </div>
        <?php endif; ?>

        <!-- Phone Number Warning -->
        <?php if (empty($userProfile) || empty($userProfile['phone1'])): ?>
            <div class="alert" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <strong>Action Required:</strong> You must have at least one phone number to post a product. 
                <a href="user_profile.php" style="color: #721c24; text-decoration: underline;">Update Profile</a>
            </div>
        <?php endif; ?>

        <h2>My Products</h2>
        <?php if (count($myProducts) > 0): ?>
            <div class="product-grid">
                <?php foreach ($myProducts as $product): ?>
                    <div class="product-card">
                        <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image'] ?? 'default.png'); ?>" alt="Product Image">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo htmlspecialchars($product['prices']); ?></p>
                        <div style="margin-top: 10px;">
                            <a href="product_edit.php?id=<?php echo $product['id']; ?>" style="color: blue; margin-right: 10px;">Edit</a>
                            <a href="../controllers/product.php?action=delete&id=<?php echo $product['id']; ?>" style="color: red;" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>