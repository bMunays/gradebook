<?php
require_once __DIR__ . '/auth.php';

$token = $_GET['token'] ?? '';
$assessment_id = $_GET['assessment_id'] ?? null;

$user_id = validate_token($token, $pdo);
if (!$user_id) json_response("error", "Invalid token");

$stmt = $pdo->prepare("
    SELECT student_id, mark
    FROM marks
    WHERE assessment_id = ?
");
$stmt->execute([$assessment_id]);

json_response("success", $stmt->fetchAll());
