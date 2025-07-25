<?php
require 'config.php';
require 'includes/header.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

// Get fixed deductions
$deductionStmt = $pdo->query("SELECT SUM(amount) as total_deductions FROM deductions");
$totalDeductions = $deductionStmt->fetch(PDO::FETCH_ASSOC)['total_deductions'] ?? 0;

// Get employee info
$stmt = $pdo->prepare("
    SELECT 
        u.full_name,
        p.rate_per_hour,
        IFNULL(SUM(TIMESTAMPDIFF(MINUTE, a.time_in, a.time_out)) / 60, 0) AS total_hours,
        IFNULL(p.rate_per_hour * SUM(TIMESTAMPDIFF(MINUTE, a.time_in, a.time_out)) / 60, 0) AS gross,
        IFNULL(SUM(ca.amount), 0) AS cash_advance
    FROM users u
    LEFT JOIN positions p ON u.position_id = p.id
    LEFT JOIN attendance_logs a ON u.id = a.user_id
    LEFT JOIN cash_advance ca ON u.id = ca.user_id
    WHERE u.id = ?
    GROUP BY u.full_name, p.rate_per_hour
");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

$gross = $employee['gross'] ?? 0;
$cashAdvance = $employee['cash_advance'] ?? 0;
$netPay = $gross - $totalDeductions - $cashAdvance;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Payroll</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        th {
            background-color: #2980b9;
            color: white;
            text-align: left;
            width: 40%;
            white-space: nowrap;
        }

        td {
            text-align: right;
            width: 60%;
        }

        tr:nth-child(even) {
            background-color: #f4f6f9;
        }

        .net-pay {
            font-weight: bold;
            color: #27ae60;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>My Payroll Summary</h2>

    <?php if ($employee): ?>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Full Name</th>
                    <td><?= htmlspecialchars($employee['full_name']) ?></td>
                </tr>
                <tr>
                    <th>Total Hours</th>
                    <td><?= number_format($employee['total_hours'], 2) ?> hrs</td>
                </tr>
                <tr>
                    <th>Rate per Hour</th>
                    <td>₱<?= number_format($employee['rate_per_hour'], 2) ?></td>
                </tr>
                <tr>
                    <th>Gross Pay</th>
                    <td>₱<?= number_format($gross, 2) ?></td>
                </tr>
                <tr>
                    <th>Total Deductions</th>
                    <td>₱<?= number_format($totalDeductions, 2) ?></td>
                </tr>
                <tr>
                    <th>Cash Advance</th>
                    <td>₱<?= number_format($cashAdvance, 2) ?></td>
                </tr>
                <tr>
                    <th><strong>Net Pay</strong></th>
                    <td class="net-pay">₱<?= number_format($netPay, 2) ?></td>
                </tr>
            </table>
        </div>
        <div class="back-button">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    <?php else: ?>
        <p style="text-align:center; color: #c0392b;">No payroll data found.</p>
    <?php endif; ?>
</div>

</body>
</html>
