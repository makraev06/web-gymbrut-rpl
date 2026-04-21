<?php session_start(); ?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | GYMBRUT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>

<body>
    <div class="auth-shell">
        <div class="auth-card">
            <section class="auth-left d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-box d-inline-flex mb-4">
                        <div class="brand-icon"><i class="bi bi-fire"></i></div>
                        <div>
                            <div class="brand-title">GYMBRUT</div>
                            <div class="brand-subtitle">Start Your Fitness Journey</div>
                        </div>
                    </div>

                    <div class="auth-kicker">Join us</div>
                    <h1 class="auth-title mt-2 mb-3">Mulai perjalanan fitness kamu dengan tampilan yang simpel dan
                        fresh.</h1>
                    <p class="auth-text mb-0">
                        Daftar sebagai member untuk mengakses membership, jadwal latihan, progress, dan berbagai fitur
                        gym management dalam satu akun.
                    </p>

                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="bi bi-lightning-charge"></i></div>
                            <div>
                                <strong class="d-block">Pendaftaran cepat</strong>
                                <small class="text-soft">Form dibuat lebih ringkas, rapi, dan mudah dipahami.</small>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="bi bi-heart-pulse"></i></div>
                            <div>
                                <strong class="d-block">Target fitness personal</strong>
                                <small class="text-soft">Pilih tujuan latihan sesuai kebutuhanmu.</small>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="bi bi-check2-circle"></i></div>
                            <div>
                                <strong class="d-block">UI nyaman dilihat</strong>
                                <small class="text-soft">Nuansa modern, ringan, dan cocok untuk anak muda.</small>
                            </div>
                        </div>
                    </div>

                    <div class="auth-mini-stats">
                        <div class="auth-mini-card">
                            <small>Workout Types</small>
                            <strong>5+</strong>
                        </div>
                        <div class="auth-mini-card">
                            <small>Check-in</small>
                            <strong>1 Klik</strong>
                        </div>
                        <div class="auth-mini-card">
                            <small>Tracking</small>
                            <strong>Realtime</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="auth-right">
                <div class="auth-panel-top">
                    <div>
                        <h2 class="fw-bold mb-1">Buat akun member</h2>
                        <p class="text-soft mb-0">Isi data berikut untuk mulai menggunakan sistem GYMBRUT.</p>
                    </div>
                    <a href="login.php" class="gradient-btn">Login</a>
                </div>

                <div class="auth-form-card">
                    <form class="row g-3" action="#" method="post">
                        <div class="col-md-6">
                            <label class="form-label">Nama lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="member@email.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Pilih gender</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Usia</label>
                            <input type="text" name="usia" class="form-control" placeholder="22">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tinggi badan</label>
                            <input type="text" name="tinggi" class="form-control" placeholder="170 cm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Berat badan</label>
                            <input type="text" name="berat" class="form-control" placeholder="68 kg">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Target fitness</label>
                            <select name="target_fitness" class="form-select">
                                <option value="">Pilih target</option>
                                <option>Fat Loss</option>
                                <option>Bulking</option>
                                <option>Strength</option>
                                <option>Cardio</option>
                                <option>Beginner Fitness</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi password</label>
                            <input type="password" name="confirm_password" class="form-control"
                                placeholder="Ulangi password">
                        </div>

                        <div class="col-12 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agreeRegister">
                                <label class="form-check-label text-soft" for="agreeRegister">
                                    Saya setuju dengan data pendaftaran yang diisi.
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-grid mt-2">
                            <button type="submit" class="gradient-btn py-3">
                                <i class="bi bi-person-plus-fill"></i>
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>

                    <div class="auth-divider">atau</div>

                    <div class="text-center">
                        <span class="text-soft">Sudah punya akun?</span>
                        <a href="login.php" class="fw-semibold ms-1" style="color: var(--primary);">Masuk di sini</a>
                    </div>
                </div>

                <div class="auth-note">
                    <strong>Desain lebih clean</strong><br>
                    Form register dibuat lebih ringan, modern, dan tidak terlalu ramai supaya nyaman dipakai.
                </div>
            </section>
        </div>
    </div>
</body>

</html>