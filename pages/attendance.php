<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Determine which month to show (defaults to the current month, e.g., '2026-10')
$filter_month = isset($_GET['month_filter']) ? $_GET['month_filter'] : date('Y-m');

// 2. Fetch filtered attendance records 
$sql = "SELECT a.*, e.first_name, e.last_name, e.department 
        FROM attendance a 
        JOIN employees e ON a.employee_id = e.id 
        WHERE DATE_FORMAT(a.attendance_date, '%Y-%m') = :filter_month
        ORDER BY a.attendance_date DESC, a.time_in DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([':filter_month' => $filter_month]);
$attendance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch employee list for the Mark Attendance modal
$empStmt = $conn->query("SELECT id, first_name, last_name FROM employees ORDER BY first_name ASC");
$employeeList = $empStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance - Hotel Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #1a233a; color: #fff; padding: 40px 0; }
        .card { background-color: #27314f; border: none; border-radius: 10px; }
        .table { color: #fff; vertical-align: middle; }
        .table thead th { border-bottom: 2px solid #3b476b; color: #a1a8b3; }
        .table td { border-bottom: 1px solid #3b476b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../index.php" class="btn btn-outline-light">&larr; Back to Dashboard</a>
            <h4 class="m-0">Daily Attendance Log</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                <i class="bi bi-clock-history me-2"></i> Mark Attendance
            </button>
        </div>

            <div class="card p-4 shadow-lg">
            <!-- NEW: Month Filter Bar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 text-light">Showing records for: <strong><?php echo date('F Y', strtotime($filter_month)); ?></strong></h6>
                <form method="GET" class="d-flex gap-2">
                    <input type="month" name="month_filter" class="form-control form-control-sm" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" value="<?php echo htmlspecialchars($filter_month); ?>" required>
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="attendance.php" class="btn btn-sm btn-outline-light">Clear</a>
                </form>
            </div>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($attendance_logs) > 0): ?>
                        <?php foreach ($attendance_logs as $log): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($log['attendance_date'])); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($log['department']); ?></td>
                            <td><?php echo $log['time_in'] ? date('h:i A', strtotime($log['time_in'])) : '--:--'; ?></td>
                            <td><?php echo $log['time_out'] ? date('h:i A', strtotime($log['time_out'])) : '--:--'; ?></td>
                            <td>
                                <?php 
                                    $badge = 'bg-success';
                                    if ($log['status'] == 'Late') $badge = 'bg-warning text-dark';
                                    if ($log['status'] == 'Absent') $badge = 'bg-danger';
                                    if ($log['status'] == 'On Leave') $badge = 'bg-info text-dark';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $log['status']; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Mark Attendance Modal -->
        <div class="modal fade" id="markAttendanceModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Mark Attendance</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="../controllers/mark_attendance.php" method="POST">
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label text-dark">Employee</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">Select Employee...</option>
                                    <?php foreach ($employeeList as $emp): ?>
                                        <option value="<?php echo $emp['id']; ?>">
                                            <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark">Date</label>
                                <!-- Defaults to today's date automatically -->
                                <input type="date" name="attendance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark">Time In</label>
                                    <input type="time" name="time_in" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark">Time Out</label>
                                    <input type="time" name="time_out" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Present">Present</option>
                                    <option value="Late">Late</option>
                                    <option value="Absent">Absent</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>