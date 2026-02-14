<?php
require_once __DIR__ . '/../config/db.php';

$project_id = $_GET['id'];

$project = $pdo->prepare("SELECT * FROM zimsec_projects WHERE id = ?");
$project->execute([$project_id]);
$project = $project->fetch();

$components = $pdo->prepare("
    SELECT * FROM zimsec_project_components WHERE project_id = ?
");
$components->execute([$project_id]);
$components = $components->fetchAll();

$students = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname
");
$students->execute([$project['class_id']]);
$students = $students->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("DELETE FROM zimsec_project_scores WHERE project_id = ?")
        ->execute([$project_id]);

    foreach ($_POST['score'] as $student_id => $comp_scores) {
        foreach ($comp_scores as $component_id => $score) {
            $pdo->prepare("
                INSERT INTO zimsec_project_scores (project_id, student_id, component_id, score)
                VALUES (?, ?, ?, ?)
            ")->execute([$project_id, $student_id, $component_id, $score]);
        }
    }

    echo "<p>Scores saved.</p>";
}
?>

<h2>Enter ZIMSEC Project Scores: <?= htmlspecialchars($project['title']) ?></h2>

<form method="POST">
<table class="table">
    <tr>
        <th>Student</th>
        <?php foreach ($components as $c): ?>
            <th><?= htmlspecialchars($c['component']) ?> (<?= $c['max_score'] ?>)</th>
        <?php endforeach; ?>
    </tr>

<?php foreach ($students as $s): ?>
    <tr>
        <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>

        <?php foreach ($components as $c): ?>
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
