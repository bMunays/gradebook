<?php
require_once __DIR__ . '/../config/db.php';

$assessment_id = $_GET['assessment_id'] ?? null;

if (!$assessment_id) {
    echo "<p>No assessment selected.</p>";
    return;
}

$ass_stmt = $pdo->prepare("
    SELECT a.*, c.name AS class_name
    FROM assessments a
    JOIN classes c ON a.class_id = c.id
    WHERE a.id = ?
");
$ass_stmt->execute([$assessment_id]);
$assessment = $ass_stmt->fetch();

if (!$assessment) {
    echo "<p>Assessment not found.</p>";
    return;
}

$class_id = $assessment['class_id'];

$students_stmt = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname, s.first_name
");
$students_stmt->execute([$class_id]);
$students = $students_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($_POST['mark'] as $student_id => $mark) {
        if ($mark === '' || $mark === null) {
            $pdo->prepare("
                DELETE FROM marks
                WHERE assessment_id = ? AND student_id = ?
            ")->execute([$assessment_id, $student_id]);
        } else {
            $mark = (float)$mark;

            $exists = $pdo->prepare("
                SELECT COUNT(*) FROM marks
                WHERE assessment_id = ? AND student_id = ?
            ");
            $exists->execute([$assessment_id, $student_id]);
            $count = $exists->fetchColumn();

            if ($count > 0) {
                $pdo->prepare("
                    UPDATE marks
                    SET mark = ?
                    WHERE assessment_id = ? AND student_id = ?
                ")->execute([$mark, $assessment_id, $student_id]);
            } else {
                $pdo->prepare("
                    INSERT INTO marks (assessment_id, student_id, mark)
                    VALUES (?, ?, ?)
                ")->execute([$assessment_id, $student_id, $mark]);
            }
        }
    }

    echo "<p>Marks saved.</p>";
}
?>

<h2>Enter Marks: <?= htmlspecialchars($assessment['title']) ?> (<?= htmlspecialchars($assessment['class_name']) ?>)</h2>

<form method="POST">
    <table class="table">
        <tr>
            <th>Student</th>
            <th>Mark (out of <?= (float)$assessment['max_mark'] ?>)</th>
        </tr>

        <?php foreach ($students as $s): ?>
            <?php
            $m = $pdo->prepare("
                SELECT mark FROM marks
                WHERE assessment_id = ? AND student_id = ?
            ");
            $m->execute([$assessment_id, $s['id']]);
            $mark = $m->fetchColumn();
            ?>
            <tr>
                <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>
                <td>
                    <input type="number" step="0.01"
                           name="mark[<?= $s['id'] ?>]"
                           value="<?= $mark !== false ? htmlspecialchars($mark) : '' ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit">Save Marks</button>
</form>
