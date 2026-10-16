<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['month'])) {
    die("Invalid request.");
}

$emp_id = $_GET['id'];
$month = $_GET['month'];
$penalty_rate = 100.00;

// Fetch specific employee's payroll data
$sql = "SELECT e.*, 
               (SELECT COUNT(id) FROM attendance WHERE employee_id = e.id AND status IN ('Present', 'Late') AND DATE_FORMAT(attendance_date, '%Y-%m') = :month) as days_worked,
               (SELECT COUNT(id) FROM attendance WHERE employee_id = e.id AND status = 'Late' AND DATE_FORMAT(attendance_date, '%Y-%m') = :month) as days_late,
               (SELECT COALESCE(amount, 0) FROM bonuses WHERE employee_id = e.id AND bonus_month = :month) as bonus
        FROM employees e
        WHERE e.id = :emp_id";

$stmt = $conn->prepare($sql);
$stmt->execute([':month' => $month, ':emp_id' => $emp_id]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emp) die("Employee not found.");

// Math Engine
$gross_salary = $emp['basic_daily_rate'] * $emp['days_worked'];
$late_deduction = $emp['days_late'] * $penalty_rate;
$bonus = $emp['bonus'];
$net_salary = $gross_salary + $bonus - $late_deduction;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Force a clean, white layout for printing */
        body { background-color: #f8f9fa; color: #000; padding: 40px; font-family: 'Courier New', Courier, monospace; }
        .payslip-container { background: #fff; max-width: 800px; margin: 0 auto; padding: 40px; border: 1px solid #ddd; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px; }
        .table th, .table td { border-color: #ccc; }
        .total-row { font-weight: bold; font-size: 1.2rem; border-top: 2px solid #000; }
        
        /* Hide buttons when printing */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; padding: 0; }
            .payslip-container { box-shadow: none; border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <!-- Print Button -->
        <div class="text-end mb-4 no-print">
            <button class="btn btn-primary" onclick="window.print()">Print Payslip</button>
            <button class="btn btn-secondary" onclick="window.close()">Close</button>
        </div>

        <div class="header text-center">
            <h2>HOTEL PAYROLL SYSTEM</h2>
            <p class="m-0">Employee Payslip</p>
            <p class="m-0 text-muted">For the Month of <?php echo date('F Y', strtotime($month)); ?></p>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <strong>Employee ID:</strong> <?php echo htmlspecialchars($emp['employee_code'] ?? 'EMP-' . str_pad($emp['id'], 3, '0', STR_PAD_LEFT)); ?><br>
                <strong>Name:</strong> <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
            </div>
            <div class="col-sm-6 text-end">
                <strong>Department:</strong> <?php echo htmlspecialchars($emp['department']); ?><br>
                <strong>Position:</strong> <?php echo htmlspecialchars($emp['position'] ?? 'Staff'); ?>
            </div>
        </div>

        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th>Earnings & Deductions</th>
                    <th class="text-end">Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Daily Rate</td>
                    <td class="text-end"><?php echo number_format($emp['basic_daily_rate'], 2); ?></td>
                </tr>
                <tr>
                    <td>Days Worked (<?php echo $emp['days_worked']; ?> days)</td>
                    <td class="text-end"><?php echo number_format($gross_salary, 2); ?></td>
                </tr>
                <tr>
                    <td>Performance Bonus</td>
                    <td class="text-end"><?php echo number_format($bonus, 2); ?></td>
                </tr>
                <tr>
                    <td>Late Penalties (<?php echo $emp['days_late']; ?> days at -₱100)</td>
                    <td class="text-end text-danger">- <?php echo number_format($late_deduction, 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td>NET SALARY</td>
                    <td class="text-end">₱ <?php echo number_format($net_salary, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-5 pt-5 text-center">
            <div class="col-6">
                <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                <p class="mt-2">Employer Signature</p>
            </div>
            <div class="col-6">
                <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                <p class="mt-2">Employee Signature</p>
            </div>
        </div>
    </div>
</body>
</html>