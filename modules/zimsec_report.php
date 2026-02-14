<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/zimsec_helpers.php';

$project_id = $_GET['id'];

$project = $pdo->prepare("
    SELECT zp.*, c.name AS class_name
    FROM zimsec_projects zp
    JOIN classes c ON zp.class_id = c.id
    WHERE zp.id = ?
");
$project->execute([$project_id]);
$project = $project->fetch();

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

<h2>ZIMSEC Project Report: <?= htmlspecialchars($project['title']) ?></h2>

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
