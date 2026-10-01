<?php
// Halaman manajemen data supplier.
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
    if (mysqli_query($koneksi, "DELETE FROM suppliers WHERE id=$id")) {
        writeLog("Delete Supplier", "Deleted supplier ID: " . $id);
        $_SESSION['success'] = "Supplier berhasil dihapus!";
    }
    header("Location: suppliers.php");
    exit;
}

// SAVE / UPDATE
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_supplier']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE suppliers SET nama_supplier='$nama', kontak='$kontak', alamat='$alamat' WHERE id=$id";
        $log = "Update Supplier: $nama";
    } else {
        $sql = "INSERT INTO suppliers (nama_supplier, kontak, alamat) VALUES ('$nama', '$kontak', '$alamat')";
        $log = "Add Supplier: $nama";
    }

    if (mysqli_query($koneksi, $sql)) {
        writeLog($log, $log);
        $_SESSION['success'] = "Data supplier berhasil disimpan!";
    }
    header("Location: suppliers.php");
    exit;
}

$q_suppliers = mysqli_query($koneksi, "SELECT * FROM suppliers ORDER BY nama_supplier ASC");
$total_suppliers = mysqli_num_rows($q_suppliers);
$active_partners = $total_suppliers; // Simplification for now

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM suppliers WHERE id=$id"));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Supplier | Santan Ardafa</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
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

        .search-input {
            flex: 3;
        }

        .btn-cari {
            background: var(--primary);
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            cursor: pointer;
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

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

        /* TABLE STYLES */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table thead th {
            background: var(--primary);
            color: white;
            font-weight: 700;
            padding: 15px;
            font-size: 0.85rem;
            text-align: left;
            border-bottom: 2px solid rgba(46, 125, 50, 0.1);
        }

        .custom-table tbody td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background: #F1F8E9;
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <a href="suppliers.php" class="active"><i class="fas fa-truck"></i> <span>Suppliers</span></a>
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

        <div class="main-content">

            <!-- HEADER -->
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h1 class="header-title" style="margin:0;">Manajemen Supplier</h1>
                    <p class="header-subtitle" style="margin-top:5px;">Kelola data pemasok barang dan kontak relasi</p>
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
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div id="alert-success" class="alert-dismissible"
                    style="background:#E8F5E9;color:#2E7D32;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #C8E6C9;">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- FORM CARD -->
            <div class="card animate-up">
                <div class="card-header">
                    <?= $edit_data ? 'Edit Supplier' : 'Tambah Supplier Baru' ?>
                </div>

                <form method="post">
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Nama Perusahaan / Supplier</label>
                            <input type="text" name="nama_supplier" class="form-control"
                                value="<?= $edit_data['nama_supplier'] ?? '' ?>" required placeholder="PT. Jaya Abadi">
                        </div>
                        <div class="form-group">
                            <label>Kontak (WA/Telp)</label>
                            <input type="text" name="kontak" class="form-control"
                                value="<?= $edit_data['kontak'] ?? '' ?>" placeholder="08xxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <input type="text" name="alamat" class="form-control"
                                value="<?= $edit_data['alamat'] ?? '' ?>" placeholder="Jl. Raya No. 123">
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <?php if ($edit_data): ?>
                            <a href="suppliers.php"
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
                    style="border:none; margin-bottom:0; display:flex; justify-content:space-between; align-items:center; background:rgba(46, 125, 50, 0.03); color:var(--primary); padding: 15px 25px;">
                    <h4
                        style="margin:0; font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-truck"></i> Daftar Supplier
                    </h4>
                    <div style="position:relative;">
                        <i class="fas fa-search"
                            style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:var(--primary); opacity:0.5; font-size:0.8rem;"></i>
                        <input type="text" id="localSearch" class="form-control"
                            style="width:250px; padding-left:40px; height:40px; font-size:0.85rem;"
                            placeholder="Cari supplier...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="supplierTable">
                        <thead>
                            <tr>
                                <th style="width: 100px; text-align: center;">KODE</th>
                                <th style="text-align: left;">SUPPLIER</th>
                                <th style="text-align: left;">KONTAK</th>
                                <th style="text-align: left;">ALAMAT</th>
                                <th style="text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($q_suppliers, 0);
                            while ($row = mysqli_fetch_assoc($q_suppliers)):
                                $wa_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['kontak']);
                                ?>
                                <tr>
                                    <td align="center">
                                        <span
                                            style="font-weight: 700; color: var(--primary); font-size: 0.85rem; letter-spacing: 0.5px;">SUP-<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:var(--text-main); font-size:1rem;">
                                            <?= ucwords($row['nama_supplier']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['kontak'])): ?>
                                            <a href="<?= $wa_link ?>" target="_blank"
                                                style="color:#25D366; text-decoration:none; font-weight:700; display:flex; align-items:center; gap:8px;">
                                                <i class="fab fa-whatsapp" style="font-size:1.1rem;"></i> <?= $row['kontak'] ?>
                                            </a>
                                        <?php else: ?>
                                            <span style="color:var(--text-muted); opacity:0.3;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size:0.85rem; color:var(--text-muted); font-weight:500;">
                                        <?= $row['alamat'] ?: '<span style="opacity:0.3">-</span>' ?>
                                    </td>
                                    <td align="center">
                                        <div style="display:flex; gap:8px; justify-content:center;">
                                            <a href="?edit=<?= $row['id'] ?>" class="btn-action btn-edit"
                                                style="height:32px; padding:0 15px; display:flex; align-items:center; font-weight:700; border-radius:8px;">Edit</a>
                                            <a href="?hapus=<?= $row['id'] ?>"
                                                onclick="return confirm('Hapus supplier ini?')"
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
            let rows = document.querySelectorAll('#supplierTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
</body>

</html>