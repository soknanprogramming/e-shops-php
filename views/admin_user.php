<?php
session_start();
require_once '../configs/connect.php';

// 1. Auth Check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// 2. Fetch Users
if (isset($_GET['filter']) && $_GET['filter'] === 'requesting') {
    $stmt = $conn->prepare("SELECT * FROM User WHERE request_post_permission = 1 AND (can_post = 0 OR can_post IS NULL) ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT * FROM User ORDER BY created_at DESC");
}
$stmt->execute();
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body { font-family: sans-serif; display: flex; }
        .main-content { flex-grow: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 4px; font-size: 0.9rem; }
        .btn-green { background-color: #28a745; }
        .btn-red { background-color: #dc3545; }
        .btn-blue { background-color: #007bff; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 0.8rem; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-secondary { background-color: #6c757d; }
        .filter-links { margin-bottom: 15px; }
        .filter-links a { margin-right: 15px; text-decoration: none; color: #007bff; }
        .filter-links a.active { font-weight: bold; color: black; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>User Management</h1>
        
        <div class="filter-links">
            <a href="admin_user.php" class="<?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">All Users</a>
            <a href="admin_user.php?filter=requesting" class="<?php echo (isset($_GET['filter']) && $_GET['filter'] === 'requesting') ? 'active' : ''; ?>">Pending Requests</a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Posting Permission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                    <td>
                        <?php if ($user['can_post']): ?>
                            <span class="badge bg-success">Allowed</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Restricted</span>
                            <?php if (isset($user['request_post_permission']) && $user['request_post_permission'] == 1): ?>
                                <span class="badge" style="background-color: #ffc107; color: black; margin-left: 5px;">Requesting</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="../controllers/user.php?action=toggle_permission&id=<?php echo $user['id']; ?>" class="btn <?php echo $user['can_post'] ? 'btn-red' : 'btn-green'; ?>">
                            <?php echo $user['can_post'] ? 'Revoke Post' : 'Allow Post'; ?>
                        </a>
                        <a href="../controllers/user.php?action=toggle_role&id=<?php echo $user['id']; ?>" class="btn btn-blue">Toggle Role</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>