<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';
require_once '../repos/ProfileRepository.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    
    // 1. Check Login
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    // Fetch fresh user permissions from DB to prevent using a stale session value
    $stmtUser = $conn->prepare("SELECT can_post FROM User WHERE id = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $user = $stmtUser->fetch();

    // Check if user has permission to post
    if (!$user || $user['can_post'] != 1) {
        header("Location: ../views/user_dashboard.php?error=អ្នកត្រូវការការអនុញ្ញាតពីអ្នកគ្រប់គ្រងដើម្បីដាក់លក់ទំនិញ");
        exit();
    }

    // Check if user has a phone number
    $profileRepo = new ProfileRepository($conn);
    $userProfile = $profileRepo->getByUserId($_SESSION['user_id']);

    if (empty($userProfile) || empty($userProfile['phone1'])) {
        header("Location: ../views/user_profile.php?error=អ្នកត្រូវតែមានលេខទូរស័ព្ទយ៉ាងហោចណាស់មួយដើម្បីដាក់លក់ទំនិញ");
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
        if (!mkdir($target_dir, 0777, true)) {
            die("Failed to create target directory: " . $target_dir);
        }
    }

    if (!is_writable($target_dir)) {
        die("Target directory is not writable: " . realpath($target_dir));
    }

    // Check for PHP Upload Errors
    if ($image['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => 'ឯកសារដែលបានបង្ហោះលើសពីទំហំកំណត់ក្នុង php.ini',
            UPLOAD_ERR_FORM_SIZE  => 'ឯកសារដែលបានបង្ហោះលើសពីទំហំកំណត់ក្នុងទម្រង់ HTML',
            UPLOAD_ERR_PARTIAL    => 'ឯកសារត្រូវបានបង្ហោះតែមួយផ្នែកប៉ុណ្ណោះ',
            UPLOAD_ERR_NO_FILE    => 'មិនមានឯកសារត្រូវបានបង្ហោះទេ',
            UPLOAD_ERR_NO_TMP_DIR => 'បាត់ថតបណ្តោះអាសន្ន',
            UPLOAD_ERR_CANT_WRITE => 'បរាជ័យក្នុងការសរសេរឯកសារទៅក្នុងថាស',
            UPLOAD_ERR_EXTENSION  => 'ផ្នែកបន្ថែម PHP បានបញ្ឈប់ការបង្ហោះឯកសារ',
        ];
        $msg = $error_messages[$image['error']] ?? 'កំហុសបង្ហោះមិនស្គាល់';
        die("កំហុសក្នុងការបង្ហោះ៖ " . $msg);
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

            header("Location: ../views/user_dashboard.php?success=បានដាក់លក់ទំនិញដោយជោគជ័យ");
            exit();
        } catch (PDOException $e) {
            echo "កំហុសមូលដ្ឋានទិន្នន័យ៖ " . $e->getMessage();
        }
    } else {
        echo "បរាជ័យក្នុងការបង្ហោះរូបភាព។";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $prices = $_POST['prices'];
    $discounts = !empty($_POST['discounts']) ? $_POST['discounts'] : 0;
    $location = $_POST['location'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    $target_dir = "../uploads/products/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle Image Uploads (Only if new files are selected)
    $main_image = isset($_FILES['image']) ? upload_file($_FILES['image'], 'prod_main_', $target_dir) : null;
    
    $additional_images = [];
    for ($i = 1; $i <= 5; $i++) {
        $key = 'image' . $i;
        $additional_images[$key] = isset($_FILES[$key]) ? upload_file($_FILES[$key], "prod_{$i}_", $target_dir) : null;
    }

    try {
        $productRepo = new ProductRepository($conn);
        $productRepo->update($product_id, [
            'name' => $name,
            'prices' => $prices,
            'discounts' => $discounts,
            'category_id' => $category_id,
            'owner_id' => $_SESSION['user_id'],
            'location' => $location,
            'description' => $description,
            'image' => $main_image,
            'image1' => $additional_images['image1'],
            'image2' => $additional_images['image2'],
            'image3' => $additional_images['image3'],
            'image4' => $additional_images['image4'],
            'image5' => $additional_images['image5']
        ]);

        header("Location: ../views/user_dashboard.php?success=បានធ្វើបច្ចុប្បន្នភាពទំនិញដោយជោគជ័យ");
        exit();
    } catch (PDOException $e) {
        echo "កំហុសមូលដ្ឋានទិន្នន័យ៖ " . $e->getMessage();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    $product_repo = new ProductRepository($conn);

    // If admin, they can delete any product. If regular user, only their own.
    $ownerId = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? null : $_SESSION['user_id'];

    $deletedImages = $product_repo->delete($_GET['id'], $ownerId);

    // Determine redirect destination based on HTTP_REFERER
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $redirect = "../views/user_dashboard.php"; // default
    
    if (strpos($referer, 'admin_product.php') !== false) {
        $redirect = "../views/admin_product.php";
    } elseif (strpos($referer, 'product_detail.php') !== false) {
        $redirect = "../views/product_detail.php";
    } elseif (strpos($referer, 'home.php') !== false) {
        $redirect = "../views/home.php";
    }

    if ($deletedImages) {
        // Delete physical files
        $target_dir = "../uploads/products/";
        $fields = ['main_image', 'image1', 'image2', 'image3', 'image4', 'image5'];
        foreach ($fields as $field) {
            if (!empty($deletedImages[$field])) {
                $filePath = $target_dir . $deletedImages[$field];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        header("Location: " . $redirect . "?success=បានលុបទំនិញដោយជោគជ័យ");
    } else {
        header("Location: " . $redirect . "?error=បរាជ័យក្នុងការលុបទំនិញ");
    }
    exit();
}

// Admin Toggle Visibility
if (isset($_GET['action']) && $_GET['action'] === 'toggle_visibility' && isset($_GET['id']) && isset($_GET['status'])) {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: ../views/login.php");
        exit();
    }

    $id = $_GET['id'];
    $status = $_GET['status'] == 1 ? 1 : 0;
    $redirect = $_GET['redirect'] ?? 'admin_product.php';

    // Security check for redirect to prevent open redirect vulnerabilities
    // Only allow specific views
    $allowed_redirects = ['admin_product.php', 'product_detail.php'];
    $redirect_base = basename($redirect);
    if (!in_array($redirect_base, $allowed_redirects)) {
        $redirect = 'admin_product.php';
    } else {
        // If it's product_detail.php, keep the ID in the redirect
        if ($redirect_base === 'product_detail.php' && isset($_GET['id'])) {
            $redirect = 'product_detail.php?id=' . $_GET['id'];
        }
    }

    $product_repo = new ProductRepository($conn);
    if ($product_repo->toggleVisibility($id, $status)) {
        header("Location: ../views/" . $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . "success=បានធ្វើបច្ចុប្បន្នភាពការបង្ហាញទំនិញ");
    } else {
        header("Location: ../views/" . $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . "error=បរាជ័យក្នុងការធ្វើបច្ចុប្បន្នភាពការបង្ហាញ");
    }
    exit();
}
?>