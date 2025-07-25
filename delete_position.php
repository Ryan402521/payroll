<?php
require 'config.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM positions WHERE id = ?");
$stmt->execute([$id]);
header("Location: positions.php");
