<?php
require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
?>

<h2>Seating Plans</h2>

<p>Select a class to manage seating layouts.</p>

<ul>
<?php foreach ($classes as $c): ?>
    <li>
        <?= htmlspecialchars($c['name']) ?> &#x2014;
        <a href="index.php?page=seating_list&class_id=<?= $c['id'] ?>">View Plans</a> |
        <a href="index.php?page=seating_create&class_id=<?= $c['id'] ?>">Create New Plan</a>
    </li>
<?php endforeach; ?>
</ul>
