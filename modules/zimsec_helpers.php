<?php
function zimsec_teacher_total($pdo, $project_id, $student_id) {
    $stmt = $pdo->prepare("
        SELECT SUM(z.score * c.weight) AS total
        FROM zimsec_project_scores z
        JOIN zimsec_project_components c ON z.component_id = c.id
        WHERE z.project_id = ? AND z.student_id = ?
    ");
    $stmt->execute([$project_id, $student_id]);
    return $stmt->fetchColumn() ?: 0;
}

function zimsec_moderated_total($pdo, $project_id, $student_id) {
    $stmt = $pdo->prepare("
        SELECT moderated_total
        FROM zimsec_project_moderation
        WHERE project_id = ? AND student_id = ?
    ");
    $stmt->execute([$project_id, $student_id]);
    return $stmt->fetchColumn() ?: null;
}
