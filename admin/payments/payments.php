<?php
session_start();

$pageTitle = 'Payments';
$topbarTitle = 'Payments';
$topbarSubtitle = 'Verifikasi pembayaran member.';
$searchPlaceholder = 'Cari pembayaran...';

include __DIR__ . '/../../includes/layout_top.php';

$payments = gymbrut_query_all($conn, "
  SELECT
    p.payment_id,
    p.amount,
    p.status,
    p.payment_date,
    p.proof_file,
    u.name AS member_name,
    u.email,
    mp.package_name
  FROM payments p
  JOIN memberships m ON p.membership_id = m.membership_id
  JOIN users u ON m.user_id = u.user_id
  JOIN membership_packages mp ON m.package_id = mp.package_id
  ORDER BY p.payment_date DESC
");
?>

<section class="page-section">
  <div class="membership-section-header">
    <div>
      <h3 class="section-title">Data Pembayaran</h3>
      <p class="section-subtitle">Approve atau reject pembayaran member.</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Invoice</th>
            <th>Member</th>
            <th>Paket</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Bukti</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($payments)): ?>
            <tr>
              <td colspan="8" class="text-soft">Belum ada data pembayaran.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($payments as $payment): ?>
            <?php
            $badge = 'badge-pending';
            if ($payment['status'] === 'verified')
              $badge = 'badge-active';
            if ($payment['status'] === 'rejected')
              $badge = 'badge-failed';
            ?>

            <tr>
              <td><strong>INV-<?= str_pad($payment['payment_id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
              <td>
                <strong><?= e($payment['member_name']) ?></strong><br>
                <span class="text-soft"><?= e($payment['email']) ?></span>
              </td>
              <td><?= e($payment['package_name']) ?></td>
              <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
              <td>
                <span class="badge-soft <?= $badge ?>">
                  <?= e(ucfirst($payment['status'])) ?>
                </span>
              </td>
              <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
              <td>
                <?php if (!empty($payment['proof_file'])): ?>
                  <a href="../../uploads/payments/<?= e($payment['proof_file']) ?>" target="_blank"
                    class="btn-outline-soft btn-sm">
                    Lihat
                  </a>
                <?php else: ?>
                  <span class="text-soft">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($payment['status'] === 'pending'): ?>
                  <div class="d-flex gap-8">
                    <a href="verifyPayments.php?id=<?= e($payment['payment_id']) ?>" class="btn-outline-soft btn-sm"
                      onclick="return confirm('Verifikasi pembayaran ini?')">
                      Verify
                    </a>

                    <a href="rejectPayments.php?id=<?= e($payment['payment_id']) ?>" class="btn-outline-soft btn-sm"
                      onclick="return confirm('Tolak pembayaran ini?')">
                      Reject
                    </a>
                  </div>
                <?php else: ?>
                  <span class="text-soft">Selesai</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/layout_bottom.php'; ?>