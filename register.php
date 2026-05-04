<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Nama, email, dan password wajib diisi.';
    } else {
        // cek email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users (name, email, phone, password, role, created_at)
                VALUES (?, ?, ?, ?, 'member', NOW())
            ");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed);
            $stmt->execute();

            $_SESSION['success_register'] = 'Registrasi berhasil! Silakan login.';
            header("Location: login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GYMBRUT</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="assets/img/Logo%20Gymbrut.svg" alt="Gymbrut Logo">
            </div>

            <h1 class="auth-title">Register</h1>
            <p class="auth-subtitle">Buat akun baru di GYMBRUT</p>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">

                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">No HP</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="gradient-btn w-100">
                    Daftar
                </button>
            </form>

            <div class="auth-links">
                Sudah punya akun? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>

</html>