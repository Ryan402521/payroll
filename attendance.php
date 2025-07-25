<?php
session_start();
require 'config.php';

$user_id = $_SESSION['user']['id'] ?? null;

if (!$user_id) {
    die("Unauthorized.");
}

$date = date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $date]);
$log = $stmt->fetch();

if (!$log) {
    // Time In
    $stmt = $pdo->prepare("INSERT INTO attendance_logs (user_id, date, time_in) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $date]);
    $_SESSION['popup_message'] = "🟢 Time In recorded successfully!";
} elseif (!$log['time_out']) {
    // Time Out
    $stmt = $pdo->prepare("UPDATE attendance_logs SET time_out = NOW() WHERE id = ?");
    $stmt->execute([$log['id']]);
    $_SESSION['popup_message'] = "🔴 Time Out recorded successfully!";
} else {
    $_SESSION['popup_message'] = "✅ You already timed in and out today.";
}

header("Location: dashboard.php");
exit;
?>
