<?php
require_once '../config/db.php';

// 2. Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    try {
        $sql = "UPDATE employees SET 
                    department = :dept, 
                    position = :pos, 
                    employment_status = :status, 
                    basic_daily_rate = :rate 
                WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':dept'   => $_POST['department'],
            ':pos'    => $_POST['position'],
            ':status' => $_POST['employment_status'],
            ':rate'   => $_POST['basic_daily_rate'],
            ':id'     => $_POST['id'] // Matches the specific employee
        ]);

        header("Location: ../index.php");
        exit();

    } catch(PDOException $e) {
        die("Error updating employee: " . $e->getMessage());
    }
}
?>