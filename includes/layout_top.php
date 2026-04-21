<?php
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentDir === 'admin' || $currentDir === 'user') ? '../' : '';

include $basePath . 'includes/auth_check.php';
include $basePath . 'config/database.php';
?>
<!doctype html>
<html lang="en">
<?php include $basePath . 'includes/head.php'; ?>

<body>
    <div class="page-shell">
        <?php include $basePath . 'includes/sidebar.php'; ?>
        <div class="main-panel">
            <?php include $basePath . 'includes/topbar.php'; ?>