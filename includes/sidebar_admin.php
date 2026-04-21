<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar-panel">
    <div class="brand-box">
        <div class="brand-icon">
            <i class="fas fa-dumbbell"></i>
        </div>
        <div>
            <div class="brand-title">GYMBRUT</div>
            <div class="brand-subtitle">ADMIN PANEL</div>
        </div>
    </div>

    <div class="sidebar-label">MAIN MENU</div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>

        <a href="members.php" class="sidebar-link <?= $currentPage === 'members.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Data Member</span>
        </a>

        <a href="membership.php" class="sidebar-link <?= $currentPage === 'membership.php' ? 'active' : '' ?>">
            <i class="fas fa-id-card"></i>
            <span>Membership</span>
        </a>

        <a href="payments.php" class="sidebar-link <?= $currentPage === 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i>
            <span>Payments</span>
        </a>

        <a href="profile.php" class="sidebar-link <?= $currentPage === 'profile.php' ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>

        <a href="reports.php" class="sidebar-link <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
            <i class="fas fa-file-lines"></i>
            <span>Reports</span>
        </a>

        <a href="workouts.php" class="sidebar-link <?= $currentPage === 'workouts.php' ? 'active' : '' ?>">
            <i class="fas fa-dumbbell"></i>
            <span>Workouts</span>
        </a>
    </nav>

    <div class="sidebar-label mt-4">ACCOUNT</div>

    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-3">
            <div class="sidebar-user-avatar">
                <?= strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1)); ?>
            </div>
            <div>
                <strong><?= htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></strong><br>
                <small>Administrator</small>
            </div>
        </div>

        <a href="../logout.php" class="gradient-btn small-btn sidebar-logout w-100 mt-3">
            <i class="fas fa-right-from-bracket"></i>
            Logout
        </a>
    </div>
</aside>