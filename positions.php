<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Add Position
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_position'])) {
    $title = trim($_POST['title']);
    $rate = floatval($_POST['rate']);
    $stmt = $pdo->prepare("INSERT INTO positions (title, rate_per_hour) VALUES (?, ?)");
    $stmt->execute([$title, $rate]);
    header("Location: positions.php");
    exit;
}

// Edit Position
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_position'])) {
    $title = trim($_POST['title']);
    $rate = floatval($_POST['rate']);
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("UPDATE positions SET title = ?, rate_per_hour = ? WHERE id = ?");
    $stmt->execute([$title, $rate, $id]);
    header("Location: positions.php");
    exit;
}

$positions = $pdo->query("SELECT * FROM positions")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Positions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: url('uploads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            color: #000;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-open .main-content {
            margin-left: 270px;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #fff;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
        }

        .btn {
            margin-right: 5px;
        }

        .inline-form input {
            display: inline-block;
            width: auto;
            margin-right: 5px;
        }

        .inline-form .form-control-sm {
            height: 30px;
            font-size: 0.875rem;
        }

        label {
            color: #fff;
        }

        input.form-control, input.form-control-sm {
            background-color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="main-content">
    <h2>Positions</h2>

    <!-- ADD Position Form -->
    <form method="post" class="mb-4 d-flex gap-2 align-items-end">
        <input type="hidden" name="add_position" value="1">
        <div>
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div>
            <label>Rate per Hour:</label>
            <input type="number" step="0.01" name="rate" class="form-control" required>
        </div>
        <button class="btn btn-primary" type="submit">➕ Add</button>
    </form>

    <!-- Positions Table -->
    <table class="table table-bordered align-middle bg-light bg-opacity-75">
        <thead>
            <tr><th>ID</th><th>Title</th><th>Rate</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($positions as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <form method="post" class="inline-form">
                        <input type="hidden" name="edit_position" value="1">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($p['title']) ?>" class="form-control form-control-sm" required>
                </td>
                <td>
                        <input type="number" step="0.01" name="rate" value="<?= $p['rate_per_hour'] ?>" class="form-control form-control-sm" required>
                </td>
                <td>
                        <button class="btn btn-sm btn-success" type="submit">💾 Save</button>
                        <a class="btn btn-sm btn-danger" href="delete_position.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this position?')">🗑️ Delete</a>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
