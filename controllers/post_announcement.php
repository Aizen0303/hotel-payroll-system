<?php
session_start();
require_once '../config/db.php';

// Security Guard: Only Admins can post announcements
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    try {
        $sql = "INSERT INTO announcements (user_id, title, message) VALUES (:user_id, :title, :message)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':message' => $message
        ]);

        header("Location: ../pages/announcements.php?success=Announcement posted successfully!");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>