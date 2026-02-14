<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT zp.*, c.name AS class_name
    FROM zimsec_projects zp
    JOIN classes c ON zp.class_id = c.id
    WHERE zp.id = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch();

$components = $pdo->prepare("
    SELECT * FROM zimsec_project_components WHERE project_id = ?
");
$components->execute([$id]);
$components = $components->fetchAll();
?>

<h2>ZIMSEC Project: <?= htmlspecialchars($p['title']) ?></h2>

<p><strong>Class:</strong> <?= htmlspecialchars($p['class_name']) ?></p>
<p><strong>Type:</strong> <?= $p['project_type'] ?></p>
<p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($p['description'])) ?></p>

<h3>Components</h3>
<table class="table">
    <tr>
        <th>Component</th>
        <th>Max Score</th>
        <th>Weight</th>
    </tr>
<?php foreach ($components as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['component']) ?></td>
        <td><?= $c['max_score'] ?></td>
        <td><?= $c['weight'] ?></td>
    </tr>
<?php endforeach; ?>
</table>

<a class="btn" href="index.php?page=zimsec_score&id=<?= $p['id'] ?>">Enter Scores</a>
<a class="btn" href="index.php?page=zimsec_moderate&id=<?= $p['id'] ?>">Moderate</a>
