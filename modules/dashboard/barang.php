<?php
// Halaman manajemen data barang (Master Data Barang).
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

$error = null;
$success = null;

// =======================
// DELETE DATA
// =======================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $item = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_barang, gambar FROM barang WHERE id=$id"));
    if (mysqli_query($koneksi, "DELETE FROM barang WHERE id=$id")) {
        writeLog("Delete Item", "Deleted item: " . ($item['nama_barang'] ?? 'Unknown ID ' . $id));
        if (!empty($item['gambar']) && file_exists("../../assets/images/barang/" . $item['gambar'])) {
            unlink("../../assets/images/barang/" . $item['gambar']);
        }

        // REORDER IDs & KODE BARANG
        // Note: This operation can be risky for foreign keys. Ensure relations are handled or table is standalone.
        mysqli_query($koneksi, "SET @count = 0");
        mysqli_query($koneksi, "UPDATE barang SET barang.id = @count:= @count + 1");
        mysqli_query($koneksi, "ALTER TABLE barang AUTO_INCREMENT = 1");

        // Update Kode Barang to match new IDs
        mysqli_query($koneksi, "UPDATE barang SET kode_barang = CONCAT('BRG-', LPAD(id, 4, '0'))");

        $_SESSION['success'] = "Data barang berhasil dihapus dan urutan ID diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal menghapus: " . mysqli_error($koneksi);
    }
    header("Location: barang.php");
    exit;
}

