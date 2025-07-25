<?php
require 'config.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM cash_advance WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: cash_advance.php");
exit;
