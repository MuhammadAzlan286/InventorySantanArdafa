<?php
// Halaman laporan penjualan dan analitik.
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


// ================= FILTER =================
$filter = $_GET['filter'] ?? 'semua';
$f_day = $_GET['filter_day'] ?? '';
$f_month = $_GET['filter_month'] ?? '';
$f_year = $_GET['filter_year'] ?? '';

$where_list = [];
if ($f_day != '')
    $where_list[] = "DAY(t.tanggal) = '" . mysqli_real_escape_string($koneksi, $f_day) . "'";
if ($f_month != '')
    $where_list[] = "MONTH(t.tanggal) = '" . mysqli_real_escape_string($koneksi, $f_month) . "'";
if ($f_year != '')
    $where_list[] = "YEAR(t.tanggal) = '" . mysqli_real_escape_string($koneksi, $f_year) . "'";

$where = "";
$where_exp = "";

if (count($where_list) > 0) {
    $where = "WHERE " . implode(" AND ", $where_list);

    // For expenses (adjust column names if they differ, usually 'tanggal')
    $where_exp_list = [];
    if ($f_day != '')
        $where_exp_list[] = "DAY(tanggal) = '" . mysqli_real_escape_string($koneksi, $f_day) . "'";
    if ($f_month != '')
        $where_exp_list[] = "MONTH(tanggal) = '" . mysqli_real_escape_string($koneksi, $f_month) . "'";
    if ($f_year != '')
        $where_exp_list[] = "YEAR(tanggal) = '" . mysqli_real_escape_string($koneksi, $f_year) . "'";
    $where_exp = "WHERE " . implode(" AND ", $where_exp_list);

    $filter = 'custom'; // Mark as custom to handle UI active state
} else {
    switch ($filter) {
        case 'harian':
            $where = "WHERE DATE(t.tanggal) = CURDATE()";
            $where_exp = "WHERE DATE(tanggal) = CURDATE()";
            break;

        case 'mingguan':
            $where = "WHERE t.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $where_exp = "WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;

        case 'bulanan':
            $where = "WHERE MONTH(t.tanggal) = MONTH(CURDATE()) AND YEAR(t.tanggal) = YEAR(CURDATE())";
            $where_exp = "WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
            break;

        case 'tahunan':
            $where = "WHERE YEAR(t.tanggal) = YEAR(CURDATE())";
            $where_exp = "WHERE YEAR(tanggal) = YEAR(CURDATE())";
            break;
    }
}

// Year list for dropdown
$years_q = mysqli_query($koneksi, "SELECT DISTINCT YEAR(tanggal) as year FROM transaksi ORDER BY year DESC");
$available_years = [];
while ($y = mysqli_fetch_assoc($years_q))
    $available_years[] = $y['year'];
if (empty($available_years))
    $available_years[] = date('Y');

$sql = "
    SELECT 
        b.kode_barang,
        b.nama_barang,
        b.kategori,
        b.harga_beli,
        t.qty,
        t.total_harga,
        t.status,
        t.tanggal
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id
    $where
    ORDER BY t.tanggal DESC
";

$result = mysqli_query($koneksi, $sql);

// ================= RINGKASAN =================
$total_pendapatan = 0;
$total_keuntungan = 0;
$total_transaksi = 0;
$total_item = 0;
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $total_pendapatan += (int) $row['total_harga'];

    // Hitung Keuntungan Bersih: Total Jual - (Harga Beli * Qty)
    $profit = (int) $row['total_harga'] - ((int) $row['harga_beli'] * (int) $row['qty']);
    $row['keuntungan'] = $profit;
    $total_keuntungan += $profit;

    $total_transaksi++;
    $total_item += (int) $row['qty'];
    $data[] = $row;
}

// Hitung Pengeluaran (sesuai filter waktu)
$q_exp = mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM pengeluaran $where_exp");
$r_exp = mysqli_fetch_assoc($q_exp);
$total_pengeluaran = $r_exp['total'] ?? 0;

$laba_bersih = $total_keuntungan - $total_pengeluaran;

