<?php
require_once __DIR__ . '/../config/db.php';

$assessment_id = $_GET['assessment_id'];

$stmt = $pdo->prepare("
    SELECT a.*, c.name AS class_name
    FROM assessments a
    JOIN classes c ON a.class_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$assessment_id]);
$assessment = $stmt->fetch();

$class_id = $assessment['class_id'];

$students = $pdo->prepare("
    SELECT s.*, e.status
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname
");
$students->execute([$class_id]);

// Skip expelled students
// Expelled Students Cannot Receive Marks
// TODO: Decide exactly where to logically place this code
if ($s['status'] === 'expelled') {
    continue;
}

// Save marks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['mark'] as $student_id => $mark) {
        $stmt = $pdo->prepare("
            INSERT INTO marks (assessment_id, student_id, mark)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE mark = VALUES(mark)
        ");
        $stmt->execute([$assessment_id, $student_id, $mark]);
    }
}

// TODO: Decide where this code should logically go.
// Add colour for marks
$percent = ($value / $assessment['max_mark']) * 100;

$color = $percent >= 75 ? "#059669" : ($percent >= 50 ? "#d97706" : "#dc2626");

?>

<h2>Marks: <?= htmlspecialchars($assessment['title']) ?> (<?= $assessment['class_name'] ?>)</h2>

<form method="POST">
<table class="table">
    <!-- Decide exactly where to place code -->
    <td style="color: <?= $color ?>;">
    <?= number_format($percent, 1) ?>%

    <tr>
        <th>Student</th>
        <th>Mark (out of <?= $assessment['max_mark'] ?>)</th>
    </tr>
    </td>

<?php
$marks = $pdo->prepare("SELECT student_id, mark FROM marks WHERE assessment_id = ?");
$marks->execute([$assessment_id]);
$existing_marks = $marks->fetchAll(PDO::FETCH_KEY_PAIR);

$values = [];

foreach ($students as $s):
    $sid = $s['id'];
    $value = $existing_marks[$sid] ?? '';
    if ($value !== '') $values[] = $value;
?>
    <tr>
        <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>
        <td>
            <input type="number" name="mark[<?= $sid ?>]" value="<?= $value ?>" step="0.01" min="0" max="<?= $assessment['max_mark'] ?>">
        </td>
    </tr>
<?php endforeach; ?>
</table>

<button type="submit">Save Marks</button>
</form>

<?php
// Statistics
if (count($values) > 0) {
    $min = min($values);
    $max = max($values);
    $avg = array_sum($values) / count($values);

    // Standard deviation
    $variance = 0;
    foreach ($values as $v) {
        $variance += pow($v - $avg, 2);
    }
    $std_dev = sqrt($variance / count($values));


?>
<h3>Statistics</h3>
<ul>
    <li><strong>Minimum:</strong> <?= number_format($min, 2) ?></li>
    <li><strong>Average:</strong> <?= number_format($avg, 2) ?></li>
    <li><strong>Maximum:</strong> <?= number_format($max, 2) ?></li>
    <li><strong>Standard Deviation:</strong> <?= number_format($std_dev, 2) ?></li>
</ul>
<?php } ?>
