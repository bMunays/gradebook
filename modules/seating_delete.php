<?php
require_once __DIR__ . '/../config/db.php';

$plan_id = $_GET['id'] ?? null;

if ($plan_id) {
    $pdo->prepare("DELETE FROM seating_plans WHERE id = ?")->execute([$plan_id]);
}

header("Location: index.php?page=seating");
exit;
