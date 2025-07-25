<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name   = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $role        = $_POST['role'] ?? 'employee';  // Added role input
    $profile_pic = 'default-user.png';

    $allowed_roles = ['employee', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        $errors[] = "Invalid role selected.";
    }

    // Check email uniqueness
    $check_email = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $check_email->execute([$email]);
    if ($check_email->fetchColumn() > 0) {
        $errors[] = "Email is already registered.";
    }

    // Validate uploaded image
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (empty($errors)) {
        if (!empty($_FILES['profile_pic']['name'])) {
            $file_name = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
            $target_path = 'uploads/' . $file_name;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                $profile_pic = $file_name;
            }
        }

        // Generate unique ID
        function generateRandomId($length = 6) {
            return 'USR' . strtoupper(substr(bin2hex(random_bytes(8)), 0, $length));
        }

        do {
            $id = generateRandomId();
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $check->execute([$id]);
        } while ($check->fetchColumn() > 0);

        // Hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert with dynamic role
        $stmt = $pdo->prepare("INSERT INTO users (id, full_name, email, password, role, position_id, profile_pic)
                               VALUES (?, ?, ?, ?, ?, NULL, ?)");
        $stmt->execute([$id, $full_name, $email, $hashed, $role, $profile_pic]);

        $_SESSION['popup_message'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
        }
        .main-content {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        input, select {
            margin-bottom: 15px;
        }
        #preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h2>👤 Register</h2>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="full_name" class="form-control" placeholder="Full Name" required />
        <input type="email" name="email" class="form-control" placeholder="Email Address" required />
        <input type="password" name="password" class="form-control" placeholder="Password" required />

        <select name="role" class="form-control" required>
            <option value="" disabled selected>Select Role</option>
            <option value="employee">Employee</option>
            <option value="admin">Admin</option>
        </select>

        <input type="file" name="profile_pic" class="form-control" accept="image/*" onchange="previewImage(event)" />
        <img id="preview" src="uploads/default-user.png" alt="Preview" onerror="this.onerror=null;this.src='uploads/default-user.png';" />

        <button type="submit" name="register" class="btn btn-success w-100">Register</button>
        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const output = document.getElementById('preview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
