<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    echo "<p>No class selected.</p>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

$students = $pdo->prepare("
    SELECT s.*
    FROM students s
    JOIN enrollments e ON s.id = e.student_id
    WHERE e.class_id = ? AND e.status = 'active'
    ORDER BY s.surname, s.first_name
");
$students->execute([$class_id]);
$students = $students->fetchAll();


// ------------------------------------------------------------
// SAVE SEATING PLAN
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat'])) {

    $rows = (int)$_POST['rows'];
    $cols = (int)$_POST['cols'];

    $layout = [];
    foreach ($_POST['seat'] as $pos => $student_id) {
        if ($student_id) {
            $layout[$pos] = (int)$student_id;
        }
    }

    // Determine year
    $year = date('Y');

    // Count existing plans for this class + year
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM seating_plans
        WHERE class_id = ? AND YEAR(created_at) = ?
    ");
    $count_stmt->execute([$class_id, $year]);
    $plan_number = $count_stmt->fetchColumn() + 1;

    // Generate name
    $name = $class['name'] . " – Plan " . $plan_number;

    // Save plan
    $stmt = $pdo->prepare("
        INSERT INTO seating_plans (`class_id`, `name`, `rows`, `cols`, `layout_json`, `created_at`)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $class_id,
        $name,
        $rows,
        $cols,
        json_encode($layout)
    ]);

    echo "<p>Seating plan saved as <strong>$name</strong>.</p>";
    echo '<p><a href="index.php?page=seating_list&class_id='.$class_id.'">Back to Plans</a></p>';
    return;
}

?>

<h2>Create Seating Plan: <?= htmlspecialchars($class['name']) ?></h2>

<form method="POST">
    <label>Rows</label>
    <input type="number" name="rows" value="3" min="1">

    <label>Columns</label>
    <input type="number" name="cols" value="4" min="1">

    <button type="submit">Generate Grid</button>
</form>

<?php
// ------------------------------------------------------------
// GENERATE GRID
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rows'], $_POST['cols']) && !isset($_POST['seat'])) {

    $rows = (int)$_POST['rows'];
    $cols = (int)$_POST['cols'];

    if ($rows < 1 || $cols < 1) {
        echo "<p>Invalid grid size.</p>";
        return;
    }
?>

<h3>Assign Students to Seats</h3>

<form method="POST">
    <input type="hidden" name="rows" value="<?= $rows ?>">
    <input type="hidden" name="cols" value="<?= $cols ?>">

    <div class="seating-grid" style="display:grid; gap:10px; grid-template-columns: repeat(<?= $cols ?>, 1fr);">

        <?php for ($r = 1; $r <= $rows; $r++): ?>
            <?php for ($c = 1; $c <= $cols; $c++): ?>
                <?php $pos = $r . '-' . $c; ?>
                <div>
                    <strong>Seat <?= $pos ?></strong><br>
                    <select name="seat[<?= $pos ?>]">
                        <option value="">&#x2014; Empty &#x2014;</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endfor; ?>
        <?php endfor; ?>

    </div>

    <button type="submit">Save Seating Plan</button>
</form>

<?php } ?>
