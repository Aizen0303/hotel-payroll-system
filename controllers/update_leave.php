<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $leave_id = $_GET['id'];
    $new_status = $_GET['status']; // 'Approved' or 'Denied'

    // Security check to ensure only valid statuses are accepted
    if (!in_array($new_status, ['Approved', 'Denied'])) {
        die("Invalid status update.");
    }

    try {
        $sql = "UPDATE leave_requests SET status = :status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':status' => $new_status,
            ':id' => $leave_id
        ]);

        header("Location: ../pages/manage_leaves.php?success=Leave successfully " . strtolower($new_status) . ".");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>