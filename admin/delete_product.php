<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: dashboard.php");
exit;