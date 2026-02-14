<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/zimsec_helpers.php';

$project_id = $_GET['id'];

$project = $pdo->prepare("SELECT * FROM zimsec_projects WHERE id = ?");
$project->execute([$project_id]);
$p = $project->fetch();

$students = $pdo->prepare("
    SELECT DISTINCT s.*
    FROM students s
    JOIN zimsec_project_scores z ON s.id = z.student_id
    WHERE z.project_id = ?
    ORDER BY s.surname
");
$students->execute([$project_id]);
$students = $students->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZIMSEC Project Report</title>
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

<h2>ZIMSEC Project Report: <?= htmlspecialchars($p['title']) ?></h2>

<table class="table">
    <tr>
        <th>Student</th>
        <th>Teacher Total</th>
        <th>Moderated Total</th>
    </tr>

<?php foreach ($students as $s): ?>
    <?php
    $teacher_total = zimsec_teacher_total($pdo, $project_id, $s['id']);
    $moderated_total = zimsec_moderated_total($pdo, $project_id, $s['id']);
    ?>
    <tr>
        <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>
        <td><?= number_format($teacher_total, 2) ?></td>
        <td><?= $moderated_total !== null ? number_format($moderated_total, 2) : '—' ?></td>
    </tr>
<?php endforeach; ?>
</table>

</body>
</html>
