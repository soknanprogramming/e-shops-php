<?php
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if user is an admin
// The database stores is_admin as 1 (true) or 0 (false)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // User is not an admin, redirect to home
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>. You have access to this protected admin area.</p>
    </div>
</body>
</html>