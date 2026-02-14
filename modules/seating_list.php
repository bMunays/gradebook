<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    echo "<p>No class selected.</p>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

$plans = $pdo->prepare("
    SELECT *
    FROM seating_plans
    WHERE class_id = ?
    ORDER BY created_at DESC, id DESC
");
$plans->execute([$class_id]);
$plans = $plans->fetchAll();
?>

<h2>Seating Plans for <?= htmlspecialchars($class['name']) ?></h2>

<a href="index.php?page=seating_create&class_id=<?= $class_id ?>" class="btn">+ Create New Plan</a>

<?php if (!$plans): ?>
    <p>No seating plans created yet.</p>
<?php else: ?>

<table class="table">
    <tr>
        <th>Name</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($plans as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
            <td>
                <a href="index.php?page=seating_view&id=<?= $p['id'] ?>">View</a> |
                <a href="index.php?page=seating_edit&id=<?= $p['id'] ?>">Edit</a> |
                <a href="index.php?page=seating_delete&id=<?= $p['id'] ?>" onclick="return confirm('Delete this plan?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php endif; ?>
