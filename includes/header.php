<?php
// includes/header.php
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>
<body>

<header>
    <span><?php echo APP_NAME; ?></span>

    <?php if (isset($_SESSION['full_name'])): ?>
        <div class="login-status">
            Logged in as: <?= htmlspecialchars($_SESSION['full_name']) ?>
        </div>
    <?php endif; ?>
</header>

<?php include __DIR__ . '/nav.php'; ?>

<div class="container">
