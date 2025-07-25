<?php
require 'config.php';

// Check if POST and required fields exist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $ot_date = $_POST['ot_date'] ?? null;
    $hours = $_POST['hours'] ?? null;
    $rate = $_POST['rate'] ?? null;

    // Basic validation (you can expand this)
    if (!$id || !$user_id || !$ot_date || !$hours || !$rate) {
        die('Missing required fields.');
    }

    // Prepare and execute update statement
    $stmt = $pdo->prepare("UPDATE overtime SET user_id = ?, ot_date = ?, hours = ?, rate = ? WHERE id = ?");
    $stmt->execute([$user_id, $ot_date, $hours, $rate, $id]);

    // Redirect back to the main page
    header('Location: overtime.php');
    exit;
} else {
    die('Invalid request method.');
}
