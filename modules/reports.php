<?php
// modules/reports.php
// Reports home: Student Search + existing report generators

require_once __DIR__ . '/../config/db.php';

// Fetch classes for dropdowns
$classes = $pdo->query("SELECT * FROM classes ORDER BY level, name")->fetchAll();

// Handle student search
$search_results = [];
$search_name    = $_GET['search_name']    ?? '';
$search_id      = $_GET['search_id']      ?? '';
$search_class   = $_GET['search_class']   ?? '';

if ($search_name !== '' || $search_id !== '' || $search_class !== '') {
    $sql = "
        SELECT s.*, c.name AS class_name, e.class_id
        FROM students s
        LEFT JOIN enrollments e ON s.id = e.student_id AND e.status = 'active'
        LEFT JOIN classes c ON e.class_id = c.id
        WHERE 1=1
    ";
    $params = [];

    if ($search_name !== '') {
        $sql .= " AND (s.surname LIKE ? OR s.first_name LIKE ?)";
        $like = '%' . $search_name . '%';
        $params[] = $like;
        $params[] = $like;
    }

    if ($search_id !== '') {
        $sql .= " AND s.id = ?";
        $params[] = $search_id;
    }

    if ($search_class !== '') {
        $sql .= " AND e.class_id = ?";
        $params[] = $search_class;
    }

    $sql .= " ORDER BY s.surname, s.first_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll();
}
?>

<h2>Reports</h2>

<h3>Student Search</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="reports">

    <label>Student Name (partial)</label>
    <input type="text" name="search_name" value="<?= htmlspecialchars($search_name) ?>">

    <label>Student ID</label>
    <input type="number" name="search_id" value="<?= htmlspecialchars($search_id) ?>">

    <label>Class</label>
    <select name="search_class">
        <option value="">— Any —</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $search_class == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Search</button>
</form>

<?php if (!empty($search_results)): ?>
    <h4>Search Results</h4>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Class</th>
            <th>Action</th>
        </tr>
        <?php foreach ($search_results as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['id']) ?></td>
                <td><?= htmlspecialchars($s['surname'] . ', ' . $s['first_name']) ?></td>
                <td><?= htmlspecialchars($s['class_name'] ?? '—') ?></td>
                <td>
                    <?php if (!empty($s['class_id'])): ?>
                        <a href="index.php?page=report_student&student_id=<?= $s['id'] ?>&class_id=<?= $s['class_id'] ?>">
                            View Report
                        </a>
                    <?php else: ?>
                        <span>No active class</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif ($search_name !== '' || $search_id !== '' || $search_class !== ''): ?>
    <p>No students found matching your search.</p>
<?php endif; ?>

<hr>

<?php
// Existing content from your original modules/reports.php kept as-is below
// (Generate Student Report, Class Report, Term Summary)
?>

<h3>Generate Student Report</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="report_student">
    <label>Select Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Student ID</label>
    <input type="number" name="student_id" required>

    <label>Term</label>
    <input type="number" name="term" value="1">

    <button type="submit">Generate</button>
</form>

<hr>

<h3>Generate Class Report</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="report_class">
    <label>Select Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Term</label>
    <input type="number" name="term" value="1">

    <button type="submit">Generate</button>
</form>

<hr>

<h3>Generate Term Summary</h3>
<form method="GET" action="index.php">
    <input type="hidden" name="page" value="report_term">
    <label>Select Class</label>
    <select name="class_id" required>
        <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Term</label>
    <input type="number" name="term" value="1">

    <button type="submit">Generate</button>
</form>
