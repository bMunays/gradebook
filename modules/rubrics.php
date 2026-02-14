<?php
// modules/rubrics.php
// modules/rubrics_add.php
// modules/rubrics_edit.php
// modules/rubrics_delete.php
// modules/rubric_score.php

require_once __DIR__ . '/../config/db.php';

$rubrics = $pdo->query("SELECT * FROM rubrics ORDER BY created_at DESC")->fetchAll();
?>

<h2>Rubrics</h2>

<a href="index.php?page=rubrics_add" class="btn">+ Create Rubric</a>

<table class="table">
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>

<?php foreach ($rubrics as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= nl2br(htmlspecialchars($r['description'])) ?></td>
        <td>
            <a href="index.php?page=rubrics_edit&id=<?= $r['id'] ?>">Edit</a> |
            <a href="index.php?page=rubrics_delete&id=<?= $r['id'] ?>" onclick="return confirm('Delete rubric?')">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>

