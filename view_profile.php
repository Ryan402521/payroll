<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$full_name   = htmlspecialchars($user['full_name']);
$email       = htmlspecialchars($user['email'] ?? 'Not set');
$role        = htmlspecialchars($user['role']);
$profile_pic = htmlspecialchars($user['profile_pic'] ?? 'default-user.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 500px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.95); /* Light background with transparency */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
            margin-bottom: 20px;
        }

        .profile-container h2 {
            margin-bottom: 5px;
            font-size: 24px;
            color: #333;
        }

        .profile-container p {
            margin: 5px 0;
            color: #666;
        }

        .button-group {
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            margin: 5px;
            padding: 10px 18px;
            background-color: #2d3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background-color: #1f2a38;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <img src="uploads/<?= $profile_pic ?>" alt="Profile Picture">
    <h2><?= $full_name ?></h2>
    <p><strong>Email:</strong> <?= $email ?></p>
    <p><strong>Role:</strong> <?= $role ?></p>

    <div class="button-group">
        <a href="edit_profile.php" class="btn">✏️ Edit My Profile</a>
        <a href="dashboard.php" class="btn">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
