<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'] ?? '';
    $date     = $_POST['date'] ?? '';
    $time_in  = $_POST['time_in'] ?? '';
    $time_out = $_POST['time_out'] ?? '';

    // Basic validation
    if ($id && $date && $time_in && $time_out) {
        $stmt = $pdo->prepare("UPDATE attendance_logs SET date = ?, time_in = ?, time_out = ? WHERE id = ?");
        $stmt->execute([$date, $time_in, $time_out, $id]);
    }
}

header("Location: attendance_logs.php");
exit;
