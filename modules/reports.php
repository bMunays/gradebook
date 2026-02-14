<?php
// modules/reports.php
// modules/report_student.php
// modules/report_class.php
// modules/report_term.php


require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
?>

<h2>Reports</h2>

<h3>Generate Student Report</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="report_student">
    <label>Select Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Student ID</label>
    <input type="number" name="student_id" required>

    <label>Term</label>
    <input type="number" name="term" value="1">

    <button type="submit">Generate</button>
</form>

<hr>

<h3>Generate Class Report</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="report_class">
    <label>Select Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Term</label>
    <input type="number" name="term" value="1">

    <button type="submit">Generate</button>
</form>

<hr>

<h3>Generate Term Summary</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="report_term">
    <label>Select Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Term</label>
    <input type="number" name="term" value="1">

    <button type="submit">Generate</button>
</form>
