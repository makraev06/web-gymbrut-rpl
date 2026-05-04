<?php
/* admin/members.php */
session_start();
$_SESSION['role'] = $_SESSION['role'] ?? 'admin';
$_SESSION['name'] = $_SESSION['name'] ?? 'Michael Admin';

$pageTitle = 'Manage Members';
$topbarTitle = 'Members';
$topbarSubtitle = 'Kelola seluruh data member gym secara rapi dan terstruktur.';
$searchPlaceholder = 'Cari nama member atau email...';

include '../../includes/layout_top.php';

$members = gymbrut_query_all($conn, "
  SELECT 
    u.user_id,
    u.name,
    u.email,
    u.phone,
    u.created_at,
    mp.package_name,
    m.start_date,
    m.end_date,
    m.status
  FROM users u
  LEFT JOIN memberships m 
    ON m.membership_id = (
      SELECT m2.membership_id 
      FROM memberships m2
      WHERE m2.user_id = u.user_id 
      ORDER BY m2.start_date DESC 
      LIMIT 1
    )
  LEFT JOIN membership_packages mp 
    ON m.package_id = mp.package_id
  WHERE u.role = 'member'
  ORDER BY u.user_id DESC
");
?>

<section class="page-section">
  <div class="table-card">
    <div class="card-header-inline">
      <div>
        <h3 class="section-title">Data Member</h3>
        <p class="section-subtitle">Daftar member aktif, pending, dan nonaktif.</p>
      </div>

      <div class="d-flex align-center gap-8">
        <a href="addMember.php" class="gradient-btn btn-sm">
          <i class="bi bi-plus-lg"></i> Tambah Member
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>No HP</th>
            <th>Paket</th>
            <th>Join Date</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $member): ?>
            <tr>
              <td><strong><?= e($member['name']) ?></strong></td>
              <td><?= e($member['email']) ?></td>
              <td><?= e($member['phone'] ?? '-') ?></td>
              <td><?= e($member['package_name'] ?? 'Belum pilih paket') ?></td>
              <td>
                <?= !empty($member['start_date'])
                  ? date('d M Y', strtotime($member['start_date']))
                  : date('d M Y', strtotime($member['created_at'])) ?>
              </td>
              <td>
                <?php
                $status = $member['status'] ?? 'pending';

                $badgeClass = 'badge-pending';
                if ($status === 'aktif')
                  $badgeClass = 'badge-active';
                if ($status === 'expired')
                  $badgeClass = 'badge-expired';
                ?>
                <span class="badge-soft <?= $badgeClass ?>">
                  <?= e(ucfirst($status)) ?>
                </span>
              </td>
              <td class="text-end">
                <div class="d-flex align-center justify-between gap-8" style="justify-content:flex-end;">
                  <a href="editMember.php?id=<?= e($member['user_id']) ?>" class="btn-outline-soft btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>

                  <a href="deleteMember.php?id=<?= e($member['user_id']) ?>" class="btn-outline-soft btn-sm"
                    onclick="return confirm('Yakin ingin menghapus member ini?')">
                    <i class="bi bi-trash3"></i> Hapus
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include '../../includes/layout_bottom.php'; ?>