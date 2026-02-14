<?php
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "<p>No user logged in.</p>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';

    $pdo->prepare("
        UPDATE users
        SET full_name = ?, email = ?
        WHERE id = ?
    ")->execute([
        $full_name,
        $email,
        $user_id
    ]);

    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    echo "<p>Profile updated.</p>";
}

$full_name_val = isset($user['full_name']) ? (string)$user['full_name'] : '';
$email_val = isset($user['email']) ? (string)$user['email'] : '';
?>

<h2>User Profile</h2>

<form method="POST">
    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($full_name_val, ENT_QUOTES, 'UTF-8') ?>">

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email_val, ENT_QUOTES, 'UTF-8') ?>">

    <button type="submit">Save Profile</button>
</form>
