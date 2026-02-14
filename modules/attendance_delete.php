<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

// Delete records first
$pdo->prepare("DELETE FROM attendance_records WHERE session_id = ?")->execute([$id]);

// Delete session
$pdo->prepare("DELETE FROM attendance_sessions WHERE id = ?")->execute([$id]);

header("Location: index.php?page=attendance");
exit;
