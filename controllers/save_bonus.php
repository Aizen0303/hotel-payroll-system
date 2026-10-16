<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = $_POST['employee_id'];
    $bonus_month = $_POST['bonus_month'];
    $amount = $_POST['amount'];

    try {
        // ON DUPLICATE KEY UPDATE ensures that if the Admin sets a bonus, 
        // and later changes their mind and sets a new amount, it overwrites the old one 
        // instead of adding a second bonus for the same month.
        $sql = "INSERT INTO bonuses (employee_id, bonus_month, amount) 
                VALUES (:emp_id, :b_month, :amt) 
                ON DUPLICATE KEY UPDATE amount = :amt_update";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':emp_id' => $employee_id,
            ':b_month' => $bonus_month,
            ':amt' => $amount,
            ':amt_update' => $amount
        ]);

        // Redirect back to the payroll page with the exact same month filter applied
        header("Location: ../pages/payroll.php?month_filter=" . urlencode($bonus_month) . "&success=Bonus successfully updated!");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>