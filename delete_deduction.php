<?php
session_start();
require 'config.php';

if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied.");
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM deductions WHERE id = ?");
$stmt->execute([$id]);

header("Location: deductions.php");
exit;
