<?php
$pageTitle = 'Dashboard Admin';
$pageSubtitle = 'Pantau operasional gym, membership, pembayaran, dan workout member.';
include '../includes/layout_top.php';
?>

<div class="hero-banner mb-4">
    <span class="banner-pill">
        <i class="fas fa-bolt"></i> Admin Overview
    </span>

    <h2 style="margin: 14px 0 8px; font-size: 2rem; font-weight: 800;">
        Kelola operasional GYMBRUT dengan lebih simpel
    </h2>

    <p class="text-soft" style="margin: 0; max-width: 760px;">
        Lihat ringkasan member, membership aktif, pembayaran, dan workout program
        dalam satu dashboard yang lebih rapi dan mudah dibaca.
    </p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:14px;">
                <div>
                    <div class="stat-label">Total Member</div>
                    <div class="stat-value">1,248</div>
                    <div class="text-soft" style="font-size: 0.9rem; margin-top: 10px;">
                        +8.4% dari bulan lalu
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:14px;">
                <div>
                    <div class="stat-label">Membership Aktif</div>
                    <div class="stat-value">932</div>
                    <div class="text-soft" style="font-size: 0.9rem; margin-top: 10px;">
                        Paket bulanan paling banyak
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:14px;">
                <div>
                    <div class="stat-label">Pembayaran Pending</div>
                    <div class="stat-value">24</div>
                    <div class="text-soft" style="font-size: 0.9rem; margin-top: 10px;">
                        Menunggu verifikasi admin
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:14px;">
                <div>
                    <div class="stat-label">Workout Program</div>
                    <div class="stat-value">18</div>
                    <div class="text-soft" style="font-size: 0.9rem; margin-top: 10px;">
                        Siap digunakan member
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="premium-card h-100">
            <div
                style="display:flex; justify-content:space-between; align-items:center; gap:14px; margin-bottom:16px; flex-wrap:wrap;">
                <div>
                    <div class="section-title">Performa Membership</div>
                    <div class="text-soft" style="font-size:0.9rem;">
                        Pertumbuhan member aktif dalam 6 bulan terakhir
                    </div>
                </div>

                <span class="badge-soft badge-active">Growth Stable</span>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="memberGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="premium-card h-100">
            <div class="section-title" style="margin-bottom: 16px;">Ringkasan Operasional</div>

            <ul class="metric-list" style="list-style:none; margin:0; padding:0;">
                <li>
                    <span>Check-in hari ini</span>
                    <strong>146 orang</strong>
                </li>
                <li>
                    <span>Paket populer</span>
                    <strong>Bulanan</strong>
                </li>
                <li>
                    <span>Pembayaran verified</span>
                    <strong>118 transaksi</strong>
                </li>
                <li>
                    <span>Membership hampir habis</span>
                    <strong>37 akun</strong>
                </li>
                <li>
                    <span>Workout perlu update</span>
                    <strong>5 program</strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="premium-card h-100">
            <div
                style="display:flex; justify-content:space-between; align-items:center; gap:14px; margin-bottom:16px; flex-wrap:wrap;">
                <div>
                    <div class="section-title">Pendapatan Gym</div>
                    <div class="text-soft" style="font-size:0.9rem;">
                        Ringkasan pemasukan membership dan add-on
                    </div>
                </div>

                <span class="badge-soft badge-pending">Revenue Live</span>
            </div>

            <div class="dashboard-chart-wrap dashboard-chart-wrap-lg">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="premium-card h-100">
            <div class="section-title" style="margin-bottom: 16px;">Aktivitas Terbaru</div>

            <div style="display:grid; gap:12px;">
                <div class="glass-soft" style="padding:14px;">
                    <strong>Member baru masuk</strong>
                    <div class="text-soft" style="font-size:0.92rem; margin-top:6px;">
                        Nadia Pratama mendaftar paket bulanan hari ini.
                    </div>
                </div>

                <div class="glass-soft" style="padding:14px;">
                    <strong>Pembayaran diverifikasi</strong>
                    <div class="text-soft" style="font-size:0.92rem; margin-top:6px;">
                        Invoice #INV-1208 berhasil dikonfirmasi admin.
                    </div>
                </div>

                <div class="glass-soft" style="padding:14px;">
                    <strong>Workout diperbarui</strong>
                    <div class="text-soft" style="font-size:0.92rem; margin-top:6px;">
                        Program strength untuk member premium sudah ditambahkan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartTextColor = '#64748b';
    const chartGridColor = 'rgba(148, 163, 184, 0.15)';

    const memberCtx = document.getElementById('memberGrowthChart');
    if (memberCtx) {
        new Chart(memberCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Total Member',
                    data: [780, 845, 930, 1010, 1140, 1248],
                    borderColor: '#ff6b00',
                    backgroundColor: 'rgba(255,107,0,0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: chartTextColor }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: chartTextColor },
                        grid: { color: chartGridColor }
                    },
                    y: {
                        ticks: { color: chartTextColor },
                        grid: { color: chartGridColor }
                    }
                }
            }
        });
    }

    const incomeCtx = document.getElementById('incomeChart');
    if (incomeCtx) {
        new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [
                    {
                        label: 'Membership',
                        data: [48, 52, 61, 67, 80, 92],
                        backgroundColor: '#ff6b00',
                        borderRadius: 8
                    },
                    {
                        label: 'Add-on',
                        data: [6, 8, 9, 11, 12, 14],
                        backgroundColor: 'rgba(255,107,0,0.35)',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: chartTextColor }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: chartTextColor },
                        grid: { color: chartGridColor }
                    },
                    y: {
                        ticks: { color: chartTextColor },
                        grid: { color: chartGridColor }
                    }
                }
            }
        });
    }
</script>

<?php include '../includes/layout_bottom.php'; ?>