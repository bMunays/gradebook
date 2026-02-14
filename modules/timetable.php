<?php
require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("
    SELECT c.*, ts.name AS session_name
    FROM classes c
    LEFT JOIN timetable_sessions ts ON c.session_id = ts.id
    ORDER BY c.level, c.name
")->fetchAll();

$class_id = $_GET['class_id'] ?? null;
$selected_class = null;
$session = null;
$periods = [];
$entries = [];

if ($class_id) {
    $stmt = $pdo->prepare("
        SELECT c.*, ts.name AS session_name, ts.id AS session_id
        FROM classes c
        LEFT JOIN timetable_sessions ts ON c.session_id = ts.id
        WHERE c.id = ?
    ");
    $stmt->execute([$class_id]);
    $selected_class = $stmt->fetch();

    if ($selected_class && $selected_class['session_id']) {
        $session_id = $selected_class['session_id'];

        $pstmt = $pdo->prepare("
            SELECT * FROM timetable_periods
            WHERE session_id = ?
            ORDER BY start_time
        ");
        $pstmt->execute([$session_id]);
        $periods = $pstmt->fetchAll();

        $estmt = $pdo->prepare("
            SELECT * FROM timetable_entries
            WHERE class_id = ? AND session_id = ?
        ");
        $estmt->execute([$class_id, $session_id]);
        $rows = $estmt->fetchAll();

        foreach ($rows as $r) {
            $entries[$r['day_of_week']][$r['period_number']] = $r;
        }
    }
}
?>

<h2>Timetable</h2>

<form method="GET">
    <input type="hidden" name="page" value="timetable">
    <label>Select Class</label>
    <select name="class_id" onchange="this.form.submit()">
        <option value="">&#x2014; Choose &#x2014;</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $class_id == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
                <?php if ($c['session_name']): ?>
                    (<?= htmlspecialchars($c['session_name']) ?>)
                <?php endif; ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selected_class && $selected_class['session_id']): ?>

    <p>
        Class: <strong><?= htmlspecialchars($selected_class['name']) ?></strong><br>
        Session: <strong><?= htmlspecialchars($selected_class['session_name']) ?></strong>
    </p>

    <p>
        <a href="index.php?page=timetable_edit&class_id=<?= $selected_class['id'] ?>">Edit Timetable</a> |
        <a href="index.php?page=timetable_view&class_id=<?= $selected_class['id'] ?>" target="_blank">Printable View</a>
    </p>

    <?php if (!$periods): ?>
        <p>No periods defined for this session.</p>
    <?php else: ?>

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
                            echo 'P' . $pn;
                        }
                        ?><br>
                        <small>
                            <?= substr($p['start_time'], 0, 5) ?>&#x2014;<?= substr($p['end_time'], 0, 5) ?>
                        </small>
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
                                <?php
                                $cell = $entries[$d][$pn] ?? null;
                                if ($cell && $cell['subject_id']) {
                                    $sstmt = $pdo->prepare("SELECT short_code FROM subjects WHERE id = ?");
                                    $sstmt->execute([$cell['subject_id']]);
                                    $sub = $sstmt->fetchColumn();
                                    echo htmlspecialchars($sub ?: '');
                                } else {
                                    echo '&nbsp;';
                                }
                                ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php endif; ?>

<?php elseif ($class_id && !$selected_class['session_id']): ?>

    <p>This class has no session assigned. Please set session_id in the classes table.</p>

<?php endif; ?>
