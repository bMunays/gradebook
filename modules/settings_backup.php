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
    set_setting('backup_frequency', $_POST['backup_frequency'], $pdo);
    set_setting('backup_format', $_POST['backup_format'], $pdo);
    echo "<p>Backup settings saved.</p>";
}

$freq = get_setting('backup_frequency', $pdo) ?: 'manual';
$format = get_setting('backup_format', $pdo) ?: 'csv';
?>

<h2>Backup & Export Settings</h2>

<form method="POST">
    <label>Backup Frequency</label>
    <select name="backup_frequency">
        <option value="manual" <?= $freq=='manual'?'selected':'' ?>>Manual</option>
        <option value="weekly" <?= $freq=='weekly'?'selected':'' ?>>Weekly</option>
        <option value="monthly" <?= $freq=='monthly'?'selected':'' ?>>Monthly</option>
    </select>

    <label>Default Export Format</label>
    <select name="backup_format">
        <option value="csv" <?= $format=='csv'?'selected':'' ?>>CSV</option>
        <option value="xlsx" <?= $format=='xlsx'?'selected':'' ?>>Excel (XLSX)</option>
        <option value="json" <?= $format=='json'?'selected':'' ?>>JSON</option>
    </select>

    <button type="submit">Save</button>
</form>
