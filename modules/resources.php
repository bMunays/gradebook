<?php
// modules/resources.php          ? List resources
// modules/resources_add.php      ? Upload/add resource
// modules/resources_view.php     ? View resource details
// modules/resources_delete.php   ? Delete resource


require_once __DIR__ . '/../config/db.php';

$resources = $pdo->query("
    SELECT r.*, c.name AS class_name
    FROM resources r
    LEFT JOIN classes c ON r.class_id = c.id
    ORDER BY r.created_at DESC
")->fetchAll();
?>

<h2>Resources</h2>

<a href="index.php?page=resources_add" class="btn">+ Add Resource</a>

<table class="table">
    <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Class</th>
        <th>File</th>
        <th>Actions</th>
    </tr>

<?php foreach ($resources as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= $r['type'] ?></td>
        <td><?= htmlspecialchars($r['class_name']) ?></td>
        <td>
            <?php if ($r['file_path']): ?>
                <a href="<?= $r['file_path'] ?>" target="_blank">Open</a>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>
        <td>
            <a href="index.php?page=resources_view&id=<?= $r['id'] ?>">View</a> |
            <a href="index.php?page=resources_delete&id=<?= $r['id'] ?>" onclick="return confirm('Delete resource?')">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
