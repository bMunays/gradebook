<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT class_id FROM assessments WHERE id = ?");
$stmt->execute([$id]);
$class_id = $stmt->fetchColumn();

$pdo->prepare("DELETE FROM assessments WHERE id = ?")->execute([$id]);

header("Location: index.php?page=assessments&class_id=$class_id");
exit;
