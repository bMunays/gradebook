<?php
require_once __DIR__ . '/../config/db.php';

$project_id = $_GET['id'];

// Delete moderation
$pdo->prepare("DELETE FROM zimsec_project_moderation WHERE project_id = ?")
    ->execute([$project_id]);

// Delete scores
$pdo->prepare("DELETE FROM zimsec_project_scores WHERE project_id = ?")
    ->execute([$project_id]);

// Delete components
$pdo->prepare("DELETE FROM zimsec_project_components WHERE project_id = ?")
    ->execute([$project_id]);

// Delete project
$pdo->prepare("DELETE FROM zimsec_projects WHERE id = ?")
    ->execute([$project_id]);

header("Location: index.php?page=zimsec");
exit;
