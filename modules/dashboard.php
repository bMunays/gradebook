<?php
// modules/dashboard.php
?>
<h2>Dashboard</h2>
<p>Welcome! Select a module from the navigation bar.</p>

<!-- Dashboard with quick stats -->
<div class="card">
    <h3>Total Classes: <?= $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn() ?></h3>
</div>

<div class="card">
    <h3>Total Students: <?= $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() ?></h3>
</div>
