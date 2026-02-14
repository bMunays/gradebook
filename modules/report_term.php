<?php
require_once __DIR__ . '/../config/db.php';

$term = $_GET['term'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $term = $_POST['term'];
}

if (!$term):
?>

<h2>Term Summary</h2>

<form method="POST">
    <label>Term</label>
    <input type="text" name="term" value="1" required>
    <button type="submit">Generate</button>
</form>

<?php
return;
endif;

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
?>

<h2>Term Summary: Term <?= htmlspecialchars($term) ?></h2>

<?php if (!$classes): ?>
    <p>No classes found.</p>
    <?php return; ?>
<?php endif; ?>

<table class="table">
    <tr>
        <th>Class</th>
        <th>Number of Assessments</th>
    </tr>

<?php foreach ($classes as $c): ?>
    <?php
    $count = $pdo->prepare("
        SELECT COUNT(*) FROM assessments
        WHERE class_id = ? AND term = ?
    ");
    $count->execute([$c['id'], $term]);
    $num = $count->fetchColumn();
    ?>
    <tr>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= $num ?></td>
    </tr>
<?php endforeach; ?>
</table>
