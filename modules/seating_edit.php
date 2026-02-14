<?php
// Ensure the page supports icons and proper character encoding via headers
header('Content-Type: text/html; charset=utf-8');
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

$class_id = $plan['class_id'];

$students = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname, s.first_name
");
$students->execute([$class_id]);
$students = $students->fetchAll();

$layout = json_decode($plan['layout_json'], true) ?: [];

// ------------------------------------------------------------
// SAVE UPDATED PLAN
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat'])) {

    $rows = (int)$_POST['rows'];
    $cols = (int)$_POST['cols'];

    $new_layout = [];
    foreach ($_POST['seat'] as $pos => $student_id) {
        if ($student_id) {
            $new_layout[$pos] = (int)$student_id;
        }
    }

    // FIXED: Wrapped `rows` and `cols` in backticks to resolve the SQL Syntax Error (1064)
    $pdo->prepare("
        UPDATE seating_plans
        SET `rows` = ?, `cols` = ?, layout_json = ?
        WHERE id = ?
    ")->execute([
        $rows,
        $cols,
        json_encode($new_layout),
        $plan_id
    ]);

    echo "<p>Seating plan updated.</p>";
    echo '<p><a href="index.php?page=seating_list&class_id='.$class_id.'">Back to Plans</a></p>';

    // Update local variables for re-render
    $layout = $new_layout;
    $plan['rows'] = $rows;
    $plan['cols'] = $cols;

    return;
}

?>

<!-- Link to external CSS file -->
<link rel="stylesheet" href="/gradebook/public/css/style.css">

<h2>Edit Seating Plan: <?= htmlspecialchars($plan['name']) ?> (<?= htmlspecialchars($plan['class_name']) ?>)</h2>

<!-- NEW FEATURE: Student Counter and Action Buttons -->
<div style="margin-bottom: 20px; background: #eee; padding: 15px; border-radius: 5px;">
    <div style="margin-bottom: 10px; font-weight: bold;">
        Students Seated: <span id="student-count">0</span> / <?= count($students) ?>
    </div>
    <button type="button" onclick="shufflePlan()">&#128256; Shuffle Students</button>
    <button type="button" onclick="resetPlan()">&#129529; Reset All Seats</button>
</div>

<form method="POST">

    <label>Rows</label>
    <input type="number" name="rows" value="<?= (int)$plan['rows'] ?>" min="1">

    <label>Columns</label>
    <input type="number" name="cols" value="<?= (int)$plan['cols'] ?>" min="1">

    <h3>Assign Students to Seats</h3>

    <div class="seating-grid" style="display:grid; gap:10px; grid-template-columns: repeat(<?= (int)$plan['cols'] ?>, 1fr);">

        <?php for ($r = 1; $r <= (int)$plan['rows']; $r++): ?>
            <?php for ($c = 1; $c <= (int)$plan['cols']; $c++): ?>
                <?php $pos = $r . '-' . $c; ?>
                <!-- Added seat-container class for color-coding support -->
                <div class="seat-container">
                    <strong>Seat <?= $pos ?></strong><br>
                    <select name="seat[<?= $pos ?>]" class="seat-select">
                        <option value="">&#8208 Empty &#8208</option>

                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>"
                                <?= (isset($layout[$pos]) && $layout[$pos] == $s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>
            <?php endfor; ?>
        <?php endfor; ?>

    </div>

    <hr>
    <button type="submit">Save Changes</button>
</form>

<!-- Link to external JS file -->
<script src="/gradebook/public/js/app.js"></script>

<!-- Link to external CSS file -->
<link rel="stylesheet" href="/gradebook/public/css/style.css">