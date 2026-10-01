<?php
// Halaman Kasir untuk transaksi penjualan (POS).
require_once '../../config/auth.php';
require_once '../../config/auth.php';
require_once 'transaksi_model.php'; // Ganti koneksi.php dengan model yang sudah include koneksi

// Handle Hapus/Batal Transaksi
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    if (hapusTransaksi($id_hapus)) {
        header("Location: kasir.php");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus transaksi'); window.location='kasir.php';</script>";
        exit;
    }
}

/* =======================
   PROTEKSI ROLE
   ======================= */
if ($_SESSION['role'] !== 'kasir' && $_SESSION['role'] !== 'owner') {
    exit("Akses ditolak");
}

$today = date('Y-m-d');

/* =======================
   STATISTIK HARI INI
   ======================= */

// Total transaksi hari ini
$q_transaksi = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM transaksi 
    WHERE DATE(tanggal) = '$today'
");
$total_transaksi = mysqli_fetch_assoc($q_transaksi)['total'] ?? 0;

// Pendapatan hari ini (lunas)
$q_revenue = mysqli_query($koneksi, "
    SELECT SUM(total_harga) AS total 
    FROM transaksi 
    WHERE status = 'lunas'
    AND DATE(tanggal) = '$today'
");
$revenue_today = mysqli_fetch_assoc($q_revenue)['total'] ?? 0;

// Pending transaksi
$q_pending = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM transaksi 
    WHERE status = 'belum_bayar'
");
$pending_count = mysqli_fetch_assoc($q_pending)['total'] ?? 0;

// Transaksi terakhir
$recent_trx = mysqli_query($koneksi, "
    SELECT 
        t.id,
        t.tanggal,
        t.total_harga,
        t.status,
        t.qty,
        b.nama_barang,
        b.kode_barang
    FROM transaksi t
    LEFT JOIN barang b ON t.id_barang = b.id
    ORDER BY t.tanggal DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir | Santan Ardafa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../assets/css/style.css?v=1.12">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="wrapper">

        <!-- =======================
     SIDEBAR
======================= -->
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
            <a href="kasir.php" class="active">
                <i class="fas fa-home"></i> <span>Beranda</span>
            </a>
            <a href="transaksi.php">
                <i class="fas fa-cash-register"></i> <span>Mulai Transaksi</span>
            </a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- =======================
         MAIN CONTENT
    ======================= -->
        <div class="main-content">

            <!-- HEADER -->
            <div class="animate-up" style="margin-bottom: 20px; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-home" style="font-size:1.8rem; color:var(--primary);"></i>
                <div>
                    <h2 style="color: var(--primary); font-weight: 700; margin-bottom: 5px;">Dashboard Kasir</h2>
                    <div
                        style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">
                        RINGKASAN HARI INI</div>
                </div>
            </div>

            <!-- STAT CARDS (Wide Layout) -->
            <div class="animate-up"
                style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:20px;">

                <!-- Card 1: Transaksi Hari Ini -->
                <div class="card" style="border-left: 6px solid #1a4d2e; margin-bottom:0;">
                    <div class="card-body" style="padding:20px; position:relative;">
                        <div style="color: #1a4d2e; font-weight: 700; font-size: 0.9rem; margin-bottom: 15px;">Transaksi
                            Hari Ini</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1a4d2e;">
                            <?= number_format($total_transaksi) ?>
                        </div>
                        <div
                            style="position: absolute; top: 20px; right: 20px; color: #1a4d2e; opacity: 0.15; font-size: 2.5rem;">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pendapatan Hari Ini -->
                <div class="card" style="border-left: 6px solid #1a4d2e; margin-bottom:0;">
                    <div class="card-body" style="padding:20px; position:relative;">
                        <div style="color: #1a4d2e; font-weight: 700; font-size: 0.9rem; margin-bottom: 15px;">
                            Pendapatan Hari Ini</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1a4d2e;">Rp
                            <?= number_format($revenue_today, 0, ',', '.') ?>
                        </div>
                        <div
                            style="position: absolute; top: 20px; right: 20px; color: #1a4d2e; opacity: 0.15; font-size: 2.5rem;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pending -->
                <div class="card" style="border-left: 6px solid #1a4d2e; margin-bottom:0;">
                    <div class="card-body" style="padding:20px; position:relative;">
                        <div style="color: #1a4d2e; font-weight: 700; font-size: 0.9rem; margin-bottom: 15px;">Pending
                        </div>
                        <div
                            style="display: inline-block; background: #ffbf96; color: #9c4221; font-weight: 700; padding: 2px 12px; border-radius: 50px; font-size: 1.5rem;">
                            <?= number_format($pending_count) ?>
                        </div>
                        <div
                            style="position: absolute; top: 20px; right: 20px; color: #1a4d2e; opacity: 0.15; font-size: 2.5rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>


            <!-- TABEL TRANSAKSI -->
            <div class="card animate-up">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-history"></i> Riwayat Transaksi Terakhir
                    </h3>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <div style="overflow-x: auto;">
                        <table
                            style="width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                            <thead style="background-color: #1a4d2e; color: white;">
                                <tr>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">ID Transaksi
                                    </th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Tanggal</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Jam</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Kode Barang</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Nama Barang</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Qty</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Total</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Status</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 500;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.9rem; color: #374151;">
                                <?php while ($row = mysqli_fetch_assoc($recent_trx)): ?>
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td
                                            style="padding: 12px 15px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #f3f4f6;">
                                            #TRX<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                        <td
                                            style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6; font-size: 0.85rem;">
                                            <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                        </td>
                                        <td
                                            style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6; font-size: 0.85rem;">
                                            <?= date('H:i', strtotime($row['tanggal'])) ?> WIB
                                        </td>
                                        <td
                                            style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6; font-weight: 700; color: #1a4d2e; font-size: 0.85rem;">
                                            <?= $row['kode_barang'] ?? '-' ?>
                                        </td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6; font-weight: 600;">
                                            <?= $row['nama_barang'] ?? '-' ?>
                                        </td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6; text-align: left;">
                                            <?= $row['qty'] ?> Unit
                                        </td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6;">Rp
                                            <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6;">
                                            <?php if ($row['status'] == 'lunas'): ?>
                                                <span
                                                    style="background-color: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Lunas</span>
                                            <?php elseif ($row['status'] == 'belum_bayar'): ?>
                                                <span
                                                    style="background-color: #ffedd5; color: #9a3412; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Pending</span>
                                            <?php else: ?>
                                                <span
                                                    style="background-color: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;"><?= $row['status'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid #f3f4f6;">
                                            <a href="?hapus=<?= $row['id'] ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin membatalkan/menghapus transaksi ini? Stok barang akan dikembalikan.')"
                                                style="background:#dc3545; color:white; padding:8px 16px; border-radius:8px; font-size:0.8rem; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition: transform 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                                onmouseover="this.style.transform='scale(1.05)'"
                                                onmouseout="this.style.transform='scale(1)'">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($recent_trx) == 0): ?>
                                    <tr>
                                        <td colspan="5" style="padding: 20px; text-align: center; color: #9ca3af;">Belum ada
                                            transaksi hari ini</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <span class="highlight">Santan Ardafa</span> ✨ Excellence in Every Transaction • © 2026 Crafted with
                Passion
            </div>
        </div> <!-- Close main-content -->
    </div> <!-- Close wrapper -->

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