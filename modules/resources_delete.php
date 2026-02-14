<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT file_path FROM resources WHERE id = ?");
$stmt->execute([$id]);
$file = $stmt->fetchColumn();

if ($file && file_exists($file)) {
    unlink($file);
}

$pdo->prepare("DELETE FROM resources WHERE id = ?")->execute([$id]);

header("Location: index.php?page=resources");
exit;
