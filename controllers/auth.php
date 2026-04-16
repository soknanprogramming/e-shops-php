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

        // 1. Validate Password Length
        if (strlen($password) < 6) {
            $_SESSION['register_input'] = compact('name', 'first_name', 'last_name', 'email');
            session_write_close();
            header("Location: ../views/register.php?error=លេខសម្ងាត់ត្រូវមានយ៉ាងហោចណាស់ ៦ តួអក្សរ");
            exit();
        }

        // 2. Validate Passwords Match
        if ($password !== $confirm_password) {
            $_SESSION['register_input'] = compact('name', 'first_name', 'last_name', 'email');
            session_write_close();
            header("Location: ../views/register.php?error=លេខសម្ងាត់មិនផ្ទៀងផ្ទាត់គ្នាឡើយ");
            exit();
        }

        // 2. Check if Username Exists
        if ($userRepo->findByName($name)) {
            $_SESSION['register_input'] = compact('name', 'first_name', 'last_name', 'email');
            session_write_close();
            header("Location: ../views/register.php?error=ឈ្មោះអ្នកប្រើប្រាស់ត្រូវបានគេយកទៅប្រើហើយ");
            exit();
        }

        // 3. Check if Email Exists
        if ($userRepo->findByEmail($email)) {
            $_SESSION['register_input'] = compact('name', 'first_name', 'last_name', 'email');
            session_write_close();
            header("Location: ../views/register.php?error=អ៊ីមែលនេះត្រូវបានចុះឈ្មោះរួចហើយ");
            exit();
        }

        // 4. Hash Password
        // Note: Ensure your DB 'password' column is at least 60 characters to hold the hash.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $hashed_password,
            'is_admin' => 0,
            'avatar' => 0,
            'can_post' => 0
        ];

        // 5. Create User
        $userRepo->create($data);
        header("Location: ../views/login.php?success=ការចុះឈ្មោះជោគជ័យ។ សូមចូលប្រើប្រាស់។");
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
            $_SESSION['can_post'] = $user['can_post'] ?? 0;
            header("Location: ../views/home.php");
            exit();
        } else {
            $_SESSION['login_email'] = $email;
            session_write_close();
            header("Location: ../views/login.php?error=អ៊ីមែល ឬលេខសម្ងាត់មិនត្រឹមត្រូវ");
            exit();
        }
        }
        }