<?php session_start(); ?>
<!doctype html>
<html lang="en">

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
        <div class="hero-banner auth-card">
            <section class="auth-left text-white d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-box d-inline-flex">
                        <div class="brand-icon">🔥</div>
                        <div>
                            <div class="brand-title">GYMBRUT</div>
                            <div class="brand-subtitle">Train Stronger Everyday</div>
                        </div>
                    </div>
                    <h1 class="display-5 fw-bold mt-4">Sistem manajemen gym modern untuk admin dan member.</h1>
                    <p class="text-white-50 mt-3 fs-5">Kelola membership, pembayaran, check-in, workout guide, dan
                        progress latihan dalam satu dashboard premium.</p>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-sm-4">
                        <div class="stat-card">
                            <div class="stat-label">Active Members</div>
                            <div class="stat-value">1,248</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="stat-card">
                            <div class="stat-label">Today's Check-in</div>
                            <div class="stat-value">184</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="stat-card">
                            <div class="stat-label">Monthly Income</div>
                            <div class="stat-value">92M</div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="auth-right text-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Welcome Back</h2>
                        <p class="text-white-50 mb-0">Masuk ke dashboard GYMBRUT</p>
                    </div>
                    <a href="register.php" class="gradient-btn text-decoration-none">Register</a>
                </div>
                <form>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="admin@gymbrut.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Login As</label>
                        <select class="form-select">
                            <option>Admin</option>
                            <option>Member</option>
                        </select>
                    </div>
                    <div class="d-grid mt-4">
                        <a href="dashboard.php" class="gradient-btn text-decoration-none text-center py-3">Login
                            Sekarang</a>
                    </div>
                </form>
                <div class="upload-box mt-4">
                    <i class="bi bi-lightning-charge-fill fs-1 text-warning"></i>
                    <p class="mb-1 fw-semibold mt-2">Enerjik, modern, dan siap tumbuh</p>
                    <small class="text-white-50">UI gym premium dengan warna orange, black, dan glow modern.</small>
                </div>
            </section>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>