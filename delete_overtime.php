<?php
require 'config.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM overtime WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: overtime.php");
exit;
