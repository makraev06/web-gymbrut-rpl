<?php

session_start();

$_SESSION['role'] = $_SESSION['role'] ?? 'member';
$_SESSION['name'] = $_SESSION['name'] ?? 'Member';

$pageTitle = 'Notifikasi';
$topbarTitle = 'Notifikasi';
$topbarSubtitle = 'Pemberitahuan terbaru untuk akun kamu.';
$searchPlaceholder = 'Cari notifikasi...';

include '../config/database.php';

$userId = (int) ($_SESSION['user_id'] ?? 0);

if (isset($_GET['read_all']) && $userId > 0) {
    $stmt = $conn->prepare("
        UPDATE notifications
        SET is_read = 1
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    header("Location: notifications.php");
    exit;
}

if (isset($_GET['read']) && $userId > 0) {
    $notificationId = (int) $_GET['read'];

    if ($notificationId > 0) {
        $stmt = $conn->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE notification_id = ?
            AND user_id = ?
        ");
        $stmt->bind_param("ii", $notificationId, $userId);
        $stmt->execute();
    }

    header("Location: notifications.php");
    exit;
}

include '../includes/layout_top.php';

/* Ambil notif */
$notifications = [];
$unreadCount = 0;

if ($userId > 0) {
    $stmt = $conn->prepare("
        SELECT 
            notification_id,
            title,
            message,
            type,
            is_read,
            created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 30
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ((int) $row['is_read'] === 0) {
                $unreadCount++;
            }

            $notifications[] = $row;
        }
    }
}

function notifIcon($type)
{
    if ($type === 'payment') {
        return 'bi-wallet2';
    }

    if ($type === 'membership') {
        return 'bi-award';
    }

    if ($type === 'checkin') {
        return 'bi-box-arrow-in-right';
    }

    if ($type === 'progress') {
        return 'bi-graph-up';
    }

    return 'bi-bell';
}
?>

<div class="premium-card">
    <div class="card-header-inline">
        <div>
            <h3 class="section-title">Notifikasi</h3>
            <p class="section-subtitle">
                <?= number_format($unreadCount) ?> notifikasi belum dibaca.
            </p>
        </div>

        <?php if ($unreadCount > 0): ?>
            <a href="notifications.php?read_all=1" class="btn-outline-soft btn-sm">
                <i class="bi bi-check2-all"></i>
                Tandai Semua Dibaca
            </a>
        <?php endif; ?>
    </div>

    <div class="card-list">
        <?php if (empty($notifications)): ?>
            <p class="text-soft mb-0">Belum ada notifikasi.</p>
        <?php endif; ?>

        <?php foreach ($notifications as $notif): ?>
            <?php
            $isUnread = (int) $notif['is_read'] === 0;
            $dateText = !empty($notif['created_at'])
                ? date('d M Y H:i', strtotime($notif['created_at']))
                : '-';
            ?>

            <div class="list-row" style="<?= $isUnread ? 'background:#fff7ef; border-radius:14px; padding:14px;' : '' ?>">
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div class="stat-icon" style="width:42px; height:42px; min-width:42px; font-size:18px;">
                        <i class="bi <?= e(notifIcon($notif['type'])) ?>"></i>
                    </div>

                    <div>
                        <p class="list-row-title">
                            <?= e($notif['title']) ?>
                        </p>

                        <p class="list-row-subtitle">
                            <?= e($notif['message']) ?>
                        </p>

                        <p class="text-soft mb-0">
                            <?= e($dateText) ?>
                        </p>
                    </div>
                </div>

                <?php if ($isUnread): ?>
                    <a href="notifications.php?read=<?= e($notif['notification_id']) ?>" class="btn-outline-soft btn-sm">
                        Dibaca
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>