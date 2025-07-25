<?php
session_start();
require 'config.php';

if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $stmt = $pdo->prepare("INSERT INTO deductions (description, amount) VALUES (?, ?)");
    $stmt->execute([$description, $amount]);

    header("Location: deductions.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Deduction</title>
</head>
<body>
    <h2>➕ Add Deduction</h2>
    <form method="POST">
        <label>Description:</label><br>
        <input type="text" name="description" required><br><br>

        <label>Amount (₱):</label><br>
        <input type="number" step="0.01" name="amount" required><br><br>

        <button type="submit">Save</button>
        <a href="deductions.php">Cancel</a>
    </form>
</body>
</html>
