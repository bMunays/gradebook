<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>No plan selected.</p>";
    return;
}

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS class_name
    FROM plans p
    JOIN classes c ON p.class_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$plan = $stmt->fetch();

if (!$plan) {
    echo "<p>Plan not found.</p>";
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("
        UPDATE plans
        SET week_start = ?, week_end = ?, topic = ?, objectives = ?, notes = ?
        WHERE id = ?
    ")->execute([
        $_POST['week_start'],
        $_POST['week_end'],
        $_POST['topic'],
        $_POST['objectives'],
        $_POST['notes'],
        $id
    ]);

    header("Location: index.php?page=planning&class_id=" . $plan['class_id']);
    exit;
}
?>

<h2>Edit Weekly Plan</h2>

<p>Class: <?= htmlspecialchars($plan['class_name']) ?></p>

<form method="POST">
    <label>Week Start</label>
    <input type="date" name="week_start" value="<?= htmlspecialchars($plan['week_start']) ?>" required>

    <label>Week End</label>
    <input type="date" name="week_end" value="<?= htmlspecialchars($plan['week_end']) ?>" required>

    <label>Topic</label>
    <input type="text" name="topic" value="<?= htmlspecialchars($plan['topic']) ?>" required>

    <label>Objectives</label>
    <textarea name="objectives"><?= htmlspecialchars($plan['objectives']) ?></textarea>

    <label>Notes</label>
    <textarea name="notes"><?= htmlspecialchars($plan['notes']) ?></textarea>

    <button type="submit">Save Plan</button>
</form>
