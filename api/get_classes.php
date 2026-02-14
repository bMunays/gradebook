<?php
require_once __DIR__ . '/auth.php';

$token = $_GET['token'] ?? '';

$user_id = validate_token($token, $pdo);
if (!$user_id) json_response("error", "Invalid token");

$stmt = $pdo->query("SELECT * FROM classes ORDER BY level, name");
$classes = $stmt->fetchAll();

json_response("success", $classes);
