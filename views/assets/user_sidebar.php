<div class="sidebar">
    <div class="sidebar-header">
        <h3>User Panel</h3>
    </div>
    <ul class="sidebar-menu">
        <li><a href="home.php">Back to Shop</a></li>
        <li><a href="user_dashboard.php">My Products</a></li>
        <li><a href="user_profile.php">My Profile</a></li>
        <li><a href="product_create.php">Post New Product</a></li>
        <li><a href="logout.php" style="color: #dc3545;">Logout</a></li>
    </ul>
</div>

<style>
    .sidebar {
        width: 250px;
        background-color: #fff;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        height: 100vh;
        position: sticky;
        top: 0;
    }
    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }
    .sidebar-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.2rem;
    }
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-menu li a {
        display: block;
        padding: 15px 20px;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.2s;
    }
    .sidebar-menu li a:hover {
        background-color: #f0f2f5;
        color: #007bff;
    }
</style>