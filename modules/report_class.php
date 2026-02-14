<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;
$term = $_GET['term'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $term = $_POST['term'];
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();

if (!$class_id || !$term):
?>

<h2>Class Report</h2>

<form method="POST">
    <label>Class</label>
    <select name="class_id" required>
        <option value="">— Select —</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Term</label>
    <input type="text" name="term" value="1" required>

    <button type="submit">Generate</button>
</form>

<?php
return;
endif;

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

$students = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname, s.first_name
");
$students->execute([$class_id]);
$students = $students->fetchAll();

$assessments = $pdo->prepare("
    SELECT * FROM assessments
    WHERE class_id = ? AND term = ?
    ORDER BY date
");
$assessments->execute([$class_id, $term]);
$assessments = $assessments->fetchAll();
?>

<h2>Class Report: <?= htmlspecialchars($class['name']) ?> (Term <?= htmlspecialchars($term) ?>)</h2>

<?php if (!$students || !$assessments): ?>
    <p>No data available yet for this class/term.</p>
    <?php return; ?>
<?php endif; ?>

<table class="table">
    <tr>
        <th>Student</th>
        <?php foreach ($assessments as $a): ?>
            <th><?= htmlspecialchars($a['title']) ?> (<?= $a['max_mark'] ?>)</th>
        <?php endforeach; ?>
        <th>Average %</th>
    </tr>

<?php
$overall_totals = [];
$overall_count = [];

foreach ($students as $s):
    $row_total = 0;
    $row_count = 0;
?>
    <tr>
        <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>

        <?php foreach ($assessments as $a): ?>
            <?php
            $m = $pdo->prepare("
                SELECT mark FROM marks
                WHERE assessment_id = ? AND student_id = ?
            ");
            $m->execute([$a['id'], $s['id']]);
            $mark = $m->fetchColumn();

            if ($mark !== false && $a['max_mark'] > 0) {
                $percent = ($mark / $a['max_mark']) * 100;
                $row_total += $percent;
                $row_count++;

                if (!isset($overall_totals[$a['id']])) {
                    $overall_totals[$a['id']] = 0;
                    $overall_count[$a['id']] = 0;
                }
                $overall_totals[$a['id']] += $percent;
                $overall_count[$a['id']]++;
            }
            ?>
            <td>
                <?= $mark !== false ? $mark . '/' . $a['max_mark'] : '—' ?>
            </td>
        <?php endforeach; ?>

        <td>
            <?php
            if ($row_count > 0) {
                echo number_format($row_total / $row_count, 2) . '%';
            } else {
                echo '—';
            }
            ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<h3>Assessment Averages</h3>

<table class="table">
    <tr>
        <th>Assessment</th>
        <th>Average %</th>
    </tr>
<?php foreach ($assessments as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['title']) ?></td>
        <td>
            <?php
            if (!empty($overall_count[$a['id']])) {
                echo number_format($overall_totals[$a['id']] / $overall_count[$a['id']], 2) . '%';
            } else {
                echo '—';
            }
            ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
