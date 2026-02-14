<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        INSERT INTO plans (class_id, week_start, week_end, topic, objectives, activities, assessment, homework, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $class_id,
        $_POST['week_start'],
        $_POST['week_end'],
        $_POST['topic'],
        $_POST['objectives'],
        $_POST['activities'],
        $_POST['assessment'],
        $_POST['homework'],
        $_POST['notes']
    ]);

    header("Location: index.php?page=planning&class_id=$class_id");
    exit;
}
?>

<h2>Add Weekly Plan</h2>

<form method="POST">
    <label>Week Start</label>
    <input type="date" name="week_start" required>

    <label>Week End</label>
    <input type="date" name="week_end" required>

    <label>Topic</label>
    <input type="text" name="topic">

    <label>Objectives</label>
    <textarea name="objectives"></textarea>

    <label>Activities</label>
    <textarea name="activities"></textarea>

    <label>Assessment</label>
    <textarea name="assessment"></textarea>

    <label>Homework</label>
    <textarea name="homework"></textarea>

    <label>Notes</label>
    <textarea name="notes"></textarea>

    <button type="submit">Save</button>
</form>
