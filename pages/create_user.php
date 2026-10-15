<?php
session_start();
require_once '../config/db.php'; // Required to fetch the user list

// Security Guard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all registered users
$stmtUsers = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
$usersList = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a233a; color: #fff; padding: 40px 0; }
        .card { background-color: #27314f; border: none; border-radius: 10px; }
        .form-control, .form-select { background-color: #1a233a; color: #fff; border: 1px solid #3b476b; }
        .form-control:focus, .form-select:focus { background-color: #1a233a; color: #fff; border-color: #4a6cf7; box-shadow: none; }
        .table { color: #fff; vertical-align: middle; }
        .table thead th { border-bottom: 2px solid #3b476b; }
        .table td { border-bottom: 1px solid #3b476b; }
        .modal-content { background-color: #27314f; color: #fff; border: 1px solid #3b476b; }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="mb-4">
            <a href="../index.php" class="btn btn-outline-light">&larr; Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- LEFT COLUMN: Create User Form -->
            <div class="col-md-4">
                <div class="card p-4 shadow-lg">
                    <h5 class="mb-4">Create Account</h5>
                    <form action="../controllers/create_user_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Account Role</label>
                            <select name="role" class="form-select" required>
                                <option value="employee">Employee</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small">Temporary Password</label>
                            <input type="password" name="password" class="form-control" required
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" 
                                   title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character (e.g., @, #, $).">
                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Must contain 8+ chars, 1 uppercase, 1 number, and 1 special char.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN: Users Table & Reset Modals -->
            <div class="col-md-8">
                <div class="card p-4 shadow-lg">
                    <h5 class="mb-4">Registered Users</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usersList as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td>
                                    <span class="badge <?php echo $u['role'] === 'admin' ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo ucfirst($u['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetModal<?php echo $u['id']; ?>">
                                        Reset Password
                                    </button>
                                </td>
                            </tr>

                            <!-- Reset Password Modal for this specific user -->
                            <div class="modal fade" id="resetModal<?php echo $u['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title">Reset Password: <?php echo htmlspecialchars($u['username']); ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="../controllers/reset_password.php" method="POST">
                                            <div class="modal-body border-0 text-start">
                                                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">New Password</label>
                                                    <input type="password" name="new_password" class="form-control" required
                                                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" 
                                                           title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character (e.g., @, #, $).">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">Save New Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS is required for the Modals to open -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>