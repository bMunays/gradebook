<?php
require_once __DIR__ . '/../config/db.php';

$students = $pdo->query("
    SELECT s.*, c.name AS class_name
    FROM students s
    LEFT JOIN enrollments e ON s.id = e.student_id AND e.status = 'active'
    LEFT JOIN classes c ON e.class_id = c.id
    ORDER BY s.surname, s.first_name
")->fetchAll();
?>

<h2>Students</h2>

<a href="index.php?page=students_add" class="btn">+ Add Student</a>

<table class="table">
    <tr>
        <th>Name</th>
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Current Class</th>
        <th>Actions</th>
    </tr>

<?php foreach ($students as $s): ?>
    <?php
    $first_name = isset($s['first_name']) ? (string)$s['first_name'] : '';
    $surname    = isset($s['surname']) ? (string)$s['surname'] : '';
    $gender     = isset($s['gender']) ? (string)$s['gender'] : '';
    $dob        = isset($s['dob']) ? (string)$s['dob'] : '';
    $class_name = isset($s['class_name']) ? (string)$s['class_name'] : '—';
    ?>
    <tr>
        <td><?= htmlspecialchars($surname . ', ' . $first_name, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($gender, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($dob, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($class_name, ENT_QUOTES, 'UTF-8') ?></td>
        <td>
            <a href="index.php?page=students_edit&id=<?= $s['id'] ?>">Edit</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
