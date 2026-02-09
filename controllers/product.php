<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    
    // 1. Check Login
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $name = $_POST['name'];
    $prices = $_POST['prices'];
    $discounts = !empty($_POST['discounts']) ? $_POST['discounts'] : 0;
    $location = $_POST['location'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $image = $_FILES['image'];

    // 2. Handle Image Upload
    $target_dir = "../uploads/products/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Helper function for uploading
    function upload_file($file, $prefix, $target_dir) {
        if (empty($file['name'])) return null;
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid($prefix, true) . '.' . $ext;
        if (move_uploaded_file($file["tmp_name"], $target_dir . $new_filename)) {
            return $new_filename;
        }
        return null;
    }

    $new_filename = upload_file($image, 'prod_main_', $target_dir);

    if ($new_filename) {
        // Upload additional images
        $additional_images = [];
        for ($i = 1; $i <= 5; $i++) {
            $key = 'image' . $i;
            $additional_images[$key] = isset($_FILES[$key]) ? upload_file($_FILES[$key], "prod_{$i}_", $target_dir) : null;
        }

        try {
            $productRepo = new ProductRepository($conn);
            $productRepo->create([
                'name' => $name,
                'prices' => $prices,
                'discounts' => $discounts,
                'category_id' => $category_id,
                'owner_id' => $_SESSION['user_id'],
                'image' => $new_filename,
                'image1' => $additional_images['image1'],
                'image2' => $additional_images['image2'],
                'image3' => $additional_images['image3'],
                'image4' => $additional_images['image4'],
                'image5' => $additional_images['image5'],
                'location' => $location,
                'description' => $description
            ]);

            header("Location: ../views/user_dashboard.php?success=Product posted successfully");
            exit();
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>