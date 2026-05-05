<?php
/* member/memberships.php */
ob_start();
session_start();

$_SESSION['role'] = $_SESSION['role'] ?? 'member';
$_SESSION['name'] = $_SESSION['name'] ?? 'Member';

$pageTitle = 'Membership Saya';
$topbarTitle = 'Membership Saya';
$topbarSubtitle = 'Lihat detail paket, masa berlaku, status pembayaran, dan pilih paket membership.';
$searchPlaceholder = 'Cari membership...';

include '../includes/layout_top.php';

$userId = (int) ($_SESSION['user_id'] ?? 0);

$success = '';
$error = '';

/* =========================
   HELPER BADGE
========================= */
function membershipBadgeClass($status)
{
  if ($status === 'aktif') {
    return 'badge-active';
  }

  if ($status === 'pending') {
    return 'badge-pending';
  }

  return 'badge-expired';
}

function paymentBadgeClass($status)
{
  if ($status === 'verified') {
    return 'badge-active';
  }

  if ($status === 'pending') {
    return 'badge-pending';
  }

  return 'badge-failed';
}

/* =========================
   AUTO EXPIRED MEMBERSHIP
========================= */
$stmt = $conn->prepare("
    UPDATE memberships
    SET status = 'expired'
    WHERE user_id = ?
    AND end_date < CURDATE()
    AND status = 'aktif'
");
$stmt->bind_param("i", $userId);
$stmt->execute();

/* =========================
   PROSES PILIH PAKET
   memberships -> pending
   payments -> pending + proof_file NULL
   redirect ke payments.php
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package_id'])) {
  $packageId = (int) $_POST['package_id'];

  // Cek apakah user masih punya membership aktif atau pending
  $stmt = $conn->prepare("
        SELECT membership_id
        FROM memberships
        WHERE user_id = ?
        AND status IN ('aktif', 'pending')
        AND end_date >= CURDATE()
        LIMIT 1
    ");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $existing = $stmt->get_result();

  if ($existing && $existing->num_rows > 0) {
    $error = 'Kamu masih punya membership aktif atau pending. Selesaikan dulu sebelum memilih paket baru.';
  } else {
    // Ambil data paket
    $stmt = $conn->prepare("
            SELECT package_id, price, duration_days
            FROM membership_packages
            WHERE package_id = ?
            LIMIT 1
        ");
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $packageResult = $stmt->get_result();

    if (!$packageResult || $packageResult->num_rows === 0) {
      $error = 'Paket tidak ditemukan.';
    } else {
      $package = $packageResult->fetch_assoc();

      $amount = (float) $package['price'];
      $durationDays = (int) $package['duration_days'];

      $startDate = date('Y-m-d');
      $endDate = date('Y-m-d', strtotime("+$durationDays days"));

      // 1. Buat membership pending
      $stmt = $conn->prepare("
                INSERT INTO memberships (user_id, package_id, start_date, end_date, status)
                VALUES (?, ?, ?, ?, 'pending')
            ");
      $stmt->bind_param("iiss", $userId, $packageId, $startDate, $endDate);

      if ($stmt->execute()) {
        $membershipId = $conn->insert_id;

        // 2. Buat payment pending dengan proof_file NULL
        $stmt = $conn->prepare("
                    INSERT INTO payments 
                    (membership_id, amount, payment_date, proof_file, status, verified_by)
                    VALUES (?, ?, NOW(), NULL, 'pending', NULL)
                ");
        $stmt->bind_param("id", $membershipId, $amount);

        if ($stmt->execute()) {
          $paymentId = $conn->insert_id;

          // 3. Langsung masuk ke halaman payment untuk upload bukti
          header("Location: payments.php?payment_id=" . $paymentId);
          exit;
        } else {
          $error = 'Membership berhasil dibuat, tapi payment gagal dibuat.';
        }
      } else {
        $error = 'Gagal memilih paket membership.';
      }
    }
  }
}
/* =========================
   MEMBERSHIP TERBARU
========================= */
$currentMembership = null;

$stmt = $conn->prepare("
    SELECT 
        m.membership_id,
        m.start_date,
        m.end_date,
        m.status,
        mp.package_name,
        mp.duration_days,
        mp.price,
        mp.description,
        p.payment_id,
        p.amount,
        p.payment_date,
        p.status AS payment_status,
        p.proof_file
    FROM memberships m
    JOIN membership_packages mp ON m.package_id = mp.package_id
    LEFT JOIN payments p ON p.payment_id = (
        SELECT p2.payment_id
        FROM payments p2
        WHERE p2.membership_id = m.membership_id
        ORDER BY p2.payment_date DESC, p2.payment_id DESC
        LIMIT 1
    )
    WHERE m.user_id = ?
    ORDER BY m.end_date DESC, m.membership_id DESC
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
  $currentMembership = $result->fetch_assoc();
}

/* =========================
   RIWAYAT MEMBERSHIP
========================= */
$memberships = [];

$stmt = $conn->prepare("
    SELECT 
        m.membership_id,
        m.start_date,
        m.end_date,
        m.status,
        mp.package_name,
        mp.price,
        mp.duration_days,
        p.payment_id,
        p.amount,
        p.payment_date,
        p.status AS payment_status,
        p.proof_file
    FROM memberships m
    JOIN membership_packages mp ON m.package_id = mp.package_id
    LEFT JOIN payments p ON p.payment_id = (
        SELECT p2.payment_id
        FROM payments p2
        WHERE p2.membership_id = m.membership_id
        ORDER BY p2.payment_date DESC, p2.payment_id DESC
        LIMIT 1
    )
    WHERE m.user_id = ?
    ORDER BY m.start_date DESC, m.membership_id DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $memberships[] = $row;
  }
}

