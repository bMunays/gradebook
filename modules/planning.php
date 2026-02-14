<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    $classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
?>
<h2>Planning / DLP</h2>
<p>Select a class to view or create plans.</p>
<ul>
<?php foreach ($classes as $c): ?>
    <li>
        <a href="index.php?page=planning&class_id=<?= $c['id'] ?>">
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
?>

<h2>Planning for <?= htmlspecialchars($class['name']) ?></h2>

<a href="index.php?page=planning_add&class_id=<?= $class_id ?>" class="btn">+ Add Weekly Plan</a>

<table class="table">
    <tr>
        <th>Week</th>
        <th>Topic</th>
        <th>Actions</th>
    </tr>
<?php
$plans = $pdo->prepare("SELECT * FROM plans WHERE class_id = ? ORDER BY week_start");
$plans->execute([$class_id]);

foreach ($plans as $p):
?>
    <tr>
        <td><?= $p['week_start'] ?> ? <?= $p['week_end'] ?></td>
        <td><?= htmlspecialchars($p['topic']) ?></td>
        <td>
            <a href="index.php?page=planning_view&id=<?= $p['id'] ?>">View</a> |
            <a href="index.php?page=planning_edit&id=<?= $p['id'] ?>">Edit</a> |
            <a href="index.php?page=planning_delete&id=<?= $p['id'] ?>" onclick="return confirm('Delete this plan?')">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
