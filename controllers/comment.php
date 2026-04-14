<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/CommentRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

$commentRepo = new CommentRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $productId = $_POST['product_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $commentRepo->add($productId, $_SESSION['user_id'], $comment);
    }

    header("Location: ../views/product_detail.php?id=" . $productId);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $commentId = $_POST['comment_id'];
    $productId = $_POST['product_id'];

    $commentRepo->delete($commentId, $_SESSION['user_id']);

    header("Location: ../views/product_detail.php?id=" . $productId);
    exit();
}

header("Location: ../views/home.php");