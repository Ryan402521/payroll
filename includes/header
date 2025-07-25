<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$full_name   = $_SESSION['user']['full_name'] ?? 'Unknown User';
$role        = $_SESSION['user']['role'] ?? 'guest';
$profile_pic = $_SESSION['user']['profile_pic'] ?? 'default-user.png';
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
            background-color: #f4f6f9;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -240px !important;  /* push further left to hide */
            width: 200px;
            height: 100%;
            background: linear-gradient(145deg, #1e293b, #111827);
            color: white;
            overflow-x: hidden;       /* prevent horizontal overflow */
            transition: left 0.3s ease, transform 0.3s ease;
            transform: translateX(-40px);
            z-index: 9999;
            padding: 20px 15px;
            box-shadow: 3px 0 6px rgba(0, 0, 0, 0.3);
        }

        .sidebar.show {
            left: 0 !important;
            transform: translateX(0) !important;
        }

        body.sidebar-open .sidebar {
            left: 0 !important;
        }

        body.sidebar-open {
            overflow-x: hidden; /* prevent horizontal scroll when sidebar open */
        }

        .sidebar a {
            display: block;
            padding: 10px 12px;
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
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
            transition: border 0.3s ease, transform 0.3s ease;
        }

        .profile-dropdown:hover img {
            border: 2px solid #38bdf8;
            transform: scale(1.05);
        }

        .profile-dropdown span {
            font-weight: 500;
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
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-open .main-content {
            margin-left: 200px;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -100%;
                transform: translateX(0);
            }

            .sidebar.show {
                left: 0 !important;
            }

            body.sidebar-open .main-content {
                margin-left: 0;
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
                <img src="uploads/<?= htmlspecialchars($profile_pic) ?>" alt="Profile">
            </div>
            <div class="dropdown-menu" id="profileMenu">
                <a href="view_profile.php">👤 View Profile</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
    function openSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.add("show");
        document.body.classList.add("sidebar-open");
        localStorage.setItem("sidebar", "open");
    }

    function closeSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.remove("show");
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
        if (!sidebar) return;

        if (state === "open") {
            sidebar.classList.add("show");
            document.body.classList.add("sidebar-open");
        } else {
            sidebar.classList.remove("show");
            document.body.classList.remove("sidebar-open");
        }
    };
</script>
