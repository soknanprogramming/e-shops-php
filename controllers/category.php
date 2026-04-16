<?php
session_start();
require_once '../repos/CategoryRepository.php';
require_once '../configs/connect.php';

$categoryRepo = new CategoryRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    
    // 1. Auth Check
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: ../views/login.php");
        exit();
    }

    $name = $_POST['name'];
    $image = $_FILES['image'];

    // 2. Basic Validation
    if (empty($name) || empty($image['name'])) {
        header("Location: ../views/admin_category_add.php?error=Please fill all fields");
        exit();
    }

    // 3. Handle Image Upload
    $target_dir = "../uploads/categories/";
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_extension = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($image_extension, $allowed_extensions)) {
        header("Location: ../views/admin_category_add.php?error=Invalid file format");
        exit();
    }

    // Create unique filename to avoid overwriting
    $new_filename = uniqid('cat_', true) . '.' . $image_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        try {
            // 4. Insert into Database
            $categoryRepo->create([
                'name' => $name,
                'category_image' => $new_filename
            ]);

            header("Location: ../views/admin_category.php?success=Category added successfully");
            exit();

        } catch (PDOException $e) {
            header("Location: ../views/admin_category_add.php?error=Database error: " . $e->getMessage());
            exit();
        }
    } else {
        header("Location: ../views/admin_category_add.php?error=Failed to upload image");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    
    // 1. Auth Check
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: ../views/login.php");
        exit();
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $current_image = $_POST['current_image'];
    $image = $_FILES['image'];

    if (empty($name)) {
        header("Location: ../views/admin_category_edit.php?id=$id&error=Name is required");
        exit();
    }

    $final_image_name = $current_image;

    // Handle Image Upload if a new one is selected
    if (!empty($image['name'])) {
        $target_dir = "../uploads/categories/";
        $image_extension = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($image_extension, $allowed_extensions)) {
            $new_filename = uniqid('cat_', true) . '.' . $image_extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                $final_image_name = $new_filename;
            }
        }
    }

    try {
        $categoryRepo->update([
            'id' => $id,
            'name' => $name,
            'category_image' => $final_image_name
        ]);
        header("Location: ../views/admin_category.php?success=Category updated successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: ../views/admin_category_edit.php?id=$id&error=" . $e->getMessage());
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    
    // 1. Auth Check
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: ../views/login.php");
        exit();
    }

    $id = $_GET['id'];

    try {
        // 1. Check if category has products
        if ($categoryRepo->hasProducts($id)) {
            header("Location: ../views/admin_category.php?error=Cannot delete category because it has products in it.");
            exit();
        }

        // 2. Get category to find image path
        $category = $categoryRepo->findById($id);
        
        // 3. Delete from DB first
        if ($categoryRepo->delete($id)) {
            // 4. If DB delete successful, delete image file
            if ($category && !empty($category['category_image'])) {
                $image_path = "../uploads/categories/" . $category['category_image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            header("Location: ../views/admin_category.php?success=Category deleted successfully");
        } else {
            header("Location: ../views/admin_category.php?error=Failed to delete category");
        }
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: ../views/admin_category.php?error=Cannot delete category because it is being used by other records.");
        } else {
            header("Location: ../views/admin_category.php?error=" . urlencode($e->getMessage()));
        }
        exit();
    }
}
?>