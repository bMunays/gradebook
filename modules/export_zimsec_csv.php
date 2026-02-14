<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/zimsec_helpers.php';

$project_id = $_GET['id'];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=zimsec_project_'.$project_id.'.csv');

$output = fopen('php://output', 'w');

// Fetch project
$project = $pdo->prepare("SELECT * FROM zimsec_projects WHERE id = ?");
$project->execute([$project_id]);
$p = $project->fetch();

// Header
fputcsv($output, ['ZIMSEC Project Report']);
fputcsv($output, ['Title', $p['title']]);
fputcsv($output, ['Type', $p['project_type']]);
fputcsv($output, []);

// Students
$students = $pdo->prepare("
    SELECT DISTINCT s.*
    FROM students s
    JOIN zimsec_project_scores z ON s.id = z.student_id
    WHERE z.project_id = ?
    ORDER BY s.surname
");
$students->execute([$project_id]);
$students = $students->fetchAll();

fputcsv($output, ['Student','Teacher Total','Moderated Total']);

foreach ($students as $s) {
    $teacher_total = zimsec_teacher_total($pdo, $project_id, $s['id']);
    $moderated_total = zimsec_moderated_total($pdo, $project_id, $s['id']);

    fputcsv($output, [
        $s['surname'] . ', ' . $s['first_name'],
        number_format($teacher_total, 2),
        $moderated_total !== null ? number_format($moderated_total, 2) : ''
    ]);
}

fclose($output);
exit;
