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

$entries = [];
$estmt = $pdo->prepare("
    SELECT te.*, s.short_code
    FROM timetable_entries te
    LEFT JOIN subjects s ON te.subject_id = s.id
    WHERE te.class_id = ? AND te.session_id = ?
");
$estmt->execute([$class_id, $session_id]);
foreach ($estmt->fetchAll() as $r) {
    $entries[$r['day_of_week']][$r['period_number']] = $r['short_code'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable - <?= htmlspecialchars($class['name']) ?></title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Timetable: <?= htmlspecialchars($class['name']) ?> (<?= htmlspecialchars($class['session_name']) ?>)</h2>

<table>
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
                        <?= htmlspecialchars($entries[$d][$pn] ?? '') ?>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>

</table>

</body>
</html>
