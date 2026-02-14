<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'];
$student_id = $_GET['student_id'];
$term = $_GET['term'];

// Fetch student
$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$student_id]);
$s = $student->fetch();

// Fetch class
$class = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$class->execute([$class_id]);
$c = $class->fetch();

// Attendance summary
$attendance = $pdo->prepare("
    SELECT status, COUNT(*) AS count
    FROM attendance_records r
    JOIN attendance_sessions s ON r.session_id = s.id
    WHERE r.student_id = ? AND s.class_id = ?
    GROUP BY status
");
$attendance->execute([$student_id, $class_id]);
$attendance_summary = $attendance->fetchAll(PDO::FETCH_KEY_PAIR);

// Marks summary
$marks = $pdo->prepare("
    SELECT a.title, a.type, a.max_mark, m.mark
    FROM marks m
    JOIN assessments a ON m.assessment_id = a.id
    WHERE m.student_id = ? AND a.class_id = ? AND a.term = ?
    ORDER BY a.date
");
$marks->execute([$student_id, $class_id, $term]);
$marks_list = $marks->fetchAll();

// Calculate term average
$values = [];
foreach ($marks_list as $m) {
    if ($m['mark'] !== null) {
        $values[] = ($m['mark'] / $m['max_mark']) * 100;
    }
}
$term_avg = count($values) ? array_sum($values) / count($values) : 0;
?>

<h2>Student Report</h2>

<!--
Add export links to existing reports
Similarly in report_class.php and report_term.php (with appropriate params).
 -->
<a class="btn" href="index.php?page=export_student_csv&class_id=<?= $class_id ?>&student_id=<?= $student_id ?>&term=<?= $term ?>">Export CSV</a>
<a class="btn" href="index.php?page=export_student_print&class_id=<?= $class_id ?>&student_id=<?= $student_id ?>&term=<?= $term ?>" target="_blank">Print / PDF</a>

<!-- FIXED: Added checks to ensure $s and $c are arrays before accessing keys -->
<h3><?= htmlspecialchars(($s['surname'] ?? 'Unknown') . ', ' . ($s['first_name'] ?? 'Student')) ?></h3>
<p><strong>Class:</strong> <?= htmlspecialchars($c['name'] ?? 'Unknown') ?></p>
<p><strong>Term:</strong> <?= $term ?></p>

<h3>Attendance Summary</h3>
<ul>
    <li>Present: <?= $attendance_summary['present'] ?? 0 ?></li>
    <li>Absent: <?= $attendance_summary['absent'] ?? 0 ?></li>
    <li>Justified: <?= $attendance_summary['justified'] ?? 0 ?></li>
    <li>Sick: <?= $attendance_summary['sick'] ?? 0 ?></li>
    <li>Expelled: <?= $attendance_summary['expelled'] ?? 0 ?></li>
</ul>

<h3>Marks Summary</h3>
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

<h3>Term Average</h3>
<p><strong><?= number_format($term_avg, 2) ?>%</strong></p>