/* =========================
   DAFTAR PAKET TERSEDIA
========================= */
$packages = [];

$q = $conn->query("
    SELECT package_id, package_name, duration_days, price, description
    FROM membership_packages
    ORDER BY price ASC
");

if ($q) {
  while ($row = $q->fetch_assoc()) {
    $packages[] = $row;
  }
}

/* =========================
   FORMAT DATA MEMBERSHIP TERBARU
========================= */
$membershipStatus = 'Belum Ada';
$membershipBadge = 'badge-expired';
$packageName = 'Belum ada membership';
$startDateText = '-';
$endDateText = '-';
$remainingDaysText = '-';
$priceText = 'Rp 0';
$paymentStatus = 'Belum Ada';
$paymentBadge = 'badge-expired';
$paymentAmountText = 'Rp 0';
$paymentDateText = '-';
$description = 'Kamu belum memiliki membership.';

if ($currentMembership) {
  $membershipStatusRaw = $currentMembership['status'];

  if (
    !empty($currentMembership['end_date']) &&
    strtotime($currentMembership['end_date']) < strtotime(date('Y-m-d')) &&
    $membershipStatusRaw === 'aktif'
  ) {
    $membershipStatusRaw = 'expired';
  }

  $membershipStatus = ucfirst($membershipStatusRaw);
  $membershipBadge = membershipBadgeClass($membershipStatusRaw);

  $packageName = $currentMembership['package_name'];
  $startDateText = date('d M Y', strtotime($currentMembership['start_date']));
  $endDateText = date('d M Y', strtotime($currentMembership['end_date']));
  $priceText = 'Rp ' . number_format((float) $currentMembership['price'], 0, ',', '.');
  $description = $currentMembership['description'] ?: 'Belum ada deskripsi paket.';

  $today = new DateTime(date('Y-m-d'));
  $endDate = new DateTime($currentMembership['end_date']);

  if ($endDate >= $today && $membershipStatusRaw === 'aktif') {
    $remainingDaysText = $today->diff($endDate)->days . ' hari lagi';
  } elseif ($membershipStatusRaw === 'pending') {
    $remainingDaysText = 'Menunggu pembayaran/verifikasi';
  } else {
    $remainingDaysText = 'Sudah berakhir';
  }

  $paymentStatusRaw = $currentMembership['payment_status'] ?? 'pending';
  $paymentStatus = ucfirst($paymentStatusRaw);
  $paymentBadge = paymentBadgeClass($paymentStatusRaw);

  $paymentAmountText = 'Rp ' . number_format((float) ($currentMembership['amount'] ?? 0), 0, ',', '.');

  $paymentDateText = !empty($currentMembership['payment_date'])
    ? date('d M Y', strtotime($currentMembership['payment_date']))
    : '-';
}
?>

<div class="dashboard-hero mb-4">
  <div>
    <span class="banner-pill">
      <i class="bi bi-award-fill"></i> Membership Member
    </span>

    <h2>Membership Saya</h2>
    <p>
      Pantau paket membership, masa berlaku, status pembayaran,
      dan pilih paket membership yang tersedia.
    </p>
  </div>

  <div class="dashboard-hero-card">
    <span>Status Membership</span>
    <strong><?= e($membershipStatus) ?></strong>
    <small><?= e($packageName) ?></small>
  </div>
</div>

<?php if ($success): ?>
  <div class="alert alert-success">
    <i class="bi bi-check-circle"></i>
    <?= e($success) ?>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger">
    <i class="bi bi-exclamation-circle"></i>
    <?= e($error) ?>
  </div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <div class="col-md-6 col-xl-3">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Paket Saat Ini</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= e($packageName) ?>
        </h3>
        <span class="stat-meta"><?= e($priceText) ?></span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-award"></i>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Status Membership</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= e($membershipStatus) ?>
        </h3>
        <span class="stat-meta">Berlaku sampai <?= e($endDateText) ?></span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-calendar-check"></i>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Sisa Masa Aktif</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= e($remainingDaysText) ?>
        </h3>
        <span class="stat-meta">Dihitung dari tanggal hari ini</span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-hourglass-split"></i>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Status Pembayaran</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= e($paymentStatus) ?>
        </h3>
        <span class="stat-meta"><?= e($paymentAmountText) ?></span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-wallet2"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-xl-6">
    <div class="premium-card h-100">
      <div class="card-header-inline">
        <div>
          <h3 class="section-title">Detail Membership</h3>
          <p class="section-subtitle">
            Informasi paket membership terbaru kamu.
          </p>
        </div>

        <span class="badge-soft <?= e($membershipBadge) ?>">
          <?= e($membershipStatus) ?>
        </span>
      </div>

      <?php if (!$currentMembership): ?>
        <p class="text-soft mb-0">
          Kamu belum memiliki membership. Silakan pilih paket di bawah.
        </p>
      <?php else: ?>
        <ul class="metric-list">
          <li>
            <span>Paket</span>
            <strong><?= e($packageName) ?></strong>
          </li>
          <li>
            <span>Tanggal Mulai</span>
            <strong><?= e($startDateText) ?></strong>
          </li>
          <li>
            <span>Tanggal Berakhir</span>
            <strong><?= e($endDateText) ?></strong>
          </li>
          <li>
            <span>Sisa Masa Aktif</span>
            <strong><?= e($remainingDaysText) ?></strong>
          </li>
          <li>
            <span>Harga Paket</span>
            <strong><?= e($priceText) ?></strong>
          </li>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-xl-6">
    <div class="premium-card h-100">
      <div class="card-header-inline">
        <div>
          <h3 class="section-title">Pembayaran Membership</h3>
          <p class="section-subtitle">
            Status pembayaran untuk membership terbaru.
          </p>
        </div>

        <span class="badge-soft <?= e($paymentBadge) ?>">
          <?= e($paymentStatus) ?>
        </span>
      </div>

      <?php if (!$currentMembership): ?>
        <p class="text-soft mb-0">
          Belum ada data pembayaran.
        </p>
      <?php else: ?>
        <ul class="metric-list">
          <li>
            <span>Invoice</span>
            <strong>
              <?= !empty($currentMembership['payment_id']) ? 'INV-' . e($currentMembership['payment_id']) : '-' ?>
            </strong>
          </li>
          <li>
            <span>Nominal</span>
            <strong><?= e($paymentAmountText) ?></strong>
          </li>
          <li>
            <span>Status</span>
            <strong><?= e($paymentStatus) ?></strong>
          </li>
          <li>
            <span>Tanggal Pembayaran</span>
            <strong><?= e($paymentDateText) ?></strong>
          </li>
          <li>
            <span>Bukti Pembayaran</span>
            <strong>
              <?php if (!empty($currentMembership['proof_file'])): ?>
                <a href="../uploads/payments/<?= e($currentMembership['proof_file']) ?>" target="_blank">
                  Lihat Bukti
                </a>
              <?php else: ?>
                Belum upload
              <?php endif; ?>
            </strong>
          </li>
        </ul>

        <?php if (($currentMembership['payment_status'] ?? '') !== 'verified'): ?>
          <a href="payments.php?payment_id=<?= e($currentMembership['payment_id']) ?>" class="gradient-btn btn-sm mt-3">
            <i class="bi bi-upload"></i>
            Upload Bukti Pembayaran
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-xl-12">
    <div class="premium-card">
      <div class="card-header-inline">
        <div>
          <h3 class="section-title">Deskripsi Paket</h3>
          <p class="section-subtitle">
            Detail manfaat membership yang kamu miliki.
          </p>
        </div>
      </div>

      <p class="text-soft mb-0">
        <?= e($description) ?>
      </p>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="premium-card">
      <div class="card-header-inline">
        <div>
          <h3 class="section-title">Riwayat Membership</h3>
          <p class="section-subtitle">
            Semua paket membership yang pernah kamu miliki.
          </p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Paket</th>
              <th>Mulai</th>
              <th>Berakhir</th>
              <th>Harga</th>
              <th>Status Membership</th>
              <th>Status Payment</th>
              <th>Bukti</th>
            </tr>
          </thead>

          <tbody>
            <?php if (empty($memberships)): ?>
              <tr>
                <td colspan="7" class="text-soft">
                  Belum ada riwayat membership.
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($memberships as $item): ?>
              <?php
              $itemStatus = $item['status'];

              if (
                !empty($item['end_date']) &&
                strtotime($item['end_date']) < strtotime(date('Y-m-d')) &&
                $itemStatus === 'aktif'
              ) {
                $itemStatus = 'expired';
              }

              $itemMembershipBadge = membershipBadgeClass($itemStatus);

              $itemPaymentStatus = $item['payment_status'] ?? 'pending';
              $itemPaymentBadge = paymentBadgeClass($itemPaymentStatus);
              ?>

              <tr>
                <td>
                  <strong><?= e($item['package_name']) ?></strong>
                </td>

                <td>
                  <?= e(date('d M Y', strtotime($item['start_date']))) ?>
                </td>

                <td>
                  <?= e(date('d M Y', strtotime($item['end_date']))) ?>
                </td>

                <td>
                  Rp <?= number_format((float) $item['price'], 0, ',', '.') ?>
                </td>

                <td>
                  <span class="badge-soft <?= e($itemMembershipBadge) ?>">
                    <?= e(ucfirst($itemStatus)) ?>
                  </span>
                </td>

                <td>
                  <span class="badge-soft <?= e($itemPaymentBadge) ?>">
                    <?= e(ucfirst($itemPaymentStatus)) ?>
                  </span>
                </td>

                <td>
                  <?php if (!empty($item['proof_file'])): ?>
                    <a href="../uploads/payments/<?= e($item['proof_file']) ?>" target="_blank"
                      class="btn-outline-soft btn-sm">
                      Lihat
                    </a>
                  <?php elseif (!empty($item['payment_id']) && $itemPaymentStatus !== 'verified'): ?>
                    <a href="payments.php?payment_id=<?= e($item['payment_id']) ?>" class="gradient-btn btn-sm">
                      Upload
                    </a>
                  <?php else: ?>
                    <span class="text-soft">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-12">
    <div class="premium-card">
      <div class="card-header-inline">
        <div>
          <h3 class="section-title">Daftar Paket Tersedia</h3>
          <p class="section-subtitle">
            Pilih paket membership, lalu upload bukti pembayaran.
          </p>
        </div>
      </div>

      <div class="row g-4">
        <?php foreach ($packages as $package): ?>
          <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
              <p class="stat-label"><?= e($package['duration_days']) ?> hari</p>

              <h3 class="stat-value" style="font-size:24px;">
                <?= e($package['package_name']) ?>
              </h3>

              <p class="stat-meta">
                Rp <?= number_format((float) $package['price'], 0, ',', '.') ?>
              </p>

              <p class="text-soft">
                <?= e($package['description'] ?? 'Tidak ada deskripsi.') ?>
              </p>

              <form method="POST"
                onsubmit="return confirm('Yakin ingin memilih paket ini? Setelah memilih paket, kamu akan diarahkan ke halaman pembayaran.');">
                <input type="hidden" name="package_id" value="<?= e($package['package_id']) ?>">

                <button type="submit" class="gradient-btn w-100">
                  <i class="bi bi-cart-check"></i>
                  Pilih Paket
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($packages)): ?>
          <div class="col-12">
            <p class="text-soft mb-0">
              Belum ada paket membership tersedia.
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>