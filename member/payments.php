<?php
/* member/payments.php */
session_start();

require_once '../config/database.php';

$_SESSION['role'] = $_SESSION['role'] ?? 'member';
$_SESSION['name'] = $_SESSION['name'] ?? 'Member';

$pageTitle = 'Pembayaran Saya';
$topbarTitle = 'Pembayaran Saya';
$topbarSubtitle = 'Upload bukti pembayaran dan pantau status verifikasi membership kamu.';
$searchPlaceholder = 'Cari pembayaran...';

$userId = (int) ($_SESSION['user_id'] ?? 0);

$success = '';
$error = '';

/* =========================
   UPLOAD BUKTI PEMBAYARAN
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_proof'])) {
    $paymentId = (int) ($_POST['payment_id'] ?? 0);

    if ($userId <= 0) {
        $error = 'Session user tidak ditemukan. Silakan login ulang.';
    } elseif ($paymentId <= 0) {
        $error = 'Data pembayaran tidak valid.';
    } else {
        $stmt = $conn->prepare("
            SELECT 
                p.payment_id,
                p.status,
                p.proof_file,
                m.user_id
            FROM payments p
            JOIN memberships m ON p.membership_id = m.membership_id
            WHERE p.payment_id = ?
            AND m.user_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $paymentId, $userId);
        $stmt->execute();
        $paymentResult = $stmt->get_result();

        if (!$paymentResult || $paymentResult->num_rows === 0) {
            $error = 'Data pembayaran tidak ditemukan.';
        } else {
            $payment = $paymentResult->fetch_assoc();

            if ($payment['status'] === 'verified') {
                $error = 'Pembayaran ini sudah diverifikasi, bukti tidak bisa diubah.';
            } elseif (!isset($_FILES['proof_file']) || $_FILES['proof_file']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Silakan upload bukti pembayaran terlebih dahulu.';
            } else {
                $file = $_FILES['proof_file'];

                $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
                $fileName = $file['name'];
                $fileTmp = $file['tmp_name'];
                $fileSize = $file['size'];

                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedExt)) {
                    $error = 'Format file harus JPG, JPEG, PNG, atau PDF.';
                } elseif ($fileSize > 2 * 1024 * 1024) {
                    $error = 'Ukuran file maksimal 2MB.';
                } else {
                    $uploadDir = '../uploads/payments/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $newFileName = 'payment_' . $paymentId . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmp, $targetPath)) {
                        $stmt = $conn->prepare("
                            UPDATE payments
                            SET 
                                proof_file = ?,
                                status = 'pending',
                                payment_date = NOW()
                            WHERE payment_id = ?
                        ");
                        $stmt->bind_param("si", $newFileName, $paymentId);

                        if ($stmt->execute()) {
                            $success = 'Bukti pembayaran berhasil diupload. Silakan tunggu verifikasi admin.';
                        } else {
                            $error = 'Gagal menyimpan bukti pembayaran ke database.';
                        }
                    } else {
                        $error = 'Gagal upload file bukti pembayaran.';
                    }
                }
            }
        }
    }
}

/* =========================
   AMBIL PAYMENT PENDING USER
========================= */
$pendingPayment = null;

