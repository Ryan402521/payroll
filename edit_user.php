<?php
require 'config.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];

    $profile_pic = $user['profile_pic'];

    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $filename;
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, profile_pic = ? WHERE id = ?");
    $stmt->execute([$name, $email, $role, $profile_pic, $id]);

    header("Location: employees.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit User</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 320px;
        height: 320px;
        background: #fff;
        margin: 40px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        overflow-y: auto;
        box-sizing: border-box;
    }
    h2 {
        font-size: 1.2rem;
        margin-bottom: 15px;
        text-align: center;
        color: #333;
    }
    label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #555;
    }
    input[type="text"],
    input[type="email"],
    select,
    input[type="file"] {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 0.9rem;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    select:focus,
    input[type="file"]:focus {
        outline: none;
        border-color: #007bff;
    }
    img.profile-pic {
        display: block;
        margin: 10px auto 20px auto;
        border-radius: 50%;
        width: 90px;
        height: 90px;
        object-fit: cover;
        border: 3px solid #007bff;
        box-shadow: 0 4px 10px rgba(0,123,255,0.3);
    }
    button {
        width: 100%;
        padding: 10px;
        font-size: 1rem;
        background-color: #007bff;
        border: none;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        font-weight: 700;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #0056b3;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Full Name:</label>
        <input id="name" type="text" name="name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

        <label for="email">Email:</label>
        <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
        </select>

        <label>Profile Picture:</label>
        <img class="profile-pic" src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture">

        <input type="file" name="profile_pic" accept="image/*">

        <button type="submit">Save</button>
    </form>
</div>

</body>
</html>
