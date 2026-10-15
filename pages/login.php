<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Hotel Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #1a233a; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { background: #27314f; border-radius: 12px; width: 100%; max-width: 400px; color: #fff; }
        .form-control { background-color: #1a233a; border: 1px solid #3b476b; color: #fff; }
        .form-control:focus { background-color: #1a233a; color: #fff; border-color: #4a6cf7; box-shadow: none; }
    </style>
</head>
<body>
<div class="login-card p-4 shadow-lg">
    <div class="text-center mb-4">
        <i class="bi bi-buildings-fill text-primary display-4"></i>
        <h4 class="fw-bold mt-2">Hotel Payroll</h4>
        <p class="text-muted small">Sign in to access management dashboard</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger py-2 small text-center"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form action="../controllers/login_process.php" method="POST">
        <div class="mb-3">
            <label class="form-label small">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="admin">
        </div>
        <div class="mb-4">
            <label class="form-label small">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In</button>
    </form>
</div>
</body>
</html>