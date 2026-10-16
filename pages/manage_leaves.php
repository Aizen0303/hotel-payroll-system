<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all leave requests joined with employee details
$sql = "SELECT l.*, e.first_name, e.last_name, e.department 
        FROM leave_requests l 
        JOIN employees e ON l.employee_id = e.id 
        ORDER BY l.leave_date DESC, l.created_at DESC";
$stmt = $conn->query($sql);
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch employee list for the "File Leave" modal
$empStmt = $conn->query("SELECT id, first_name, last_name FROM employees ORDER BY first_name ASC");
$employeeList = $empStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Leaves - Hotel Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #1a233a; color: #fff; padding: 40px 0; }
        .card { background-color: #27314f; border: none; border-radius: 10px; }
        .table { color: #fff; vertical-align: middle; }
        .table thead th { border-bottom: 2px solid #3b476b; color: #a1a8b3; }
        .table td { border-bottom: 1px solid #3b476b; }
        .modal-content { background-color: #27314f; color: #fff; border: 1px solid #3b476b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../index.php" class="btn btn-outline-light">&larr; Back to Dashboard</a>
            <h4 class="m-0">Leave Management</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fileLeaveModal">
                <i class="bi bi-calendar-plus me-2"></i> File New Leave
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="card p-4 shadow-lg">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date Requested</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($leaves) > 0): ?>
                        <?php foreach ($leaves as $leave): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($leave['leave_date'])); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                            <td>
                                <?php 
                                    $badge = 'bg-warning text-dark';
                                    if ($leave['status'] == 'Approved') $badge = 'bg-success';
                                    if ($leave['status'] == 'Denied') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $leave['status']; ?></span>
                            </td>
                            <td>
                                <?php if ($leave['status'] === 'Pending'): ?>
                                    <a href="../controllers/update_leave.php?id=<?php echo $leave['id']; ?>&status=Approved" class="btn btn-sm btn-success">Approve</a>
                                    <a href="../controllers/update_leave.php?id=<?php echo $leave['id']; ?>&status=Denied" class="btn btn-sm btn-outline-danger">Deny</a>
                                <?php else: ?>
                                    <span class="text-muted small">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No leave requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- File Leave Modal -->
    <div class="modal fade" id="fileLeaveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title">File Employee Leave</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/file_leave.php" method="POST">
                    <div class="modal-body border-0 text-start">
                        <div class="mb-3">
                            <label class="form-label text-light">Employee</label>
                            <select name="employee_id" class="form-select" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" required>
                                <option value="">Select Employee...</option>
                                <?php foreach ($employeeList as $emp): ?>
                                    <option value="<?php echo $emp['id']; ?>">
                                        <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light">Date of Leave</label>
                            <input type="date" name="leave_date" class="form-control" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light">Leave Type</label>
                            <select name="leave_type" class="form-select" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" required>
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Maternity/Paternity">Maternity/Paternity</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>