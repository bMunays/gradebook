<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);

header("Location: index.php?page=students");
exit;
