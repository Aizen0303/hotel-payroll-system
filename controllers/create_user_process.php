<?php
session_start();
require_once '../config/db.php';

// Double-check Admin status before processing
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $raw_password = $_POST['password'];

    // BACKEND REGEX VALIDATION
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
    
    if (!preg_match($regex, $raw_password)) {
        header("Location: ../pages/create_user.php?error=Password does not meet strict security requirements.");
        exit();
    }

    // Only hash the password AFTER it passes the Regex test
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':role' => $role
        ]);
        
        header("Location: ../pages/create_user.php?success=Account successfully created!");
        exit();

    } catch (PDOException $e) {
        // Error code 23000 means the username is already taken (UNIQUE constraint)
        if ($e->getCode() == 23000) {
            header("Location: ../pages/create_user.php?error=That username is already taken.");
        } else {
            header("Location: ../pages/create_user.php?error=Database error: " . $e->getMessage());
        }
        exit();
    }
}