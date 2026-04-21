<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$isAdminPage = ($currentFolder === 'admin');
$isUserPage = ($currentFolder === 'user');

if ($isAdminPage) {
    require_once __DIR__ . '/auth_admin.php';
} elseif ($isUserPage) {
    require_once __DIR__ . '/auth_user.php';
}

$sidebarFile = '';
if ($isAdminPage) {
    $sidebarFile = __DIR__ . '/sidebar_admin.php';
} elseif ($isUserPage) {
    $sidebarFile = __DIR__ . '/sidebar_member.php';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GYMBRUT'; ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap biar row/col/d-flex/gap dll konek -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <?php if ($isAdminPage || $isUserPage): ?>
        <link rel="stylesheet" href="../assets/css/theme.css">
    <?php else: ?>
        <link rel="stylesheet" href="assets/css/theme.css">
    <?php endif; ?>
</head>

<body>

    <div class="page-shell">
        <?php if ($sidebarFile !== '' && file_exists($sidebarFile))
            include $sidebarFile; ?>

        <main class="main-panel">
            <div class="topbar-panel">
                <div>
                    <h1 class="page-heading"><?= $pageTitle ?? 'Dashboard'; ?></h1>
                    <p class="page-subheading">
                        <?= $pageSubtitle ?? 'Selamat datang di GYMBRUT'; ?>
                    </p>
                </div>

                <div class="topbar-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari data gym..." />
                    </div>

                    <button class="icon-btn" type="button">
                        <i class="fas fa-bell"></i>
                    </button>

                    <div class="profile-chip">
                        <div class="profile-chip-avatar">
                            <?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div>
                            <strong><?= htmlspecialchars($_SESSION['name'] ?? 'User'); ?></strong><br>
                            <small
                                class="text-soft"><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? 'member')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>