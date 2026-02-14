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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $session_stmt = $pdo->prepare("
        INSERT INTO attendance_sessions (class_id, session_date, period)
        VALUES (?, ?, ?)
    ");
    $session_stmt->execute([
        $class_id,
        $_POST['session_date'],
        $_POST['period']
    ]);

    $session_id = $pdo->lastInsertId();

    foreach ($_POST['status'] as $student_id => $status) {
        $rec = $pdo->prepare("
            INSERT INTO attendance_records (session_id, student_id, status)
            VALUES (?, ?, ?)
        ");
        $rec->execute([$session_id, $student_id, $status]);
    }

    echo "<p>Attendance saved.</p>";
}
?>

<h2>Take Attendance: <?= htmlspecialchars($class['name']) ?></h2>

<form method="POST">
    <label>Date</label>
    <input type="date" name="session_date" value="<?= date('Y-m-d') ?>">

    <label>Period / Lesson</label>
    <input type="text" name="period">

    <?php if (!$students): ?>
        <p>No students enrolled in this class.</p>
    <?php else: ?>

    <table class="table">
        <tr>
            <th>Student</th>
            <th>Status</th>
        </tr>

        <?php foreach ($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>
                <td>
                    <select name="status[<?= $s['id'] ?>]">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                    </select>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit">Save Attendance</button>

    <?php endif; ?>
</form>
