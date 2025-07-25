<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $ca_date = $_POST['ca_date'];
    $amount = $_POST['amount'];

    $stmt = $pdo->prepare("INSERT INTO cash_advance (user_id, ca_date, amount) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $ca_date, $amount]);

    header("Location: cash_advance.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
include 'includes/header.php';
?>

<div class="main-content">
    <h2>➕ New Cash Advance</h2>
    <form method="POST">
        <label>Date:</label>
        <input type="date" name="ca_date" required class="form-control mb-2">

        <label>Employee:</label>
        <select name="user_id" class="form-control mb-2" required>
            <option value="">Select employee</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>"><?= $u['id'] ?> - <?= htmlspecialchars($u['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Amount:</label>
        <input type="number" name="amount" step="0.01" required class="form-control mb-2">

        <button type="submit" class="btn btn-success">💾 Save</button>
        <a href="cash_advance.php" class="btn btn-secondary">Back</a>
    </form>
</div>