// ================= EXPORT ACTION (After calculations) =================
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Laporan_Penjualan_' . $filter . '_' . date('Ymd') . '.csv');
    $output = fopen('php://output', 'w');

    // Title & Info
    fputcsv($output, ['LAPORAN PENJUALAN - SANTAN ARDAFA']);
    fputcsv($output, ['Filter Periode', ucwords($filter)]);
    fputcsv($output, ['Tanggal Unduh', date('d-m-Y H:i:s')]);
    fputcsv($output, []); // Spacer

    // Data Headers
    fputcsv($output, ['Kode Barang', 'Nama Barang', 'Kategori', 'Qty', 'Total Harga', 'Profit', 'Tanggal', 'Jam']);

    // Data Rows
    foreach ($data as $row) {
        $dt = strtotime($row['tanggal']);
        fputcsv($output, [
            $row['kode_barang'],
            $row['nama_barang'],
            $row['kategori'],
            $row['qty'],
            $row['total_harga'],
            $row['keuntungan'],
            date('Y-m-d', $dt),
            date('H:i:s', $dt)
        ]);
    }

    fputcsv($output, []); // Summary Spacer
    fputcsv($output, ['RINGKASAN FINANSIAL']);
    fputcsv($output, ['Total Pendapatan Kotor', 'Rp ' . number_format($total_pendapatan, 0, ',', '.')]);
    fputcsv($output, ['Total Pengeluaran Operasional', 'Rp ' . number_format($total_pengeluaran, 0, ',', '.')]);
    fputcsv($output, ['Laba Bersih (Net Profit)', 'Rp ' . number_format($laba_bersih, 0, ',', '.')]);

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan | Santan Ardafa</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media print {

            .sidebar,
            .navbar-item-relative,
            .header-actions-row,
            .badge-dot,
            .btn-logout {
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

            .stock-summary-grid {
                gap: 15px !important;
                margin-bottom: 25px !important;
            }

            .summary-card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                background: #fff !important;
                color: #000 !important;
            }

            .summary-card i {
                display: none !important;
            }

            .summary-card * {
                color: #000 !important;
            }

            .summary-card[style*="background: linear-gradient"] {
                background: #f9f9f9 !important;
                border: 2px solid #2e7d32 !important;
            }

            .table-container {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }

            .table th {
                background: #f4f4f4 !important;
                color: #000 !important;
            }
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
            <a href="laporan.php" class="active"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan
                    Penjualan</span></a>
            <a href="logs.php"><i class="fas fa-history"></i> <span>System History</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Kelola User</span></a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <div class="main-content">
            <div></div> <!-- Spacer -->

            <div class="dashboard-header animate-up"
                style="flex-direction: column; align-items: flex-start; gap: 20px; padding: 20px 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div class="header-left">
                        <h3 style="font-size:1.5rem; font-weight:800; color:var(--text-main); margin:0;">Laporan
                            Penjualan</h3>
                        <p style="color:var(--text-muted); margin-top:5px; font-weight:500;">Monitoring performa dan
                            laba transaksi</p>
                    </div>

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
                                                Sisa stok: <strong><?= $item['stok'] ?></strong></div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div
                                    style="background: rgba(46,125,50,0.05); padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: var(--primary); border-bottom: 1px solid rgba(46,125,50,0.1); display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-shopping-cart"></i> TRANSAKSI BARU
                                </div>
                                <?php foreach ($recent_notif as $n): ?>
                                    <div class="dropdown-item" data-id="<?= $n['id'] ?>" style="cursor:pointer;"
                                        onclick="markNotificationAsRead(this, <?= $n['id'] ?>)">
                                        <div class="notification-title" style="color:var(--primary); font-weight:700;">
                                            <?= $n['nama_barang'] ?>
                                        </div>
                                        <div class="notification-desc" style="font-size:0.85rem;">
                                            Terjual <?= $n['qty'] ?> unit dengan total <strong>Rp
                                                <?= number_format($n['total_harga'], 0, ',', '.') ?></strong>
                                        </div>
                                        <div class="notification-time"
                                            style="font-size:0.75rem; color:var(--text-muted); margin-top:5px;">
                                            <?= date('d M, H:i', strtotime($n['tanggal'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-actions-row"
                    style="width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; background: rgba(255,255,255,0.4); padding: 12px 15px; border-radius: 12px; border: 1.5px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                        <div class="filter-toolbar"
                            style="margin:0; background:white; border:1px solid var(--border-color); padding:4px; border-radius:10px; display:flex; gap:5px;">
                            <a href="?filter=semua" class="<?= $filter == 'semua' ? 'active' : '' ?>"
                                style="padding: 6px 15px; border-radius: 8px; font-size: 0.85rem;">Semua</a>
                            <a href="?filter=harian" class="<?= $filter == 'harian' ? 'active' : '' ?>"
                                style="padding: 6px 15px; border-radius: 8px; font-size: 0.85rem;">Hari Ini</a>
                            <a href="?filter=mingguan" class="<?= $filter == 'mingguan' ? 'active' : '' ?>"
                                style="padding: 6px 15px; border-radius: 8px; font-size: 0.85rem;">7 Hari</a>
                            <a href="?filter=bulanan" class="<?= $filter == 'bulanan' ? 'active' : '' ?>"
                                style="padding: 6px 15px; border-radius: 8px; font-size: 0.85rem;">Bulan Ini</a>
                        </div>

                        <div style="height: 30px; border-left: 1.5px solid var(--border-color); margin: 0 10px;"></div>

                        <form method="GET" style="display:flex; align-items:center; gap:8px;">
                            <input type="hidden" name="filter" value="<?= $filter ?>">
                            <select name="filter_day" class="form-control"
                                style="width: 85px; height: 38px; font-size: 0.85rem; border-radius: 10px; box-shadow: var(--shadow-soft); padding: 0 10px;"
                                onchange="this.form.submit()">
                                <option value="">Tgl</option>
                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?= $i ?>" <?= $f_day == $i ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>

                            <select name="filter_month" class="form-control"
                                style="width: 140px; height: 38px; font-size: 0.85rem; border-radius: 10px; box-shadow: var(--shadow-soft); padding: 0 10px;"
                                onchange="this.form.submit()">
                                <option value="">Bulan</option>
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
                                    <option value="<?= $num ?>" <?= $f_month == $num ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>

                            <select name="filter_year" class="form-control"
                                style="width: 105px; height: 38px; font-size: 0.85rem; border-radius: 10px; box-shadow: var(--shadow-soft); padding: 0 10px;"
                                onchange="this.form.submit()">
                                <option value="">Tahun</option>
                                <?php foreach ($available_years as $y): ?>
                                    <option value="<?= $y ?>" <?= $f_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>

                            <?php if ($f_day != '' || $f_month != '' || $f_year != ''): ?>
                                <a href="laporan.php" class="btn btn-light"
                                    style="padding: 8px; border-radius: 10px; color: #ef4444;" title="Reset Filter">
                                    <i class="fas fa-undo"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <a href="?filter=<?= $filter ?>&filter_day=<?= $f_day ?>&filter_month=<?= $f_month ?>&filter_year=<?= $f_year ?>&export=true"
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

            <!-- SUMMARY -->
            <div class="stock-summary-grid animate-up"
                style="grid-template-columns: repeat(2, 1fr); gap: 1.25rem; margin-top: 10px; margin-bottom: 25px;">
                <div class="summary-card"
                    style="background: white; border: 1.5px solid var(--border-color); box-shadow: var(--shadow-soft); padding: 25px;">
                    <div class="summary-text"
                        style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div>
                            <h4
                                style="color:var(--text-muted); font-size: 0.9rem; font-weight:700; text-transform:uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                Pendapatan Kotor</h4>
                            <div class="count" style="font-size: 1.6rem; color: var(--text-main); font-weight: 800;">Rp
                                <?= number_format($total_pendapatan, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="summary-icon"
                            style="background: rgba(46,125,50,0.1); color: var(--primary); width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <div class="summary-card"
                    style="background: white; border: 1.5px solid var(--border-color); box-shadow: var(--shadow-soft); padding: 25px;">
                    <div class="summary-text"
                        style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div>
                            <h4
                                style="color:var(--text-muted); font-size: 0.9rem; font-weight:700; text-transform:uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                Total Pengeluaran</h4>
                            <div class="count" style="font-size: 1.6rem; color: #fe7096; font-weight: 800;">Rp
                                <?= number_format($total_pengeluaran, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="summary-icon"
                            style="background: rgba(254,112,150,0.1); color: #fe7096; width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fas fa-minus-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="summary-card"
                    style="grid-column: span 2; background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%); border: none; padding: 30px; box-shadow: 0 10px 20px rgba(46, 125, 50, 0.2);">
                    <div class="summary-text"
                        style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                        <div>
                            <h4
                                style="color:rgba(255,255,255,0.8); font-size: 0.95rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom: 10px;">
                                Laba Bersih (Net Profit)</h4>
                            <div class="count" style="font-size:2.5rem; color:white; font-weight: 800;">Rp
                                <?= number_format($laba_bersih, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="summary-icon"
                            style="background:rgba(255,255,255,0.2); color:white; width:70px; height:70px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-container animate-up"
                style="border: 1.5px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-soft);">
                <div class="card-header"
                    style="border-bottom:1px solid var(--border-color); background:rgba(46, 125, 50, 0.04); color:var(--primary); padding: 20px 25px; display: flex; justify-content: space-between; align-items: center;">
                    <h4
                        style="margin:0; font-weight:800; font-size:1.1rem; display:flex; align-items:center; gap:12px; letter-spacing: 0.5px;">
                        <i class="fas fa-history"></i> RIWAYAT PENJUALAN
                    </h4>
                    <span
                        style="font-size: 0.85rem; font-weight: 600; background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px;">
                        <?= count($data) ?> Transaksi
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">NO</th>
                                <th style="width: 100px; text-align: center;">KODE</th>
                                <th style="text-align: left;">BARANG</th>
                                <th style="text-align: center;">KATEGORI</th>
                                <th style="text-align: center;">QTY</th>
                                <th style="text-align: right;">TOTAL</th>
                                <th style="text-align: right;">PROFIT</th>
                                <th style="text-align: center;">STATUS</th>
                                <th style="text-align: center;">TANGGAL</th>
                                <th style="text-align: center;">JAM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data) > 0): ?>
                                <?php $no = 1;
                                foreach ($data as $row): ?>
                                    <tr>
                                        <td align="center" style="font-weight: 500; opacity: 0.5;"><?= $no++ ?></td>
                                        <td align="center"
                                            style="color:var(--primary); font-weight:700; font-size:0.85rem; letter-spacing:0.5px;">
                                            <?= $row['kode_barang'] ?>
                                        </td>
                                        <td style="font-weight:700; color:var(--text-main);"><?= ucwords($row['nama_barang']) ?>
                                        </td>
                                        <td align="center">
                                            <span
                                                style="font-size:0.75rem; color:var(--text-muted); background:rgba(255,255,255,0.08); padding:4px 10px; border-radius:6px; font-weight:700; text-transform:uppercase;">
                                                <?= $row['kategori'] ?>
                                            </span>
                                        </td>
                                        <td align="center">
                                            <span
                                                style="font-weight:800; color:var(--text-main); background:rgba(46,125,50,0.05); padding:3px 10px; border-radius:6px;"><?= $row['qty'] ?></span>
                                        </td>
                                        <td align="right" style="font-weight:700; color:var(--text-main);">Rp
                                            <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td align="right" style="color:var(--primary); font-weight:800;">Rp
                                            <?= number_format($row['keuntungan'], 0, ',', '.') ?>
                                        </td>
                                        <td align="center">
                                            <span
                                                style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:<?= $row['status'] == 'Lunas' ? '#2e7d32' : '#f9a825' ?>;">
                                                <i
                                                    class="fas <?= $row['status'] == 'Lunas' ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                        <td align="center" style="font-size:0.85rem; color:var(--text-muted); font-weight:500;">
                                            <?= date('d-m-Y', strtotime($row['tanggal'])) ?>
                                        </td>
                                        <td align="center" style="font-size:0.85rem; color:var(--text-muted); font-weight:500;">
                                            <i class="far fa-clock" style="font-size:0.7rem; opacity:0.5;"></i>
                                            <?= date('H:i', strtotime($row['tanggal'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" align="center" style="padding:30px; color:#9c9fa6;">Tidak ada data
                                        transaksi untuk periode ini.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
                                div.style.borderBottom = '1px solid #2d303e';
                                div.innerHTML = `
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-weight:600; color:#b66dff;">${item.nama_barang}</div>
                                <div style="font-size:0.8rem; background:rgba(182,109,255,0.1); color:#b66dff; padding:2px 8px; border-radius:4px;">Stok: ${item.stok}</div>
                            </div>
                            <div style="font-size:0.75rem; color:#9c9fa6; margin-top:4px;">
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
                            suggestionsBox.innerHTML = '<div style="padding:15px; text-align:center; color:#9c9fa6; font-size:0.85rem;"><i class="fas fa-exclamation-circle" style="margin-right:5px;"></i> Barang tidak tersedia</div>';
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
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Barang tidak tersedia',
                                        text: 'Maaf, barang yang Anda cari tidak dapat ditemukan.',
                                        confirmButtonColor: '#b66dff',
                                        background: '#1e212b',
                                        color: '#e2e8f0'
                                    });
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
        });
    </script>
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