<?php
require_once __DIR__ . '/../config/db.php';

$session_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT a.*, c.name AS class_name
    FROM attendance_sessions a
    JOIN classes c ON a.class_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$session_id]);
$session = $stmt->fetch();

$records = $pdo->prepare("
    SELECT s.first_name, s.surname, r.status
    FROM attendance_records r
    JOIN students s ON r.student_id = s.id
    WHERE r.session_id = ?
    ORDER BY s.surname
");
$records->execute([$session_id]);
?>

<h2>Attendance for <?= $session['class_name'] ?> — <?= $session['session_date'] ?></h2>

<table class="table">
    <tr>
        <th>Student</th>
        <th>Status</th>
    </tr>

<?php foreach ($records as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['surname'] . ', ' . $r['first_name']) ?></td>
        <td><?= ucfirst($r['status']) ?></td>
    </tr>
<?php endforeach; ?>
</table>
