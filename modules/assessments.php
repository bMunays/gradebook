<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    $classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
    ?>
    <h2>Assessments</h2>
    <p>Select a class first.</p>
    <ul>
    <?php foreach ($classes as $c): ?>
        <li>
            <a href="index.php?page=assessments&class_id=<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php
    return;
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

$assessments = $pdo->prepare("
    SELECT * FROM assessments
    WHERE class_id = ?
    ORDER BY date DESC
");
$assessments->execute([$class_id]);
$assessments = $assessments->fetchAll();
?>

<h2>Assessments for <?= htmlspecialchars($class['name']) ?></h2>

<a href="index.php?page=assessments_add&class_id=<?= $class_id ?>" class="btn">+ Add Assessment</a>

<table class="table">
    <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Date</th>
        <th>Max Mark</th>
        <th>Actions</th>
    </tr>

<?php foreach ($assessments as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['title']) ?></td>
        <td><?= htmlspecialchars($a['type']) ?></td>
        <td><?= htmlspecialchars($a['date']) ?></td>
        <td><?= htmlspecialchars($a['max_mark']) ?></td>
        <td>
            <a href="index.php?page=marks_edit&assessment_id=<?= $a['id'] ?>">Enter Marks</a> |
            <a href="index.php?page=assessments_edit&id=<?= $a['id'] ?>">Edit</a> |
            <a href="index.php?page=assessments_delete&id=<?= $a['id'] ?>" onclick="return confirm('Delete assessment?')">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
