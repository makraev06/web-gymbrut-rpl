<?php
$pageTitle = 'Dashboard | GYMBRUT';
$activePage = 'dashboard';
$topbarTitle = 'Dashboard Gym Management';
$searchPlaceholder = 'Cari member aktif, invoice, check-in...';
include '../includes/layout_top.php';
$role = strtolower($_SESSION['user_role'] ?? 'admin');
?>
<div class="hero-banner mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="banner-pill"><i class="bi bi-stars"></i> Premium Fitness Management</span>
            <h2 class="mt-3 mb-2 fw-bold">NO PAIN NO GAIN 💪</h2>
            <p class="text-white-50 mb-0">Transformasi project lama menjadi sistem manajemen gym modern dengan admin
                dashboard, membership tracking, pembayaran, check-in, dan progress latihan.</p>
        </div>
        <div class="col-lg-4 text-center">
            <div class="progress-ring">
                <div class="progress-ring-inner">
                    <strong class="d-block fs-3">75%</strong>
                    <small class="text-white-50">Membership Cycle</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($role === 'admin'): ?>
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Total Member Aktif</div>
                        <div class="stat-value">1,248</div>
                        <div class="text-success small">+8.4% vs bulan lalu</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Membership Habis Bulan Ini</div>
                        <div class="stat-value">86</div>
                        <div class="text-warning small">perlu follow up</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Pendapatan Bulan Ini</div>
                        <div class="stat-value">Rp 92,4Jt</div>
                        <div class="text-info small">target 88% tercapai</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Check-in Hari Ini</div>
                        <div class="stat-value">184</div>
                        <div class="text-danger small">jam sibuk 17:00</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-geo-alt-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="chart-box premium-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="section-title">Member Growth</div><small class="text-white-50">Pertumbuhan member aktif
                            6 bulan terakhir</small>
                    </div><span class="badge-soft badge-active">Growth +14%</span>
                </div><canvas id="memberGrowthChart"></canvas>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="premium-card h-100">
                <div class="section-title mb-3">Quick Insight</div>
                <ul class="metric-list list-unstyled mb-0">
                    <li><span>Pending payment</span><strong>18 transaksi</strong></li>
                    <li><span>Top package</span><strong>Bulanan</strong></li>
                    <li><span>Most active hour</span><strong>18.00 - 20.00</strong></li>
                    <li><span>New members this week</span><strong>34 orang</strong></li>
                    <li><span>Retention rate</span><strong>91%</strong></li>
                </ul>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="chart-box premium-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="section-title">Income Analytics</div><small class="text-white-50">Performa income
                            membership dan add-on</small>
                    </div><span class="badge-soft badge-pending">Revenue Live</span>
                </div><canvas id="incomeChart"></canvas>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="premium-card h-100">
                <div class="section-title mb-3">Recent Activities</div>
                <div class="d-grid gap-3">
                    <div class="glass-soft p-3 rounded-4"><strong>Member baru</strong>
                        <div class="text-white-50 small">Nadia Pratama daftar paket bulanan.</div>
                    </div>
                    <div class="glass-soft p-3 rounded-4"><strong>Payment verified</strong>
                        <div class="text-white-50 small">Invoice #INV-1208 berhasil diverifikasi admin.</div>
                    </div>
                    <div class="glass-soft p-3 rounded-4"><strong>Peak traffic</strong>
                        <div class="text-white-50 small">Area strength penuh pada pukul 18:12.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Membership Status</div>
                        <div class="stat-value">Aktif</div>
                        <div class="text-success small">hingga 28 Mei 2026</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Hari Tersisa</div>
                        <div class="stat-value">17</div>
                        <div class="text-warning small">siap perpanjang</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Progress Latihan</div>
                        <div class="stat-value">78%</div>
                        <div class="text-info small">4/5 target minggu ini</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-trophy-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Workout Hari Ini</div>
                        <div class="stat-value">Strength</div>
                        <div class="text-danger small">Push Day 18:00</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-fire"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="premium-card h-100">
                <div class="section-title">Motivational Quote</div>
                <blockquote class="fs-3 fw-bold mt-3">"The body achieves what the mind believes."</blockquote>
                <p class="text-white-50">Tetap konsisten, fokus pada progres, dan jaga ritme latihanmu.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="glass-soft p-3 rounded-4"><strong>Jadwal Hari Ini</strong>
                            <div class="text-white-50 small">Warm up, bench press, shoulder press, tricep pushdown</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-soft p-3 rounded-4"><strong>Target Mingguan</strong>
                            <div class="text-white-50 small">5 sesi latihan, defisit 300 kalori, tidur 7 jam+</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="premium-card h-100 text-center">
                <div class="section-title mb-3">Membership Circle</div>
                <div class="progress-ring">
                    <div class="progress-ring-inner"><strong class="d-block fs-3">17</strong><small
                            class="text-white-50">hari tersisa</small></div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    const chartOptions = { responsive: true, plugins: { legend: { labels: { color: '#fff' } } }, scales: { x: { ticks: { color: '#ddd' }, grid: { color: 'rgba(255,255,255,.08)' } }, y: { ticks: { color: '#ddd' }, grid: { color: 'rgba(255,255,255,.08)' } } } };
    const memberCtx = document.getElementById('memberGrowthChart');
    if (memberCtx) new Chart(memberCtx, { type: 'line', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'], datasets: [{ label: 'Members', data: [780, 845, 930, 1010, 1140, 1248], borderColor: '#ff7a00', backgroundColor: 'rgba(255,122,0,.18)', tension: .35, fill: true }] }, options: chartOptions });
    const incomeCtx = document.getElementById('incomeChart');
    if (incomeCtx) new Chart(incomeCtx, { type: 'bar', data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'], datasets: [{ label: 'Income', data: [48, 52, 61, 67, 80, 92], backgroundColor: 'rgba(255,122,0,.8)', borderRadius: 14 }, { label: 'Add-on', data: [6, 8, 9, 11, 12, 14], backgroundColor: 'rgba(255,255,255,.24)', borderRadius: 14 }] }, options: chartOptions });
</script>
<?php include '../includes/layout_bottom.php'; ?>