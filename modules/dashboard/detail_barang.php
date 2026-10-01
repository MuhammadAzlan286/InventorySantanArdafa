<?php
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
require_once 'owner_model.php';

// Data untuk Navbar Notifikasi
$recent_notif = getUnreadNotifications(5);
$unread_count = countUnreadNotifications();
$low_stock_items = getLowStockItems(5);

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil ID dari parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: barang.php");
    exit;
}

// Ambil data barang
$query = "SELECT b.*, s.nama_supplier, s.kontak as supplier_kontak, s.alamat as supplier_alamat 
          FROM barang b 
          LEFT JOIN suppliers s ON b.supplier_id = s.id 
          WHERE b.id = $id";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Barang tidak ditemukan!";
    header("Location: barang.php");
    exit;
}

$barang = mysqli_fetch_assoc($result);

// Hitung margin keuntungan
$margin = 0;
$margin_persen = 0;
if ($barang['harga_beli'] > 0) {
    $margin = $barang['harga_jual'] - $barang['harga_beli'];
    $margin_persen = ($margin / $barang['harga_beli']) * 100;
}

// Hitung total nilai stok
$total_nilai_beli = $barang['stok'] * $barang['harga_beli'];
$total_nilai_jual = $barang['stok'] * $barang['harga_jual'];

// Status stok
$status_stok = 'Aman';
$status_color = '#43A047';
if ($barang['stok'] <= 0) {
    $status_stok = 'Habis';
    $status_color = '#D32F2F';
} elseif ($barang['stok'] <= $barang['stok_min']) {
    $status_stok = 'Menipis';
    $status_color = '#EF6C00';
}

