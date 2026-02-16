<div class="navbar">
    <div class="logo">
        <a href="home.php">Sana</a>
    </div>
    <div class="links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="product_create.php" class="btn-post">Post Product</a>
            <a href="user_dashboard.php">My Dashboard</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        <?php endif; ?>
    </div>
</div>

<style>
    .navbar { 
        background-color: #ffffff; 
        padding: 15px 30px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
        font-family: 'Inter', sans-serif;
    }
    .logo a { color: #007bff; text-decoration: none; font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px; }
    .links { display: flex; align-items: center; gap: 20px; }
    .navbar a { color: #4a5568; text-decoration: none; font-weight: 500; font-size: 0.95rem; transition: color 0.2s; }
    .navbar a:hover { color: #007bff; }
    
    .btn-post { background-color: #007bff; color: white !important; padding: 10px 20px; border-radius: 50px; font-weight: 600 !important; box-shadow: 0 2px 5px rgba(0,123,255,0.3); transition: all 0.2s; }
    .btn-post:hover { background-color: #0056b3; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,123,255,0.4); }
    
    .btn-register { color: #007bff !important; font-weight: 600 !important; }
    .btn-logout { color: #dc3545 !important; }
    .user-greeting { color: #4a5568; font-weight: 600; font-size: 0.95rem; }
</style>