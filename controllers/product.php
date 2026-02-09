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

    $image_extension = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid('prod_', true) . '.' . $image_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        try {
            $productRepo = new ProductRepository($conn);
            $productRepo->create([
                'name' => $name,
                'prices' => $prices,
                'discounts' => $discounts,
                'category_id' => $category_id,
                'owner_id' => $_SESSION['user_id'],
                'image' => $new_filename,
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