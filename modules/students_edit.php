<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

// Fetch student
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    echo "<p>Student not found.</p>";
    return;
}

// Safe defaults
$first_name = isset($student['first_name']) ? (string)$student['first_name'] : '';
$surname    = isset($student['surname']) ? (string)$student['surname'] : '';
$gender     = isset($student['gender']) ? (string)$student['gender'] : '';
$dob        = isset($student['dob']) ? (string)$student['dob'] : '';

// Fetch classes
$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();

// Fetch current class
$en = $pdo->prepare("
    SELECT class_id FROM enrollments
    WHERE student_id = ? AND status = 'active'
");
$en->execute([$id]);
$current_class_id = $en->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->prepare("
        UPDATE students
        SET first_name=?, surname=?, gender=?, dob=?
        WHERE id=?
    ")->execute([
        $_POST['first_name'],
        $_POST['surname'],
        $_POST['gender'] ?: null,
        $_POST['dob'] ?: null,
        $id
    ]);

    // Reset enrollments
    $pdo->prepare("
        UPDATE enrollments
        SET status='inactive'
        WHERE student_id=?
    ")->execute([$id]);

    // Assign new class if provided
    if (!empty($_POST['class_id'])) {
        $pdo->prepare("
            INSERT INTO enrollments (student_id, class_id, status)
            VALUES (?, ?, 'active')
        ")->execute([$id, $_POST['class_id']]);
    }

    header("Location: index.php?page=students");
    exit;
}
?>

<h2>Edit Student</h2>

<form method="POST">

    <label>First Name</label>
    <input type="text" name="first_name"
           value="<?= htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8') ?>" required>

    <label>Surname</label>
    <input type="text" name="surname"
           value="<?= htmlspecialchars($surname, ENT_QUOTES, 'UTF-8') ?>" required>

    <label>Gender</label>
    <select name="gender">
        <option value="">— Select —</option>
        <option value="M" <?= $gender === 'M' ? 'selected' : '' ?>>Male</option>
        <option value="F" <?= $gender === 'F' ? 'selected' : '' ?>>Female</option>
    </select>

    <label>Date of Birth</label>
    <input type="date" name="dob"
           value="<?= htmlspecialchars($dob, ENT_QUOTES, 'UTF-8') ?>">

    <label>Current Class</label>
    <select name="class_id">
        <option value="">&#x2014; None &#x2014;</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"
                <?= ($c['id'] == $current_class_id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Save Changes</button>
</form>