// Cek kadaluarsa
$kadaluarsa_status = '-';
$kadaluarsa_color = '#9c9fa6';
if (!empty($barang['tgl_kadaluarsa'])) {
    $today = new DateTime();
    $exp_date = new DateTime($barang['tgl_kadaluarsa']);
    $diff = $today->diff($exp_date);
    
    if ($exp_date < $today) {
        $kadaluarsa_status = 'Kadaluarsa';
        $kadaluarsa_color = '#D32F2F';
    } elseif ($diff->days <= 30) {
        $kadaluarsa_status = 'Segera Kadaluarsa (' . $diff->days . ' hari)';
        $kadaluarsa_color = '#EF6C00';
    } else {
        $kadaluarsa_status = 'Aman (' . $diff->days . ' hari)';
        $kadaluarsa_color = '#43A047';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang - <?= $barang['nama_barang'] ?> | Santan Ardafa</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
    <style>
        body {
            background: var(--bg-body);
            color: var(--text-main);
            font-family: 'Ubuntu', sans-serif;
        }

        .detail-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .detail-header {
            background: linear-gradient(135deg, var(--primary) 0%, #2E7D32 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            color: white;
            box-shadow: var(--shadow-soft);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .card {
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            padding: 25px;
            border: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 700;
            color: var(--text-main);
            text-align: right;
        }

        .image-container {
            width: 100%;
            height: 300px;
            background: rgba(0, 0, 0, 0.02);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid var(--border-color);
            margin-bottom: 20px;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.1) 0%, rgba(46, 125, 50, 0.05) 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(46, 125, 50, 0.2);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .btn-back {
            background: white;
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateX(-5px);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
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
            <a href="barang.php" class="active"><i class="fas fa-box"></i> <span>Kelola Barang</span></a>
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

        <div class="main-content">
            <div class="detail-container">
                <!-- Header -->
                <div class="detail-header animate-up">
                    <a href="barang.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Barang
                    </a>
                    <h1 style="margin: 20px 0 10px 0; font-size: 2rem;"><?= ucwords($barang['nama_barang']) ?></h1>
                    <p style="opacity: 0.9; margin: 0;">Kode: <?= $barang['kode_barang'] ?> | Kategori: <?= $barang['kategori'] ?></p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid animate-up">
                    <div class="stat-card">
                        <div class="stat-value"><?= $barang['stok'] ?></div>
                        <div class="stat-label">Stok Tersedia</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">Rp <?= number_format($barang['harga_jual'], 0, ',', '.') ?></div>
                        <div class="stat-label">Harga Jual</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" style="color: <?= $margin >= 0 ? '#43A047' : '#D32F2F' ?>">
                            <?= number_format($margin_persen, 1) ?>%
                        </div>
                        <div class="stat-label">Margin Keuntungan</div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="detail-grid animate-up">
                    <!-- Left Column - Image -->
                    <div class="card">
                        <div class="card-title">
                            <i class="fas fa-image"></i> Gambar Produk
                        </div>
                        <div class="image-container">
                            <?php
                            $image_path = "../../assets/images/barang/" . $barang['gambar'];
                            $old_path = "../../assets/images/" . $barang['gambar'];

                            if (!empty($barang['gambar']) && file_exists($image_path)): ?>
                                <img src="<?= $image_path ?>" alt="<?= $barang['nama_barang'] ?>">
                            <?php elseif (!empty($barang['gambar']) && file_exists($old_path)): ?>
                                <img src="<?= $old_path ?>" alt="<?= $barang['nama_barang'] ?>">
                            <?php else: ?>
                                <div style="text-align: center; color: #9c9fa6;">
                                    <i class="fas fa-image" style="font-size: 4rem; opacity: 0.3; margin-bottom: 15px;"></i>
                                    <p>Tidak ada gambar</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Cards -->
                        <div style="margin-top: 20px;">
                            <div class="info-row">
                                <span class="info-label">Status Stok</span>
                                <span class="badge" style="background: <?= $status_color ?>20; color: <?= $status_color ?>">
                                    <?= $status_stok ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Status Kadaluarsa</span>
                                <span class="badge" style="background: <?= $kadaluarsa_color ?>20; color: <?= $kadaluarsa_color ?>">
                                    <?= $kadaluarsa_status ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Details -->
                    <div>
                        <!-- Informasi Umum -->
                        <div class="card" style="margin-bottom: 25px;">
                            <div class="card-title">
                                <i class="fas fa-info-circle"></i> Informasi Umum
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kode Barang</span>
                                <span class="info-value"><?= $barang['kode_barang'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Nama Barang</span>
                                <span class="info-value"><?= ucwords($barang['nama_barang']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kategori</span>
                                <span class="info-value"><?= $barang['kategori'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Satuan</span>
                                <span class="info-value"><?= $barang['satuan'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Posisi</span>
                                <span class="info-value"><?= $barang['posisi'] ?: '-' ?></span>
                            </div>
                        </div>

                        <!-- Informasi Harga & Stok -->
                        <div class="card" style="margin-bottom: 25px;">
                            <div class="card-title">
                                <i class="fas fa-dollar-sign"></i> Informasi Harga & Stok
                            </div>
                            <div class="info-row">
                                <span class="info-label">Harga Beli</span>
                                <span class="info-value">Rp <?= number_format($barang['harga_beli'], 0, ',', '.') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Harga Jual</span>
                                <span class="info-value">Rp <?= number_format($barang['harga_jual'], 0, ',', '.') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Margin</span>
                                <span class="info-value" style="color: <?= $margin >= 0 ? '#43A047' : '#D32F2F' ?>">
                                    Rp <?= number_format($margin, 0, ',', '.') ?> (<?= number_format($margin_persen, 1) ?>%)
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Stok Saat Ini</span>
                                <span class="info-value"><?= $barang['stok'] ?> <?= $barang['satuan'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Stok Minimum</span>
                                <span class="info-value"><?= $barang['stok_min'] ?> <?= $barang['satuan'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Total Nilai (Beli)</span>
                                <span class="info-value">Rp <?= number_format($total_nilai_beli, 0, ',', '.') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Total Nilai (Jual)</span>
                                <span class="info-value">Rp <?= number_format($total_nilai_jual, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <!-- Informasi Supplier -->
                        <div class="card" style="margin-bottom: 25px;">
                            <div class="card-title">
                                <i class="fas fa-truck"></i> Informasi Supplier
                            </div>
                            <div class="info-row">
                                <span class="info-label">Nama Supplier</span>
                                <span class="info-value"><?= $barang['nama_supplier'] ?: '-' ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kontak</span>
                                <span class="info-value"><?= $barang['supplier_kontak'] ?: '-' ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Alamat</span>
                                <span class="info-value"><?= $barang['supplier_alamat'] ?: '-' ?></span>
                            </div>
                        </div>

                        <!-- Informasi Tanggal -->
                        <div class="card">
                            <div class="card-title">
                                <i class="fas fa-calendar"></i> Informasi Tanggal
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tanggal Masuk</span>
                                <span class="info-value"><?= date('d F Y', strtotime($barang['tgl_masuk'])) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tanggal Kadaluarsa</span>
                                <span class="info-value">
                                    <?= $barang['tgl_kadaluarsa'] ? date('d F Y', strtotime($barang['tgl_kadaluarsa'])) : '-' ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Dibuat Pada</span>
                                <span class="info-value"><?= date('d F Y H:i', strtotime($barang['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card animate-up" style="text-align: center;">
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="barang.php?edit=<?= $barang['id'] ?>" 
                           style="background: #E8F5E9; color: var(--primary); padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-edit"></i> Edit Barang
                        </a>
                        <a href="barang.php" 
                           style="background: var(--primary); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animation on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.animate-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.5s ease';
            observer.observe(el);
        });
    </script>
</body>

</html>
