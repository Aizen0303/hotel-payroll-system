<?php
session_start();
require_once '../config/db.php';

// Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all announcements, joined with the users table to get the author's name
$sql = "SELECT a.*, u.username FROM announcements a 
        JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC";
$stmt = $conn->query($sql);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements - Hotel Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #1a233a; color: #fff; padding: 40px 0; }
        .card { background-color: #27314f; border: none; border-radius: 10px; }
        .announcement-card { background-color: #1a233a; border: 1px solid #3b476b; border-left: 4px solid #8b5cf6; }
        .modal-content { background-color: #27314f; color: #fff; border: 1px solid #3b476b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../index.php" class="btn btn-outline-light">&larr; Back to Dashboard</a>
            <h4 class="m-0">Company Announcements</h4>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
                    <i class="bi bi-megaphone-fill me-2"></i> Post Announcement
                </button>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="card p-4 shadow-lg">
            <?php if (count($announcements) > 0): ?>
                <div class="row g-4">
                    <?php foreach ($announcements as $post): ?>
                        <div class="col-12">
                            <div class="card p-4 announcement-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold m-0 text-white"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($post['created_at'])); ?></small>
                                </div>
                                <p class="text-light mb-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars($post['message']); ?></p>
                                <div class="text-muted small">
                                    <i class="bi bi-person-circle me-1"></i> Posted by <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p>No announcements have been posted yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Post Announcement Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title">Create New Announcement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../controllers/post_announcement.php" method="POST">
                    <div class="modal-body border-0 text-start">
                        <div class="mb-3">
                            <label class="form-label text-light">Title / Subject</label>
                            <input type="text" name="title" class="form-control" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light">Message</label>
                            <textarea name="message" class="form-control" rows="5" style="background-color: #1a233a; color: #fff; border: 1px solid #3b476b;" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Post Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>