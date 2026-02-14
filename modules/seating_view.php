<?php
require_once __DIR__ . '/../config/db.php';

$plan_id = $_GET['id'] ?? null;

if (!$plan_id) {
    echo "<p>No seating plan selected.</p>";
    return;
}

$stmt = $pdo->prepare("
    SELECT sp.*, c.name AS class_name
    FROM seating_plans sp
    JOIN classes c ON sp.class_id = c.id
    WHERE sp.id = ?
");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    echo "<p>Seating plan not found.</p>";
    return;
}

$layout = json_decode($plan['layout_json'], true) ?: [];

$students = $pdo->prepare("
    SELECT id, first_name, surname
    FROM students
");
$students->execute();
$student_map = [];
foreach ($students->fetchAll() as $s) {
    $student_map[$s['id']] = $s['surname'] . ', ' . $s['first_name'];
}
?>

<h2><?= htmlspecialchars($plan['name']) ?> (<?= htmlspecialchars($plan['class_name']) ?>)</h2>

<div class="seating-grid" style="display:grid; gap:10px; grid-template-columns: repeat(<?= (int)$plan['cols'] ?>, 1fr);">

<?php for ($r = 1; $r <= (int)$plan['rows']; $r++): ?>
    <?php for ($c = 1; $c <= (int)$plan['cols']; $c++): ?>
        <?php $pos = $r . '-' . $c; ?>
        <div class="seat-box">
            <strong>Seat <?= $pos ?></strong><br>
            <?php if (isset($layout[$pos])): ?>
                <?= htmlspecialchars($student_map[$layout[$pos]] ?? 'Unknown') ?>
            <?php else: ?>
                <em>Empty</em>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
<?php endfor; ?>

</div>
