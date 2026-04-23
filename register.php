<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            /* 
               Karena role di database Anda adalah enum('admin','member'),
               maka default register dibuat menjadi member
            */
            $role = 'member';

            $stmt = $conn->prepare("
                INSERT INTO users (name, email, phone, password, role)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $role);

            if ($stmt->execute()) {
                $_SESSION['success_register'] = 'Registrasi berhasil. Silakan login.';
                header("Location: login.php");
                exit;
            } else {
                $error = 'Registrasi gagal. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gymbrut</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>

<body>
    <div class="auth-page"> 
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fas fa-user-plus"></i>
            </div>

            <h1 class="auth-title">GYMBRUT</h1>
            <p class="auth-subtitle">Buat akun baru untuk mulai bergabung</p>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control"
                        placeholder="Masukkan nama lengkap"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="Masukkan email" 
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Nomor HP</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                        placeholder="Masukkan nomor HP"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Masukkan password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="form-control" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="gradient-btn w-100">Register</button>
            </form>

            <div class="auth-links">
                Sudah punya akun? <a href="login.php">Login sekarang</a>
            </div>
        </div>
    </div>
</body>

</html>