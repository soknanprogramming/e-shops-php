<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/UserRepository.php';

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

$userRepo = new UserRepository($conn);
$pendingRequests = $userRepo->getPendingRequests();
$pendingCount = count($pendingRequests);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        .stats-container { display: flex; gap: 20px; margin-top: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; text-align: center; }
        .stat-card h2 { margin: 0; font-size: 2.5rem; color: #007bff; }
        .stat-card p { margin: 10px 0 0; color: #666; font-weight: bold; }
        .stat-card.alert { border-left: 5px solid #ffc107; }
        .btn-view { display: inline-block; margin-top: 15px; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>. You have access to this protected admin area.</p>

        <div class="stats-container">
            <div class="stat-card <?php echo $pendingCount > 0 ? 'alert' : ''; ?>">
                <h2><?php echo $pendingCount; ?></h2>
                <p>Pending Post Requests</p>
                <?php if ($pendingCount > 0): ?>
                    <a href="admin_user.php?filter=requesting" class="btn-view">Review Requests</a>
                <?php endif; ?>
            </div>
            <!-- Other stats could go here -->
        </div>
    </div>
</body>
</html>