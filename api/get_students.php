<?php
require_once __DIR__ . '/auth.php';

$token = $_GET['token'] ?? '';
$class_id = $_GET['class_id'] ?? null;

$user_id = validate_token($token, $pdo);
if (!$user_id) json_response("error", "Invalid token");

$stmt = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname
");
$stmt->execute([$class_id]);

json_response("success", $stmt->fetchAll());
