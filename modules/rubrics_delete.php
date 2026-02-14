<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$pdo->prepare("DELETE FROM rubric_criteria WHERE rubric_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM rubrics WHERE id = ?")->execute([$id]);

header("Location: index.php?page=rubrics");
exit;
