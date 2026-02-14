<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'];
$student_id = $_GET['student_id'];
$term = $_GET['term'];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=student_report_'.$student_id.'_term_'.$term.'.csv');

$output = fopen('php://output', 'w');

// Fetch student and class
$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$student_id]);
$s = $student->fetch();

$class = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$class->execute([$class_id]);
$c = $class->fetch();

// Header info
fputcsv($output, ['Student Report']);
fputcsv($output, ['Name', $s['surname'].', '.$s['first_name']]);
fputcsv($output, ['Class', $c['name']]);
fputcsv($output, ['Term', $term]);
fputcsv($output, []);

// Marks
$marks = $pdo->prepare("
    SELECT a.title, a.type, a.max_mark, m.mark
    FROM marks m
    JOIN assessments a ON m.assessment_id = a.id
    WHERE m.student_id = ? AND a.class_id = ? AND a.term = ?
    ORDER BY a.date
");
$marks->execute([$student_id, $class_id, $term]);

fputcsv($output, ['Assessment','Type','Mark','Out of','Percent']);

foreach ($marks as $m) {
    $percent = $m['mark'] !== null && $m['max_mark'] > 0
        ? ($m['mark'] / $m['max_mark']) * 100
        : 0;
    fputcsv($output, [
        $m['title'],
        $m['type'],
        $m['mark'],
        $m['max_mark'],
        number_format($percent,2)
    ]);
}

fclose($output);
exit;
