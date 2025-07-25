<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id']) || empty($_POST['ca_date']) || !isset($_POST['amount'])) {
        die('Invalid input');
    }

    $id = $_POST['id'];
    $ca_date = $_POST['ca_date'];
    $amount = $_POST['amount'];

    // Update only ca_date and amount; user_id remains unchanged
    $stmt = $pdo->prepare("UPDATE cash_advance SET ca_date = ?, amount = ? WHERE id = ?");
    $stmt->execute([$ca_date, $amount, $id]);

    header("Location: cash_advance.php");
    exit;
}
