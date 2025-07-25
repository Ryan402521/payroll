<?php
require 'config.php';

$id = $_GET['id'];

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Redirect to employees.php after delete
header("Location: /payroll/employees.php");  // Adjust path if needed
exit;
