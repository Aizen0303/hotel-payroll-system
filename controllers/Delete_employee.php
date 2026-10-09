<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    try {
        $sql = "DELETE FROM employees WHERE id = :id";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([':id' => $_POST['id']]);

        header("Location: ../index.php");
        exit();

    } catch(PDOException $e) {
        die("Error deleting employee: " . $e->getMessage());
    }
} else {

    header("Location: ../index.php");
    exit();
}
?>