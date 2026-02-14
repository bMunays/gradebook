<?php
require_once __DIR__ . '/auth.php';

$token = $_POST['token'] ?? '';
$class_id = $_POST['class_id'] ?? null;
$session_date = $_POST['session_date'] ?? date('Y-m-d');
$period = $_POST['period'] ?? '';
$records = json_decode($_POST['records'], true);

$user_id = validate_token($token, $pdo);
if (!$user_id) json_response("error", "Invalid token");

// Create session
$stmt = $pdo->prepare("
    INSERT INTO attendance_sessions (class_id, session_date, period)
    VALUES (?, ?, ?)
");
$stmt->execute([$class_id, $session_date, $period]);

$session_id = $pdo->lastInsertId();

// Insert records
foreach ($records as $student_id => $status) {
    $stmt = $pdo->prepare("
        INSERT INTO attendance_records (session_id, student_id, status)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$session_id, $student_id, $status]);
}

json_response("success", "Attendance saved");