// =======================
// SIMPAN / UPDATE DATA
// =======================
if (isset($_POST['simpan'])) {
    // $kode   = mysqli_real_escape_string($koneksi, $_POST['kode_barang']); // REMOVED MANUAL INPUT
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kat = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $sat = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $stok = intval($_POST['stok']);
    $min = intval($_POST['stok_min']);
    $hb = intval($_POST['harga_beli']);
    $hj = intval($_POST['harga_jual']);
    $masuk = $_POST['tgl_masuk'];
    $exp = !empty($_POST['tgl_kadaluarsa']) ? "'" . $_POST['tgl_kadaluarsa'] . "'" : "NULL";
    $posisi = mysqli_real_escape_string($koneksi, $_POST['posisi']);
    $supplier_id = intval($_POST['supplier_id']);

    // UPLOAD GAMBAR
    $gambar_sql = "";
    $gambar_field = "";
    $gambar_val = "";

    if (!empty($_FILES['gambar']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = "IMG_" . time() . "_" . rand(100, 999) . "." . $ext;
            $target = "../../assets/images/barang/" . $new_name;

            if (!is_dir("../../assets/images/barang/")) {
                mkdir("../../assets/images/barang/", 0777, true);
            }

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                $gambar_sql = ", gambar='$new_name'";
                $gambar_field = ", gambar";
                $gambar_val = ", '$new_name'";

                if (!empty($_POST['id_barang'])) {
                    $id_old = intval($_POST['id_barang']);
                    $old_img = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT gambar FROM barang WHERE id=$id_old"));
                    if (!empty($old_img['gambar']) && file_exists("../../assets/images/barang/" . $old_img['gambar'])) {
                        unlink("../../assets/images/barang/" . $old_img['gambar']);
                    }
                }
            }
        }
    }

    if (!empty($_POST['id_barang'])) {
        // UPDATE
        $id = intval($_POST['id_barang']);
        $sql = "UPDATE barang SET 
                nama_barang='$nama',
                kategori='$kat',
                satuan='$sat',
                stok='$stok',
                stok_min='$min',
                harga_beli='$hb',
                harga_jual='$hj',
                tgl_masuk='$masuk',
                tgl_kadaluarsa=$exp,
                posisi='$posisi',
                supplier_id='$supplier_id'
                $gambar_sql
                WHERE id=$id";
        $msg = "Data berhasil diperbarui!";
        $log_action = "Update Item";

        if (mysqli_query($koneksi, $sql)) {
            writeLog($log_action, $log_action . ": " . $nama);
            $_SESSION['success'] = $msg;
            header("Location: barang.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($koneksi);
        }

    } else {
        // INSERT
        // Note: we don't insert kode_barang yet, we generate it after we get the ID
        $sql = "INSERT INTO barang 
                (nama_barang, kategori, satuan, stok, stok_min, harga_beli, harga_jual, tgl_masuk, tgl_kadaluarsa, posisi, supplier_id $gambar_field)
                VALUES 
                ('$nama', '$kat', '$sat', '$stok', '$min', '$hb', '$hj', '$masuk', $exp, '$posisi', '$supplier_id' $gambar_val)";
        $msg = "Barang baru berhasil ditambahkan!";
        $log_action = "Add Item";

        if (mysqli_query($koneksi, $sql)) {
            $new_id = mysqli_insert_id($koneksi);
            // Auto Generate Code based on ID (e.g., BRG-0001)
            $new_code = "BRG-" . str_pad($new_id, 4, '0', STR_PAD_LEFT);
            mysqli_query($koneksi, "UPDATE barang SET kode_barang='$new_code' WHERE id=$new_id");

            writeLog($log_action, $log_action . ": " . $nama . " (Kode: " . $new_code . ")");
            $_SESSION['success'] = $msg;
            header("Location: barang.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($koneksi);
        }
    }
}

// =======================
// AMBIL DATA SUPPLIER
// =======================
$q_sup = mysqli_query($koneksi, "SELECT * FROM suppliers ORDER BY nama_supplier ASC");
$suppliers = [];
while ($rs = mysqli_fetch_assoc($q_sup)) {
    $suppliers[] = $rs;
}

// =======================
// AMBIL DATA UNTUK EDIT
// =======================
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $q_edit = mysqli_query($koneksi, "SELECT * FROM barang WHERE id=$id");
    if (mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// =======================
// LIST DATA & FILTERING
// =======================
$filterKategori = $_GET['kategori'] ?? '';
$filterSearch = $_GET['search'] ?? '';
$whereClauses = [];

if ($filterKategori != '') {
    $filterKategori = mysqli_real_escape_string($koneksi, $filterKategori);
    $whereClauses[] = "kategori='$filterKategori'";
}

if ($filterSearch != '') {
    $filterSearch = mysqli_real_escape_string($koneksi, $filterSearch);
    $whereClauses[] = "(nama_barang LIKE '%$filterSearch%' OR kode_barang LIKE '%$filterSearch%')";
}

$whereSearch = "";
if (count($whereClauses) > 0) {
    $whereSearch = "WHERE " . implode(" AND ", $whereClauses);
}

$q_barang = mysqli_query($koneksi, "SELECT * FROM barang $whereSearch ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang | Santan Ardafa</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .category-select {
            flex: 1;
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

        /* 4-Column Grid for Form */
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
        <!-- Sidebar included (keep existing sidebar html if needed, or include via php) -->
        <!-- Assuming sidebar is fixed in layout, using simplified structure here -->
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

            <!-- HEADER -->
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h1 class="header-title" style="margin:0;">Kelola Inventory</h1>
                    <p class="header-subtitle" style="margin-top:5px;">Manajemen data barang dan stok gudang</p>
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

                    <div style="display: flex; gap: 10px; width: 100%; max-width: 600px;">
                        <div style="position:relative; flex: 2;">
                            <input type="text" id="navbarSearch" class="form-control"
                                placeholder="Cari barang / kode..." style="padding-left: 40px;"
                                value="<?= htmlspecialchars($filterSearch) ?>">
                            <i class="fas fa-search"
                                style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:var(--primary); opacity:0.6;"></i>
                        </div>
                        <select id="categoryFilter" class="form-control category-select" style="flex: 1;">
                            <option value="">Semua Kategori</option>
                            <option value="Makanan" <?= $filterKategori == 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                            <option value="Minuman" <?= $filterKategori == 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                            <option value="Sembako" <?= $filterKategori == 'Sembako' ? 'selected' : '' ?>>Sembako</option>
                            <option value="Lainnya" <?= $filterKategori == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php
            $msg_error = $_SESSION['error'] ?? $error;
            $msg_success = $_SESSION['success'] ?? $success;
            unset($_SESSION['error'], $_SESSION['success']);
            ?>

            <?php if ($msg_error): ?>
                <div id="alert-error" class="alert-dismissible"
                    style="background:#FFEBEE;color:#D32F2F;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #FFCDD2;">
                    <i class="fas fa-exclamation-circle"></i> <?= $msg_error ?>
                </div>
            <?php endif; ?>

            <?php if ($msg_success): ?>
                <div id="alert-success" class="alert-dismissible"
                    style="background:#E8F5E9;color:#2E7D32;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid #C8E6C9;">
                    <i class="fas fa-check-circle"></i> <?= $msg_success ?>
                </div>
            <?php endif; ?>

            <!-- 2. FORM CARD -->
            <div class="card animate-up">
                <div class="card-header">
                    <?= $edit_data ? 'Edit Barang' : 'Tambah Barang Baru' ?>
                </div>

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_barang" value="<?= $edit_data['id'] ?? '' ?>">

                    <!-- ROW 1 -->
                    <div class="form-grid-4">
                        <!-- Kode Barang removed (Auto-generated) -->
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control"
                                value="<?= $edit_data['nama_barang'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="">Pilih</option>
                                <option value="Makanan" <?= ($edit_data['kategori'] ?? '') == 'Makanan' ? 'selected' : '' ?>>
                                    Makanan</option>
                                <option value="Minuman" <?= ($edit_data['kategori'] ?? '') == 'Minuman' ? 'selected' : '' ?>>
                                    Minuman</option>
                                <option value="Sembako" <?= ($edit_data['kategori'] ?? '') == 'Sembako' ? 'selected' : '' ?>>
                                    Sembako</option>
                                <option value="Lainnya" <?= ($edit_data['kategori'] ?? '') == 'Lainnya' ? 'selected' : '' ?>>
                                    Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <select name="satuan" class="form-control">
                                <option value="Pcs" <?= ($edit_data['satuan'] ?? 'Pcs') == 'Pcs' ? 'selected' : '' ?>>Pcs
                                </option>
                                <option value="Kg" <?= ($edit_data['satuan'] ?? '') == 'Kg' ? 'selected' : '' ?>>Kg
                                </option>
                                <option value="Liter" <?= ($edit_data['satuan'] ?? '') == 'Liter' ? 'selected' : '' ?>>
                                    Liter
                                </option>
                                <option value="Box" <?= ($edit_data['satuan'] ?? '') == 'Box' ? 'selected' : '' ?>>Box
                                </option>
                                <option value="Pack" <?= ($edit_data['satuan'] ?? '') == 'Pack' ? 'selected' : '' ?>>Pack
                                </option>
                                <option value="Botol" <?= ($edit_data['satuan'] ?? '') == 'Botol' ? 'selected' : '' ?>>
                                    Botol
                                </option>
                                <option value="Sachet" <?= ($edit_data['satuan'] ?? '') == 'Sachet' ? 'selected' : '' ?>>
                                    Sachet
                                </option>
                                <option value="Karton" <?= ($edit_data['satuan'] ?? '') == 'Karton' ? 'selected' : '' ?>>
                                    Karton
                                </option>
                                <option value="Renceng" <?= ($edit_data['satuan'] ?? '') == 'Renceng' ? 'selected' : '' ?>>
                                    Renceng</option>
                                <option value="Lusin" <?= ($edit_data['satuan'] ?? '') == 'Lusin' ? 'selected' : '' ?>>
                                    Lusin
                                </option>
                                <option value="Unit" <?= ($edit_data['satuan'] ?? '') == 'Unit' ? 'selected' : '' ?>>Unit
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Posisi Barang</label>
                            <input type="text" name="posisi" class="form-control"
                                value="<?= $edit_data['posisi'] ?? '' ?>" placeholder="Rak / Gudang">
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="form-grid-4">
                        <div class="form-group">
                            <label>Pilih Supplier</label>
                            <select name="supplier_id" class="form-control">
                                <option value="0">Pilih Supplier</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($edit_data['supplier_id'] ?? 0) == $s['id'] ? 'selected' : '' ?>>
                                        <?= $s['nama_supplier'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" class="form-control"
                                value="<?= $edit_data['harga_beli'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Harga Jual (Rp)</label>
                            <input type="number" name="harga_jual" class="form-control"
                                value="<?= $edit_data['harga_jual'] ?? '' ?>" required>
                        </div>
                        <!-- Empty 4th slot or maybe move something else here? Let's leave it 3/4 filled for now or span it -->
                        <div></div>
                    </div>

                    <!-- ROW 3 -->
                    <div class="form-grid-4">
                        <div class="form-group">
                            <label>Stok Saat Ini</label>
                            <input type="number" name="stok" class="form-control"
                                value="<?= $edit_data['stok'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Stok Minimum</label>
                            <input type="number" name="stok_min" class="form-control"
                                value="<?= $edit_data['stok_min'] ?? 5 ?>">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control"
                                value="<?= $edit_data['tgl_masuk'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Kadaluarsa</label>
                            <input type="date" name="tgl_kadaluarsa" class="form-control"
                                value="<?= $edit_data['tgl_kadaluarsa'] ?? '' ?>">
                        </div>
                    </div>

                    <!-- ROW 4: Image & Submit -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                        <div class="form-group" style="width: 300px;">
                            <label>Upload Gambar Barang</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" name="simpan"
                            style="background:var(--primary); color:white; padding:12px 30px; border:none; border-radius:8px; font-weight:bold; cursor:pointer; height:45px; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-save"></i> Simpan Barang
                        </button>
                    </div>

                </form>
            </div>

            <!-- 3. TABLE CARD -->
            <div class="table-container animate-up">
                <div class="card-header"
                    style="border:none; margin-bottom:0; display:flex; justify-content:space-between; align-items:center; background:rgba(46, 125, 50, 0.03); color:var(--primary); padding: 15px 25px;">
                    <h4
                        style="margin:0; font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-list"></i> Daftar Barang
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table" id="barangTable">
                        <thead>
                            <tr>
                                <th style="width: 100px; text-align: center;">KODE</th>
                                <th style="text-align: left;">BARANG</th>
                                <th style="text-align: right;">HARGA</th>
                                <th style="text-align: center;">STOK</th>
                                <th style="text-align: center;">POSISI</th>
                                <th style="text-align: center;">KADALUARSA</th>
                                <th style="text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = mysqli_fetch_assoc($q_barang)): ?>
                                <tr data-kategori="<?= $r['kategori'] ?>">
                                    <td align="center">
                                        <span
                                            style="font-weight: 700; color: var(--primary); font-size: 0.85rem; letter-spacing: 0.5px;"><?= $r['kode_barang'] ?></span>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <?php
                                            $image_path = "../../assets/images/barang/" . $r['gambar'];
                                            $old_path = "../../assets/images/" . $r['gambar'];

                                            if (!empty($r['gambar']) && file_exists($image_path)): ?>
                                                <img src="<?= $image_path ?>"
                                                    style="width:45px; height:45px; object-fit:cover; border-radius:10px; border: 1.5px solid var(--border-color); box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                            <?php elseif (!empty($r['gambar']) && file_exists($old_path)): ?>
                                                <img src="<?= $old_path ?>"
                                                    style="width:45px; height:45px; object-fit:cover; border-radius:10px; border: 1.5px solid var(--border-color); box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                            <?php else: ?>
                                                <div
                                                    style="width:45px; height:45px; background:rgba(0,0,0,0.03); border-radius:10px; display:flex; align-items:center; justify-content:center; color:#9c9fa6; border: 1.5px solid var(--border-color);">
                                                    <i class="fas fa-image" style="font-size:1.2rem; opacity:0.3;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div
                                                    style="font-weight:700; color:var(--text-main); font-size:1rem; line-height:1.2;">
                                                    <?= ucwords($r['nama_barang']) ?>
                                                </div>
                                                <div
                                                    style="font-size:0.75rem; color:var(--text-muted); font-weight:500; margin-top:2px;">
                                                    <?= $r['kategori'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="right">
                                        <div style="font-weight:700; color:var(--text-main); font-size:0.95rem;">Rp
                                            <?= number_format($r['harga_jual'], 0, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <span
                                            style="background:rgba(46,125,50,0.08); color:var(--primary); padding:5px 14px; border-radius:8px; font-weight:800; font-size:0.85rem; min-width:45px; display:inline-block;">
                                            <?= $r['stok'] ?>
                                        </span>
                                    </td>
                                    <td align="center">
                                        <div style="font-size:0.85rem; color:var(--text-muted); font-weight:500;">
                                            <?= $r['posisi'] ?: '<span style="opacity:0.3">-</span>' ?>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div style="font-size:0.85rem; color:var(--text-muted); font-weight:500;">
                                            <?= $r['tgl_kadaluarsa'] ? date('d-m-Y', strtotime($r['tgl_kadaluarsa'])) : '<span style="opacity:0.3">-</span>' ?>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div style="display:flex; gap:8px; justify-content:center;">
                                            <a href="detail_barang.php?id=<?= $r['id'] ?>" class="btn-action"
                                                style="background:#E3F2FD; color:#1976D2; height:32px; padding:0 15px; display:flex; align-items:center; font-weight:700; border-radius:8px;">
                                                <i class="fas fa-eye" style="margin-right:5px;"></i>Detail
                                            </a>
                                            <a href="?edit=<?= $r['id'] ?>" class="btn-action btn-edit"
                                                style="height:32px; padding:0 15px; display:flex; align-items:center; font-weight:700; border-radius:8px;">Edit</a>
                                            <a href="?hapus=<?= $r['id'] ?>" onclick="return confirm('Hapus barang ini?')"
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. GLOBAL NAVBAR SEARCH (AJAX)
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
                                div.innerHTML = `
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-weight:600; color:var(--primary);">${item.nama_barang}</div>
                                <div style="font-size:0.8rem; background:rgba(182,109,255,0.1); color:var(--primary); padding:2px 8px; border-radius:4px;">Stok: ${item.stok}</div>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">
                                <i class="fas fa-map-marker-alt" style="margin-right:5px;"></i> Posisi: ${item.posisi || '-'}
                            </div>
                        `;
                                div.addEventListener('click', () => {
                                    // Filter within this page
                                    const category = document.getElementById('categoryFilter').value;
                                    window.location.href = `barang.php?search=${encodeURIComponent(item.nama_barang)}&kategori=${encodeURIComponent(category)}`;
                                });
                                suggestionsBox.appendChild(div);
                            });
                            suggestionsBox.style.display = 'block';
                        } else {
                            suggestionsBox.innerHTML = '<div style="padding:15px; text-align:center; color:#94a3b8; font-size:0.85rem;"><i class="fas fa-exclamation-circle" style="margin-right:5px;"></i> Barang tidak tersedia</div>';
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
                                    const category = document.getElementById('categoryFilter').value;
                                    window.location.href = `barang.php?search=${encodeURIComponent(query)}&kategori=${encodeURIComponent(category)}`;
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

            categoryFilter.addEventListener('change', function () {
                const search = searchInput.value.trim();
                const category = this.value;
                window.location.href = `barang.php?search=${encodeURIComponent(search)}&kategori=${encodeURIComponent(category)}`;
            });

            // 3. AUTO-HIDE NOTIFICATIONS
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 2000);
            });
        });
    </script>
    <scriptsrc="../../assets/js/notifications.js"></script>
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