<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$user      = $_SESSION['user'] ?? [];
$full_name = $user['full_name'] ?? 'Guest';
$role      = $user['role'] ?? 'guest';
$user_id   = $user['id'] ?? 0;

unset($_SESSION['popup_message']);

$todayLog = null;
if ($role === 'employee') {
    $stmt = $pdo->prepare("SELECT * FROM attendance_logs WHERE user_id = ? AND date = CURDATE()");
    $stmt->execute([$user_id]);
    $todayLog = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Payroll System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            transition: padding-left 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 200px;
            height: 100%;
            background: linear-gradient(145deg, #1e293b, #111827);
            color: white;
            transition: left 0.3s ease;
            z-index: 9999;
            padding: 20px;
            box-shadow: 3px 0 6px rgba(0, 0, 0, 0.3);
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: #f1f5f9;
            text-decoration: none;
            font-weight: 500;
            border-radius: 6px;
            margin-bottom: 8px;
            transition: background 0.2s ease;
        }

        .sidebar a:hover {
            background-color: #1f2937;
        }

        .sidebar .close-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 20px;
            cursor: pointer;
            color: #94a3b8;
            background: none;
            border: none;
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 25px;
            background: linear-gradient(135deg, #1f2937, #374151);
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .menu-toggle {
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
        }

        .left-info {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
        }

        .profile-dropdown {
            position: relative;
            cursor: pointer;
        }

        .profile-dropdown-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-dropdown img {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        .profile-dropdown:hover img {
            border-color: #38bdf8;
            transform: scale(1.05);
        }

        .profile-dropdown span {
            font-weight: 500;
            color: #f9fafb;
        }

        .dropdown-menu {
            position: absolute;
            top: 60px;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 8px;
            display: none;
            min-width: 160px;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 16px;
            text-decoration: none;
            color: #1f2937;
            font-weight: 500;
        }

        .dropdown-menu a:hover {
            background-color: #f3f4f6;
        }

        .main-content {
            max-width: 700px;
            margin: 40px auto;
            padding: 40px 20px;
            background-color: rgba(255, 255, 255, 0.88);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

       

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            color: #333;
        }

        .employee-section, .admin-section {
            text-align: center;
        }

        .clock {
            font-size: 48px;
            font-weight: bold;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: inset 0 0 8px rgba(0,0,0,0.05);
            color: #212529;
        }

        .btn-attendance {
            padding: 12px 30px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 8px;
        }

        .btn-view-logs, .btn-payroll {
            display: inline-block;
            padding: 10px 25px;
            margin: 10px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 8px;
        }

        .btn-payroll {
            background-color: #ffc107;
            color: #212529;
        }

        .admin-section ul {
            list-style: none;
            padding: 0;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 400px;
        }

        .admin-section li {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .admin-section a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .btn-attendance, .btn-view-logs, .btn-payroll {
                width: 100%;
                margin: 8px 0;
            }

            .admin-section ul {
                width: 100%;
            }

            .main-content {
                margin: 20px;
            }
        }
    </style>
</head>
<body>

<?php if ($role === 'admin'): ?>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="closeSidebar()">✖</button>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="employees.php">👥 View Users</a>
        <a href="attendance_logs.php">🕒 Attendance</a>
        <a href="positions.php">🏷️ Positions</a>
        <a href="cash_advance.php">💸 Cash Advance</a>
        <a href="overtime.php">⏱️ Overtime</a>
        <a href="deductions.php">➖ Deductions</a>
        <a href="payroll.php">📂 Payroll Management</a>
    </div>
<?php endif; ?>

<!-- Topbar -->
<div class="topbar">
    <?php if ($role === 'admin'): ?>
        <div class="menu-toggle" onclick="openSidebar()">☰</div>
    <?php endif; ?>
    <div class="left-info">🧾 My Payroll System</div>
    <div class="right-info" style="display: flex; align-items: center; gap: 10px;">
        <div class="profile-dropdown" onclick="toggleDropdown()">
            <div class="profile-dropdown-content">
                <span><?= htmlspecialchars($full_name) ?> (<?= htmlspecialchars($role) ?>)</span>
                <img src="uploads/<?= htmlspecialchars($_SESSION['user']['profile_pic'] ?? 'default-user.png') ?>" alt="Profile">
            </div>
            <div class="dropdown-menu" id="profileMenu">
                <a href="view_profile.php">👤 View Profile</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <?php if ($role === 'employee'): ?>
        <div class="employee-section">
            <div id="clock" class="clock"></div>
            <form action="attendance.php" method="POST">
                <button type="submit" class="btn-attendance">⏱️ TIME IN / TIME OUT</button>
            </form>
            <a href="my_attendance.php" class="btn-view-logs">📋My Attendance</a>
            <a href="my_payroll.php" class="btn-payroll">💵 My Payroll</a>

        </div>
    <?php elseif ($role === 'admin'): ?>
        <div class="admin-section">
            <ul>
                <li><a href="employees.php">👥 Manage Employees</a></li>
                <li><a href="positions.php">📌 Manage Positions</a></li>
                <li><a href="attendance_logs.php">🕒 View Attendance Logs</a></li>
                <li><a href="payroll.php">💰 Payroll Management</a></li>
            </ul>
        </div>
    <?php else: ?>
        <p style="text-align:center;">You do not have access to this dashboard.</p>
    <?php endif; ?>
</div>

<script>
    function updateClock() {
        const clock = document.getElementById('clock');
        if (clock) {
            const now = new Date();
            clock.textContent = now.toLocaleTimeString();
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    function openSidebar() {
        document.getElementById("sidebar").classList.add("show");
        document.body.classList.add("sidebar-open");
        localStorage.setItem("sidebar", "open");
    }

    function closeSidebar() {
        document.getElementById("sidebar").classList.remove("show");
        document.body.classList.remove("sidebar-open");
        localStorage.setItem("sidebar", "closed");
    }

    function toggleDropdown() {
        const menu = document.getElementById("profileMenu");
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    }

    window.onclick = function(e) {
        if (!e.target.closest('.profile-dropdown')) {
            document.getElementById("profileMenu").style.display = "none";
        }
    };

    window.onload = function () {
        const state = localStorage.getItem("sidebar");
        const sidebar = document.getElementById("sidebar");
        if (sidebar && state === "open") {
            sidebar.classList.add("show");
            document.body.classList.add("sidebar-open");
        }
    };
</script>

</body>
</html>
