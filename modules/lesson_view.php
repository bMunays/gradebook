<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT l.*, c.name AS class_name
    FROM lessons l
    JOIN classes c ON l.class_id = c.id
    WHERE l.id = ?
");
$stmt->execute([$id]);
$lesson = $stmt->fetch();
?>

<h2>Lesson: <?= htmlspecialchars($lesson['class_name']) ?> — <?= $lesson['lesson_date'] ?> (<?= htmlspecialchars($lesson['period']) ?>)</h2>

<p><strong>Topic:</strong> <?= htmlspecialchars($lesson['topic']) ?></p>

<h3>Objectives</h3>
<p><?= nl2br(htmlspecialchars($lesson['objectives'])) ?></p>

<h3>Activities</h3>
<p><?= nl2br(htmlspecialchars($lesson['activities'])) ?></p>

<h3>Resources</h3>
<p><?= nl2br(htmlspecialchars($lesson['resources'])) ?></p>

<h3>Homework</h3>
<p><?= nl2br(htmlspecialchars($lesson['homework'])) ?></p>
