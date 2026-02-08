<?php
session_start();
require_once '../repos/UserRepository.php';
require_once '../configs/connect.php';

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

// 3. Fetch Users
$users = [];
$search = $_GET['search'] ?? '';

try {
    $userRepo = new UserRepository($conn);
    if (!empty($search)) {
        $users = $userRepo->search($search);
    } else {
        $users = $userRepo->getAll();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
        th { background-color: #e9ecef; }
    </style>
</head>
<body>
    <?php include './assets/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>User Management</h1>
        
        <div style="margin-bottom: 20px;">
            <form action="" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="admin_user.php" style="padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="../controllers/user.php?action=toggle_role&id=<?php echo $user['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to change this user\'s role?');"
                                   style="color: blue; text-decoration: underline; cursor: pointer;">
                                    <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                                </a>
                            <?php else: ?>
                                <span style="color: gray;">(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
