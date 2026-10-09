<?php

require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {

        $sql = "INSERT INTO employees (employees_code, first_name, last_name, department, position, employment_status, basic_daily_rate, hire_date) 
                VALUES (:emp_code, :fname, :lname, :dept, :pos, :status, :rate, :hdate)";
        
        $stmt = $conn->prepare($sql);
    
        $stmt->execute([
            ':emp_code' => $_POST['employees_code'],
            ':fname'    => $_POST['first_name'],
            ':lname'    => $_POST['last_name'],
            ':dept'     => $_POST['department'],
            ':pos'      => $_POST['position'],
            ':status'   => $_POST['employment_status'],
            ':rate'     => $_POST['basic_daily_rate'],
            ':hdate'    => $_POST['hire_date']
        ]);

        header("Location: ../index.php");
        exit();

    } catch(PDOException $e) {
        die("Error adding employee: " . $e->getMessage());
    }
}
?>