<?php
session_start();

$pageTitle = 'Edit Member';
$topbarTitle = 'Edit Member';
$topbarSubtitle = 'Ubah data member dan membership.';
$searchPlaceholder = 'Cari...';

include '../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);

$member = gymbrut_query_one($conn, "
  SELECT 
    u.user_id,
    u.name,
    u.email,
    u.phone,
    ms.membership_id,
    ms.package_id,
    ms.start_date,
    ms.end_date,
    ms.status
  FROM users u
  LEFT JOIN memberships ms ON u.user_id = ms.user_id
  WHERE u.user_id = $id
  LIMIT 1
");

if (!$member) {
    header("Location: members.php");
    exit;
}

$packages = gymbrut_query_all($conn, "
  SELECT package_id, package_name, duration_days, price
  FROM membership_packages
  ORDER BY price ASC
");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $package_id = (int) ($_POST['package_id'] ?? 0);
    $start_date = $_POST['start_date'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'aktif';

    if ($name === '' || $email === '' || $package_id === 0) {
        $error = 'Nama, email, dan paket wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = 'Email sudah dipakai member lain.';
        } else {
            if ($password !== '') {
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("
          UPDATE users
          SET name = ?, email = ?, phone = ?, password = ?
          WHERE user_id = ?
        ");
                $stmt->bind_param("ssssi", $name, $email, $phone, $hashed, $id);
            } else {
                $stmt = $conn->prepare("
          UPDATE users
          SET name = ?, email = ?, phone = ?
          WHERE user_id = ?
        ");
                $stmt->bind_param("sssi", $name, $email, $phone, $id);
            }

            $stmt->execute();

            $stmt = $conn->prepare("SELECT duration_days FROM membership_packages WHERE package_id = ?");
            $stmt->bind_param("i", $package_id);
            $stmt->execute();
            $package = $stmt->get_result()->fetch_assoc();

            $duration = (int) $package['duration_days'];
            $end_date = date('Y-m-d', strtotime($start_date . " +{$duration} days"));

            if (!empty($member['membership_id'])) {
                $stmt = $conn->prepare("
          UPDATE memberships
          SET package_id = ?, start_date = ?, end_date = ?, status = ?
          WHERE membership_id = ?
        ");
                $stmt->bind_param("isssi", $package_id, $start_date, $end_date, $status, $member['membership_id']);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("
          INSERT INTO memberships (user_id, package_id, start_date, end_date, status)
          VALUES (?, ?, ?, ?, ?)
        ");
                $stmt->bind_param("iisss", $id, $package_id, $start_date, $end_date, $status);
                $stmt->execute();
            }

            header("Location: members.php");
            exit;
        }
    }
}
include '../../includes/layout_top.php';
?>

<section class="page-section">
    <div class="membership-section-header">
        <div>
            <h3 class="section-title">Edit Member</h3>
            <p class="section-subtitle">Password boleh dikosongkan jika tidak ingin diubah.</p>
        </div>

        <a href="members.php" class="btn-outline-soft btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="auth-alert auth-alert-danger mb-3">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="<?= e($member['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($member['email']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">No HP</label>
                <input type="text" name="phone" class="form-control" value="<?= e($member['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
            </div>

            <div class="form-group">
                <label class="form-label">Paket</label>
                <select name="package_id" class="form-select" required>
                    <option value="">Pilih Paket</option>
                    <?php foreach ($packages as $package): ?>
                        <option value="<?= e($package['package_id']) ?>" <?= (int) $member['package_id'] === (int) $package['package_id'] ? 'selected' : '' ?>>
                            <?= e($package['package_name']) ?> - Rp
                            <?= number_format($package['price'], 0, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control"
                    value="<?= e($member['start_date'] ?? date('Y-m-d')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="aktif" <?= ($member['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="pending" <?= ($member['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending
                    </option>
                    <option value="expired" <?= ($member['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired
                    </option>
                </select>
            </div>

            <div class="form-group full">
                <button type="submit" class="gradient-btn">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</section>

<?php include '../../includes/layout_bottom.php'; ?>