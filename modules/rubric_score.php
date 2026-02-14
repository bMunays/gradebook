<?php
require_once __DIR__ . '/../config/db.php';

$assessment_id = $_GET['assessment_id'];

$assessment = $pdo->prepare("
    SELECT a.*, r.title AS rubric_title
    FROM assessments a
    JOIN rubrics r ON a.rubric_id = r.id
    WHERE a.id = ?
");
$assessment->execute([$assessment_id]);
$assessment = $assessment->fetch();

$criteria = $pdo->prepare("
    SELECT * FROM rubric_criteria WHERE rubric_id = ?
");
$criteria->execute([$assessment['rubric_id']]);
$criteria = $criteria->fetchAll();

$students = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname
");
$students->execute([$assessment['class_id']]);
$students = $students->fetchAll();

// Save scores
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("
        DELETE FROM rubric_scores WHERE assessment_id = ?
    ")->execute([$assessment_id]);

    foreach ($_POST['score'] as $student_id => $crit_scores) {
        foreach ($crit_scores as $criterion_id => $score) {
            $pdo->prepare("
                INSERT INTO rubric_scores (assessment_id, student_id, criterion_id, score)
                VALUES (?, ?, ?, ?)
            ")->execute([$assessment_id, $student_id, $criterion_id, $score]);
        }
    }

    echo "<p>Scores saved.</p>";
}
?>

<h2>Rubric Scoring: <?= htmlspecialchars($assessment['title']) ?></h2>
<h3>Rubric: <?= htmlspecialchars($assessment['rubric_title']) ?></h3>

<form method="POST">
<table class="table">
    <tr>
        <th>Student</th>
        <?php foreach ($criteria as $c): ?>
            <th><?= htmlspecialchars($c['criterion']) ?> (<?= $c['max_score'] ?>)</th>
        <?php endforeach; ?>
    </tr>

<?php foreach ($students as $s): ?>
    <tr>
        <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>

        <?php foreach ($criteria as $c): ?>
            <td>
                <input type="number"
                       name="score[<?= $s['id'] ?>][<?= $c['id'] ?>]"
                       min="0"
                       max="<?= $c['max_score'] ?>"
                       step="0.1">
            </td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</table>

<button type="submit">Save Scores</button>
</form>
