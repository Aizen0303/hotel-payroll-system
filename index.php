<?php
// 1. Connect to the database
require_once 'config/db.php';

$stmtCount = $conn->query("SELECT COUNT(*) as total_active FROM employees");
$activeEmployees = $stmtCount->fetch(PDO::FETCH_ASSOC)['total_active'];

$stmtList = $conn->query("SELECT * FROM employees ORDER BY id DESC LIMIT 5");
$employeeList = $stmtList->fetchAll(PDO::FETCH_ASSOC);

// 2. Load the Bootstrap Header
require_once 'includes/header.php';
?>

<!-- Custom Styles to Match the Video's UI -->
<style>
    body { background-color: #f4f7fc; }
    
    /* Dark Sidebar Styling */
    .sidebar { min-height: 100vh; background-color: #1a233a; width: 250px; }
    .sidebar .brand { color: #fff; font-weight: bold; font-size: 1.2rem; padding: 20px; border-bottom: 1px solid #2a3553; }
    .sidebar a { color: #a1a8b3; text-decoration: none; display: block; padding: 15px 20px; font-weight: 500; transition: 0.3s; }
    .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #27314f; border-left: 4px solid #4a6cf7; }
    
    /* Video-matching Custom Card Colors */
    .card-stat { color: #fff; border: none; border-radius: 10px; }
    .bg-purple { background-color: #8b5cf6; }
    .bg-orange { background-color: #f97316; }
    .bg-green { background-color: #22c55e; }
    
    /* Top Navbar Search Bar */
    .top-search { background-color: #f0f2f5; border: none; border-radius: 20px; padding: 8px 20px; width: 100%; max-width: 300px; }
</style>

<!-- Ensure Bootstrap Icons are loaded for the sidebar and UI icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="d-flex">
    <!-- 1. Sidebar Navigation -->
    <div class="sidebar flex-shrink-0 shadow">
        <div class="brand d-flex align-items-center">
            <i class="bi bi-buildings-fill me-2 fs-4 text-primary"></i> Hotel Payroll
        </div>
        <div class="mt-3">
            <a href="#" class="active"><i class="bi bi-grid-fill me-3"></i> Dashboard</a>
            <a href="#"><i class="bi bi-people-fill me-3"></i> Employees</a>
            <a href="#"><i class="bi bi-calendar-check-fill me-3"></i> Attendance</a>
            <a href="#"><i class="bi bi-wallet-fill me-3"></i> Payroll</a>
            <a href="#"><i class="bi bi-envelope-fill me-3"></i> Manage Leaves</a>
            <a href="#"><i class="bi bi-megaphone-fill me-3"></i> Announcements</a>
            <a href="#" class="text-danger mt-5"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1 overflow-hidden">
        
        <!-- 2. Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3 d-flex justify-content-between">
            <div>
                <input type="text" class="top-search" placeholder="Search...">
            </div>
            <div class="d-flex align-items-center">
                <span class="fw-bold me-3 text-dark">Admin</span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle shadow-sm" width="40" alt="Admin">
            </div>
        </nav>

        <!-- 3. Dashboard Content Container -->
        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-1 text-dark">Dashboard</h3>
            <p class="text-muted mb-4">Welcome back, Admin!</p>

            <!-- Top Summary Cards (Purple, Orange, Green) -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card card-stat bg-purple shadow-sm h-100 p-3">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <h2 class="fw-bold mb-0">100%</h2>
                            <p class="mb-0 fs-5">Today's Attendance</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat bg-orange shadow-sm h-100 p-3">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <h2 class="fw-bold mb-0"><?php echo $activeEmployees; ?></h2>
                                <p class="mb-0 fs-5">Active Employees</p>
                            </div>
                     </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat bg-green shadow-sm h-100 p-3">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <h2 class="fw-bold mb-0">₱ 145,000.00</h2>
                            <p class="mb-0 fs-5">Payroll This Month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Bottom Section: Tables & Lists -->
            <div class="row g-4">
                
                <!-- Recent Salary Slips (Wider Column) -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                 <h5 class="fw-bold mb-0">Employee Roster</h5>
                                 <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal"> + Add Employee</button>
                             </div>
                        <div class="table-responsive">
                                <table class="table table-hover align-middle">
    <thead class="text-muted">
        <tr>
            <th>Employee</th>
            <th>Department</th>
            <th>Daily Rate</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($employeeList as $emp): ?>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <!-- Automatically generates an avatar based on their name -->
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($emp['first_name'] . ' ' . $emp['last_name']); ?>&background=random" class="rounded-circle me-3" width="35">
                    <span class="fw-bold"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></span>
                </div>
            </td>
            <td><?php echo htmlspecialchars($emp['department']); ?></td>
            <td class="fw-bold">₱ <?php echo number_format($emp['basic_daily_rate'], 2); ?></td>
            <td>
                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">
                    <?php echo htmlspecialchars($emp['employment_status']); ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- On Leave Today (Narrower Column) -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">On Leave Today</h5>
                            
                            <div class="d-flex align-items-center mb-4">
                                <img src="https://ui-avatars.com/api/?name=Mark+Smith&background=random" class="rounded-circle me-3" width="40">
                                <div>
                                    <p class="mb-0 fw-bold">Mark Smith</p>
                                    <small class="text-muted">Housekeeping</small>
                                </div>
                                <span class="badge bg-warning text-dark ms-auto border rounded-pill px-3 py-2">Leave</span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

// 4. Load the Bootstrap Footer and Scripts
<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Register New Employee</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="controllers/add_employee.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employees Code</label>
                            <input type="text" name="employees_code" class="form-control" required placeholder="EMP-003">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" name="hire_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select" required>
                                <option value="Front Desk">Front Desk</option>
                                <option value="Housekeeping">Housekeeping</option>
                                <option value="Food & Beverage">Food & Beverage</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Management">Management</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="employment_status" class="form-select" required>
                                <option value="Regular">Regular</option>
                                <option value="Probationary">Probationary</option>
                                <option value="Contractual">Contractual</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Basic Daily Rate (₱)</label>
                            <input type="number" step="0.01" name="basic_daily_rate" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>