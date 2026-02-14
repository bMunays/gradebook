<?php
require_once __DIR__ . '/auth.php';

$token = $_POST['token'] ?? '';
$assessment_id = $_POST['assessment_id'] ?? null;
$marks = json_decode($_POST['marks'], true);

$user_id = validate_token($token, $pdo);
if (!$user_id) json_response("error", "Invalid token");

foreach ($marks as $student_id => $mark) {
    $stmt = $pdo->prepare("
        INSERT INTO marks (assessment_id, student_id, mark)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE mark = VALUES(mark)
    ");
    $stmt->execute([$assessment_id, $student_id, $mark]);
}

json_response("success", "Marks saved");
