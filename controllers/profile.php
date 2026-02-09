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

    // Handle Image Uploads
    $target_dir = "../uploads/profiles/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    function upload_profile_img($file, $prefix, $target_dir) {
        if (empty($file['name'])) return null;
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid($prefix, true) . '.' . $ext;
        if (move_uploaded_file($file["tmp_name"], $target_dir . $new_filename)) {
            return $new_filename;
        }
        return null;
    }

    // Get existing profile to preserve images if not uploaded
    $existingProfile = $profileRepo->getByUserId($userId);
    
    $user_image = $existingProfile['user_image'] ?? null;
    if (isset($_FILES['user_image']) && !empty($_FILES['user_image']['name'])) {
        $uploaded = upload_profile_img($_FILES['user_image'], 'u_img_', $target_dir);
        if ($uploaded) $user_image = $uploaded;
    }

    $background_image = $existingProfile['background_image'] ?? null;
    if (isset($_FILES['background_image']) && !empty($_FILES['background_image']['name'])) {
        $uploaded = upload_profile_img($_FILES['background_image'], 'bg_img_', $target_dir);
        if ($uploaded) $background_image = $uploaded;
    }

    // Update Profile Table info (Phones, Bio)
    $profileRepo->save($userId, [
        'phone1' => $_POST['phone1'],
        'phone2' => $_POST['phone2'],
        'bio' => $_POST['bio'],
        'user_image' => $user_image,
        'background_image' => $background_image
    ]);

    header("Location: ../views/user_profile.php?success=Profile updated successfully");
    exit();
}
?>