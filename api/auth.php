<?php
require_once __DIR__ . '/../config/db.php';

function generate_token() {
    return bin2hex(random_bytes(32));
}

function validate_token($token, $pdo) {
    $stmt = $pdo->prepare("SELECT user_id FROM api_tokens WHERE token = ?");
    $stmt->execute([$token]);
    return $stmt->fetchColumn();
}

function json_response($status, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => $status,
        "data" => $data
    ]);
    exit;
}
