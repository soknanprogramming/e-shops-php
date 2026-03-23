<?php
// $host = 'localhost';
// $user = 'root';
// $password = '';
// $database = 'khmer24_db';

// Docker
$host = 'mysql_db';
$user = 'app_user';
$password = 'secret';
$database = 'app_db';


try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}