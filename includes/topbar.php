<?php
$topbarTitle = $topbarTitle ?? ($pageTitle ?? 'Dashboard');
$topbarSubtitle = $topbarSubtitle ?? '';
$searchPlaceholder = $searchPlaceholder ?? 'Cari...';

$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'member';

$initial = strtoupper(substr($userName, 0, 1));

$topbarPhoto = '../assets/img/default-avatar.svg';

if (isset($conn) && !empty($_SESSION['user_id'])) {
    $topbarUserId = (int) $_SESSION['user_id'];

    $stmtPhoto = $conn->prepare("SELECT photo FROM users WHERE user_id = ? LIMIT 1");
    $stmtPhoto->bind_param("i", $topbarUserId);
    $stmtPhoto->execute();
    $photoResult = $stmtPhoto->get_result()->fetch_assoc();

    if ($photoResult && !empty($photoResult['photo'])) {
        $topbarPhoto = '../assets/uploads/profile/' . $photoResult['photo'];
    }
}

$notifCount = 0;
$notifications = [];

if ($userRole === 'member' && isset($conn)) {
    $notifUserId = (int) ($_SESSION['user_id'] ?? 0);

    if ($notifUserId > 0) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM notifications
            WHERE user_id = ?
            AND is_read = 0
        ");
        $stmt->bind_param("i", $notifUserId);
        $stmt->execute();
        $countResult = $stmt->get_result();

        if ($countResult && $countResult->num_rows > 0) {
            $notifCount = (int) $countResult->fetch_assoc()['total'];
        }

        $stmt = $conn->prepare("
            SELECT notification_id, title, message, type, is_read, created_at
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $stmt->bind_param("i", $notifUserId);
        $stmt->execute();
        $notifResult = $stmt->get_result();

        if ($notifResult) {
            while ($row = $notifResult->fetch_assoc()) {
                $notifications[] = $row;
            }
        }
    }
}
?>

<div class="topbar">
    <div class="topbar-left">
        <h1><?= e($topbarTitle) ?></h1>

        <?php if (!empty($topbarSubtitle)): ?>
        <p><?= e($topbarSubtitle) ?></p>
        <?php endif; ?>
    </div>

    <div class="topbar-right">
        <div class="topbar-search topbar-search-clean" style="
        width: 360px;
        height: 54px;
        min-height: 54px;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: 18px;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 16px;
        box-sizing: border-box;
    ">
            <i class="bi bi-search" style="font-size:20px; color:#334155;"></i>

            <span class="topbar-search-placeholder" style="
            color: #334155;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        ">
                <?= e($searchPlaceholder) ?>
            </span>
        </div>

        <?php if ($userRole === 'member'): ?>
        <div class="notif-wrapper">
            <a href="../member/notifications.php" class="icon-btn notif-toggle">
                <i class="bi bi-bell"></i>

                <?php if ($notifCount > 0): ?>
                <span class="notif-badge"><?= e($notifCount) ?></span>
                <?php endif; ?>
            </a>
        </div>
        <?php else: ?>
        <button type="button" class="icon-btn">
            <i class="bi bi-bell"></i>
        </button>
        <?php endif; ?>

        <div class="profile-chip">
            <span class="profile-chip-avatar profile-chip-avatar-img">
                <img src="<?= e($topbarPhoto) ?>" alt="Foto Profile">
            </span>

            <div>
                <strong><?= e($userName) ?></strong>
                <small><?= e(ucfirst($userRole)) ?></small>
            </div>
        </div>
    </div>
</div>

</script>