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
    <div class="auth-shell simple-auth">
        <div class="auth-card simple-auth-card">
            <div class="auth-right">
                <div class="simple-auth-box">
                    <div class="simple-auth-logo"></div>

                    <h1 class="simple-auth-brand">GYMBRUT</h1>
                    <p class="simple-auth-subtitle">Buat akun baru untuk mulai bergabung</p>

                    <?php if (!empty($error)): ?>
                        <div class="auth-alert auth-alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="simple-auth-form">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <div class="simple-input-wrap">
                                <i class="fas fa-user"></i>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Masukkan nama lengkap"
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <div class="simple-input-wrap">
                                <i class="far fa-envelope"></i>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="Masukkan email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Nomor HP</label>
                            <div class="simple-input-wrap">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="phone" name="phone" class="form-control"
                                    placeholder="Masukkan nomor HP"
                                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="simple-input-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Konfirmasi Password</label>
                            <div class="simple-input-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirm_password" name="confirm_password"
                                    class="form-control" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <button type="submit" class="gradient-btn">Register</button>
                    </form>

                    <div class="simple-auth-links">
                        Sudah punya akun? <a href="login.php">Login sekarang</a>
                    </div>

                    <div class="simple-auth-footer">
                        <div class="simple-auth-footer-links">
                            <a href="#">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                            <a href="#">Support</a>
                        </div>

                        <div class="simple-auth-version">V2.4.1 Secure Access</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>