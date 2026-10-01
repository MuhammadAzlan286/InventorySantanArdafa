<?php
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
require_once 'owner_model.php';

// Data untuk Navbar Notifikasi
$recent_notif = getUnreadNotifications(5);
$unread_count = countUnreadNotifications();
$low_stock_items = getLowStockItems(5);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    exit("Akses ditolak");
}

// Filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where = "";
if (!empty($search)) {
    $where = "WHERE action LIKE '%$search%' OR nama_user LIKE '%$search%' OR details LIKE '%$search%'";
}

$q_logs = mysqli_query($koneksi, "SELECT * FROM activity_logs $where ORDER BY tanggal DESC LIMIT 100");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | Santan Ardafa</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .log-item {
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
            transition: 0.3s;
        }

        .log-item:hover {
            background: rgba(46, 125, 50, 0.05);
        }

        .log-action {
            font-weight: 700;
            color: var(--primary);
        }

        .log-user {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .log-details {
            margin-top: 5px;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        .log-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .badge-role {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 4px;
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary);
        }
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
            <a href="owner.php"><i class="fas fa-home"></i> <span>Beranda</span></a>

            <div class="sidebar-section-title">Fitur</div>
            <a href="barang.php"><i class="fas fa-box"></i> <span>Kelola Barang</span></a>
            <a href="stok.php"><i class="fas fa-cubes"></i> <span>Daftar Stok</span></a>
            <a href="suppliers.php"><i class="fas fa-truck"></i> <span>Suppliers</span></a>
            <a href="pengeluaran.php"><i class="fas fa-wallet"></i> <span>Biaya Operasional</span></a>
            <a href="laporan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Penjualan</span></a>
            <a href="logs.php" class="active"><i class="fas fa-history"></i> <span>System History</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Kelola User</span></a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <div class="main-content">
            <!-- 1. DASHBOARD HEADER -->
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h1 style="margin:0;">System History</h1>
                    <p style="margin-top:5px;">Rekaman aktivitas seluruh pengguna di sistem.</p>
                </div>
                <div class="header-right"
                    style="flex: 2; justify-content: flex-end; display:flex; align-items:center; gap:20px;">
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
                        <div class="notification-dropdown" style="text-align: left;">
                            <div class="dropdown-header"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Notifikasi Sistem</span>
                                <?php if ($unread_count > 0): ?>
                                    <a href="javascript:void(0)" onclick="markAllNotificationsAsRead()"
                                        style="font-size: 0.75rem; color: #fe7096; font-weight: 700; text-transform: none; text-decoration: underline;">Tandai
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
                                            style="border-left: 3px solid #EF6C00; display: block; text-decoration: none;">
                                            <div class="notification-title" style="color:#EF6C00; font-weight:700;">
                                                <?= $item['nama_barang'] ?>
                                            </div>
                                            <div class="notification-desc" style="font-size:0.85rem; color:var(--text-main);">
                                                Sisa stok: <strong><?= $item['stok'] ?></strong> (Batas:
                                                <?= $item['stok_min'] ?>)
                                            </div>
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
                                        <div class="notification-desc" style="font-size:0.85rem; color:var(--text-main);">
                                            Terjual <?= $n['qty'] ?> unit dengan total <strong>Rp
                                                <?= number_format($n['total_harga'], 0, ',', '.') ?></strong></div>
                                        <div class="notification-time"
                                            style="font-size:0.75rem; color:var(--text-muted); margin-top:5px;">
                                            <?= date('d M, H:i', strtotime($n['tanggal'])) ?>
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

                    <div style="display: flex; gap: 10px; width: 100%; max-width: 500px;">
                        <form method="get" style="display:flex; gap:10px; width:100%;">
                            <div style="position:relative; flex: 2;">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari log / aktivitas..." value="<?= htmlspecialchars($search) ?>"
                                    style="padding-left: 40px;">
                                <i class="fas fa-search"
                                    style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:var(--primary); opacity:0.6;"></i>
                            </div>
                            <button type="submit"
                                style="background:var(--primary); color:white; border:none; padding:0 20px; border-radius:12px; font-weight:700; cursor:pointer;">Filter</button>
                            <a href="logs.php"
                                style="background:white; color:var(--primary); width:45px; height:45px; border-radius:12px; display:flex; align-items:center; justify-content:center; text-decoration:none; border: 1.5px solid var(--border-color); box-shadow:var(--shadow-soft);">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card animate-up" style="padding:0;">
                <div class="card-header"
                    style="border:none; margin-bottom:0; background:rgba(46, 125, 50, 0.03); color:var(--primary); padding: 15px 25px;">
                    <h4
                        style="margin:0; font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-history"></i> System History
                    </h4>
                </div>
                <div id="logContainer">
                    <?php if (mysqli_num_rows($q_logs) > 0): ?>
                        <?php while ($log = mysqli_fetch_assoc($q_logs)): ?>
                            <div class="log-item">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <div>
                                        <span class="log-action"><?= $log['action'] ?></span>
                                        <span class="log-user" style="margin-left:10px;">by
                                            <strong><?= $log['nama_user'] ?></strong> <span
                                                class="badge-role"><?= ucfirst($log['role']) ?></span></span>
                                        <div class="log-details"><?= $log['details'] ?></div>
                                    </div>
                                    <div class="log-time">
                                        <i class="far fa-clock"></i> <?= date('d M Y, H:i:s', strtotime($log['tanggal'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding:50px; text-align:center; color:var(--text-muted);">
                            <i class="fas fa-history" style="font-size:3rem; margin-bottom:15px; opacity:0.2;"></i>
                            <p>Belum ada data aktivitas yang tercatat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="../../assets/js/notifications.js"></script>
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