if ($userId > 0) {
    $stmt = $conn->prepare("
        SELECT 
            p.payment_id,
            p.membership_id,
            p.amount,
            p.status,
            p.proof_file,
            p.payment_date,
            m.start_date,
            m.end_date,
            m.status AS membership_status,
            mp.package_name,
            mp.duration_days
        FROM payments p
        JOIN memberships m ON p.membership_id = m.membership_id
        JOIN membership_packages mp ON m.package_id = mp.package_id
        WHERE m.user_id = ?
        AND p.status = 'pending'
        ORDER BY p.payment_id DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $pendingResult = $stmt->get_result();

    if ($pendingResult && $pendingResult->num_rows > 0) {
        $pendingPayment = $pendingResult->fetch_assoc();
    }
}

/* =========================
   AMBIL RIWAYAT PAYMENT USER
========================= */
$payments = [];

if ($userId > 0) {
    $stmt = $conn->prepare("
        SELECT 
            p.payment_id,
            p.membership_id,
            p.amount,
            p.payment_date,
            p.proof_file,
            p.status,
            m.start_date,
            m.end_date,
            m.status AS membership_status,
            mp.package_name
        FROM payments p
        JOIN memberships m ON p.membership_id = m.membership_id
        JOIN membership_packages mp ON m.package_id = mp.package_id
        WHERE m.user_id = ?
        ORDER BY p.payment_date DESC, p.payment_id DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
    }
}

$highlightPaymentId = isset($_GET['payment_id']) ? (int) $_GET['payment_id'] : 0;

function paymentBadgeClassMember($status)
{
    if ($status === 'verified') {
        return 'badge-active';
    }

    if ($status === 'pending') {
        return 'badge-pending';
    }

    return 'badge-failed';
}

include '../includes/layout_top.php';
?>

<div class="dashboard-hero mb-4">
    <div>
        <span class="banner-pill">
            <i class="bi bi-wallet2"></i> Payment Member
        </span>

        <h2>Pembayaran Membership</h2>
        <p>
            Pilih paket membership, scan QR pembayaran, lalu upload bukti transfer.
            Admin akan memverifikasi pembayaran kamu.
        </p>
    </div>

    <div class="dashboard-hero-card">
        <span>Total Pembayaran</span>
        <strong><?= count($payments) ?></strong>
        <small>Riwayat pembayaran kamu</small>
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

<?php if (!empty($pendingPayment)): ?>
    <div class="premium-card mb-4">
        <div class="card-header-inline">
            <div>
                <h3 class="section-title">QR Pembayaran</h3>
                <p class="section-subtitle">
                    Scan QR berikut untuk melakukan pembayaran, lalu upload bukti pembayaran.
                </p>
            </div>
        </div>

        <div class="payment-qr-wrapper" style="display:flex; gap:24px; align-items:center; flex-wrap:wrap;">
            <div class="payment-qr-box" style="
                width:220px;
                height:220px;
                background:#ffffff;
                border:1px solid #e2e8f0;
                border-radius:20px;
                display:flex;
                align-items:center;
                justify-content:center;
                padding:14px;
            ">
                <img src="../assets/img/qr-payment.png" alt="QR Pembayaran"
                    style="width:100%; height:100%; object-fit:contain;">
            </div>

            <div class="payment-qr-info">
                <h4 style="font-size:18px; font-weight:800; margin-bottom:8px;">
                    Scan QR untuk membayar
                </h4>

                <p class="text-soft mb-2">
                    Gunakan aplikasi e-wallet atau mobile banking untuk scan QR ini.
                </p>

                <p class="mb-1">
                    <strong>Paket:</strong>
                    <?= e($pendingPayment['package_name']) ?>
                </p>

                <p class="mb-1">
                    <strong>Total Bayar:</strong>
                    Rp <?= number_format((float) $pendingPayment['amount'], 0, ',', '.') ?>
                </p>

                <p class="mb-1">
                    <strong>Status:</strong>
                    <?= e(ucfirst($pendingPayment['status'])) ?>
                </p>

                <p class="text-soft mb-0">
                    Setelah transfer, upload bukti pembayaran pada form di bawah.
                </p>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-header-inline">
            <div>
                <h3 class="section-title">Upload Bukti Pembayaran</h3>
                <p class="section-subtitle">
                    Upload screenshot atau foto bukti transfer kamu.
                </p>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="payment_id" value="<?= e($pendingPayment['payment_id']) ?>">

            <div class="form-group mb-3">
                <label class="form-label">Bukti Pembayaran</label>
                <input type="file" name="proof_file" class="form-control" accept="image/*,.pdf" required>
                <small class="text-soft">
                    Format: JPG, JPEG, PNG, atau PDF. Maksimal 2MB.
                </small>
            </div>

            <?php if (!empty($pendingPayment['proof_file'])): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i>
                    Kamu sudah pernah upload bukti pembayaran. Upload ulang akan mengganti bukti sebelumnya.
                </div>
            <?php endif; ?>

            <button type="submit" name="upload_proof" class="gradient-btn">
                <i class="bi bi-upload"></i>
                Upload Bukti
            </button>
        </form>
    </div>
<?php else: ?>
    <div class="premium-card mb-4">
        <div class="card-header-inline">
            <div>
                <h3 class="section-title">QR Pembayaran</h3>
                <p class="section-subtitle">
                    QR pembayaran akan muncul setelah kamu memilih paket membership.
                </p>
            </div>

            <a href="memberships.php" class="gradient-btn btn-sm">
                <i class="bi bi-award"></i>
                Pilih Paket
            </a>
        </div>

        <p class="text-soft mb-0">
            Pilih paket membership terlebih dahulu untuk melihat QR pembayaran.
        </p>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12">
        <div class="premium-card">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Riwayat Pembayaran</h3>
                    <p class="section-subtitle">
                        Daftar pembayaran membership kamu beserta status verifikasinya.
                    </p>
                </div>

                <a href="memberships.php" class="btn-outline-soft btn-sm">
                    <i class="bi bi-award"></i>
                    Pilih Paket
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Paket</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                            <th>Bukti</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="6">
                                    <span class="text-soft">
                                        Belum ada riwayat pembayaran.
                                    </span>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($payments as $payment): ?>
                            <?php
                            $isHighlight = $highlightPaymentId === (int) $payment['payment_id'];
                            $paymentDate = !empty($payment['payment_date'])
                                ? date('d M Y H:i', strtotime($payment['payment_date']))
                                : '-';
                            ?>

                            <tr style="<?= $isHighlight ? 'background:#fff7ef;' : '' ?>">
                                <td>
                                    #PAY-<?= e(str_pad($payment['payment_id'], 4, '0', STR_PAD_LEFT)) ?>
                                </td>

                                <td>
                                    <?= e($payment['package_name']) ?>
                                </td>

                                <td>
                                    Rp <?= number_format((float) $payment['amount'], 0, ',', '.') ?>
                                </td>

                                <td>
                                    <?= e($paymentDate) ?>
                                </td>

                                <td>
                                    <?php if (!empty($payment['proof_file'])): ?>
                                        <a href="../uploads/payments/<?= e($payment['proof_file']) ?>" target="_blank"
                                            class="btn-outline-soft btn-sm">
                                            Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-soft">Belum upload</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge-soft <?= e(paymentBadgeClassMember($payment['status'])) ?>">
                                        <?= e(ucfirst($payment['status'])) ?>
                                    </span>
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