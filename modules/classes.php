<?php
require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();
?>

<h2>Classes</h2>

<a href="index.php?page=classes_add" class="btn">+ Add Class</a>

<table class="table">
        <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Actions</th>
        </tr>

<?php foreach ($classes as $c): ?>
        <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['level']) ?></td>
                <td>
                        <a href="index.php?page=classes_edit&id=<?= $c['id'] ?>">Edit</a> |
                        <a href="index.php?page=classes_delete&id=<?= $c['id'] ?>" onclick="return confirm('Delete class?')">Delete</a> |
                        <a href="index.php?page=classes&id=<?= $c['id'] ?>">View Students</a>
                </td>
        </tr>
<?php endforeach; ?>
</table>

<?php
if (isset($_GET['id'])) {
        $class_id = $_GET['id'];

        $stmt = $pdo->prepare("
                SELECT s.*
                FROM students s
                JOIN enrollments e ON s.id = e.student_id
                WHERE e.class_id = ? AND e.status = 'active'
                ORDER BY s.surname, s.first_name
        ");
        $stmt->execute([$class_id]);
        $students = $stmt->fetchAll();

        echo "<h3>Students in this Class</h3>";

        if (!$students) {
                echo "<p>No students enrolled in this class yet.</p>";
        } else {
                echo '<table class="table">
                                <tr>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                </tr>';
                foreach ($students as $s) {
                        echo '<tr>
                                        <td>'.htmlspecialchars($s['surname'].', '.$s['first_name']).'</td>
                                        <td>'.htmlspecialchars($s['gender'] ?? '').'</td>
                                        <td>'.htmlspecialchars($s['dob'] ?? '').'</td>
                                    </tr>';
                }
                echo '</table>';
        }
}
?>
