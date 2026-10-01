<?php
// Dashboard utama untuk Owner.
require_once '../../config/auth.php';
require_once 'owner_model.php';

// Proteksi: Hanya Owner yang boleh akses
if ($_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil Data dari Model
$total_barang = totalBarang();
$total_stok = totalStok();
$total_transaksi = totalTransaksi();
$total_lunas = totalTransaksiLunas();
$total_pendapatan = totalPendapatan();
$low_stock_count = countLowStock();
$chart_data = getSalesChartData();
$best_selling = getBestSellingProducts(5);
$category_profit = getCategoryProfitDistribution();
$recent_notif = getUnreadNotifications(5);
$unread_count = countUnreadNotifications();
$low_stock_items = getLowStockItems(5);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Owner | Santan Ardafa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- External CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.13">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Styles moved to assets/css/style.css */
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-store"></i> Santan Ardafa
            </div>

            <div
                style="padding: 0 0 20px 15px; display:flex; align-items:center; gap:15px; margin-bottom:20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="position:relative;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']) ?>&background=random"
                        style="width:40px; height:40px; border-radius:50%; border: 2px solid white;">
                    <div
                        style="position:absolute; bottom:0; right:0; width:10px; height:10px; background:#43A047; border-radius:50%; border:2px solid white;">
                    </div>
                </div>
                <div>
                    <div class="sidebar-user-name"><?= $_SESSION['nama'] ?></div>
                    <div class="sidebar-user-role"><?= ucfirst($_SESSION['role']) ?></div>
                </div>
            </div>

            <div class="sidebar-section-title">Utama</div>
            <a href="owner.php" class="active"><i class="fas fa-home"></i> <span>Beranda</span></a>

            <div class="sidebar-section-title">Fitur</div>
            <a href="barang.php"><i class="fas fa-box"></i> <span>Kelola Barang</span></a>
            <a href="stok.php"><i class="fas fa-cubes"></i> <span>Daftar Stok</span></a>
            <a href="suppliers.php"><i class="fas fa-truck"></i> <span>Suppliers</span></a>
            <a href="pengeluaran.php"><i class="fas fa-wallet"></i> <span>Biaya Operasional</span></a>
            <a href="laporan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Penjualan</span></a>
            <a href="logs.php"><i class="fas fa-history"></i> <span>System History</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Kelola User</span></a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">

            <div></div> <!-- Spacer -->

            <!-- ALERT STOK MENIPIS (Conditional) -->
            <?php if ($low_stock_count > 0): ?>
                <div class="alert-box alert-danger"
                    style="padding: 0.75rem 1.25rem; margin-bottom: 2rem; border-radius: 12px; gap: 12px; align-items: center;">
                    <div class="icon">
                        <i class="fas fa-triangle-exclamation" style="font-size: 1.2rem;"></i>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap; flex: 1;">
                        <h4 style="margin:0; font-weight:700; font-size: 0.95rem;">Perhatian Stok:</h4>
                        <p style="margin:0; font-size:0.85rem; color: var(--text-muted); flex: 1;">Terdapat
                            <strong><?= $low_stock_count ?> barang</strong> yang menipis atau habis.
                        </p>
                        <a href="stok.php"
                            style="color:#fe7096; font-weight:700; font-size:0.85rem; text-decoration:underline;">
                            Lihat detail <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Header Section -->
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h3 style="font-size:1.5rem; font-weight:800; margin:0;">Dashboard Overview</h3>
                    <p style="color:var(--text-muted); font-weight:500; margin-top:5px;">Selamat datang kembali,
                        ringkasan performa hari ini.</p>
                </div>
                <div class="header-right" style="display:flex; align-items:center; gap:20px;">
                    <!-- Notification Bell -->
                    <div class="navbar-item-relative" id="notificationContainer">
                        <div onclick="toggleNotification(event)"
                            style="background:white; width:45px; height:45px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--primary); border:1.5px solid var(--border-color); box-shadow:var(--shadow-soft); position:relative; cursor:pointer;">
                            <i class="far fa-bell" style="font-size:1.2rem;"></i>
                            <?php if ($unread_count > 0): ?>
                                <div class="badge-dot" id="bellBadge"
                                    style="background:#fe7096; width:18px; height:18px; display:flex; align-items:center; justify-content:center; color:white; font-size:10px; font-weight:700; top:-5px; right:-5px; position:absolute;">
                                    <?= $unread_count ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="notification-dropdown">
                            <div class="dropdown-header"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Notifikasi Sistem</span>
                                <?php if ($unread_count > 0): ?>
                                    <a href="javascript:void(0)" onclick="markAllNotificationsAsRead()"
                                        style="font-size: 0.7rem; color: #b66dff; font-weight: 700; text-transform: none; text-decoration: underline;">Tandai
                                        Semua Dibaca</a>
                                <?php endif; ?>
                            </div>
                            <div id="notificationList">
                                <!-- Section: Low Stock -->
                                <?php if (!empty($low_stock_items)): ?>
                                    <div
                                        style="background: rgba(255,167,38,0.05); padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: #EF6C00; border-bottom: 1px solid rgba(255,167,38,0.1); display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-exclamation-triangle"></i> STOK MENIPIS
                                    </div>
                                    <?php foreach ($low_stock_items as $item): ?>
                                        <a href="stok.php?search=<?= urlencode($item['nama_barang']) ?>" class="dropdown-item"
                                            style="border-left: 3px solid #EF6C00;">
                                            <div class="notification-title" style="color:#EF6C00; font-weight:700;">
                                                <?= $item['nama_barang'] ?>
                                            </div>
                                            <div class="notification-desc">Sisa stok: <strong><?= $item['stok'] ?></strong>
                                                (Batas: <?= $item['stok_min'] ?>)</div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Section: Recent Sales -->
                                <div
                                    style="background: rgba(46,125,50,0.05); padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: var(--primary); border-bottom: 1px solid rgba(46,125,50,0.1); border-top: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-shopping-cart"></i> TRANSAKSI BARU
                                </div>
                                <?php foreach ($recent_notif as $n): ?>
                                    <div class="dropdown-item" data-id="<?= $n['id'] ?>" style="cursor:pointer;"
                                        onclick="markNotificationAsRead(this, <?= $n['id'] ?>)">
                                        <div class="notification-title" style="color:var(--primary); font-weight:700;">
                                            <?= $n['nama_barang'] ?>
                                        </div>
                                        <div class="notification-desc">Terjual <?= $n['qty'] ?> unit dengan total <strong>Rp
                                                <?= number_format($n['total_harga'], 0, ',', '.') ?></strong></div>
                                        <div class="notification-time"><?= date('d M, H:i', strtotime($n['tanggal'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (empty($recent_notif) && empty($low_stock_items)): ?>
                                    <div class="dropdown-item" style="text-align:center; color:#9c9fa6; padding: 30px;">
                                        <i class="fas fa-bell-slash"
                                            style="font-size: 1.5rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                                        Tidak ada notifikasi baru
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div
                        style="background:white; padding:10px 20px; border-radius:12px; color:var(--primary); font-size:0.9rem; font-weight:600; box-shadow:var(--shadow-soft); border:1.5px solid var(--border-color); display:flex; align-items:center; height:45px;">
                        <i class="far fa-calendar-alt" style="margin-right:8px;"></i> <?= date('d F Y') ?>
                    </div>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="animate-up"
                style="display:grid; grid-template-columns:repeat(<?= $low_stock_count > 0 ? 4 : 3 ?>, 1fr); gap:1.5rem; margin-bottom:2rem;">

                <div class="card" style="border-left: 5px solid #29B6F6; position:relative;">
                    <div class="card-body" style="padding:20px;">
                        <h4 style="color:var(--text-muted); font-size:0.9rem; margin-bottom:5px;">Total Barang</h4>
                        <h2 style="color:var(--text-main); font-size:2rem; font-weight:800; margin:0;">
                            <?= number_format($total_barang) ?>
                        </h2>
                        <p style="font-size:0.8rem; color:var(--text-muted); opacity:0.8;">Item di database</p>
                        <div
                            style="position:absolute; top:15px; right:15px; width:50px; height:50px; background:#E1F5FE; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#0288D1; font-size:1.5rem;">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>

                <div class="card" style="border-left: 5px solid #66BB6A; position:relative;">
                    <div class="card-body" style="padding:20px;">
                        <h4 style="color:var(--text-muted); font-size:0.9rem; margin-bottom:5px;">Total Stok</h4>
                        <h2 style="color:var(--text-main); font-size:2rem; font-weight:800; margin:0;">
                            <?= number_format($total_stok) ?>
                        </h2>
                        <p style="font-size:0.8rem; color:var(--text-muted); opacity:0.8;">Total unit tersedia</p>
                        <div
                            style="position:absolute; top:15px; right:15px; width:50px; height:50px; background:#E8F5E9; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#2E7D32; font-size:1.5rem;">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>

                <div class="card" style="border-left: 5px solid #FFA726; position:relative;">
                    <div class="card-body" style="padding:20px;">
                        <h4 style="color:var(--text-muted); font-size:0.9rem; margin-bottom:5px;">Transaksi Lunas</h4>
                        <h2 style="color:var(--text-main); font-size:2rem; font-weight:800; margin:0;">
                            <?= number_format($total_lunas) ?>
                        </h2>
                        <p style="font-size:0.8rem; color:var(--text-muted); opacity:0.8;">Proses selesai</p>
                        <div
                            style="position:absolute; top:15px; right:15px; width:50px; height:50px; background:#FFF3E0; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#EF6C00; font-size:1.5rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <?php if ($low_stock_count > 0): ?>
                    <div class="card" style="border-left: 5px solid #EF5350; position:relative; background:#FFEBEE;">
                        <div class="card-body" style="padding:20px;">
                            <h4 style="color:#C62828; font-size:0.9rem; margin-bottom:5px;">Stok Menipis</h4>
                            <h2 style="color:#C62828; font-size:2rem; font-weight:800; margin:0;">
                                <?= number_format($low_stock_count) ?>
                            </h2>
                            <p style="font-size:0.8rem; color:#C62828; opacity:0.8;">Segera restock!</p>
                            <div
                                style="position:absolute; top:15px; right:15px; width:50px; height:50px; background:white; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#D32F2F; font-size:1.5rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem;" class="animate-up">
                <!-- REVENUE HIGHLIGHT -->
                <div class="card"
                    style="display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding: 2rem;">
                    <h3 style="color:var(--text-muted); margin-bottom:10px; font-size:1.1rem;">Total Pendapatan</h3>
                    <h1 style="font-size:2.5rem; font-weight:800; color:var(--primary); margin-bottom:10px;">Rp
                        <?= number_format($total_pendapatan, 0, ',', '.') ?>
                    </h1>
                    <p style="font-size:0.85rem; color:var(--text-muted);">Statistik berdasarkan transaksi lunas</p>
                    <div style="margin-top:20px; padding:15px; background:#E8F5E9; border-radius:10px; width:80%;">
                        <span style="color:var(--primary); font-weight:700;"><i class="fas fa-arrow-up"></i> Performa
                            Stabil</span>
                    </div>
                </div>

                <!-- SALES CHART -->
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa-solid fa-chart-line"></i> Grafik Pendapatan (7 Hari Terakhir)
                        </h3>
                    </div>
                    <div style="padding: 1.5rem;">
                        <canvas id="salesChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.5rem; margin-top:2rem;"
                class="animate-up">
                <!-- BEST SELLING -->
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa-solid fa-fire" style="color:#ff9800;"></i> Produk Terlaris (Qty)
                        </h3>
                    </div>
                    <div style="padding: 1.5rem;">
                        <div style="height:300px;">
                            <canvas id="bestSellingChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- PROFIT BY CATEGORY -->
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa-solid fa-chart-pie" style="color:var(--primary);"></i> Profit per Kategori
                        </h3>
                    </div>
                    <div style="padding: 1.5rem;">
                        <div style="height:300px;">
                            <canvas id="categoryProfitChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <span class="highlight">Santan Ardafa</span> ✨ Excellence in Every Transaction • © 2026 Crafted with
                Passion
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_data['labels']) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($chart_data['values']) ?>,
                    borderColor: '#b66dff',
                    backgroundColor: 'rgba(182, 109, 255, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#b66dff',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }, /* Darker grid for white card */
                        ticks: { color: '#546E7A', font: { family: 'Ubuntu' } } /* Dark text */
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#546E7A', font: { family: 'Ubuntu' } }
                    }
                }
            }
        });

        // Best Selling Chart (Bar)
        const ctxBest = document.getElementById('bestSellingChart').getContext('2d');
        new Chart(ctxBest, {
            type: 'bar',
            data: {
                labels: <?= json_encode($best_selling['labels']) ?>,
                datasets: [{
                    label: 'Qty Terjual',
                    data: <?= json_encode($best_selling['values']) ?>,
                    backgroundColor: 'rgba(7, 205, 174, 0.6)',
                    borderColor: '#07cdae',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#546E7A' } },
                    x: { grid: { display: false }, ticks: { color: '#546E7A' } }
                }
            }
        });

        // Category Profit Chart (Doughnut)
        const ctxCat = document.getElementById('categoryProfitChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($category_profit['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($category_profit['values']) ?>,
                    backgroundColor: [
                        '#b66dff', '#07cdae', '#fe7096', '#ffab00', '#3b82f6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#9c9fa6', padding: 20 }
                    }
                },
                cutout: '70%'
            }
        });

        const searchInput = document.getElementById('navbarSearch');
        const suggestionsBox = document.getElementById('searchSuggestions');

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            if (query.length < 2) {
                suggestionsBox.style.display = 'none';
                return;
            }

            fetch(`search_ajax.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.style.padding = '12px 15px';
                            div.style.cursor = 'pointer';
                            div.style.borderBottom = '1px solid var(--border-color)';
                            div.style.textAlign = 'left';
                            div.innerHTML = `
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-weight:600; color:var(--primary);">${item.nama_barang}</div>
                                <div style="font-size:0.8rem; background:rgba(182,109,255,0.1); color:var(--primary); padding:2px 8px; border-radius:4px;">Stock: ${item.stok}</div>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">
                                <i class="fas fa-map-marker-alt" style="margin-right:5px;"></i> Posisi: ${item.posisi || '-'}
                            </div>
                        `;
                            div.addEventListener('click', () => {
                                window.location.href = `stok.php?search=${encodeURIComponent(item.nama_barang)}`;
                            });
                            suggestionsBox.appendChild(div);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.innerHTML = '<div style="padding:15px; text-align:center; color:var(--text-muted); font-size:0.85rem;"><i class="fas fa-exclamation-circle" style="margin-right:5px;"></i> Barang tidak tersedia</div>';
                        suggestionsBox.style.display = 'block';
                    }
                });
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query.length > 0) {
                    fetch(`search_ajax.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                window.location.href = `stok.php?search=${encodeURIComponent(query)}`;
                            }
                        });
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Close search suggestions if clicking outside
            document.addEventListener('click', function (e) {
                const searchInput = document.getElementById("navbarSearch");
                const suggestions = document.getElementById("searchSuggestions");
                if (!searchInput.contains(e.target) && !suggestions.contains(e.target)) {
                    suggestions.style.display = "none";
                }
            });

            // Close notification when clicking outside
            // (Functionality moved to notifications.js)
        });
    </script>

    <!-- Global Notification Script -->
    <script src="../../assets/js/notifications.js"></script>
    </script>

    <script>
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const scrollPos = localStorage.getItem('sidebarScrollPos');
            if (scrollPos) sidebar.scrollTop = scrollPos;
            sidebar.addEventListener('scroll', () => {
                localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        }
    </script>
</body>

</html>
```