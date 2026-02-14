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
    set_setting('term_start', $_POST['term_start'], $pdo);
    set_setting('term_end', $_POST['term_end'], $pdo);
    set_setting('holidays', $_POST['holidays'], $pdo);
    echo "<p>Academic settings saved.</p>";
}

$term_start = get_setting('term_start', $pdo);
$term_end = get_setting('term_end', $pdo);
$holidays = get_setting('holidays', $pdo);
?>

<h2>Academic Settings</h2>

<form method="POST">
    <label>Term Start Date</label>
    <input type="date" name="term_start" value="<?= $term_start ?>">

    <label>Term End Date</label>
    <input type="date" name="term_end" value="<?= $term_end ?>">

    <label>Holidays (comma-separated dates)</label>
    <input type="text" name="holidays" value="<?= htmlspecialchars($holidays) ?>">

    <button type="submit">Save</button>
</form>
