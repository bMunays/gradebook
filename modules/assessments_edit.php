<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>No assessment selected.</p>";
    return;
}

$stmt = $pdo->prepare("
    SELECT a.*, c.name AS class_name
    FROM assessments a
    JOIN classes c ON a.class_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$assessment = $stmt->fetch();

if (!$assessment) {
    echo "<p>Assessment not found.</p>";
    return;
}

$rubrics = $pdo->query("SELECT * FROM rubrics ORDER BY title")->fetchAll();
$zp = $pdo->query("SELECT * FROM zimsec_projects ORDER BY title")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("
        UPDATE assessments
        SET title = ?, type = ?, max_mark = ?, date = ?, term = ?, weighting = ?, rubric_id = ?, zimsec_project_id = ?
        WHERE id = ?
    ")->execute([
        $_POST['title'],
        $_POST['type'],
        $_POST['max_mark'],
        $_POST['date'],
        $_POST['term'],
        $_POST['weighting'],
        $_POST['rubric_id'] ?: null,
        $_POST['zimsec_project_id'] ?: null,
        $id
    ]);

    header("Location: index.php?page=assessments&class_id=" . $assessment['class_id']);
    exit;
}
?>

<h2>Edit Assessment</h2>

<form method="POST">
    <p>Class: <?= htmlspecialchars($assessment['class_name']) ?></p>

    <label>Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($assessment['title']) ?>" required>

    <label>Type</label>
    <select name="type">
        <option value="exercise" <?= $assessment['type']=='exercise'?'selected':'' ?>>Exercise</option>
        <option value="test" <?= $assessment['type']=='test'?'selected':'' ?>>Test</option>
        <option value="practical" <?= $assessment['type']=='practical'?'selected':'' ?>>Practical</option>
        <option value="homework" <?= $assessment['type']=='homework'?'selected':'' ?>>Homework</option>
        <option value="exam" <?= $assessment['type']=='exam'?'selected':'' ?>>Exam</option>
        <option value="project" <?= $assessment['type']=='project'?'selected':'' ?>>Project</option>
        <option value="rubric" <?= $assessment['type']=='rubric'?'selected':'' ?>>Rubric-Based</option>
        <option value="zimsec_a" <?= $assessment['type']=='zimsec_a'?'selected':'' ?>>ZIMSEC Project A</option>
        <option value="zimsec_b" <?= $assessment['type']=='zimsec_b'?'selected':'' ?>>ZIMSEC Project B</option>
    </select>

    <label>Max Mark</label>
    <input type="number" name="max_mark" value="<?= htmlspecialchars($assessment['max_mark']) ?>" required>

    <label>Date</label>
    <input type="date" name="date" value="<?= htmlspecialchars($assessment['date']) ?>">

    <label>Term</label>
    <input type="text" name="term" value="<?= htmlspecialchars($assessment['term']) ?>" required>

    <label>Weighting</label>
    <input type="number" step="0.1" name="weighting" value="<?= htmlspecialchars($assessment['weighting']) ?>">

    <label>Rubric (only if type = Rubric-Based)</label>
    <select name="rubric_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($rubrics as $r): ?>
            <option value="<?= $r['id'] ?>" <?= $assessment['rubric_id']==$r['id']?'selected':'' ?>>
                <?= htmlspecialchars($r['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>ZIMSEC Project (only if type = ZIMSEC)</label>
    <select name="zimsec_project_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($zp as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $assessment['zimsec_project_id']==$p['id']?'selected':'' ?>>
                <?= htmlspecialchars($p['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Save Changes</button>
</form>
