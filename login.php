<?php session_start(); ?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | GYMBRUT</title>
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
                            <div class="brand-subtitle">Gym Management System</div>
                        </div>
                    </div>

                    <div class="auth-kicker">Welcome back</div>
                    <h1 class="auth-title mt-2 mb-3">Kelola gym lebih rapi, modern, dan enak dilihat.</h1>
                    <p class="auth-text mb-0">
                        Satu tempat untuk admin dan member mengelola membership, pembayaran, check-in,
                        workout plan, dan progress latihan dengan tampilan yang clean dan nyaman.
                    </p>

                    <div class="auth-feature-list">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="bi bi-grid-1x2-fill"></i></div>
                            <div>
                                <strong class="d-block">Dashboard ringkas</strong>
                                <small class="text-soft">Statistik penting tampil rapi dan mudah dibaca.</small>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
                            <div>
                                <strong class="d-block">Membership & pembayaran</strong>
                                <small class="text-soft">Pantau transaksi dan status member dalam satu alur.</small>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                            <div>
                                <strong class="d-block">Progress latihan</strong>
                                <small class="text-soft">Bantu member tetap konsisten dan termotivasi.</small>
                            </div>
                        </div>
                    </div>

                    <div class="auth-mini-stats">
                        <div class="auth-mini-card">
                            <small>Active Members</small>
                            <strong>1,248</strong>
                        </div>
                        <div class="auth-mini-card">
                            <small>Check-in Today</small>
                            <strong>184</strong>
                        </div>
                        <div class="auth-mini-card">
                            <small>Monthly Revenue</small>
                            <strong>92M</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="auth-right">
                <div class="auth-panel-top">
                    <div>
                        <h2 class="fw-bold mb-1">Masuk ke akun</h2>
                        <p class="text-soft mb-0">Silakan login untuk melanjutkan ke dashboard GYMBRUT.</p>
                    </div>
                    <a href="register.php" class="gradient-btn">Register</a>
                </div>

                <div class="auth-form-card">
                    <form action="login_process.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="bi bi-envelope text-secondary"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-start-0 rounded-end-4"
                                    placeholder="contoh@email.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4">
                                    <i class="bi bi-lock text-secondary"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-start-0 rounded-end-4"
                                    placeholder="Masukkan password">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Masuk sebagai</label>
                            <select name="role" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="member">Member</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label text-soft" for="rememberMe">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none" style="color: var(--primary);">Lupa password?</a>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="gradient-btn py-3">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Login Sekarang
                            </button>
                        </div>
                    </form>

                    <div class="auth-divider">atau</div>

                    <div class="text-center">
                        <span class="text-soft">Belum punya akun?</span>
                        <a href="register.php" class="fw-semibold ms-1" style="color: var(--primary);">Buat akun
                            baru</a>
                    </div>
                </div>

                <div class="auth-note">
                    <strong>Vibe baru GYMBRUT</strong><br>
                    Tampilan dibuat lebih terang, rapi, modern, dan tetap profesional supaya terasa seperti web app gym
                    masa kini.
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>