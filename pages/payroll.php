<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$filter_month = isset($_GET['month_filter']) ? $_GET['month_filter'] : date('Y-m');

// Core Payroll Engine: Now counts both Present and Late as worked days, and isolates Late days for deductions
$sql = "SELECT e.id, e.first_name, e.last_name, e.department, e.basic_daily_rate,
               (SELECT COUNT(id) FROM attendance WHERE employee_id = e.id AND status IN ('Present', 'Late') AND DATE_FORMAT(attendance_date, '%Y-%m') = :filter_month) as days_worked,
               (SELECT COUNT(id) FROM attendance WHERE employee_id = e.id AND status = 'Late' AND DATE_FORMAT(attendance_date, '%Y-%m') = :filter_month) as days_late,
               (SELECT COALESCE(amount, 0) FROM bonuses WHERE employee_id = e.id AND bonus_month = :filter_month) as bonus
        FROM employees e
        ORDER BY e.first_name ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([':filter_month' => $filter_month]);
$payroll_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll Dashboard - Hotel Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #1a233a; color: #fff; padding: 40px 0; }
        .card { background-color: #27314f; border: none; border-radius: 10px; }
        .table { color: #fff; vertical-align: middle; font-size: 0.95rem; }
        .table thead th { border-bottom: 2px solid #3b476b; color: #a1a8b3; }
        .table td { border-bottom: 1px solid #3b476b; }
        .salary-highlight { font-weight: bold; color: #28a745; }
        .net-highlight { font-weight: bold; color: #ffc107; font-size: 1.1rem; }
        .deduction { color: #dc3545; }
        .modal-content { background-color: #27314f; color: #fff; border: 1px solid #3b476b; }
    </style>
</head>
<body>
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../index.php" class="btn btn-outline-light">&larr; Back to Dashboard</a>
            <h4 class="m-0">Payroll Calculation</h4>
            <button class="btn btn-success" onclick="window.print()">
                <i class="bi bi-printer-fill me-2"></i> Print Report
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="card p-4 shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 text-light">Payroll Period: <strong><?php echo date('F Y', strtotime($filter_month)); ?></strong></h6>
                <form method="GET" class="d-flex gap-2">
                    <input type="month" name="month_filter" class="form-control form-control-sm" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" value="<?php echo htmlspecialchars($filter_month); ?>" required>
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="payroll.php" class="btn btn-sm btn-outline-light">Clear</a>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mt-2">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Days Worked</th>
                            <th>Gross Salary</th>
                            <th>Late Penalty (-₱100/day)</th>
                            <th>Bonus</th>
                            <th>Net Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_company_payroll = 0;
                        $penalty_rate = 100.00; // Deduct 100 pesos per late day

                        if (count($payroll_data) > 0): 
                            foreach ($payroll_data as $emp): 
                                // The Math Engine
                                $gross_salary = $emp['basic_daily_rate'] * $emp['days_worked'];
                                $late_deduction = $emp['days_late'] * $penalty_rate;
                                $bonus = $emp['bonus'];
                                
                                // Final Calculation
                                $net_salary = $gross_salary + $bonus - $late_deduction;
                                $total_company_payroll += $net_salary;
                        ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                <td>
                                    <span class="badge bg-primary rounded-pill"><?php echo $emp['days_worked']; ?></span>
                                </td>
                                <td>₱ <?php echo number_format($gross_salary, 2); ?></td>
                                <td class="deduction">
                                    <?php if($emp['days_late'] > 0): ?>
                                        - ₱ <?php echo number_format($late_deduction, 2); ?> <small class="text-muted">(<?php echo $emp['days_late']; ?>x)</small>
                                    <?php else: ?>
                                        ₱ 0.00
                                    <?php endif; ?>
                                </td>
                                <td class="text-info">+ ₱ <?php echo number_format($bonus, 2); ?></td>
                                <td class="net-highlight">₱ <?php echo number_format($net_salary, 2); ?></td>
                                <td>
                                   <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#bonusModal<?php echo $emp['id']; ?>">
                                    Set Bonus </button>
                                    <a href="payslip.php?id=<?php echo $emp['id']; ?>&month=<?php echo urlencode($filter_month); ?>" class="btn btn-sm btn-outline-success ms-1" target="_blank">
                                    <i class="bi bi-receipt"></i> Payslip </a>
                                </td>
                            </tr>

                            <!-- Bonus Modal -->
                            <div class="modal fade" id="bonusModal<?php echo $emp['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title">Set Bonus: <?php echo htmlspecialchars($emp['first_name']); ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="../controllers/save_bonus.php" method="POST">
                                            <div class="modal-body border-0 text-start">
                                                <input type="hidden" name="employee_id" value="<?php echo $emp['id']; ?>">
                                                <input type="hidden" name="bonus_month" value="<?php echo htmlspecialchars($filter_month); ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Performance Bonus Amount (₱)</label>
                                                    <input type="number" step="0.01" min="0" name="amount" class="form-control" value="<?php echo $bonus; ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-info text-dark fw-bold">Save Bonus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endforeach; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No employees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end fs-5 pt-4">Total Company Net Payout:</th>
                            <th colspan="2" class="fs-5 pt-4 text-warning">₱ <?php echo number_format($total_company_payroll, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>