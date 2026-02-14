<?php
require_once __DIR__ . '/../config/db.php';

$project_id = $_GET['id'];

// Fetch project
$stmt = $pdo->prepare("SELECT * FROM zimsec_projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

// Fetch classes
$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();

// Fetch rubrics
$rubrics = $pdo->query("SELECT * FROM rubrics ORDER BY title")->fetchAll();

// Fetch components
$components = $pdo->prepare("
    SELECT * FROM zimsec_project_components WHERE project_id = ?
");
$components->execute([$project_id]);
$components = $components->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update project
    $pdo->prepare("
        UPDATE zimsec_projects
        SET class_id=?, title=?, project_type=?, description=?, max_mark=?, rubric_id=?
        WHERE id=?
    ")->execute([
        $_POST['class_id'],
        $_POST['title'],
        $_POST['project_type'],
        $_POST['description'],
        $_POST['max_mark'],
        $_POST['rubric_id'] ?: null,
        $project_id
    ]);

    // Delete old components
    $pdo->prepare("DELETE FROM zimsec_project_components WHERE project_id=?")
        ->execute([$project_id]);

    // Insert new components
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

    header("Location: index.php?page=zimsec_view&id=$project_id");
    exit;
}
?>

<h2>Edit ZIMSEC Project</h2>

<form method="POST">

    <label>Class</label>
    <select name="class_id">
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id']==$project['class_id']?'selected':'' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Project Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>

    <label>Project Type</label>
    <select name="project_type">
        <option value="A" <?= $project['project_type']=='A'?'selected':'' ?>>Project A</option>
        <option value="B" <?= $project['project_type']=='B'?'selected':'' ?>>Project B</option>
    </select>

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($project['description']) ?></textarea>

    <label>Max Mark</label>
    <input type="number" name="max_mark" value="<?= $project['max_mark'] ?>">

    <label>Rubric (optional)</label>
    <select name="rubric_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($rubrics as $r): ?>
            <option value="<?= $r['id'] ?>" <?= $r['id']==$project['rubric_id']?'selected':'' ?>>
                <?= htmlspecialchars($r['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h3>Components</h3>

    <div id="components">
        <?php foreach ($components as $c): ?>
        <div class="component-row">
            <input type="text" name="component[]" value="<?= htmlspecialchars($c['component']) ?>">
            <input type="number" name="max_score[]" value="<?= $c['max_score'] ?>">
            <input type="number" step="0.1" name="weight[]" value="<?= $c['weight'] ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addComponent()">+ Add Component</button>

    <button type="submit">Save Changes</button>
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
