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

<?php include '../includes/layout_bottom.php'; ?>