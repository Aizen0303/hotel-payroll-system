<?php
session_start();
require_once '../config/db.php';

// Verify Admin Status
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $new_password = $_POST['new_password'];

    // Enforce the same strict Regex rules on the reset
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
    
    if (!preg_match($regex, $new_password)) {
        header("Location: ../pages/create_user.php?error=Reset failed: Password does not meet strict security requirements.");
        exit();
    }

    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    try {
        // Overwrite the old hash with the new hash
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => $hashed_password,
            ':id' => $id
        ]);
        
        header("Location: ../pages/create_user.php?success=Password successfully reset!");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/create_user.php?error=Database error: " . $e->getMessage());
        exit();
    }
}
?>