<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("INSERT INTO rubrics (title, description) VALUES (?, ?)");
    $stmt->execute([$_POST['title'], $_POST['description']]);

    $rubric_id = $pdo->lastInsertId();

    foreach ($_POST['criterion'] as $i => $crit) {
        if (trim($crit) === '') continue;

        $stmt = $pdo->prepare("
            INSERT INTO rubric_criteria (rubric_id, criterion, max_score, weight)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $rubric_id,
            $crit,
            $_POST['max_score'][$i],
            $_POST['weight'][$i]
        ]);
    }

    header("Location: index.php?page=rubrics");
    exit;
}
?>

<h2>Create Rubric</h2>

<form method="POST">
    <label>Rubric Title</label>
    <input type="text" name="title" required>

    <label>Description</label>
    <textarea name="description"></textarea>

    <h3>Criteria</h3>

    <div id="criteria">
        <div class="criterion-row">
            <input type="text" name="criterion[]" placeholder="Criterion description">
            <input type="number" name="max_score[]" placeholder="Max Score" value="10">
            <input type="number" step="0.1" name="weight[]" placeholder="Weight" value="1.0">
        </div>
    </div>

    <button type="button" onclick="addCriterion()">+ Add Criterion</button>

    <button type="submit">Save Rubric</button>
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
