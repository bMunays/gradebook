<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'];
$student_id = $_GET['student_id'];
$term = $_GET['term'];

$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$student_id]);
$s = $student->fetch();

$class = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$class->execute([$class_id]);
$c = $class->fetch();

$marks = $pdo->prepare("
    SELECT a.title, a.type, a.max_mark, m.mark
    FROM marks m
    JOIN assessments a ON m.assessment_id = a.id
    WHERE m.student_id = ? AND a.class_id = ? AND a.term = ?
    ORDER BY a.date
");
$marks->execute([$student_id, $class_id, $term]);
$marks_list = $marks->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Report</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        @media print {
            .no-print { display:none; }
        }
    </style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()">Print / Save as PDF</button>
</div>

<h2>Student Report</h2>
<p><strong>Name:</strong> <?= htmlspecialchars($s['surname'].', '.$s['first_name']) ?></p>
<p><strong>Class:</strong> <?= htmlspecialchars($c['name']) ?></p>
<p><strong>Term:</strong> <?= $term ?></p>

<table class="table">
    <tr>
        <th>Assessment</th>
        <th>Type</th>
        <th>Mark</th>
        <th>%</th>
    </tr>
<?php foreach ($marks_list as $m): ?>
    <tr>
        <td><?= htmlspecialchars($m['title']) ?></td>
        <td><?= $m['type'] ?></td>
        <td><?= $m['mark'] ?>/<?= $m['max_mark'] ?></td>
        <td><?= number_format(($m['mark'] / $m['max_mark']) * 100, 2) ?>%</td>
    </tr>
<?php endforeach; ?>
</table>
</body>
</html>
