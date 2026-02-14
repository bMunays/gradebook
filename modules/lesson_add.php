<?php
require_once __DIR__ . '/../config/db.php';

$plan_id = $_GET['plan_id'];
$class_id = $_GET['class_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        INSERT INTO lessons (class_id, plan_id, lesson_date, period, topic, objectives, activities, resources, homework)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $class_id,
        $plan_id,
        $_POST['lesson_date'],
        $_POST['period'],
        $_POST['topic'],
        $_POST['objectives'],
        $_POST['activities'],
        $_POST['resources'],
        $_POST['homework']
    ]);

    header("Location: index.php?page=planning_view&id=$plan_id");
    exit;
}
?>

<h2>Add Lesson</h2>

<form method="POST">
    <label>Date</label>
    <input type="date" name="lesson_date" required>

    <label>Period</label>
    <input type="text" name="period">

    <label>Topic</label>
    <input type="text" name="topic">

    <label>Objectives</label>
    <textarea name="objectives"></textarea>

    <label>Activities</label>
    <textarea name="activities"></textarea>

    <label>Resources</label>
    <textarea name="resources"></textarea>

    <label>Homework</label>
    <textarea name="homework"></textarea>

    <button type="submit">Save</button>
</form>
