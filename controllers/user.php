<?php
session_start();
require_once '../repos/UserRepository.php';
require_once '../configs/connect.php';

// 1. Auth Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../views/login.php");
    exit();
}

$userRepo = new UserRepository($conn);

// 2. Toggle Role Action
if (isset($_GET['action']) && $_GET['action'] === 'toggle_role' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prevent changing own role to avoid locking yourself out
    if ($id == $_SESSION['user_id']) {
        header("Location: ../views/admin_user.php?error=You cannot change your own role");
        exit();
    }

    $user = $userRepo->findById($id);
    
    if ($user) {
        // Toggle: if 1 becomes 0, if 0 becomes 1
        $new_role = $user['is_admin'] == 1 ? 0 : 1;
        
        $userRepo->update($id, ['is_admin' => $new_role]);
        
        $msg = $new_role ? "User promoted to Admin" : "User demoted to User";
        header("Location: ../views/admin_user.php?success=" . urlencode($msg));
        exit();
    } else {
        header("Location: ../views/admin_user.php?error=User not found");
        exit();
    }
}
?>
