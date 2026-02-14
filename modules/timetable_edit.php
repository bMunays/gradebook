<?php
require_once __DIR__ . '/../config/db.php';

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    echo "<p>No class selected.</p>";
    return;
}

$stmt = $pdo->prepare("
    SELECT c.*, ts.name AS session_name, ts.id AS session_id, ts.period_code_prefix
    FROM classes c
    LEFT JOIN timetable_sessions ts ON c.session_id = ts.id
    WHERE c.id = ?
");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

if (!$class || !$class['session_id']) {
    echo "<p>Class not found or no session assigned.</p>";
    return;
}

$session_id = $class['session_id'];

$pstmt = $pdo->prepare("
    SELECT * FROM timetable_periods
    WHERE session_id = ?
    ORDER BY start_time
");
$pstmt->execute([$session_id]);
$periods = $pstmt->fetchAll();

$subjects = $pdo->query("
    SELECT * FROM subjects
    WHERE active = 1
    ORDER BY name
")->fetchAll();

$entries = [];
$estmt = $pdo->prepare("
    SELECT * FROM timetable_entries
    WHERE class_id = ? AND session_id = ?
");
$estmt->execute([$class_id, $session_id]);
foreach ($estmt->fetchAll() as $r) {
    $entries[$r['day_of_week']][$r['period_number']] = $r['subject_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("
        DELETE FROM timetable_entries
        WHERE class_id = ? AND session_id = ?
    ")->execute([$class_id, $session_id]);

    if (!empty($_POST['timetable']) && is_array($_POST['timetable'])) {
        $ins = $pdo->prepare("
            INSERT INTO timetable_entries (class_id, session_id, day_of_week, period_number, subject_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($_POST['timetable'] as $day => $periodData) {
            foreach ($periodData as $pn => $subject_id) {
                if ($subject_id !== '') {
                    $ins->execute([
                        $class_id,
                        $session_id,
                        $day,
                        (int)$pn,
                        (int)$subject_id
                    ]);
                }
            }
        }
    }

    echo "<p>Timetable saved.</p>";
    echo '<p><a href="index.php?page=timetable&class_id='.$class_id.'">Back to Timetable</a></p>';
    return;
}

?>

<h2>Edit Timetable: <?= htmlspecialchars($class['name']) ?> (<?= htmlspecialchars($class['session_name']) ?>)</h2>

<form method="POST">
    <table class="table">
        <tr>
            <th>Day / Time</th>
            <?php foreach ($periods as $p): ?>
                <th>
                    <?php
                    $pn = (int)$p['period_number'];
                    if ($pn === -1) {
                        echo 'BREAK';
                    } elseif ($pn === 0) {
                        echo 'Assembly';
                    } else {
                        echo $class['period_code_prefix'] . $pn;
                    }
                    ?><br>
                    <small><?= substr($p['start_time'],0,5) ?>&#x2014;<?= substr($p['end_time'],0,5) ?></small>
                </th>
            <?php endforeach; ?>
        </tr>

        <?php
        $days = ['Mon','Tue','Wed','Thu','Fri'];
        foreach ($days as $d):
        ?>
            <tr>
                <td><strong><?= $d ?></strong></td>
                <?php foreach ($periods as $p): ?>
                    <?php $pn = (int)$p['period_number']; ?>
                    <td>
                        <?php if ($pn === -1): ?>
                            <em>BREAK</em>
                        <?php else: ?>
                            <?php $current = $entries[$d][$pn] ?? ''; ?>
                            <select name="timetable[<?= $d ?>][<?= $pn ?>]">
                                <option value="">&#x2014; None &#x2014;</option>
                                <?php foreach ($subjects as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($current == $s['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['short_code']) ?> &#x2014; <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>

    </table>

    <button type="submit">Save Timetable</button>
</form>
