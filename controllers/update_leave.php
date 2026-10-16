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

    if (!in_array($new_status, ['Approved', 'Denied'])) {
        die("Invalid status update.");
    }

    try {
        // 1. Update the leave request status
        $sql = "UPDATE leave_requests SET status = :status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':status' => $new_status,
            ':id' => $leave_id
        ]);

        // 2. AUTOMATION: If Approved, automatically log them as 'On Leave' in the Attendance table
        if ($new_status === 'Approved') {
            // Fetch the employee_id and date for this specific leave
            $leaveStmt = $conn->prepare("SELECT employee_id, leave_date FROM leave_requests WHERE id = :id");
            $leaveStmt->execute([':id' => $leave_id]);
            $leaveData = $leaveStmt->fetch(PDO::FETCH_ASSOC);

            if ($leaveData) {
                $attSql = "INSERT INTO attendance (employee_id, attendance_date, status) 
                           VALUES (:emp_id, :leave_date, 'On Leave')";
                $attStmt = $conn->prepare($attSql);
                $attStmt->execute([
                    ':emp_id' => $leaveData['employee_id'],
                    ':leave_date' => $leaveData['leave_date']
                ]);
            }
        }

        header("Location: ../pages/manage_leaves.php?success=Leave successfully " . strtolower($new_status) . ".");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>