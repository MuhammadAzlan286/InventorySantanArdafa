<?php
// Halaman manajemen pengeluaran operasional.
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

// DELETE
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM pengeluaran WHERE id=$id")) {
        writeLog("Delete Expense", "Deleted expense ID: " . $id);
        $_SESSION['success'] = "Biaya berhasil dihapus!";
    }
    header("Location: pengeluaran.php");
    exit;
}

// SAVE / UPDATE
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_pengeluaran']);
    $nominal = intval($_POST['nominal']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $tanggal = $_POST['tanggal'];

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE pengeluaran SET nama_pengeluaran='$nama', nominal='$nominal', kategori='$kategori', tanggal='$tanggal' WHERE id=$id";
        $log = "Update Expense: $nama";
    } else {
        $sql = "INSERT INTO pengeluaran (nama_pengeluaran, nominal, kategori, tanggal) VALUES ('$nama', '$nominal', '$kategori', '$tanggal')";
        $log = "Add Expense: $nama";
    }

    if (mysqli_query($koneksi, $sql)) {
        writeLog($log, $log . " (" . number_format($nominal, 0, ',', '.') . ")");
        $_SESSION['success'] = "Biaya berhasil disimpan!";
    }
    header("Location: pengeluaran.php");
    exit;
}

// =======================
// FILTERING & DATA
// =======================
$filter_month = $_GET['filter_month'] ?? '';
$filter_year = $_GET['filter_year'] ?? '';
$filter_day = $_GET['filter_day'] ?? '';
$where_list = [];

if ($filter_day != '') {
    $where_list[] = "DAY(tanggal) = '" . mysqli_real_escape_string($koneksi, $filter_day) . "'";
}
if ($filter_month != '') {
    $where_list[] = "MONTH(tanggal) = '" . mysqli_real_escape_string($koneksi, $filter_month) . "'";
}
if ($filter_year != '') {
    $where_list[] = "YEAR(tanggal) = '" . mysqli_real_escape_string($koneksi, $filter_year) . "'";
}

$where_clause = "";
if (count($where_list) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $where_list);
}

// Year list for dropdown
$years_q = mysqli_query($koneksi, "SELECT DISTINCT YEAR(tanggal) as year FROM pengeluaran ORDER BY year DESC");
$available_years = [];
while ($y = mysqli_fetch_assoc($years_q))
    $available_years[] = $y['year'];
if (empty($available_years))
    $available_years[] = date('Y');

$q_expenses = mysqli_query($koneksi, "SELECT * FROM pengeluaran $where_clause ORDER BY tanggal DESC");

$total_filtered = 0;
if ($where_clause != "") {
    $total_filtered = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran $where_clause"))['total'] ?? 0;
}

$total_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran WHERE DATE(tanggal) = CURRENT_DATE()"))['total'] ?? 0;
$total_kemarin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran WHERE DATE(tanggal) = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)"))['total'] ?? 0;

$total_minggu_lalu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran WHERE YEARWEEK(tanggal, 1) = YEARWEEK(DATE_SUB(CURRENT_DATE(), INTERVAL 1 WEEK), 1)"))['total'] ?? 0;

$total_bulan_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())"))['total'] ?? 0;
$total_bulan_lalu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran WHERE MONTH(tanggal) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(tanggal) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))"))['total'] ?? 0;

$total_tahun_lalu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran WHERE YEAR(tanggal) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 YEAR))"))['total'] ?? 0;

$top_kat_q = mysqli_query($koneksi, "SELECT kategori, SUM(nominal) as total FROM pengeluaran GROUP BY kategori ORDER BY total DESC LIMIT 1");
$top_kategori = mysqli_fetch_assoc($top_kat_q)['kategori'] ?? '-';

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengeluaran WHERE id=$id"));
}

