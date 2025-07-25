<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = $_POST['id'];
    $full_name  = $_POST['full_name'];
    $email      = $_POST['email'];
    $role       = $_POST['role'];
    $position_id = $_POST['position_id'] ?? null;

    // Handle file upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = 'uploads/';
        $filename = basename($_FILES['profile_pic']['name']);
        $targetPath = $uploadDir . time() . '_' . $filename;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
            // Update with profile image
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, role=?, position_id=?, profile_pic=? WHERE id=?");
            $stmt->execute([$full_name, $email, $role, $position_id, basename($targetPath), $id]);
        }
    } else {
        // Update without changing image
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, role=?, position_id=? WHERE id=?");
        $stmt->execute([$full_name, $email, $role, $position_id, $id]);
    }

    // Refresh session if the logged-in user updated their own profile
    if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $id) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($updatedUser) {
            $_SESSION['user'] = $updatedUser;
        }
    }

    header("Location: employees.php");
    exit;
}
?>
