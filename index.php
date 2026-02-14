<?php
// index.php

// Protect all pages
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$allowed_pages = [

    // Dashboard
    'dashboard',

    // Classes
    'classes',
    'classes_add',
    'classes_edit',
    'classes_delete',

    // Students
    'students',
    'students_add',
    'students_edit',
    'students_delete',

    // Attendance
    'attendance',
    'attendance_take',
    'attendance_view',
    'attendance_delete',

    // Assessments & Marks
    'assessments',
    'assessments_add',
    'assessments_edit',
    'assessments_delete',
    'marks',
    'marks_edit',

    // Planning (DLP + Lessons)
    'planning',
    'planning_add',
    'planning_edit',
    'planning_view',
    'planning_delete',
    'lesson_add',
    'lesson_view',
    'lesson_edit',
    'lesson_delete',

    // Resources
    'resources',
    'resources_add',
    'resources_view',
    'resources_delete',

    // Seating Plans
    'seating',
    'seating_create',
    'seating_edit',
    'seating_delete',
    'seating_list',
    'seating_view',


    // Settings
    'settings',
    'settings_profile',
    'settings_display',
    'settings_academic',
    'settings_backup',
    'settings_password',

    // Reports
    'reports',
    'report_student',
    'report_class',
    'report_term',

    // Export (CSV + Print/PDF)
    'export_student_csv',
    'export_student_print',
    'export_class_csv',
    'export_class_print',
    'export_term_csv',
    'export_term_print',
    'export_zimsec_csv',
    'export_zimsec_print',

    // Rubrics
    'rubrics',
    'rubrics_add',
    'rubrics_edit',
    'rubrics_delete',
    'rubric_score',

    // ZIMSEC Projects
    'zimsec',
    'zimsec_add',
    'zimsec_edit',
    'zimsec_delete',
    'zimsec_view',
    'zimsec_score',
    'zimsec_moderate',
    'zimsec_report',

    // Timetable
    'timetable',
    'timetable_edit',
    'timetable_view',                              

];

/**
  * A page is missing from $allowed_pages,
  * OR the filename doesn’t match the page name,
  * OR the module file doesn’t exist,
  *
  * …then the router silently redirects to dashboard.
**/

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/modules/' . $page . '.php';
include __DIR__ . '/includes/footer.php';
