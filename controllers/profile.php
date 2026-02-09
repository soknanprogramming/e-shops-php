<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/UserRepository.php';
require_once '../repos/ProfileRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRepo = new UserRepository($conn);
    $profileRepo = new ProfileRepository($conn);

    $userId = $_SESSION['user_id'];
    
    // Update User Table info (First Name, Last Name)
    $userRepo->update($userId, [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    ]);

    // Update Profile Table info (Phones, Bio)
    $profileRepo->save($userId, [
        'phone1' => $_POST['phone1'],
        'phone2' => $_POST['phone2'],
        'bio' => $_POST['bio']
    ]);

    header("Location: ../views/user_profile.php?success=Profile updated successfully");
    exit();
}
?>