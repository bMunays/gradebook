<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM rubrics WHERE id = ?");
$stmt->execute([$id]);
$rubric = $stmt->fetch();

$criteria = $pdo->prepare("SELECT * FROM rubric_criteria WHERE rubric_id = ?");
$criteria->execute([$id]);
$criteria = $criteria->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("UPDATE rubrics SET title=?, description=? WHERE id=?")
        ->execute([$_POST['title'], $_POST['description'], $id]);

    $pdo->prepare("DELETE FROM rubric_criteria WHERE rubric_id=?")->execute([$id]);

    foreach ($_POST['criterion'] as $i => $crit) {
        if (trim($crit) === '') continue;

        $pdo->prepare("
            INSERT INTO rubric_criteria (rubric_id, criterion, max_score, weight)
            VALUES (?, ?, ?, ?)
        ")->execute([
            $id,
            $crit,
            $_POST['max_score'][$i],
            $_POST['weight'][$i]
        ]);
    }

    header("Location: index.php?page=rubrics");
    exit;
}
?>

<h2>Edit Rubric</h2>

<form method="POST">
    <label>Rubric Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($rubric['title']) ?>" required>

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($rubric['description']) ?></textarea>

    <h3>Criteria</h3>

    <div id="criteria">
        <?php foreach ($criteria as $c): ?>
        <div class="criterion-row">
            <input type="text" name="criterion[]" value="<?= htmlspecialchars($c['criterion']) ?>">
            <input type="number" name="max_score[]" value="<?= $c['max_score'] ?>">
            <input type="number" step="0.1" name="weight[]" value="<?= $c['weight'] ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addCriterion()">+ Add Criterion</button>

    <button type="submit">Save Changes</button>
</form>

<script>
function addCriterion() {
    const div = document.createElement('div');
    div.className = 'criterion-row';
    div.innerHTML = `
        <input type="text" name="criterion[]" placeholder="Criterion description">
        <input type="number" name="max_score[]" placeholder="Max Score" value="10">
        <input type="number" step="0.1" name="weight[]" placeholder="Weight" value="1.0">
    `;
    document.getElementById('criteria').appendChild(div);
}
</script>
