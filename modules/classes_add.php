<?php
require_once __DIR__ . '/../config/db.php';

$sessions = $pdo->query("
    SELECT id, name
    FROM timetable_sessions
    ORDER BY id
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name       = $_POST['name'] ?? '';
    $level      = $_POST['level'] ?? '';
    $year       = (int)($_POST['year'] ?? date('Y'));
    $session_id = $_POST['session_id'] !== '' ? (int)$_POST['session_id'] : null;

    // No teacher field in the form yet ? force NULL
    $teacher_id = null;

    $stmt = $pdo->prepare("
        INSERT INTO classes (name, session_id, level, year, teacher_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $name,
        $session_id,
        $level,
        $year,
        $teacher_id
    ]);

    echo "<p>Class added.</p>";
    echo '<p><a href="index.php?page=classes">Back to Classes</a></p>';
    return;
}


/**
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name       = $_POST['name'] ?? '';
    $level      = $_POST['level'] ?? '';
    $year       = (int)($_POST['year'] ?? date('Y'));
    $session_id = $_POST['session_id'] !== '' ? (int)$_POST['session_id'] : null;
    $teacher_id = $_POST['teacher_id'] !== '' ? (int)$_POST['teacher_id'] : null;

    $stmt = $pdo->prepare("
        INSERT INTO classes (name, session_id, level, year, teacher_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $name,
        $session_id,
        $level,
        $year,
        $teacher_id
    ]);

    echo "<p>Class added.</p>";
    echo '<p><a href="index.php?page=classes">Back to Classes</a></p>';
    return;
}**/
?>

<h2>Add Class</h2>

<form method="POST">
    <label>Class Name</label>
    <input type="text" name="name" placeholder="Form 1A" required>

    <label>Level</label>
    <input type="text" name="level" placeholder="Form 1">

    <label>Year</label>
    <input type="number" name="year" value="<?= date('Y') ?>" required>

    <label>Session</label>
    <select name="session_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($sessions as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- If you already have teacher selection, keep it here -->
    <!--
    <label>Class Teacher</label>
    <select name="teacher_id">
        ...
    </select>
    -->

    <button type="submit">Save Class</button>
</form>
