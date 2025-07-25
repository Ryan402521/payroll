<?php
session_start();
require 'config.php';

$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    die("Unauthorized access.");
}

// Fetch user's attendance logs
$stmt = $pdo->prepare("SELECT * FROM attendance_logs WHERE user_id = ? ORDER BY date DESC");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<!-- ✅ Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: url('uploads/bg.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
    }
    .main-content {
        max-width: 900px;
        margin: 50px auto;
        background: rgba(255,255,255,0.9);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        backdrop-filter: blur(5px);
    }
    h3 {
        font-weight: bold;
        margin-bottom: 25px;
        color: #333;
    }
    table th {
        background-color: #343a40;
        color: #fff;
    }
    table td, table th {
        vertical-align: middle;
        text-align: center;
    }
    .btn-back {
        margin-bottom: 25px;
        background-color: #6c757d;
        color: white;
        border-radius: 6px;
        padding: 8px 20px;
        text-decoration: none;
    }
    .btn-back:hover {
        background-color: #5a6268;
        color: white;
    }
</style>

<div class="main-content">
    <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a>
    <h3>📋 My Attendance Logs</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($logs) > 0): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['date']) ?></td>
                            <td><?= $log['time_in'] ? htmlspecialchars($log['time_in']) : '—' ?></td>
                            <td><?= $log['time_out'] ? htmlspecialchars($log['time_out']) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
