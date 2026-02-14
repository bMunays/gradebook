<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?page=classes");
exit;
