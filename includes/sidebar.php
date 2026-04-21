<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$activePage = $activePage ?? 'dashboard';
$role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? 'admin');

$currentDir = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentDir === 'admin' || $currentDir === 'user') ? '../' : '';

function nav_link($key, $href, $icon, $label, $activePage, $basePath = '')
{
    $active = $key === $activePage ? 'active' : '';
    echo '<a href="' . $basePath . $href . '" class="sidebar-link ' . $active . '">';
    echo '<i class="bi ' . $icon . '"></i>';
    echo '<span>' . $label . '</span>';
    echo '</a>';
}
?>

<aside class="sidebar-panel">
    <div class="brand-box">
        <div class="brand-icon"><i class="bi bi-fire"></i></div>
        <div>
            <div class="brand-title">GYMBRUT</div>
            <div class="brand-subtitle">Fitness Management</div>
        </div>
    </div>

    <div class="sidebar-label">Main Menu</div>
    <nav class="sidebar-nav">
        <?php nav_link('dashboard', 'dashboard.php', 'bi-grid-1x2-fill', 'Dashboard', $activePage); ?>

        <?php if ($role === 'admin'): ?>
            <?php nav_link('members', 'members.php', 'bi-people-fill', 'Members', $activePage); ?>
            <?php nav_link('memberships', 'memberships.php', 'bi-credit-card-2-front-fill', 'Membership', $activePage); ?>
            <?php nav_link('payments', 'payments.php', 'bi-cash-coin', 'Payments', $activePage); ?>
            <?php nav_link('workouts', 'workouts.php', 'bi-bicycle', 'Workout', $activePage); ?>
            <?php nav_link('reports', 'reports.php', 'bi-file-earmark-bar-graph-fill', 'Reports', $activePage); ?>
            <?php nav_link('profile', 'profile.php', 'bi-person-circle', 'Profile', $activePage); ?>
        <?php else: ?>
            <?php nav_link('memberships', 'memberships.php', 'bi-credit-card-2-front-fill', 'Membership', $activePage); ?>
            <?php nav_link('payments', 'payments.php', 'bi-wallet2', 'Payments', $activePage); ?>
            <?php nav_link('checkin', 'checkin.php', 'bi-geo-alt-fill', 'Check-in', $activePage); ?>
            <?php nav_link('workouts', 'workouts.php', 'bi-activity', 'Workout Plan', $activePage); ?>
            <?php nav_link('progress', 'progress.php', 'bi-graph-up-arrow', 'Progress', $activePage); ?>
            <?php nav_link('reports', 'reports.php', 'bi-bar-chart-line-fill', 'Reports', $activePage); ?>
            <?php nav_link('profile', 'profile.php', 'bi-person-circle', 'Profile', $activePage); ?>
        <?php endif; ?>
    </nav>

    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-3">
            <div class="sidebar-user-avatar">
                <?= strtoupper(substr($_SESSION['user_name'] ?? $_SESSION['name'] ?? 'G', 0, 1)); ?>
            </div>
            <div>
                <div class="fw-semibold">
                    <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['name'] ?? 'Gym User'); ?></div>
                <small><?= ucfirst($role); ?></small>
            </div>
        </div>

        <div class="sidebar-logout d-grid">
            <a href="<?= $basePath; ?>logout.php" class="btn btn-light border rounded-4 py-2">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </a>
        </div>
    </div>
</aside>