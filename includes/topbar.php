<?php
$topbarTitle = $topbarTitle ?? 'Dashboard';
$searchPlaceholder = $searchPlaceholder ?? 'Cari data gym...';
$role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? 'admin');
$userName = $_SESSION['user_name'] ?? $_SESSION['name'] ?? 'Gym User';
?>

<header class="topbar-panel">
    <div>
        <h1 class="page-heading"><?= e($topbarTitle) ?></h1>
        <p class="page-subheading">
            <?= $role === 'admin' ? 'Pantau operasional gym dengan tampilan yang lebih rapi.' : 'Lihat progress dan aktivitas gym kamu hari ini.' ?>
        </p>
    </div>

    <div class="topbar-actions">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="<?= e($searchPlaceholder) ?>">
        </div>

        <button class="icon-btn" type="button" title="Notifikasi">
            <i class="bi bi-bell"></i>
        </button>

        <button class="icon-btn" type="button" title="Kalender">
            <i class="bi bi-calendar3"></i>
        </button>

        <div class="profile-chip">
            <div class="profile-chip-avatar">
                <?= strtoupper(substr($userName, 0, 1)); ?>
            </div>
            <div class="text-start">
                <div class="fw-semibold" style="line-height:1.1;"><?= e($userName) ?></div>
                <small class="text-soft"><?= ucfirst($role) ?></small>
            </div>
        </div>
    </div>
</header>