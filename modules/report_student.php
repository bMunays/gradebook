<?php
// modules/report_student.php
// Replaces the old student report with:
// - Auto-detected latest term with marks
// - Student profile
// - Attendance summary
// - Marks summary + term average
// - Export links

require_once __DIR__ . '/../config/db.php';

$class_id   = $_GET['class_id']   ?? null;
$student_id = $_GET['student_id'] ?? null;
$term       = $_GET['term']       ?? null;

// Basic parameter check
if (!$class_id || !$student_id) {
    echo "<h2>Student Report</h2>";
    echo "<p>Missing class or student information.</p>";
    return;
}

// Fetch student
$student_stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student_stmt->execute([$student_id]);
$student = $student_stmt->fetch();

if (!$student) {
    echo "<h2>Student Report</h2>";
    echo "<p>Student not found.</p>";
    return;
}

// Fetch class
$class_stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$class_stmt->execute([$class_id]);
$class = $class_stmt->fetch();

if (!$class) {
    echo "<h2>Student Report</h2>";
    echo "<p>Class not found.</p>";
    return;
}

// Auto-detect latest term with marks if term not provided
if (!$term) {
    $term_stmt = $pdo->prepare("
        SELECT DISTINCT a.term
        FROM assessments a
        JOIN marks m ON m.assessment_id = a.id
        WHERE m.student_id = ? AND a.class_id = ?
        ORDER BY a.term DESC
        LIMIT 1
    ");
    $term_stmt->execute([$student_id, $class_id]);
    $latest_term = $term_stmt->fetchColumn();

    if ($latest_term !== false && $latest_term !== null) {
        $term = $latest_term;
    } else {
        // Fallback if no marks exist at all
        $term = 1;
    }
}

// Attendance summary
$attendance_stmt = $pdo->prepare("
    SELECT r.status, COUNT(*) AS count
    FROM attendance_records r
    JOIN attendance_sessions s ON r.session_id = s.id
    WHERE r.student_id = ? AND s.class_id = ?
    GROUP BY r.status
");
$attendance_stmt->execute([$student_id, $class_id]);
$attendance_summary = $attendance_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Marks summary
$marks_stmt = $pdo->prepare("
    SELECT a.title, a.type, a.max_mark, a.date, a.term, m.mark
    FROM marks m
    JOIN assessments a ON m.assessment_id = a.id
    WHERE m.student_id = ? AND a.class_id = ? AND a.term = ?
    ORDER BY a.date
");
$marks_stmt->execute([$student_id, $class_id, $term]);
$marks_list = $marks_stmt->fetchAll();

// Calculate term average
$values = [];
foreach ($marks_list as $m) {
    if ($m['mark'] !== null && $m['max_mark'] > 0) {
        $values[] = ($m['mark'] / $m['max_mark']) * 100;
    }
}
$term_avg = count($values) ? array_sum($values) / count($values) : 0;
?>

<h2>Student Report</h2>

<a class="btn" href="index.php?page=export_student_csv&class_id=<?= $class_id ?>&student_id=<?= $student_id ?>&term=<?= $term ?>">
    Export CSV
</a>
<a class="btn" href="index.php?page=export_student_print&class_id=<?= $class_id ?>&student_id=<?= $student_id ?>&term=<?= $term ?>" target="_blank">
    Print / PDF
</a>

<h3>
    <?= htmlspecialchars($student['surname'] ?? 'Unknown') ?>,
    <?= htmlspecialchars($student['first_name'] ?? 'Student') ?>
</h3>
<p><strong>Class:</strong> <?= htmlspecialchars($class['name'] ?? 'Unknown') ?></p>
<p><strong>Term:</strong> <?= htmlspecialchars($term) ?></p>

<h3>Attendance Summary</h3>
<ul>
    <li>Present:   <?= $attendance_summary['present']   ?? 0 ?></li>
    <li>Absent:    <?= $attendance_summary['absent']    ?? 0 ?></li>
    <li>Justified: <?= $attendance_summary['justified'] ?? 0 ?></li>
    <li>Sick:      <?= $attendance_summary['sick']      ?? 0 ?></li>
    <li>Expelled:  <?= $attendance_summary['expelled']  ?? 0 ?></li>
</ul>

<h3>Marks Summary</h3>

<?php if (empty($marks_list)): ?>
    <p>No marks recorded yet for this term.</p>
<?php else: ?>
    <table class="table">
        <tr>
            <th>Date</th>
            <th>Assessment</th>
            <th>Type</th>
            <th>Mark</th>
            <th>%</th>
        </tr>
        <?php foreach ($marks_list as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['date']) ?></td>
                <td><?= htmlspecialchars($m['title']) ?></td>
                <td><?= htmlspecialchars($m['type']) ?></td>
                <td><?= htmlspecialchars($m['mark']) ?>/<?= htmlspecialchars($m['max_mark']) ?></td>
                <td>
                    <?= $m['max_mark'] > 0
                        ? number_format(($m['mark'] / $m['max_mark']) * 100, 2) . '%'
                        : '—'
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h3>Term Average</h3>
<p><strong><?= number_format($term_avg, 2) ?>%</strong></p>
