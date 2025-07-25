<?php
include 'includes/header.php'; // Sidebar/header include

// DB connection
$host = "localhost";
$dbname = "employee_system";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get total deductions
$deductionStmt = $pdo->query("SELECT SUM(amount) as total_deductions FROM deductions");
$totalDeductions = $deductionStmt->fetch(PDO::FETCH_ASSOC)['total_deductions'] ?? 0;

// Fetch employee payroll data
$stmt = $pdo->query("
    SELECT 
        u.id, 
        u.full_name, 
        p.rate_per_hour, 
        IFNULL(SUM(TIMESTAMPDIFF(MINUTE, a.time_in, a.time_out)) / 60, 0) AS total_hours,
        IFNULL(p.rate_per_hour * SUM(TIMESTAMPDIFF(MINUTE, a.time_in, a.time_out)) / 60, 0) AS regular_gross,
        (
            SELECT IFNULL(SUM(ot.hours * ot.rate), 0)
            FROM overtime ot
            WHERE ot.user_id = u.id
        ) AS overtime_pay,
        IFNULL(SUM(ca.amount), 0) AS cash_advance
    FROM users u
    LEFT JOIN positions p ON u.position_id = p.id
    LEFT JOIN attendance_logs a ON u.id = a.user_id
    LEFT JOIN cash_advance ca ON u.id = ca.user_id
    WHERE u.role = 'employee'
    GROUP BY u.id, u.full_name, p.rate_per_hour
    ORDER BY u.full_name
");

$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Payroll</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            padding: 20px;
        }

        body.sidebar-open .main-content {
            margin-left: 200px;
        }

        .content {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background-color: white;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            body.sidebar-open .main-content {
                margin-left: 0;
            }

            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="content">
        <h2>Employee List with Gross Pay, Overtime, Deductions, and Net Pay</h2>

        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Regular Pay (₱)</th>
                    <th>Overtime Pay (₱)</th>
                    <th>Total Deductions (₱)</th>
                    <th>Cash Advance (₱)</th>
                    <th><strong>Net Pay (₱)</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($employees) > 0): ?>
                    <?php foreach ($employees as $emp): ?>
                        <?php
                            $gross = $emp['regular_gross'] + $emp['overtime_pay'];
                            $ca = $emp['cash_advance'];
                            $net = $gross - $totalDeductions - $ca;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($emp['id']) ?></td>
                            <td><?= htmlspecialchars($emp['full_name']) ?></td>
                            <td><?= number_format($emp['regular_gross'], 2) ?></td>
                            <td><?= number_format($emp['overtime_pay'], 2) ?></td>
                            <td><?= number_format($totalDeductions, 2) ?></td>
                            <td><?= number_format($ca, 2) ?></td>
                            <td><strong><?= number_format($net, 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No employees found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
