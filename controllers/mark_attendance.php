<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    
    // If times are left blank (e.g., marked Absent), set them to NULL for the database
    $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : null;
    $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : null;
    $status = $_POST['status'];

    try {
        $sql = "INSERT INTO attendance (employee_id, attendance_date, time_in, time_out, status) 
                VALUES (:emp_id, :date, :in, :out, :status)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':emp_id' => $employee_id,
            ':date'   => $attendance_date,
            ':in'     => $time_in,
            ':out'    => $time_out,
            ':status' => $status
        ]);

        header("Location: ../pages/attendance.php");
        exit();

    } catch (PDOException $e) {
        die("Error recording attendance: " . $e->getMessage());
    }
}
?>