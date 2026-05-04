<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$userName = $_SESSION['name'] ?? 'Member';
$userRole = 'Member';

$menus = [
    ['file' => 'dashboard.php', 'label' => 'Dashboard', 'icon' => 'bi bi-grid-1x2-fill'],
    ['file' => 'checkin.php', 'label' => 'Check In', 'icon' => 'bi bi-box-arrow-in-right'],
    ['file' => 'memberships.php', 'label' => 'Membership', 'icon' => 'bi bi-award-fill'],
    ['file' => 'payments.php', 'label' => 'Payments', 'icon' => 'bi bi-wallet2'],
    ['file' => 'workouts.php', 'label' => 'Workouts', 'icon' => 'bi bi-heart-pulse-fill'],
    ['file' => 'profile.php', 'label' => 'Profile', 'icon' => 'bi bi-person-circle'],
];
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-logo">
            <img src="/rpl-web/assets/img/Logo%20Gymbrut.svg" alt="Gymbrut Logo">
        </div>
        <div class="sidebar-brand-text">
            <h2>GYMBRUT</h2>
            <p>Member Area</p>
        </div>
    </div>

    <div class="sidebar-title">My Menu</div>

    <nav class="sidebar-nav">
        <?php foreach ($menus as $menu): ?>
            <a href="<?= e($menu['file']) ?>" class="sidebar-link <?= $currentPage === $menu['file'] ? 'active' : '' ?>">
                <i class="<?= e($menu['icon']) ?>"></i>
                <span><?= e($menu['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <?= strtoupper(substr($userName, 0, 1)) ?>
            </div>
            <div class="sidebar-user-info">
                <p class="sidebar-user-name"><?= e($userName) ?></p>
                <p class="sidebar-user-role"><?= e($userRole) ?></p>
            </div>
        </div>

        <a href="../logout.php" class="gradient-btn sidebar-logout"
            onclick="return confirm('Yakin ingin logout dari akun member?');">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>