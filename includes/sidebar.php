<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
$activePage = $activePage ?? 'dashboard';
$role = strtolower($_SESSION['user_role'] ?? 'admin');
function nav_link($key, $href, $icon, $label, $activePage)
{
    $active = $key === $activePage ? 'active' : '';
    echo "<a href=\"{$href}\" class=\"sidebar-link {$active}\"><i class=\"bi {$icon}\"></i><span>{$label}</span></a>";
}
?>
<aside class="sidebar-panel">
    <div class="brand-box">
        <div class="brand-icon">🔥</div>
        <div>
            <div class="brand-title">GYMBRUT</div>
            <div class="brand-subtitle">Fitness SaaS Premium</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php nav_link('dashboard', 'dashboard.php', 'bi-grid-1x2-fill', 'Dashboard', $activePage); ?>
        <?php nav_link('members', 'members.php', 'bi-people-fill', 'Members', $activePage); ?>
        <?php nav_link('memberships', 'memberships.php', 'bi-credit-card-2-front-fill', 'Membership', $activePage); ?>
        <?php nav_link('payments', 'payments.php', 'bi-cash-coin', 'Payments', $activePage); ?>
        <?php nav_link('checkin', 'checkin.php', 'bi-geo-alt-fill', 'Check-in', $activePage); ?>
        <?php nav_link('workouts', 'workouts.php', 'bi-bicycle', 'Workout', $activePage); ?>
        <?php nav_link('progress', 'progress.php', 'bi-graph-up-arrow', 'Progress', $activePage); ?>
        <?php nav_link('reports', 'reports.php', 'bi-file-earmark-bar-graph-fill', 'Reports', $activePage); ?>
        <?php nav_link('profile', 'profile.php', 'bi-gear-fill', 'Settings', $activePage); ?>
        <?php nav_link('logout', 'login.php', 'bi-box-arrow-right', 'Logout', $activePage); ?>
    </nav>

    <div class="sidebar-user glass-soft">
        <img src="<?= e($_SESSION['avatar'] ?? 'https://ui-avatars.com/api/?name=GYMBRUT&background=ff7a00&color=fff') ?>"
            alt="avatar">
        <div>
            <div class="fw-semibold text-white">
                <?= e($_SESSION['name'] ?? 'Guest User') ?>
            </div>
            <small class="text-white-50 text-uppercase">
                <?= e($_SESSION['user_role'] ?? 'member') ?>
            </small>
        </div>
    </div>
</aside>