// ================= EXPORT ACTION =================
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Laporan_Biaya_Operasional_' . date('Ymd') . '.csv');
    $output = fopen('php://output', 'w');

    fputcsv($output, ['LAPORAN BIAYA OPERASIONAL - SANTAN ARDAFA']);
    fputcsv($output, ['Tanggal Unduh', date('d-m-Y H:i:s')]);
    fputcsv($output, []);

    fputcsv($output, ['Kode', 'Keterangan', 'Kategori', 'Nominal', 'Tanggal']);

    mysqli_data_seek($q_expenses, 0);
    while ($row = mysqli_fetch_assoc($q_expenses)) {
        fputcsv($output, [
            'EXP-' . str_pad($row['id'], 3, '0', STR_PAD_LEFT),
            $row['nama_pengeluaran'],
            $row['kategori'],
            $row['nominal'],
            $row['tanggal']
        ]);
    }

    fputcsv($output, []);
    fputcsv($output, ['RINGKASAN']);
    if ($filter_day || $filter_month || $filter_year) {
        fputcsv($output, ['Total Filter Terpilih', 'Rp ' . number_format($total_filtered, 0, ',', '.')]);
    }
    fputcsv($output, ['Total Hari Ini', 'Rp ' . number_format($total_hari_ini, 0, ',', '.')]);
    fputcsv($output, ['Total Kemarin', 'Rp ' . number_format($total_kemarin, 0, ',', '.')]);
    fputcsv($output, ['Total Bulan Ini', 'Rp ' . number_format($total_bulan_ini, 0, ',', '.')]);

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biaya Operasional | Santan Ardafa</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: var(--bg-body);
            color: var(--text-main);
            font-family: 'Ubuntu', sans-serif;
        }

        /* SEARCH CARD */
        .search-card {
            background: var(--bg-card);
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        /* FORM CARD */
        .card {
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .card-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            background: #F8FAFC;
            border-radius: 6px;
            font-size: 0.85rem;
            color: var(--text-main);
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn-edit {
            background: #E8F5E9;
            color: var(--primary);
        }

        .btn-delete {
            background: #FFEBEE;
            color: #D32F2F;
        }

        .header-title {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .header-subtitle {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info h5 {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }

        .stat-info h3 {
            margin: 5px 0 0 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-icon.green {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .stat-icon.purple {
            background: #F3E5F5;
            color: #7B1FA2;
        }

        .stat-icon.orange {
            background: #FFF3E0;
            color: #EF6C00;
        }

        @media print {

            .sidebar,
            .navbar-item-relative,
            .btn-action,
            .alert-dismissible,
            .dashboard-header .header-right form,
            .card:has(form),
            .header-actions-row button,
            .header-actions-row a {
                display: none !important;
            }

            .wrapper {
                display: block !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .dashboard-header {
                padding: 10px 0 !important;
                border: none !important;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
                margin-bottom: 20px !important;
            }

            .stat-card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                background: #fff !important;
                color: #000 !important;
                padding: 15px !important;
            }

            .stat-icon {
                display: none !important;
            }

            .stat-card * {
                color: #000 !important;
            }

            .stat-card[style*="background:var(--primary)"] {
                background: #f1f5f9 !important;
                border: 2px solid var(--primary) !important;
            }

            .table-container {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }

            .card-header {
                border-bottom: 2px solid #ddd !important;
            }

            .header-actions-row {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <!-- SIDEBAR (Standard) -->
        <div class="sidebar">
            <div class="sidebar-header"><i class="fas fa-store"></i> Santan Ardafa</div>

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
            <a href="pengeluaran.php" class="active"><i class="fas fa-wallet"></i> <span>Biaya Operasional</span></a>
            <a href="laporan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Penjualan</span></a>
            <a href="logs.php"><i class="fas fa-history"></i> <span>System History</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Kelola User</span></a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <div class="main-content">

            <!-- HEADER -->
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h1 class="header-title" style="margin:0;">Biaya & Pengeluaran</h1>
                    <p class="header-subtitle" style="margin-top:5px;">Monitoring biaya operasional dan pengeluaran
                        harian</p>
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

                    <!-- Export & Print Buttons -->
                    <div style="display:flex; gap:10px;">
                        <a href="?filter_day=<?= $filter_day ?>&filter_month=<?= $filter_month ?>&filter_year=<?= $filter_year ?>&export=true"
                            style="background:#22c55e; color:white; border-radius:10px; padding:8px 18px; font-weight:700; display:inline-flex; align-items:center; gap:8px; text-decoration:none; font-size:0.85rem; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                        <button onclick="window.print()"
                            style="background:#3b82f6; color:white; border-radius:10px; border:none; padding:8px 18px; cursor:pointer; font-weight:700; display:inline-flex; align-items:center; gap:8px; font-size:0.85rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div id="alert-success" class="alert-dismissible"
                    style="background:#E8F5E9;color:#2E7D32;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #C8E6C9;">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- STATS GRID -->
            <div class="stats-grid animate-up">
                <?php if ($filter_month != '' || $filter_year != '' || $filter_day != ''): ?>
                    <div class="stat-card" style="background:var(--primary); color:white; border:none;">
                        <div class="stat-info">
                            <h5 style="color:rgba(255,255,255,0.8);">Total Terpilih</h5>
                            <h3 style="color:white;">Rp <?= number_format($total_filtered, 0, ',', '.') ?></h3>
                        </div>
                        <div class="stat-icon" style="background:rgba(255,255,255,0.2); color:white;"><i
                                class="fas fa-filter"></i></div>
                    </div>
                <?php endif; ?>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Hari Ini</h5>
                        <h3>Rp <?= number_format($total_hari_ini, 0, ',', '.') ?></h3>
                    </div>
                    <div class="stat-icon green"><i class="fas fa-calendar-day"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Kemarin</h5>
                        <h3>Rp <?= number_format($total_kemarin, 0, ',', '.') ?></h3>
                    </div>
                    <div class="stat-icon purple" style="background:#FCE4EC; color:#C2185B;"><i
                            class="fas fa-history"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Minggu Lalu</h5>
                        <h3>Rp <?= number_format($total_minggu_lalu, 0, ',', '.') ?></h3>
                    </div>
                    <div class="stat-icon orange" style="background:#E1F5FE; color:#0288D1;"><i
                            class="fas fa-calendar-week"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Bulan Ini</h5>
                        <h3>Rp <?= number_format($total_bulan_ini, 0, ',', '.') ?></h3>
                    </div>
                    <div class="stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Bulan Lalu</h5>
                        <h3>Rp <?= number_format($total_bulan_lalu, 0, ',', '.') ?></h3>
                    </div>
                    <div class="stat-icon blue" style="background:#E8EAF6; color:#303F9F;"><i
                            class="fas fa-calendar-check"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Total Tahun Lalu</h5>
                        <h3>Rp <?= number_format($total_tahun_lalu, 0, ',', '.') ?></h3>
                    </div>
                    <div class="stat-icon red" style="background:#FFF3E0; color:#E64A19;"><i
                            class="fas fa-calendar-minus"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h5>Kategori Tertinggi</h5>
                        <h3><?= $top_kategori ?></h3>
                    </div>
                    <div class="stat-icon orange"><i class="fas fa-chart-pie"></i></div>
                </div>
            </div>

            <!-- FORM CARD -->
            <div class="card animate-up">
                <div class="card-header">
                    <?= $edit_data ? 'Edit Pengeluaran' : 'Tambah Pengeluaran Baru' ?>
                </div>

                <form method="post">
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">

                    <div class="form-grid-4">
                        <div class="form-group">
                            <label>Uraian Pengeluaran</label>
                            <input type="text" name="nama_pengeluaran" class="form-control"
                                value="<?= $edit_data['nama_pengeluaran'] ?? '' ?>" required
                                placeholder="Contoh: Beli Token Listrik">
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="Operasional" <?= ($edit_data['kategori'] ?? '') == 'Operasional' ? 'selected' : '' ?>>Operasional</option>
                                <option value="Gaji" <?= ($edit_data['kategori'] ?? '') == 'Gaji' ? 'selected' : '' ?>>Gaji
                                    Karyawan</option>
                                <option value="Listrik/Air" <?= ($edit_data['kategori'] ?? '') == 'Listrik/Air' ? 'selected' : '' ?>>Listrik & Air</option>
                                <option value="Sewa" <?= ($edit_data['kategori'] ?? '') == 'Sewa' ? 'selected' : '' ?>>Sewa
                                    Tempat</option>
                                <option value="Lainnya" <?= ($edit_data['kategori'] ?? '') == 'Lainnya' ? 'selected' : '' ?>>
                                    Lain-lain</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nominal (Rp)</label>
                            <input type="number" name="nominal" class="form-control"
                                value="<?= $edit_data['nominal'] ?? '' ?>" required placeholder="0">
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="<?= $edit_data['tanggal'] ?? date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <?php if ($edit_data): ?>
                            <a href="pengeluaran.php"
                                style="background:#f1f5f9; color:#64748b; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600; font-size:0.85rem; display:flex; align-items:center;">
                                Batal
                            </a>
                        <?php endif; ?>
                        <button type="submit" name="simpan"
                            style="background:var(--primary); color:white; padding:10px 25px; border:none; border-radius:8px; font-weight:bold; cursor:pointer; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-save"></i> <?= $edit_data ? 'Simpan Perubahan' : 'Simpan Data' ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABLE CARD -->
            <div class="table-container animate-up">
                <div class="card-header"
                    style="border:none; margin-bottom:0; display:flex; justify-content:space-between; align-items:center; background:rgba(46, 125, 50, 0.03); color:var(--primary); padding: 15px 25px; flex-wrap: wrap; gap: 15px;">
                    <h4
                        style="margin:0; font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-history"></i> Riwayat Pengeluaran
                    </h4>

                    <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <!-- Day Filter -->
                        <select name="filter_day" class="form-control" style="width: 80px; height: 40px;"
                            onchange="this.form.submit()">
                            <option value="">Tgl</option>
                            <?php for ($i = 1; $i <= 31; $num = $i++): ?>
                                <option value="<?= $num ?>" <?= $filter_day == $num ? 'selected' : '' ?>><?= $num ?></option>
                            <?php endfor; ?>
                        </select>

                        <!-- Month Filter -->
                        <select name="filter_month" class="form-control" style="width: 140px; height: 40px;"
                            onchange="this.form.submit()">
                            <option value="">Semua Bulan</option>
                            <?php
                            $months = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember'
                            ];
                            foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= $filter_month == $num ? 'selected' : '' ?>><?= $name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Year Filter -->
                        <select name="filter_year" class="form-control" style="width: 100px; height: 40px;"
                            onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            <?php foreach ($available_years as $y): ?>
                                <option value="<?= $y ?>" <?= $filter_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php if ($filter_month != '' || $filter_year != '' || $filter_day != ''): ?>
                            <a href="pengeluaran.php" class="btn btn-secondary"
                                style="height: 40px; display: flex; align-items: center; padding: 0 15px; border-radius: 6px; background: #e2e8f0; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                <i class="fas fa-times-circle" style="margin-right: 5px;"></i> Reset
                            </a>
                        <?php endif; ?>

                        <div style="position:relative;">
                            <i class="fas fa-search"
                                style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:var(--primary); opacity:0.5; font-size:0.8rem;"></i>
                            <input type="text" id="localSearch" class="form-control"
                                style="width:200px; padding-left:40px; height:40px; font-size:0.85rem;"
                                placeholder="Cari di tabel...">
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table" id="expenseTable">
                        <thead>
                            <tr>
                                <th style="width: 100px; text-align: center;">KODE</th>
                                <th style="text-align: left;">KETERANGAN</th>
                                <th style="text-align: center;">KATEGORI</th>
                                <th style="text-align: right;">NOMINAL</th>
                                <th style="text-align: center;">TANGGAL</th>
                                <th style="text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($q_expenses, 0);
                            while ($row = mysqli_fetch_assoc($q_expenses)):
                                // Color markers for categories
                                $marker = '#64748b';
                                $bg_marker = '#f1f5f9';
                                if ($row['kategori'] == 'Gaji') {
                                    $marker = '#7B1FA2';
                                    $bg_marker = '#F3E5F5';
                                }
                                if ($row['kategori'] == 'Operasional') {
                                    $marker = '#2E7D32';
                                    $bg_marker = '#E8F5E9';
                                }
                                if ($row['kategori'] == 'Listrik/Air') {
                                    $marker = '#F57F17';
                                    $bg_marker = '#FFF9C4';
                                }
                                if ($row['kategori'] == 'Sewa') {
                                    $marker = '#C62828';
                                    $bg_marker = '#FFEBEE';
                                }
                                ?>
                                <tr>
                                    <td align="center">
                                        <span
                                            style="font-weight: 700; color: var(--primary); font-size: 0.85rem; letter-spacing: 0.5px;">EXP-<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:var(--text-main); font-size:1rem;">
                                            <?= ucwords($row['nama_pengeluaran']) ?>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <span
                                            style="background:<?= $bg_marker ?>; color:<?= $marker ?>; padding:5px 12px; border-radius:8px; font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; display:inline-block; min-width:100px;">
                                            <?= $row['kategori'] ?>
                                        </span>
                                    </td>
                                    <td align="right">
                                        <div style="font-weight:800; color:#c62828; font-size:1rem;">Rp
                                            <?= number_format($row['nominal'], 0, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td align="center" style="font-weight:600; color:var(--text-muted); font-size:0.85rem;">
                                        <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td align="center">
                                        <div style="display:flex; gap:8px; justify-content:center;">
                                            <a href="?edit=<?= $row['id'] ?>" class="btn-action btn-edit"
                                                style="height:32px; padding:0 15px; display:flex; align-items:center; font-weight:700; border-radius:8px;">Edit</a>
                                            <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')"
                                                class="btn-action btn-delete"
                                                style="height:32px; padding:0 15px; display:flex; align-items:center; font-weight:700; border-radius:8px;">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="../../assets/js/notifications.js"></script>
    <script>
        // Auto-hide success alert
        const alertSuccess = document.getElementById('alert-success');
        if (alertSuccess) {
            setTimeout(() => {
                alertSuccess.style.opacity = '0';
                setTimeout(() => alertSuccess.remove(), 500);
            }, 2000);
        }

        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const scrollPos = localStorage.getItem('sidebarScrollPos');
            if (scrollPos) sidebar.scrollTop = scrollPos;
            sidebar.addEventListener('scroll', () => {
                localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        }

        // Local Search Logic
        document.getElementById('localSearch').addEventListener('keyup', function () {
            let val = this.value.toLowerCase();
            let rows = document.querySelectorAll('#expenseTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
</body>

</html>