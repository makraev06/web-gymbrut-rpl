<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | GYMBRUT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body>
<div class="auth-shell">
    <div class="hero-banner auth-card">
        <section class="auth-left text-white">
            <span class="banner-pill"><i class="bi bi-fire"></i> Join The Strongest Community</span>
            <h1 class="display-5 fw-bold mt-4">Mulai perjalanan fitness kamu bersama GYMBRUT.</h1>
            <p class="text-white-50 mt-3 fs-5">Daftar member baru, pilih paket membership, dan atur target latihan langsung dari satu halaman.</p>
            <ul class="metric-list mt-4 list-unstyled">
                <li><span>Workout Guide Otomatis</span><strong>5 kategori</strong></li>
                <li><span>Check-in Cepat</span><strong>1 klik</strong></li>
                <li><span>Membership Tracking</span><strong>real-time</strong></li>
            </ul>
        </section>
        <section class="auth-right text-white">
            <h2 class="fw-bold mb-1">Register Member</h2>
            <p class="text-white-50 mb-4">Isi data di bawah ini untuk membuat akun baru.</p>
            <form class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" placeholder="Nama lengkap"></div>
                <div class="col-md-6"><label class="form-label">No HP</label><input class="form-control" placeholder="08xxxxxxxxxx"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" placeholder="member@gymbrut.com"></div>
                <div class="col-md-6"><label class="form-label">Gender</label><select class="form-select"><option>Laki-laki</option><option>Perempuan</option></select></div>
                <div class="col-md-4"><label class="form-label">Usia</label><input class="form-control" placeholder="22"></div>
                <div class="col-md-4"><label class="form-label">Tinggi</label><input class="form-control" placeholder="170 cm"></div>
                <div class="col-md-4"><label class="form-label">Berat</label><input class="form-control" placeholder="68 kg"></div>
                <div class="col-12"><label class="form-label">Target Fitness</label><select class="form-select"><option>Fat Loss</option><option>Bulking</option><option>Cardio</option><option>Strength</option><option>Beginner</option></select></div>
                <div class="col-md-6"><label class="form-label">Password</label><input type="password" class="form-control" placeholder="••••••••"></div>
                <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" class="form-control" placeholder="••••••••"></div>
                <div class="col-12 d-grid mt-2"><a href="login.php" class="gradient-btn text-center text-decoration-none py-3">Daftar Sekarang</a></div>
            </form>
        </section>
    </div>
</div>
</body>
</html>
