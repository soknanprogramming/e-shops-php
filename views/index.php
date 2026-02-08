<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // User is logged in, redirect to @home
    header("Location: home.php");
    exit();
} else {
    // User is not logged in, redirect to @login
    header("Location: login.php");
    exit();
}