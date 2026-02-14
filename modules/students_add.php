<?php
require_once __DIR__ . '/../config/db.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO students (first_name, surname, gender, dob)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['first_name'],
        $_POST['surname'],
        $_POST['gender'],
        $_POST['dob'] ?: null
    ]);

    $student_id = $pdo->lastInsertId();

    if (!empty($_POST['class_id'])) {
        $en = $pdo->prepare("
            INSERT INTO enrollments (student_id, class_id, status)
            VALUES (?, ?, 'active')
        ");
        $en->execute([$student_id, $_POST['class_id']]);
    }

    header("Location: index.php?page=students");
    exit;
}
?>

<h2>Add Student</h2>

<form method="POST">
    <label>First Name</label>
    <input type="text" name="first_name" required>

    <label>Surname</label>
    <input type="text" name="surname" required>

    <label>Gender</label>
    <select name="gender">
        <option value="">— Select —</option>
        <option value="M">Male</option>
        <option value="F">Female</option>
    </select>

    <label>Date of Birth</label>
    <input type="date" name="dob">

    <label>Class (optional)</label>
    <select name="class_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Save Student</button>
</form>
