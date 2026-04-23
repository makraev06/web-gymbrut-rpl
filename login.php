<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = $_SESSION['success_register'] ?? '';
unset($_SESSION['success_register']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            $role = strtolower($user['role'] ?? 'user');

            // Kalau di database masih pakai "member", samakan ke "user"
            if ($role === 'member') {
                $role = 'user';
            }

            $_SESSION['user_id'] = $user['user_id'] ?? $user['id'] ?? null;
            $_SESSION['name'] = $user['name'] ?? '';
            $_SESSION['email'] = $user['email'] ?? '';
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: member/dashboard.php");
            }
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gymbrut</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets\css\theme.css">
</head>

<body>
    <div class="auth-page"> 
        <div class="auth-card">
            <div class="auth-logo">
                <i class="fas fa-dumbbell"></i>
            </div>

            <h1 class="auth-title">GYMBRUT</h1>
            <p class="auth-subtitle">Sistem Login Fitness &amp; Gym Management</p>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="auth-alert auth-alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="Masukkan email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="gradient-btn w-100">Login</button>
            </form>

            <div class="auth-links">
                Belum punya akun? <a href="register.php">Register sekarang</a>
            </div>
        </div>
    </div>
</body>

</html>