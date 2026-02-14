<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS class_name
    FROM plans p
    JOIN classes c ON p.class_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$plan = $stmt->fetch();

$lessons = $pdo->prepare("SELECT * FROM lessons WHERE plan_id = ? ORDER BY lesson_date, period");
$lessons->execute([$id]);
?>

<h2>Weekly Plan: <?= htmlspecialchars($plan['class_name']) ?></h2>
<p><strong>Week:</strong> <?= $plan['week_start'] ?> ? <?= $plan['week_end'] ?></p>
<p><strong>Topic:</strong> <?= htmlspecialchars($plan['topic']) ?></p>

<h3>Objectives</h3>
<p><?= nl2br(htmlspecialchars($plan['objectives'])) ?></p>

<h3>Activities</h3>
<p><?= nl2br(htmlspecialchars($plan['activities'])) ?></p>

<h3>Assessment</h3>
<p><?= nl2br(htmlspecialchars($plan['assessment'])) ?></p>

<h3>Homework</h3>
<p><?= nl2br(htmlspecialchars($plan['homework'])) ?></p>

<h3>Notes</h3>
<p><?= nl2br(htmlspecialchars($plan['notes'])) ?></p>

<h3>Lessons in this week</h3>
<a href="index.php?page=lesson_add&plan_id=<?= $plan['id'] ?>&class_id=<?= $plan['class_id'] ?>">+ Add Lesson</a>

<table class="table">
    <tr>
        <th>Date</th>
        <th>Period</th>
        <th>Topic</th>
        <th>Actions</th>
    </tr>
<?php foreach ($lessons as $l): ?>
    <tr>
        <td><?= $l['lesson_date'] ?></td>
        <td><?= htmlspecialchars($l['period']) ?></td>
        <td><?= htmlspecialchars($l['topic']) ?></td>
        <td>
            <a href="index.php?page=lesson_view&id=<?= $l['id'] ?>">View</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
