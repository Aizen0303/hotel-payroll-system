<?php
// 1. Connect to the database
require_once 'config/db.php';

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
                            <h2 class="fw-bold mb-0">12</h2>
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
                            <h5 class="fw-bold mb-4">Recent Salary Slips</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="text-muted">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Pay Period</th>
                                            <th>Net Salary</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Tranks+Caballero&background=random" class="rounded-circle me-3" width="35">
                                                    <span class="fw-bold">Tranks Caballero</span>
                                                </div>
                                            </td>
                                            <td>Aug 01, 2026 - Aug 31, 2026</td>
                                            <td class="fw-bold">₱ 39,000.00</td>
                                            <td><button class="btn btn-sm btn-outline-primary">View Slip</button></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Jane+Doe&background=random" class="rounded-circle me-3" width="35">
                                                    <span class="fw-bold">Jane Doe</span>
                                                </div>
                                            </td>
                                            <td>Aug 01, 2026 - Aug 31, 2026</td>
                                            <td class="fw-bold">₱ 26,900.00</td>
                                            <td><button class="btn btn-sm btn-outline-primary">View Slip</button></td>
                                        </tr>
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

<?php
// 4. Load the Bootstrap Footer and Scripts
require_once 'includes/footer.php';
?>