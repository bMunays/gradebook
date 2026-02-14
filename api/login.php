<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {

    $token = generate_token();

    $stmt = $pdo->prepare("INSERT INTO api_tokens (user_id, token) VALUES (?, ?)");
    $stmt->execute([$user['id'], $token]);

    json_response("success", [
        "token" => $token,
        "user_id" => $user['id'],
        "full_name" => $user['full_name'],
        "role" => $user['role']
    ]);
}

json_response("error", "Invalid username or password");
