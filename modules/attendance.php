<?php
require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
?>

<h2>Attendance</h2>

<p>Select a class to manage attendance.</p>

<ul>
<?php foreach ($classes as $c): ?>
    <li>
        <a href="index.php?page=attendance_take&class_id=<?= $c['id'] ?>">
            <?= htmlspecialchars($c['name']) ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>
