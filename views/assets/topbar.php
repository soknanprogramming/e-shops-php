<div class="navbar">
    <div class="logo">
        <a href="home.php" style="color: white; text-decoration: none; font-weight: bold; font-size: 1.2rem; margin-left: 0;">MyShop</a>
    </div>
    <div class="links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="product_create.php" class="btn-post">Post Product</a>
            <a href="user_dashboard.php">My Dashboard</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php" style="color: #ff6b6b;">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<style>
    .navbar { background-color: #343a40; padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; }
    .navbar a { color: white; text-decoration: none; margin-left: 15px; }
    .navbar a:hover { text-decoration: underline; }
    .navbar .logo a:hover { text-decoration: none; }
    .btn-post { background-color: #28a745; padding: 5px 10px; border-radius: 4px; }
    .btn-post:hover { background-color: #218838; text-decoration: none !important; }
</style>