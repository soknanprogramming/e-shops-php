<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/LikeRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $likeRepo = new LikeRepository($conn);
    $likeRepo->toggle($_SESSION['user_id'], $_POST['product_id']);
    
    // Redirect back to the previous page
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../views/product_detail.php?id=" . $_POST['product_id']);
    }
    exit();
}

header("Location: ../views/home.php");