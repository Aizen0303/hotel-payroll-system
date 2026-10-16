<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = $_POST['employee_id'];
    $leave_date = $_POST['leave_date'];
    $leave_type = $_POST['leave_type'];

    try {
        $sql = "INSERT INTO leave_requests (employee_id, leave_date, leave_type, status) 
                VALUES (:emp_id, :leave_date, :leave_type, 'Pending')";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':emp_id' => $employee_id,
            ':leave_date' => $leave_date,
            ':leave_type' => $leave_type
        ]);

        header("Location: ../pages/manage_leaves.php?success=Leave request submitted successfully!");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>