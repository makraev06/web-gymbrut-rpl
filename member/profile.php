<?php
/* member/profile.php */
session_start();

require_once '../config/database.php';
require_once '../includes/auth_user.php';

$pageTitle = 'Profile Saya';
$topbarTitle = 'Profile Saya';
$topbarSubtitle = 'Kelola informasi akun, data tubuh, dan target fitness kamu.';
$searchPlaceholder = 'Cari profile...';
$bodyClass = 'member-profile-page';

$userId = (int) ($_SESSION['user_id'] ?? 0);
$success = '';
$error = '';

if ($userId <= 0) {
    header('Location: ../login.php');
    exit;
}

/* =========================
   AMBIL DATA USER
========================= */
$user = [
    'user_id' => $userId,
    'name' => '',
    'email' => '',
    'phone' => '',
    'gender' => '',
    'age' => '',
    'height' => '',
    'weight' => '',
    'target_fitness' => '',
    'photo' => null,
    'created_at' => null
];

$stmt = $conn->prepare("
    SELECT 
        user_id,
        name,
        email,
        phone,
        gender,
        age,
        height,
        weight,
        target_fitness,
        photo,
        created_at
    FROM users
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

/* =========================
   UPDATE FOTO PROFILE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_photo'])) {
    $photoName = $user['photo'] ?? null;

    if (empty($_POST['cropped_photo'])) {
        $error = 'Pilih foto terlebih dahulu.';
    } else {
        $croppedPhoto = $_POST['cropped_photo'];

        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $croppedPhoto)) {
            $error = 'Format foto tidak valid.';
        } else {
            $imageData = preg_replace('/^data:image\/(png|jpeg|jpg|webp);base64,/', '', $croppedPhoto);
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                $error = 'Foto gagal diproses.';
            } else {
                $uploadDir = __DIR__ . '/../assets/uploads/profile/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $photoName = 'profile_' . $userId . '_' . time() . '.png';
                $targetPath = $uploadDir . $photoName;

                if (!file_put_contents($targetPath, $imageData)) {
                    $error = 'Foto gagal disimpan.';
                } else {
                    $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $photoName, $userId);

                    if ($stmt->execute()) {
                        $user['photo'] = $photoName;
                        $success = 'Foto profile berhasil diperbarui.';
                    } else {
                        $error = 'Gagal menyimpan foto ke database.';
                    }
                }
            }
        }
    }
}

/* =========================
   UPDATE PROFILE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $age = (int) ($_POST['age'] ?? 0);
    $height = (float) ($_POST['height'] ?? 0);
    $weight = (float) ($_POST['weight'] ?? 0);
    $targetFitness = trim($_POST['target_fitness'] ?? '');

    if ($name === '') {
        $error = 'Nama wajib diisi.';
    } elseif ($email === '') {
        $error = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($age < 0) {
        $error = 'Umur tidak boleh negatif.';
    } elseif ($height < 0) {
        $error = 'Tinggi badan tidak boleh negatif.';
    } elseif ($weight < 0) {
        $error = 'Berat badan tidak boleh negatif.';
    } else {
        $stmt = $conn->prepare("
            SELECT user_id
            FROM users
            WHERE email = ?
            AND user_id != ?
            LIMIT 1
        ");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $checkEmail = $stmt->get_result();

        if ($checkEmail && $checkEmail->num_rows > 0) {
            $error = 'Email sudah digunakan oleh user lain.';
        }
    }

    if ($error === '') {
        $stmt = $conn->prepare("
            UPDATE users
            SET 
                name = ?,
                email = ?,
                phone = ?,
                gender = ?,
                age = ?,
                height = ?,
                weight = ?,
                target_fitness = ?
            WHERE user_id = ?
        ");

        $stmt->bind_param(
            "ssssiddsi",
            $name,
            $email,
            $phone,
            $gender,
            $age,
            $height,
            $weight,
            $targetFitness,
            $userId
        );

        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            $success = 'Profile berhasil diperbarui.';

            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
            $user['gender'] = $gender;
            $user['age'] = $age;
            $user['height'] = $height;
            $user['weight'] = $weight;
            $user['target_fitness'] = $targetFitness;
        } else {
            $error = 'Gagal memperbarui profile.';
        }
    }
}

    
/* =========================
   UPDATE PASSWORD
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Semua field password wajib diisi.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $dbUser = $stmt->get_result()->fetch_assoc();

        if (!$dbUser) {
            $error = 'Data user tidak ditemukan.';
        } else {
            $dbPassword = $dbUser['password'];
            $passwordValid = password_verify($currentPassword, $dbPassword) || $currentPassword === $dbPassword;

            if (!$passwordValid) {
                $error = 'Password lama salah.';
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->bind_param("si", $hashedPassword, $userId);

                if ($stmt->execute()) {
                    $success = 'Password berhasil diperbarui.';
                } else {
                    $error = 'Gagal mengubah password.';
                }
            }
        }
    }
}

/* =========================
   DATA TAMBAHAN
========================= */
$createdAtText = !empty($user['created_at'])
    ? date('d M Y', strtotime($user['created_at']))
    : '-';

$ageText = !empty($user['age']) ? $user['age'] . ' tahun' : '-';
$heightText = !empty($user['height']) ? number_format((float) $user['height'], 1, ',', '.') . ' cm' : '-';
$weightText = !empty($user['weight']) ? number_format((float) $user['weight'], 1, ',', '.') . ' kg' : '-';
$targetText = !empty($user['target_fitness']) ? $user['target_fitness'] : '-';

$profilePhoto = !empty($user['photo'])
    ? '../assets/uploads/profile/' . $user['photo']
    : '../assets/img/default-avatar.svg';

$bmi = 0;
$bmiText = '-';
$bmiStatus = '-';

if (!empty($user['height']) && !empty($user['weight']) && (float) $user['height'] > 0) {
    $heightMeter = (float) $user['height'] / 100;
    $bmi = (float) $user['weight'] / ($heightMeter * $heightMeter);
    $bmiText = number_format($bmi, 1, ',', '.');

    if ($bmi < 18.5) {
        $bmiStatus = 'Underweight';
    } elseif ($bmi < 25) {
        $bmiStatus = 'Normal';
    } elseif ($bmi < 30) {
        $bmiStatus = 'Overweight';
    } else {
        $bmiStatus = 'Obese';
    }
}

$profileCompletion = 0;
$fields = ['name', 'email', 'phone', 'gender', 'age', 'height', 'weight', 'target_fitness'];
foreach ($fields as $field) {
    if (!empty($user[$field])) {
        $profileCompletion++;
    }
}
$profileCompletionPercent = round(($profileCompletion / count($fields)) * 100);
?>

<?php include '../includes/layout_top.php'; ?>

<div class="dashboard-hero mb-4">
    <div>
        <span class="banner-pill">
            <i class="bi bi-person-circle"></i> Member Profile
        </span>

        <h2>Profile Saya</h2>
        <p>
            Kelola data akun, informasi tubuh, dan target fitness agar sistem
            bisa menampilkan progress yang lebih sesuai.
        </p>
    </div>

    <div class="dashboard-hero-card">
        <span>Kelengkapan Profile</span>
        <strong><?= e($profileCompletionPercent) ?>%</strong>
        <small><?= e($user['name'] ?: 'Member') ?></small>
    </div>
</div>

<div class="premium-card mb-4 mobile-profile-logout">
    <div class="card-header-inline">
        <div>
            <h3 class="section-title">Akun Member</h3>
            <p class="section-subtitle">
                Keluar dari akun member kamu.
            </p>
        </div>

        <a href="../logout.php" class="btn-outline-soft" onclick="return confirm('Yakin ingin logout?')"
            style="color:#dc2626; border-color:#fecaca; background:#fff1f2;">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
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


<div class="premium-card mb-4 profile-photo-card">
    <div class="profile-photo-wrapper">
        <img id="photoPreview" src="<?= e($profilePhoto) ?>" alt="Foto Profile" class="profile-photo-img">

        <div>
            <h3 class="section-title"><?= e($user['name'] ?: 'Member') ?></h3>
            <p class="section-subtitle"><?= e($user['email'] ?: '-') ?></p>

            <form method="POST" class="profile-upload-mini">
                <input type="hidden" name="update_photo" value="1">
                <input type="hidden" name="cropped_photo" id="croppedPhoto">

                <label class="btn-outline-soft btn-sm">
                    <i class="bi bi-camera-fill"></i>
                    Pilih Foto
                    <input id="photoInput" type="file" accept="image/png, image/jpeg, image/webp" hidden>
                </label>

                <button type="submit" class="gradient-btn btn-sm">
                    Simpan Foto
                </button>
            </form>

            <small class="text-soft">
                Foto akan otomatis dicrop menjadi bulat.
            </small>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 profile-stats-row">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-modern">
            <div>
                <p class="stat-label">Nama Member</p>
                <h3 class="stat-value" style="font-size:24px;">
                    <?= e($user['name'] ?: '-') ?>
                </h3>
                <span class="stat-meta"><?= e($user['email'] ?: '-') ?></span>
            </div>

            <div class="stat-icon">
                <i class="bi bi-person-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-modern">
            <div>
                <p class="stat-label">Target Fitness</p>
                <h3 class="stat-value" style="font-size:24px;">
                    <?= e($targetText) ?>
                </h3>
                <span class="stat-meta">Tujuan latihan utama</span>
            </div>

            <div class="stat-icon">
                <i class="bi bi-bullseye"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-modern">
            <div>
                <p class="stat-label">BMI</p>
                <h3 class="stat-value" style="font-size:24px;">
                    <?= e($bmiText) ?>
                </h3>
                <span class="stat-meta"><?= e($bmiStatus) ?></span>
            </div>

            <div class="stat-icon">
                <i class="bi bi-activity"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-modern">
            <div>
                <p class="stat-label">Bergabung</p>
                <h3 class="stat-value" style="font-size:24px;">
                    <?= e($createdAtText) ?>
                </h3>
                <span class="stat-meta">Tanggal akun dibuat</span>
            </div>

            <div class="stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="premium-card h-100">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Edit Profile</h3>
                    <p class="section-subtitle">
                        Perbarui informasi pribadi dan data fitness kamu.
                    </p>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= e($user['name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>"
                            placeholder="Contoh: 08123456789">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Pilih Gender</option>
                            <option value="Laki-laki" <?= ($user['gender'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>
                                Laki-laki
                            </option>
                            <option value="Perempuan" <?= ($user['gender'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>
                                Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Umur</label>
                        <input type="number" name="age" class="form-control" value="<?= e($user['age'] ?? '') ?>"
                            min="0" placeholder="Contoh: 21">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" name="height" class="form-control"
                            value="<?= e($user['height'] ?? '') ?>" min="0" placeholder="Contoh: 170">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control"
                            value="<?= e($user['weight'] ?? '') ?>" min="0" placeholder="Contoh: 65">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Target Fitness</label>
                        <select name="target_fitness" class="form-select">
                            <option value="">Pilih Target</option>
                            <option value="Bulking"
                                <?= ($user['target_fitness'] ?? '') === 'Bulking' ? 'selected' : '' ?>>
                                Bulking
                            </option>
                            <option value="Cutting"
                                <?= ($user['target_fitness'] ?? '') === 'Cutting' ? 'selected' : '' ?>>
                                Cutting
                            </option>
                            <option value="Maintain"
                                <?= ($user['target_fitness'] ?? '') === 'Maintain' ? 'selected' : '' ?>>
                                Maintain
                            </option>
                            <option value="Fat Loss"
                                <?= ($user['target_fitness'] ?? '') === 'Fat Loss' ? 'selected' : '' ?>>
                                Fat Loss
                            </option>
                            <option value="Strength"
                                <?= ($user['target_fitness'] ?? '') === 'Strength' ? 'selected' : '' ?>>
                                Strength
                            </option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="gradient-btn mt-4">
                    <i class="bi bi-save"></i>
                    Simpan Profile
                </button>
            </form>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="premium-card h-100 profile-summary-card">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Ringkasan Data</h3>
                    <p class="section-subtitle">
                        Informasi singkat dari profile kamu.
                    </p>
                </div>
            </div>

            <ul class="metric-list">
                <li>
                    <span>Nama</span>
                    <strong><?= e($user['name'] ?: '-') ?></strong>
                </li>
                <li>
                    <span>Email</span>
                    <strong><?= e($user['email'] ?: '-') ?></strong>
                </li>
                <li>
                    <span>Telepon</span>
                    <strong><?= e($user['phone'] ?: '-') ?></strong>
                </li>
                <li>
                    <span>Gender</span>
                    <strong><?= e($user['gender'] ?: '-') ?></strong>
                </li>
                <li>
                    <span>Umur</span>
                    <strong><?= e($ageText) ?></strong>
                </li>
                <li>
                    <span>Tinggi</span>
                    <strong><?= e($heightText) ?></strong>
                </li>
                <li>
                    <span>Berat</span>
                    <strong><?= e($weightText) ?></strong>
                </li>
                <li>
                    <span>Target</span>
                    <strong><?= e($targetText) ?></strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="premium-card h-100">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Ubah Password</h3>
                    <p class="section-subtitle">
                        Gunakan password minimal 6 karakter.
                    </p>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="update_password" value="1">

                <div class="form-group mb-3">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-control"
                        placeholder="Masukkan password lama" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru"
                        required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="form-control"
                        placeholder="Ulangi password baru" required>
                </div>

                <button type="submit" class="gradient-btn">
                    <i class="bi bi-key-fill"></i>
                    Update Password
                </button>
            </form>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="premium-card h-100 profile-notes-card">
            <div class="card-header-inline">
                <div>
                    <h3 class="section-title">Catatan Profile</h3>
                    <p class="section-subtitle">
                        Saran agar data member lebih rapi.
                    </p>
                </div>
            </div>

            <div class="card-list">
                <div class="list-row">
                    <div>
                        <p class="list-row-title">Lengkapi data tubuh</p>
                        <p class="list-row-subtitle">
                            Tinggi dan berat badan dipakai untuk menghitung BMI.
                        </p>
                    </div>
                    <span class="badge-soft badge-info">Info</span>
                </div>

                <div class="list-row">
                    <div>
                        <p class="list-row-title">Sesuaikan target fitness</p>
                        <p class="list-row-subtitle">
                            Target membantu member memilih workout yang sesuai.
                        </p>
                    </div>
                    <span class="badge-soft badge-pending">Tips</span>
                </div>

                <div class="list-row">
                    <div>
                        <p class="list-row-title">Gunakan password yang aman</p>
                        <p class="list-row-subtitle">
                            Minimal 6 karakter dan jangan mudah ditebak.
                        </p>
                    </div>
                    <span class="badge-soft badge-active">Aman</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const croppedPhoto = document.getElementById('croppedPhoto');

    if (!photoInput || !photoPreview || !croppedPhoto) return;

    photoInput.addEventListener('change', function() {
        const file = this.files[0];

        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            alert('Format foto harus JPG, PNG, atau WEBP.');
            this.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function(event) {
            const img = new Image();

            img.onload = function() {
                const size = Math.min(img.width, img.height);
                const startX = (img.width - size) / 2;
                const startY = (img.height - size) / 2;

                const canvas = document.createElement('canvas');
                canvas.width = 400;
                canvas.height = 400;

                const ctx = canvas.getContext('2d');

                ctx.drawImage(
                    img,
                    startX,
                    startY,
                    size,
                    size,
                    0,
                    0,
                    400,
                    400
                );

                const croppedData = canvas.toDataURL('image/png');

                photoPreview.src = croppedData;
                croppedPhoto.value = croppedData;
            };

            img.src = event.target.result;
        };

        reader.readAsDataURL(file);
    });
});
</script>

<?php include '../includes/layout_bottom.php'; ?>