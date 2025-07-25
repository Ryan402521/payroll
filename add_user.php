<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // Sanitize inputs
    $full_name   = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $role        = $_POST['role'];
    $position_id = $_POST['position_id'];
    $password    = $_POST['password'];
    $profile_pic = 'default-user.png'; // Default fallback image

    // Check email uniqueness
    $check_email = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $check_email->execute([$email]);
    if ($check_email->fetchColumn() > 0) {
        $errors[] = "Email already exists.";
    }

    // Optional: Validate profile picture extension before upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_extensions)) {
            $errors[] = "Invalid profile picture format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (empty($errors)) {
        // Handle profile picture upload
        if (!empty($_FILES['profile_pic']['name'])) {
            $file_name = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
            $target_path = 'uploads/' . $file_name;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                $profile_pic = $file_name;
            }
        }

        // Generate unique ID
        function generateRandomId($length = 6) {
            return 'DCE' . strtoupper(substr(bin2hex(random_bytes(8)), 0, $length));
        }

        do {
            $id = generateRandomId();
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $check->execute([$id]);
        } while ($check->fetchColumn() > 0);

        // Hash password securely
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (id, full_name, email, password, role, position_id, profile_pic)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $full_name, $email, $hashed, $role, $position_id, $profile_pic]);

        $_SESSION['popup_message'] = "User added successfully!";
        header("Location: employees.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
        }
        .main-content {
            max-width: 600px;
            margin: 100px auto 40px !important;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.6); /* semi-transparent white */
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            position: relative;
            z-index: 1;
        }
        h2 {
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            color: #333;
        }
        a.back-btn {
            display: inline-block;
            margin-bottom: 25px;
            color: #fff;
            background-color: #495057; /* solid dark gray */
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
            border: none;
        }
        a.back-btn:hover {
            background-color: #343a40; /* darker on hover */
            color: #fff;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-weight: bold;
            border-radius: 6px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        #preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .main-content {
                margin: 80px 15px 40px !important;
            }
        }
        /* Prevent sidebar from shifting form */
        body.sidebar-open .main-content {
            margin-left: auto !important;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="main-content">
    <h2>➕ Add User</h2>

    <a href="employees.php" class="back-btn">← Back to Users</a>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="full_name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email Address" required />
        <input type="password" name="password" placeholder="Password" required />

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="employee">Employee</option>
        </select>

        <select name="position_id" required>
            <option value="">Select Position</option>
            <?php
            $positions = $pdo->query("SELECT id, title FROM positions")->fetchAll();
            foreach ($positions as $pos) {
                echo "<option value='" . htmlspecialchars($pos['id']) . "'>" . htmlspecialchars($pos['title']) . "</option>";
            }
            ?>
        </select>

        <input type="file" name="profile_pic" accept="image/*" onchange="previewImage(event)" />
        <img id="preview" src="uploads/default-user.png" alt="Preview" onerror="this.onerror=null;this.src='uploads/default-user.png';" />

        <button class="btn btn-primary" type="submit" name="add_user">Add User</button>
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
