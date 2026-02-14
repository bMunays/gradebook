<?php
require_once __DIR__ . '/../config/db.php';

function get_setting($key, $pdo) {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key=?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn();
}

function set_setting($key, $value, $pdo) {
    $stmt = $pdo->prepare("
        INSERT INTO settings (setting_key, setting_value)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)
    ");
    $stmt->execute([$key, $value]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_setting('name_format', $_POST['name_format'], $pdo);
    set_setting('language', $_POST['language'], $pdo);
    echo "<p>Display settings saved.</p>";
}

$name_format = get_setting('name_format', $pdo) ?: 'surname_first';
$language = get_setting('language', $pdo) ?: 'en';
?>

<h2>Display Preferences</h2>

<form method="POST">
    <label>Name Format</label>
    <select name="name_format">
        <option value="surname_first" <?= $name_format=='surname_first'?'selected':'' ?>>Surname, First Name</option>
        <option value="first_surname" <?= $name_format=='first_surname'?'selected':'' ?>>First Name Surname</option>
    </select>

    <label>Language</label>
    <select name="language">
        <option value="en" <?= $language=='en'?'selected':'' ?>>English</option>
        <option value="sn" <?= $language=='sn'?'selected':'' ?>>Shona</option>
    </select>

    <button type="submit">Save</button>
</form>
