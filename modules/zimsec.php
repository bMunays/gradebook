<?php
// modules/zimsec.php
// modules/zimsec_add.php
// modules/zimsec_edit.php
// modules/zimsec_delete.php
// modules/zimsec_score.php
// modules/zimsec_moderate.php
// modules/zimsec_view.php

require_once __DIR__ . '/../config/db.php';

$projects = $pdo->query("
    SELECT zp.*, c.name AS class_name
    FROM zimsec_projects zp
    JOIN classes c ON zp.class_id = c.id
    ORDER BY zp.created_at DESC
")->fetchAll();
?>

<h2>ZIMSEC Projects</h2>

<a href="index.php?page=zimsec_add" class="btn">+ Create ZIMSEC Project</a>

<table class="table">
    <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Class</th>
        <th>Actions</th>
    </tr>

<?php foreach ($projects as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['title']) ?></td>
        <td><?= $p['project_type'] ?></td>
        <td><?= htmlspecialchars($p['class_name']) ?></td>
        <td>
            <a href="index.php?page=zimsec_view&id=<?= $p['id'] ?>">View</a> |
            <a href="index.php?page=zimsec_edit&id=<?= $p['id'] ?>">Edit</a> |
            <a href="index.php?page=zimsec_delete&id=<?= $p['id'] ?>" onclick="return confirm('Delete project?')">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
