<?php
$topbarTitle = $topbarTitle ?? 'Dashboard';
$searchPlaceholder = $searchPlaceholder ?? 'Cari member, pembayaran, workout...';
?>
<header class="topbar-panel">
    <div>
        <h1 class="page-heading mb-1">
            <?= e($topbarTitle) ?>
        </h1>
        <p class="page-subheading mb-0">NO PAIN NO GAIN 💪</p>
    </div>
    <div class="topbar-actions">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="<?= e($searchPlaceholder) ?>">
        </div>
        <button class="icon-btn"><i class="bi bi-bell-fill"></i></button>
        <button class="gradient-btn small-btn"><i class="bi bi-lightning-charge-fill"></i> Upgrade Energy</button>
    </div>
</header>