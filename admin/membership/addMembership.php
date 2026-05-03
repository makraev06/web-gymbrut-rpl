<?php
session_start();


require_once '../../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_name = trim($_POST['package_name'] ?? '');
    $duration_days = (int) ($_POST['duration_days'] ?? 0);
    $price = (int) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($package_name === '' || $duration_days <= 0 || $price <= 0) {
        $error = 'Nama paket, durasi, dan harga wajib diisi.';
    } else {
        $stmt = $conn->prepare("
      INSERT INTO membership_packages 
      (package_name, duration_days, price, description)
      VALUES (?, ?, ?, ?)
    ");
        $stmt->bind_param("siis", $package_name, $duration_days, $price, $description);
        $stmt->execute();

        header("Location: memberships.php");
        exit;
    }
}

$pageTitle = 'Tambah Paket';
$topbarTitle = 'Tambah Paket Membership';
$topbarSubtitle = 'Tambahkan paket membership baru untuk member.';
$searchPlaceholder = 'Cari...';

include '../../includes/layout_top.php';
?>

<section class="page-section">
    <div class="membership-section-header">
        <div>
            <h3 class="section-title">Tambah Paket Membership</h3>
            <p class="section-subtitle">Isi detail paket baru.</p>
        </div>

        <a href="memberships.php" class="btn-outline-soft btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <?php if (!empty($error)): ?>
            <div class="auth-alert auth-alert-danger mb-3">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-group">
                <label class="form-label">Nama Paket</label>
                <input type="text" name="package_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Durasi Hari</label>
                <input type="number" name="duration_days" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Harga</label>
                <input type="number" name="price" class="form-control" required>
            </div>

            <div class="form-group full">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control"
                    placeholder="Contoh: Akses gym selama 30 hari"></textarea>
            </div>

            <div class="form-group full">
                <button type="submit" class="gradient-btn">
                    <i class="bi bi-save"></i> Simpan Paket
                </button>
            </div>
        </form>
    </div>
</section>

<?php include '../../includes/layout_bottom.php'; ?>