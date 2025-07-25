<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .login-card {
            background-color: rgba(255, 255, 255, 0.1); /* transparent glass look */
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px); /* frosted effect */
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: white;
        }
        .container {
            margin-top: 10%;
        }
        label, h4, .alert, .form-control {
            color: white;
        }
        .form-control::placeholder {
            color: rgba(255,255,255,0.7);
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .btn-custom {
            background-color: white;
            color: black;
            font-weight: bold;
        }
        .btn-custom:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container col-md-4">
        <div class="login-card">
            <h4 class="text-center mb-4">Login</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger bg-danger text-white"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-custom w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
