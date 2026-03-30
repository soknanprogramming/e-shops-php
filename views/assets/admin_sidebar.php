<style>
    body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; }
    .sidebar { width: 250px; background-color: #343a40; color: white; flex-shrink: 0; }
    .sidebar-header { padding: 20px; background-color: #212529; }
    .sidebar-header h3 { margin: 0; font-size: 1.2rem; }
    .sidebar-menu { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu li a { display: block; padding: 15px 20px; color: #adb5bd; text-decoration: none; border-bottom: 1px solid #454d55; }
    .sidebar-menu li a:hover { background-color: #495057; color: white; }
    .main-content { flex-grow: 1; padding: 20px; background-color: #f8f9fa; }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
    </div>
    <ul class="sidebar-menu">
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_user.php">Users</a></li>
        <li><a href="admin_product.php">Products</a></li>
        <li><a href="admin_category.php">Category</a></li>
        <li><a href="home.php">View Website</a></li>
        <li><a href="logout.php" style="color: #dc3545;">Logout</a></li>
    </ul>
</div>
