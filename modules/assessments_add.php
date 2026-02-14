<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    echo "<p>No class selected.</p>";
    return;
}

$rubrics = $pdo->query("SELECT * FROM rubrics ORDER BY title")->fetchAll();
$zp = $pdo->query("SELECT * FROM zimsec_projects ORDER BY title")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO assessments (class_id, title, type, max_mark, date, term, weighting, rubric_id, zimsec_project_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $class_id,
        $_POST['title'],
        $_POST['type'],
        $_POST['max_mark'],
        $_POST['date'],
        $_POST['term'],
        $_POST['weighting'],
        $_POST['rubric_id'] ?: null,
        $_POST['zimsec_project_id'] ?: null
    ]);

    header("Location: index.php?page=assessments&class_id=$class_id");
    exit;
}
?>

<h2>Add Assessment</h2>

<form method="POST">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Type</label>
    <select name="type">
        <option value="exercise">Exercise</option>
        <option value="test">Test</option>
        <option value="practical">Practical</option>
        <option value="homework">Homework</option>
        <option value="exam">Exam</option>
        <option value="project">Project</option>
        <option value="rubric">Rubric-Based</option>
        <option value="zimsec_a">ZIMSEC Project A</option>
        <option value="zimsec_b">ZIMSEC Project B</option>
    </select>

    <label>Max Mark</label>
    <input type="number" name="max_mark" value="100" required>

    <label>Date</label>
    <input type="date" name="date" value="<?= date('Y-m-d') ?>">

    <label>Term</label>
    <input type="text" name="term" value="1">

    <label>Weighting</label>
    <input type="number" step="0.1" name="weighting" value="1.0">

    <label>Rubric (only if type = Rubric-Based)</label>
    <select name="rubric_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($rubrics as $r): ?>
            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>ZIMSEC Project (only if type = ZIMSEC)</label>
    <select name="zimsec_project_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($zp as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Save Assessment</button>
</form>
