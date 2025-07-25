<?php
require 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid ID.");
}

// Delete the attendance log
$stmt = $pdo->prepare("DELETE FROM attendance_logs WHERE id = ?");
$stmt->execute([$id]);

header("Location: attendance_logs.php");
exit;
