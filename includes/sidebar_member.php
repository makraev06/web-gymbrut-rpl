<?php
/* ==========================================
   includes/sidebar_member.php
   Sidebar sesuai folder user Anda
   ========================================== */
?>

<aside class="sidebar-panel">

    <!-- Brand -->
    <div class="brand-box">
        <div class="brand-icon">
            <i class="fas fa-dumbbell"></i>
        </div>

        <div>
            <div class="brand-title">GYMBRUT</div>
            <div class="brand-subtitle">MEMBER AREA</div>
        </div>
    </div>

    <!-- MAIN -->
    <div class="sidebar-label">MAIN MENU</div>

    <nav class="sidebar-nav">

        <!-- Dashboard -->
        <a href="dashboard.php"
            class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>

        <!-- Check In -->
        <a href="checkin.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'checkin.php' ? 'active' : '' ?>">
            <i class="fas fa-door-open"></i>
            <span>Check In</span>
        </a>

        <!-- Membership -->
        <a href="memberships.php"
            class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'memberships.php' ? 'active' : '' ?>">
            <i class="fas fa-id-card"></i>
            <span>Membership</span>
        </a>

        <!-- Payments -->
        <a href="payments.php"
            class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i>
            <span>Payments</span>
        </a>

        <!-- Workouts -->
        <a href="workouts.php"
            class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'workouts.php' ? 'active' : '' ?>">
            <i class="fas fa-dumbbell"></i>
            <span>Workouts</span>
        </a>

        <!-- Progress -->
        <a href="progress.php"
            class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'progress.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Progress</span>
        </a>

        <!-- Reports -->
        <a href="reports.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
            <i class="fas fa-file-lines"></i>
            <span>Reports</span>
        </a>

        <!-- Profile -->
        <a href="profile.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>

    </nav>

    <!-- ACCOUNT -->
    <div class="sidebar-label mt-4">ACCOUNT</div>

    <div class="sidebar-user">

        <div class="d-flex align-items-center gap-3">

            <div class="sidebar-user-avatar">
                <?= strtoupper(substr($_SESSION['name'] ?? 'M', 0, 1)); ?>
            </div>

            <div>
                <strong>
                    <?= htmlspecialchars($_SESSION['name'] ?? 'Member'); ?>
                </strong><br>
                <small>Gym Member</small>
            </div>

        </div>

        <a href="../logout.php" class="gradient-btn small-btn sidebar-logout w-100 mt-3">
            <i class="fas fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</aside>