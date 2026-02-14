<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>No class selected.</p>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$id]);
$class = $stmt->fetch();

if (!$class) {
    echo "<p>Class not found.</p>";
    return;
}

$sessions = $pdo->query("
    SELECT id, name
    FROM timetable_sessions
    ORDER BY id
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name       = $_POST['name'] ?? '';
    $level      = $_POST['level'] ?? '';
    $year       = (int)($_POST['year'] ?? $class['year']);
    $session_id = $_POST['session_id'] !== '' ? (int)$_POST['session_id'] : null;

    // No teacher field in the form yet ? keep existing or NULL
    $teacher_id = $class['teacher_id'] ?? null;

    $upd = $pdo->prepare("
        UPDATE classes
        SET name = ?, session_id = ?, level = ?, year = ?, teacher_id = ?
        WHERE id = ?
    ");
    $upd->execute([
        $name,
        $session_id,
        $level,
        $year,
        $teacher_id,
        $id
    ]);

    echo "<p>Class updated.</p>";
    echo '<p><a href="index.php?page=classes">Back to Classes</a></p>';
    return;
}

?>

<h2>Edit Class</h2>

<form method="POST">
    <label>Class Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($class['name']) ?>" required>

    <label>Level</label>
    <input type="text" name="level" value="<?= htmlspecialchars($class['level']) ?>">

    <label>Year</label>
    <input type="number" name="year" value="<?= htmlspecialchars($class['year']) ?>" required>

    <label>Session</label>
    <select name="session_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($sessions as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($class['session_id'] == $s['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Keep your existing teacher selection if present -->
    <!--
    <label>Class Teacher</label>
    <select name="teacher_id">
        ...
    </select>
    -->

    <button type="submit">Save Changes</button>
</form>
