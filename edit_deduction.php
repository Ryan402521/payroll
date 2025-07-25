<?php
session_start();
require 'config.php';

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied.");
}

// For AJAX fetch request to load data for editing
if (isset($_GET['action']) && $_GET['action'] === 'fetch' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM deductions WHERE id = ?");
    $stmt->execute([$id]);
    $deduction = $stmt->fetch();

    header('Content-Type: application/json');
    if ($deduction) {
        echo json_encode($deduction);
    } else {
        echo json_encode(['error' => 'Not found']);
    }
    exit;
}

// Handle form submission for add or edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $amount = $_POST['amount'] ?? 0;

    if ($action === 'add') {
        if ($description === '' || !is_numeric($amount)) {
            $_SESSION['error'] = "Invalid input.";
            header("Location: deductions.php");
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO deductions (description, amount) VALUES (?, ?)");
        $stmt->execute([$description, $amount]);
        $_SESSION['success'] = "Deduction added.";
        header("Location: deductions.php");
        exit;
    }

    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || $description === '' || !is_numeric($amount)) {
            $_SESSION['error'] = "Invalid input.";
            header("Location: deductions.php");
            exit;
        }
        $stmt = $pdo->prepare("UPDATE deductions SET description = ?, amount = ? WHERE id = ?");
        $stmt->execute([$description, $amount, $id]);
        $_SESSION['success'] = "Deduction updated.";
        header("Location: deductions.php");
        exit;
    }

    // Invalid action
    $_SESSION['error'] = "Invalid action.";
    header("Location: deductions.php");
    exit;
}

header("Location: deductions.php");
exit;
