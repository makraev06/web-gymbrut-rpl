<?php
session_start();
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = strtolower(trim($_POST['role'] ?? ''));

if ($email === '' || $password === '' || $role === '') {
    header("Location: login.php?error=empty");
    exit;
}

/*
|--------------------------------------------------------------------------
| MODE 1: Pakai database jika terkoneksi
|--------------------------------------------------------------------------
| Silakan sesuaikan nama tabel/kolom kalau struktur database kamu beda.
| Contoh asumsi:
| tabel: users
| kolom: id, name, email, password, role
|--------------------------------------------------------------------------
*/
if ($conn) {
    $emailEscaped = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM users WHERE email = '$emailEscaped' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        $dbPassword = $user['password'] ?? '';
        $dbRole = strtolower($user['role'] ?? '');

        $passwordValid = false;

        // support password_hash maupun password plain text
        if (password_verify($password, $dbPassword) || $password === $dbPassword) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            header("Location: login.php?error=invalid");
            exit;
        }

        if ($dbRole !== $role) {
            header("Location: login.php?error=role");
            exit;
        }

        $_SESSION['user_id'] = $user['id'] ?? 0;
        $_SESSION['name'] = $user['name'] ?? 'Gym User';
        $_SESSION['user_name'] = $user['name'] ?? 'Gym User';
        $_SESSION['email'] = $user['email'] ?? $email;
        $_SESSION['role'] = $dbRole;
        $_SESSION['user_role'] = $dbRole;

        if ($dbRole === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| MODE 2: Fallback demo login kalau database belum siap
|--------------------------------------------------------------------------
*/
$demoUsers = [
    [
        'id' => 1,
        'name' => 'Admin Gym',
        'email' => 'admin@gymbrut.com',
        'password' => 'admin123',
        'role' => 'admin'
    ],
    [
        'id' => 2,
        'name' => 'Member Gym',
        'email' => 'member@gymbrut.com',
        'password' => 'member123',
        'role' => 'member'
    ]
];

$foundUser = null;
foreach ($demoUsers as $u) {
    if ($u['email'] === $email) {
        $foundUser = $u;
        break;
    }
}

if (!$foundUser) {
    header("Location: login.php?error=invalid");
    exit;
}

if ($foundUser['password'] !== $password) {
    header("Location: login.php?error=invalid");
    exit;
}

if ($foundUser['role'] !== $role) {
    header("Location: login.php?error=role");
    exit;
}

$_SESSION['user_id'] = $foundUser['id'];
$_SESSION['name'] = $foundUser['name'];
$_SESSION['user_name'] = $foundUser['name'];
$_SESSION['email'] = $foundUser['email'];
$_SESSION['role'] = $foundUser['role'];
$_SESSION['user_role'] = $foundUser['role'];

if ($foundUser['role'] === 'admin') {
    header("Location: admin/dashboard.php");
} else {
    header("Location: user/dashboard.php");
}
exit;