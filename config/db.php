<?php
$host = 'localhost';
$dbname = 'hotel_payroll_db';
$username = 'root';
$password = ''; // The default XAMPP MySQL password is always blank

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set the PDO error mode to exception so it throws clear errors if something breaks
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
 
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>