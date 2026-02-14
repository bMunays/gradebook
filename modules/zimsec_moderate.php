<?php
require_once __DIR__ . '/../config/db.php';

$project_id = $_GET['id'];

// Fetch project
$project = $pdo->prepare("SELECT * FROM zimsec_projects WHERE id = ?");
$project->execute([$project_id]);
$project = $project->fetch();

// Fetch students who have scores
$students = $pdo->prepare("
    SELECT DISTINCT s.*
    FROM students s
    JOIN zimsec_project_scores z ON s.id = z.student_id
    WHERE z.project_id = ?
    ORDER BY s.surname
");
$students->execute([$project_id]);
$students = $students->fetchAll();

// Handle moderation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Clear old moderation
    $pdo->prepare("DELETE FROM zimsec_project_moderation WHERE project_id = ?")
        ->execute([$project_id]);

    // Insert new moderation
    foreach ($_POST['moderated'] as $student_id => $value) {
        $pdo->prepare("
            INSERT INTO zimsec_project_moderation (project_id, student_id, teacher_total, moderated_total, comment)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([
            $project_id,
            $student_id,
            $_POST['teacher_total'][$student_id],
            $value,
            $_POST['comment'][$student_id]
        ]);
    }

    echo "<p>Moderation saved.</p>";
}
?>

<h2>Moderate ZIMSEC Project: <?= htmlspecialchars($project['title']) ?></h2>

<form method="POST">
<table class="table">
    <tr>
        <th>Student</th>
        <th>Teacher Total</th>
        <th>Moderated Total</th>
        <th>Comment</th>
    </tr>

<?php foreach ($students as $s): ?>

    <?php
    // Calculate teacher total
    $total = $pdo->prepare("
        SELECT SUM(z.score * c.weight) AS total
        FROM zimsec_project_scores z
        JOIN zimsec_project_components c ON z.component_id = c.id
        WHERE z.project_id = ? AND z.student_id = ?
    ");
    $total->execute([$project_id, $s['id']]);
    $teacher_total = $total->fetchColumn() ?: 0;
    ?>

    <tr>
        <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>

        <td>
            <input type="number"
                   name="teacher_total[<?= $s['id'] ?>]"
                   value="<?= $teacher_total ?>"
                   readonly>
        </td>

        <td>
            <input type="number"
                   name="moderated[<?= $s['id'] ?>]"
                   value="<?= $teacher_total ?>"
                   step="0.1">
        </td>

        <td>
            <input type="text"
                   name="comment[<?= $s['id'] ?>]"
                   placeholder="Moderator comment">
        </td>
    </tr>

<?php endforeach; ?>
</table>

<button type="submit">Save Moderation</button>
</form>
