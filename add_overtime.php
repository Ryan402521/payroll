<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $ot_date = $_POST['ot_date'];
    $hours = $_POST['hours'];
    $rate = $_POST['rate'];

    $stmt = $pdo->prepare("INSERT INTO overtime (user_id, ot_date, hours, rate) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $ot_date, $hours, $rate]);

    header("Location: overtime.php");
    exit;
}

$users = $pdo->query("SELECT id, full_name FROM users")->fetchAll();
?>

<?php include 'includes/header.php'; ?>
<div class="main-content">
    <h2>➕ Add Overtime</h2>
    <form method="POST">
        <div class="form-group">
            <label>Employee:</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Select Employee --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']) ?>">
                        <?= htmlspecialchars($user['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date:</label>
            <input type="date" name="ot_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Hours:</label>
            <input type="number" name="hours" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Rate:</label>
            <input type="number" name="rate" class="form-control" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success">✅ Save</button>
        <a href="overtime.php" class="btn btn-secondary">⬅ Cancel</a>
    </form>
</div>
