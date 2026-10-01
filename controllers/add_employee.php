<?php
// 1. Connect to the database
require_once '../config/db.php';

// 2. Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 3. Prepare the secure SQL statement (Prevents SQL Injection)
        // Note: If your column in phpMyAdmin is still named 'employees_code' with an 's', change it below!
        $sql = "INSERT INTO employees (employees_code, first_name, last_name, department, position, employment_status, basic_daily_rate, hire_date) 
                VALUES (:emp_code, :fname, :lname, :dept, :pos, :status, :rate, :hdate)";
        
        $stmt = $conn->prepare($sql);
        
        // 4. Bind the form inputs to the SQL statement
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

        // 5. Redirect back to the dashboard after successful save
        header("Location: ../index.php");
        exit();

    } catch(PDOException $e) {
        die("Error adding employee: " . $e->getMessage());
    }
}
?>