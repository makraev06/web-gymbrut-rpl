<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPath = $_SERVER['REQUEST_URI'] ?? '';

$userName = $_SESSION['name'] ?? 'Admin';
$userRole = 'Administrator';

/* BASE URL PROJECT */
$base = '/rpl-web';

/* MENU */
$menus = [
    [
        'url' => $base . '/admin/dashboard.php',
        'key' => '/admin/dashboard.php',
        'label' => 'Dashboard',
        'icon' => 'bi bi-grid-1x2-fill'
    ],
    [
        'url' => $base . '/admin/member/members.php',
        'key' => '/admin/member/',
        'label' => 'Members',
        'icon' => 'bi bi-people-fill'
    ],
    [
        'url' => $base . '/admin/membership/memberships.php',
        'key' => '/admin/membership/',
        'label' => 'Memberships',
        'icon' => 'bi bi-card-checklist'
    ],
    [
        'url' => $base . '/admin/payments/payments.php',
        'key' => '/admin/payments/',
        'label' => 'Payments',
        'icon' => 'bi bi-credit-card-2-front-fill'
    ],
    [
        'url' => $base . '/admin/reports.php',
        'key' => '/admin/reports.php',
        'label' => 'Reports',
        'icon' => 'bi bi-bar-chart-fill'
    ],
    [
        'url' => $base . '/admin/workout/workouts.php',
        'key' => '/admin/workout/',
        'label' => 'Workouts',
        'icon' => 'bi bi-fire'
    ],
    [
        'url' => $base . '/admin/profile.php',
        'key' => '/admin/profile.php',
        'label' => 'Profile',
        'icon' => 'bi bi-person-circle'
    ],
];
?>

<aside class="sidebar">

    <!-- LOGO -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-logo">
            <img src="/rpl-web/assets/img/Logo%20Gymbrut.svg" alt="Gymbrut Logo">
        </div>
        <div class="sidebar-brand-text">
            <h2>GYMBRUT</h2>
            <p>Admin Panel</p>
        </div>
    </div>

    <div class="sidebar-title">Main Menu</div>

    <!-- MENU -->
    <nav class="sidebar-nav">
        <?php foreach ($menus as $menu): ?>
            <?php $active = strpos($currentPath, $menu['key']) !== false; ?>

            <a href="<?= htmlspecialchars($menu['url']) ?>" class="sidebar-link <?= $active ? 'active' : '' ?>">

                <i class="<?= htmlspecialchars($menu['icon']) ?>"></i>
                <span><?= htmlspecialchars($menu['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- FOOTER -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <?= strtoupper(substr($userName, 0, 1)) ?>
            </div>

            <div class="sidebar-user-info">
                <p class="sidebar-user-name"><?= htmlspecialchars($userName) ?></p>
                <p class="sidebar-user-role"><?= htmlspecialchars($userRole) ?></p>
            </div>
        </div>

        <!-- LOGOUT -->
        <a href="<?= $base ?>/logout.php" class="gradient-btn sidebar-logout"
            onclick="return confirm('Yakin ingin logout dari akun admin?');">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>