<?php
require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
$rubrics = $pdo->query("SELECT * FROM rubrics ORDER BY title")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO zimsec_projects (class_id, title, project_type, description, max_mark, rubric_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['class_id'],
        $_POST['title'],
        $_POST['project_type'],
        $_POST['description'],
        $_POST['max_mark'],
        $_POST['rubric_id'] ?: null
    ]);

    $project_id = $pdo->lastInsertId();

    foreach ($_POST['component'] as $i => $comp) {
        if (trim($comp) === '') continue;

        $pdo->prepare("
            INSERT INTO zimsec_project_components (project_id, component, max_score, weight)
            VALUES (?, ?, ?, ?)
        ")->execute([
            $project_id,
            $comp,
            $_POST['max_score'][$i],
            $_POST['weight'][$i]
        ]);
    }

    header("Location: index.php?page=zimsec");
    exit;
}
?>

<h2>Create ZIMSEC Project</h2>

<form method="POST">
    <label>Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Project Title</label>
    <input type="text" name="title" required>

    <label>Project Type</label>
    <select name="project_type">
        <option value="A">Project A</option>
        <option value="B">Project B</option>
    </select>

    <label>Description</label>
    <textarea name="description"></textarea>

    <label>Max Mark</label>
    <input type="number" name="max_mark" value="100">

    <label>Rubric (optional)</label>
    <select name="rubric_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($rubrics as $r): ?>
            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <h3>Components</h3>

    <div id="components">
        <div class="component-row">
            <input type="text" name="component[]" placeholder="Component name">
            <input type="number" name="max_score[]" placeholder="Max Score" value="20">
            <input type="number" step="0.1" name="weight[]" placeholder="Weight" value="1.0">
        </div>
    </div>

    <button type="button" onclick="addComponent()">+ Add Component</button>

    <button type="submit">Save Project</button>
</form>

<script>
function addComponent() {
    const div = document.createElement('div');
    div.className = 'component-row';
    div.innerHTML = `
        <input type="text" name="component[]" placeholder="Component name">
        <input type="number" name="max_score[]" placeholder="Max Score" value="20">
        <input type="number" step="0.1" name="weight[]" placeholder="Weight" value="1.0">
    `;
    document.getElementById('components').appendChild(div);
}
</script>
