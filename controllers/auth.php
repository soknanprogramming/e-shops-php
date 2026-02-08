<?php
session_start();

// Adjust path to reach the repository from views/controllers/
require_once '../repos/UserRepository.php';

require_once '../configs/connect.php';

$userRepo = new UserRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- REGISTER LOGIC ---
    if (isset($_POST['register'])) {
        $name = $_POST['name'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // 1. Validate Passwords Match
        if ($password !== $confirm_password) {
            header("Location: ../views/register.php?error=Passwords do not match");
            exit();
        }

        // 2. Check if Email Exists
        if ($userRepo->findByEmail($email)) {
            header("Location: ../views/register.php?error=Email already registered");
            exit();
        }

        // 3. Hash Password
        // Note: Ensure your DB 'password' column is at least 60 characters to hold the hash.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $hashed_password,
            'is_admin' => 0,
            'avatar' => 0
        ];

        // 4. Create User
        $userRepo->create($data);
        header("Location: ../views/login.php?success=Registration successful. Please login.");
        exit();
    }

    // --- LOGIN LOGIC ---
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $userRepo->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: ../views/home.php");
            exit();
        } else {
            header("Location: ../views/login.php?error=Invalid email or password");
            exit();
        }
    }
}