<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];
$full_name = $user['full_name'];
$email = $user['email'];
$profile_pic = $user['profile_pic'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['full_name'];
    $new_email = $_POST['email'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $upload_dir = 'uploads/';
        $filename = time() . '_' . basename($_FILES['profile_pic']['name']);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
            $profile_pic = $filename;

            // Optional: delete old picture if not default
            if ($user['profile_pic'] !== 'default-user.png' && file_exists("uploads/" . $user['profile_pic'])) {
                unlink("uploads/" . $user['profile_pic']);
            }
        } else {
            $error = 'Failed to upload profile picture.';
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_pic = ? WHERE id = ?");
        if ($stmt->execute([$new_name, $new_email, $profile_pic, $user_id])) {
            $_SESSION['user']['full_name'] = $new_name;
            $_SESSION['user']['email'] = $new_email;
            $_SESSION['user']['profile_pic'] = $profile_pic;
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        img.profile-preview {
            display: block;
            margin: 10px auto;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #ccc;
        }

        .btn {
            display: inline-block;
            background-color: #2d3e50;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #1f2a38;
        }

        .status {
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .status.success {
            color: green;
        }

        .status.error {
            color: red;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #2d3e50;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit My Profile</h2>

    <?php if ($success): ?>
        <div class="status success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="status error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($full_name) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="form-group">
            <label for="profile_pic">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
            <img class="profile-preview" src="uploads/<?= htmlspecialchars($profile_pic) ?>" alt="Current Picture">
        </div>

        <button type="submit" class="btn">💾 Save Changes</button>
    </form>

    <a class="back-link" href="view_profile.php">← Back to Profile</a>
</div>

</body>
</html>
