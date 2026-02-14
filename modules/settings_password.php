<?php
require_once __DIR__ . '/../config/db.php';

$user_id = 1; // placeholder until login system

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
    $stmt->execute([$new, $user_id]);

    echo "<p>Password updated.</p>";
}
?>

<h2>Change Password</h2>

<form method="POST">
    <label>New Password</label>
    <input type="password" name="new_password" required>

    <button type="submit">Update Password</button>
</form>
