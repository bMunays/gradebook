<?php
require_once __DIR__ . '/auth.php';

$token = $_GET['token'] ?? '';
$class_id = $_GET['class_id'] ?? null;

$user_id = validate_token($token, $pdo);
if (!$user_id) json_response("error", "Invalid token");

$stmt = $pdo->prepare("
    SELECT r.student_id, r.status, s.session_date
    FROM attendance_records r
    JOIN attendance_sessions s ON r.session_id = s.id
    WHERE s.class_id = ?
    ORDER BY s.session_date DESC
");
$stmt->execute([$class_id]);

json_response("success", $stmt->fetchAll());
