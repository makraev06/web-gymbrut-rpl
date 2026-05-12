<?php
/* member/dashboard.php */
session_start();

$_SESSION['role'] = $_SESSION['role'] ?? 'member';
$_SESSION['name'] = $_SESSION['name'] ?? 'Member';

$pageTitle = 'Dashboard Member';
$topbarTitle = 'Dashboard Member';
$topbarSubtitle = 'Lihat ringkasan membership, pembayaran, dan aktivitas gym kamu.';
$searchPlaceholder = 'Cari membership, pembayaran, progress...';

include '../includes/layout_top.php';

$memberName = $_SESSION['name'] ?? 'Member';
$userId = (int) ($_SESSION['user_id'] ?? 0);

/* =========================
   DATA USER UNTUK MEMBER CARD
========================= */
$memberUser = [
    'email' => '',
    'height' => null,
    'weight' => null,
    'photo' => null
];

if ($conn && $userId > 0) {
    $stmtUser = $conn->prepare("
        SELECT email, height, weight, photo
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ");
    if ($stmtUser) {
        $stmtUser->bind_param("i", $userId);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();
        if ($resultUser && $resultUser->num_rows > 0) {
            $memberUser = $resultUser->fetch_assoc();
        }
    }
}

$memberEmail = $memberUser['email'] ?? '';
$memberHeight = !empty($memberUser['height'])
    ? number_format((float) $memberUser['height'], 1, ',', '.') . ' cm'
    : '-';
$memberWeight = !empty($memberUser['weight'])
    ? number_format((float) $memberUser['weight'], 1, ',', '.') . ' kg'
    : '-';
$memberPhoto = !empty($memberUser['photo'])
    ? '../assets/uploads/profile/' . $memberUser['photo']
    : '../assets/img/default-avatar.svg';

/* =========================
   MEMBERSHIP TERBARU
========================= */
$membership = [
    'membership_id' => null,
    'package_name' => 'Belum Ada',
    'status' => 'pending',
    'end_date' => null,
    'price' => 0
];

$q = $conn->query("
    SELECT 
        m.membership_id,
        m.status,
        m.end_date,
        mp.package_name,
        mp.price
    FROM memberships m
    JOIN membership_packages mp ON m.package_id = mp.package_id
    WHERE m.user_id = $userId
    ORDER BY m.end_date DESC
    LIMIT 1
");

if ($q && $q->num_rows > 0) {
    $membership = $q->fetch_assoc();
}

/* =========================
   CHECK-IN BULAN INI
========================= */
$checkinMonth = 0;

$q = $conn->query("
    SELECT COUNT(*) AS total
    FROM checkins
    WHERE user_id = $userId
    AND MONTH(checkin_time) = MONTH(CURDATE())
    AND YEAR(checkin_time) = YEAR(CURDATE())
");

if ($q && $q->num_rows > 0) {
    $checkinMonth = (int) $q->fetch_assoc()['total'];
}

/* =========================
   PROGRESS TERAKHIR
========================= */
$progress = [
    'weight_kg' => 0,
    'muscle_mass' => 0,
    'weekly_progress' => 'Belum ada progress',
    'log_date' => null
];

$q = $conn->query("
    SELECT weight_kg, muscle_mass, weekly_progress, log_date
    FROM progress_logs
    WHERE user_id = $userId
    ORDER BY log_date DESC
    LIMIT 1
");

if ($q && $q->num_rows > 0) {
    $progress = $q->fetch_assoc();
}

/* =========================
   PEMBAYARAN TERAKHIR
========================= */
$lastPayment = [
    'payment_id' => '-',
    'amount' => 0,
    'status' => 'pending',
    'payment_date' => null
];

$q = $conn->query("
    SELECT 
        p.payment_id,
        p.amount,
        p.status,
        p.payment_date
    FROM payments p
    JOIN memberships m ON p.membership_id = m.membership_id
    WHERE m.user_id = $userId
    ORDER BY p.payment_date DESC
    LIMIT 1
");

if ($q && $q->num_rows > 0) {
    $lastPayment = $q->fetch_assoc();
}

/* =========================
   WORKOUT DARI DATABASE
========================= */
$workouts = [];

$q = $conn->query("
    SELECT title, category, sets_count, reps_count
    FROM workouts
    ORDER BY workout_id DESC
    LIMIT 3
");

if ($q) {
    while ($row = $q->fetch_assoc()) {
        $workouts[] = $row;
    }
}

/* =========================
   RIWAYAT PEMBAYARAN MEMBER
========================= */
$recentPayments = [];

$q = $conn->query("
    SELECT 
        p.payment_id,
        p.amount,
        p.status,
        p.payment_date,
        mp.package_name
    FROM payments p
    JOIN memberships m ON p.membership_id = m.membership_id
    JOIN membership_packages mp ON m.package_id = mp.package_id
    WHERE m.user_id = $userId
    ORDER BY p.payment_date DESC
    LIMIT 5
");

if ($q) {
    while ($row = $q->fetch_assoc()) {
        $recentPayments[] = $row;
    }
}

/* =========================
   DATA FORMAT
========================= */
$membershipEnd = $membership['end_date']
    ? date('d M Y', strtotime($membership['end_date']))
    : '-';

$lastPaymentDate = $lastPayment['payment_date']
    ? date('d M Y', strtotime($lastPayment['payment_date']))
    : '-';

$membershipStatus = ucfirst($membership['status']);
$paymentStatus = ucfirst($lastPayment['status']);

$paymentBadge = 'badge-pending';
if ($lastPayment['status'] === 'verified') {
    $paymentBadge = 'badge-active';
} elseif ($lastPayment['status'] === 'rejected') {
    $paymentBadge = 'badge-failed';
}

$quickStats = [
    [
        'label' => 'Membership',
        'value' => $membership['package_name'],
        'meta' => 'Status: ' . $membershipStatus . ' • sampai ' . $membershipEnd,
        'icon' => 'bi bi-award-fill'
    ],
    [
        'label' => 'Check-in Bulan Ini',
        'value' => $checkinMonth . 'x',
        'meta' => 'Aktivitas gym bulan berjalan',
        'icon' => 'bi bi-box-arrow-in-right'
    ],
    [
        'label' => 'Berat Saat Ini',
        'value' => number_format((float) $progress['weight_kg'], 1, ',', '.') . ' kg',
        'meta' => $progress['weekly_progress'],
        'icon' => 'bi bi-speedometer2'
    ],
    [
        'label' => 'Pembayaran Terakhir',
        'value' => 'Rp ' . number_format((float) $lastPayment['amount'], 0, ',', '.'),
        'meta' => $paymentStatus . ' pada ' . $lastPaymentDate,
        'icon' => 'bi bi-wallet2'
    ],
];
?>

<div class="dashboard-hero mb-4">
    <div>
        <span class="banner-pill">
            <i class="bi bi-person-heart"></i> Member Overview
        </span>

        <h2>Halo, <?= e($memberName) ?> 👋</h2>
        <p>
            Pantau membership, pembayaran, check-in, dan progress latihan kamu
            dari satu dashboard.
        </p>
    </div>

    <div class="dashboard-hero-card">
        <span>Status Membership</span>
        <strong><?= e($membershipStatus) ?></strong>
        <small><?= e($membership['package_name']) ?></small>
    </div>
</div>

<!-- ===== MEMBER CARD ===== -->
<div class="member-card-wrapper mb-4">
    <div class="member-card">
        <div class="member-card-header">
            <div class="member-card-brand">
                <i class="bi bi-shield-fill-check"></i>
                <span>GYMBRUT</span>
            </div>
            <span class="member-card-type">MEMBER CARD</span>
        </div>

        <div class="member-card-body">
            <div class="member-card-photo">
                <img src="<?= e($memberPhoto) ?>" alt="Foto Member" id="memberCardPhoto">
            </div>

            <div class="member-card-info">
                <p class="member-card-label">NAMA</p>
                <h3 class="member-card-name"><?= e($memberName) ?></h3>

                <div class="member-card-stats">
                    <div class="member-card-stat">
                        <span class="member-card-stat-label">TINGGI BADAN</span>
                        <span class="member-card-stat-value"><?= e($memberHeight) ?></span>
                    </div>
                    <div class="member-card-stat">
                        <span class="member-card-stat-label">BERAT BADAN</span>
                        <span class="member-card-stat-value"><?= e($memberWeight) ?></span>
                    </div>
                </div>
            </div>

            <div class="member-card-qr" id="memberCardQrSmall" title="Klik untuk memperbesar QR Code">
                <!-- QR code will be generated here by JS -->
            </div>
        </div>

        <div class="member-card-footer">
            <span><i class="bi bi-envelope-fill"></i> <?= e($memberEmail) ?></span>
            <span class="member-card-id">ID: <?= e($userId) ?></span>
        </div>
    </div>
</div>

<!-- ===== QR CODE OVERLAY ===== -->
<div class="qr-overlay" id="qrOverlay">
    <div class="qr-overlay-card">
        <button class="qr-overlay-close" id="qrOverlayClose" aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="qr-overlay-brand">
            <i class="bi bi-shield-fill-check"></i>
            <span>GYMBRUT</span>
        </div>

        <h3 class="qr-overlay-title">Member QR Code</h3>
        <p class="qr-overlay-subtitle">Scan QR code di bawah ini untuk check-in di front desk gym.</p>

        <p class="qr-overlay-name"><?= e($memberName) ?></p>

        <div class="qr-overlay-qr" id="memberCardQrLarge">
            <!-- Large QR code will be generated here by JS -->
        </div>

        <p class="qr-overlay-email"><?= e($memberEmail) ?></p>

        <button class="btn-outline-soft qr-overlay-back" id="qrOverlayBack">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <?php foreach ($quickStats as $item): ?>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-modern">
            <div>
                <p class="stat-label"><?= e($item['label']) ?></p>
                <h3 class="stat-value" style="font-size:24px;">
                    <?= e($item['value']) ?>
                </h3>
                <span class="stat-meta"><?= e($item['meta']) ?></span>
            </div>

            <div class="stat-icon">
                <i class="<?= e($item['icon']) ?>"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="premium-card h-100">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Ringkasan Membership</h3>
                    <p class="section-subtitle">Detail paket membership kamu saat ini.</p>
                </div>

                <span class="badge-soft <?= $membership['status'] === 'aktif' ? 'badge-active' : 'badge-pending' ?>">
                    <?= e($membershipStatus) ?>
                </span>
            </div>

            <ul class="metric-list">
                <li>
                    <span>Paket</span>
                    <strong>
                        <?= e($membership['package_name']) ?>
                    </strong>
                </li>
                <li>
                    <span>Status</span>
                    <strong>
                        <?= e($membershipStatus) ?>
                    </strong>
                </li>
                <li>
                    <span>Berakhir</span>
                    <strong>
                        <?= e($membershipEnd) ?>
                    </strong>
                </li>
                <li>
                    <span>Harga Paket</span>
                    <strong>Rp
                        <?= number_format((float) $membership['price'], 0, ',', '.') ?>
                    </strong>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="premium-card h-100">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Workout Terbaru</h3>
                    <p class="section-subtitle">Data latihan dari database workout.</p>
                </div>
            </div>

            <div class="card-list">
                <?php if (empty($workouts)): ?>
                <p class="text-soft mb-0">Belum ada data workout.</p>
                <?php endif; ?>

                <?php foreach ($workouts as $item): ?>
                <div class="list-row">
                    <div>
                        <p class="list-row-title">
                            <?= e($item['title']) ?> — <?= e($item['category']) ?>
                        </p>
                        <p class="list-row-subtitle">
                            <?= e($item['sets_count'] ?? '-') ?> set • <?= e($item['reps_count'] ?? '-') ?> reps
                        </p>
                    </div>

                    <span class="badge-soft badge-info">Workout</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="premium-card">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Riwayat Pembayaran Terakhir</h3>
                    <p class="section-subtitle">Status pembayaran membership terbaru kamu.</p>
                </div>

                <span class="badge-soft <?= e($paymentBadge) ?>">
                    <?= e($paymentStatus) ?>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Paket</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($recentPayments)): ?>
                        <tr>
                            <td colspan="5" class="text-soft">
                                Belum ada pembayaran.
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($recentPayments as $payment): ?>
                        <?php
                            $badge = 'badge-pending';

                            if ($payment['status'] === 'verified') {
                                $badge = 'badge-active';
                            } elseif ($payment['status'] === 'rejected') {
                                $badge = 'badge-failed';
                            }

                            $paymentDate = $payment['payment_date']
                                ? date('d M Y', strtotime($payment['payment_date']))
                                : '-';
                            ?>

                        <tr>
                            <td>
                                <strong>INV-<?= e($payment['payment_id']) ?></strong>
                            </td>

                            <td>
                                <?= e($payment['package_name']) ?>
                            </td>

                            <td>
                                Rp <?= number_format((float) $payment['amount'], 0, ',', '.') ?>
                            </td>

                            <td>
                                <span class="badge-soft <?= e($badge) ?>">
                                    <?= e(ucfirst($payment['status'])) ?>
                                </span>
                            </td>

                            <td>
                                <?= e($paymentDate) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var memberEmail = <?= json_encode($memberEmail) ?>;

    // Generate small QR
    var qrSmallEl = document.getElementById('memberCardQrSmall');
    if (qrSmallEl && memberEmail) {
        new QRCode(qrSmallEl, {
            text: memberEmail,
            width: 80,
            height: 80,
            colorDark: '#18212f',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    // Generate large QR
    var qrLargeEl = document.getElementById('memberCardQrLarge');
    if (qrLargeEl && memberEmail) {
        new QRCode(qrLargeEl, {
            text: memberEmail,
            width: 220,
            height: 220,
            colorDark: '#18212f',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    // QR overlay open/close
    var overlay = document.getElementById('qrOverlay');
    var closeBtn = document.getElementById('qrOverlayClose');
    var backBtn = document.getElementById('qrOverlayBack');

    if (qrSmallEl) {
        qrSmallEl.addEventListener('click', function() {
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    function closeOverlay() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeOverlay);
    if (backBtn) backBtn.addEventListener('click', closeOverlay);

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeOverlay();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeOverlay();
        }
    });
});
</script>

<?php include '../includes/layout_bottom.php'; ?>