<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT r.*, c.name AS class_name
    FROM resources r
    LEFT JOIN classes c ON r.class_id = c.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$r = $stmt->fetch();
?>

<h2>Resource Details</h2>

<p><strong>Title:</strong> <?= htmlspecialchars($r['title']) ?></p>
<p><strong>Type:</strong> <?= $r['type'] ?></p>
<p><strong>Class:</strong> <?= htmlspecialchars($r['class_name']) ?></p>
<p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($r['description'])) ?></p>

<?php if ($r['file_path']): ?>
    <p><strong>File:</strong> <a href="<?= $r['file_path'] ?>" target="_blank">Open File</a></p>
<?php endif; ?>
