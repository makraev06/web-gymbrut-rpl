<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

$requestUri = $_SERVER['REQUEST_URI'];

$isAdminPage = strpos($requestUri, '/admin/') !== false;
$isMemberPage = strpos($requestUri, '/member/') !== false;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include 'auth_admin.php';
} else {
    include 'auth_user.php';
}

$pageTitle = $pageTitle ?? 'GYMBRUT';
$basePath = ($isAdminPage || $isMemberPage) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="id">
<?php include __DIR__ . '/head.php'; ?>

<body class="<?= e($bodyClass ?? '') ?>">
    <div class="app-shell">
        <?php if ($isAdminPage): ?>
            <?php include __DIR__ . '/sidebar_admin.php'; ?>
        <?php elseif ($isMemberPage): ?>
            <?php include __DIR__ . '/sidebar_member.php'; ?>
        <?php endif; ?>

        <main class="main-content">
            <div class="content-body">
                <?php
                if ($isAdminPage || $isMemberPage) {
                    include __DIR__ . '/topbar.php';
                }
                ?>