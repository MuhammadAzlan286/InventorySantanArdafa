<?php
// Halaman manajemen pengguna (karyawan).
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
require_once 'owner_model.php';

// Data untuk Navbar Notifikasi
$recent_notif = getUnreadNotifications(5);
$unread_count = countUnreadNotifications();
$low_stock_items = getLowStockItems(5);


// Role Protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../../auth/login.php");
    exit;
}

$error = null;
$success = null;

// =======================
// DELETE USER
// =======================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Cegah menghapus diri sendiri
    $current_user_id = $_SESSION['id_user'] ?? 0;
    if ($id == $current_user_id) {
        $_SESSION['error'] = "Anda tidak bisa menghapus akun Anda sendiri!";
    } else {
        if (mysqli_query($koneksi, "DELETE FROM users WHERE id=$id")) {
            $_SESSION['success'] = "User berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus user: " . mysqli_error($koneksi);
        }
    }
    header("Location: users.php");
    exit;
}

// =======================
// SIMPAN / UPDATE USER
// =======================
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    if (!empty($_POST['id_user'])) {
        // UPDATE
        $id = intval($_POST['id_user']);

        $sql = "UPDATE users SET nama='$nama', username='$username', role='$role'";

        // Update password jika diisi
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password='$hashed_password'";
        }

        $sql .= " WHERE id=$id";
        $msg = "Data user berhasil diperbarui!";
    } else {
        // INSERT
        if (empty($password)) {
            $_SESSION['error'] = "Password wajib diisi untuk user baru!";
            header("Location: users.php");
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (nama, username, password, role) 
                VALUES ('$nama', '$username', '$hashed_password', '$role')";
        $msg = "User baru berhasil ditambahkan!";
    }

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['success'] = $msg;
        header("Location: users.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// =======================
// AMBIL DATA UNTUK EDIT
// =======================
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $q_edit = mysqli_query($koneksi, "SELECT * FROM users WHERE id=$id");
    if (mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// =======================
// LIST DATA USERS
// =======================
$q_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User | Santan Ardafa</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f0fdf4;
            color: #1a4d2e;
            font-family: 'Ubuntu', sans-serif;
        }

        /* Card Styling */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            /* Softer shadow */
            border: 1px solid rgba(74, 124, 89, 0.1);
            padding: 2rem;
            /* More breathing room */
            height: fit-content;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a4d2e;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e8f5e9;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a7c59;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #d4f1d4;
            /* Lighter border */
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #fcfdfc;
            color: #333;
            box-sizing: border-box;
            /* Ensure padding doesn't break layout */
        }

        .form-control:focus {
            outline: none;
            border-color: #4a7c59;
            background: white;
            box-shadow: 0 0 0 4px rgba(74, 124, 89, 0.1);
        }

        .form-control::placeholder {
            color: #aebdb2;
        }

        /* Button */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #4a7c59 0%, #1a4d2e 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(26, 77, 46, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(26, 77, 46, 0.3);
        }

        /* Badges */
        .badge-role {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bg-owner {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .bg-kasir {
            background: #e0f2f1;
            color: #00695c;
            border: 1px solid #b2dfdb;
        }

        /* Table Enhancements */
        .table-container {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: white;
            border: 1px solid rgba(74, 124, 89, 0.1);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
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
            <a href="logs.php"><i class="fas fa-history"></i> <span>System History</span></a>
            <a href="users.php" class="active"><i class="fas fa-users-cog"></i> <span>Kelola User</span></a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <div class="main-content">
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h3 style="font-size:1.5rem; font-weight:700; margin:0;">Kelola User</h3>
                    <p style="color:var(--text-muted); margin-top:5px;">Atur hak akses pengguna aplikasi Anda.</p>
                </div>
                <div class="header-right" style="display:flex; align-items:center;">
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

            <?php
            $msg_error = $_SESSION['error'] ?? $error;
            $msg_success = $_SESSION['success'] ?? $success;
            unset($_SESSION['error'], $_SESSION['success']);
            ?>

            <?php if ($msg_error): ?>
                <div class="alert-dismissible"
                    style="background:rgba(211,47,47,0.1);color:#d32f2f;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid rgba(211,47,47,0.2);">
                    <i class="fas fa-exclamation-circle"></i> <?= $msg_error ?>
                </div>
            <?php endif; ?>

            <?php if ($msg_success): ?>
                <div id="alert-success" class="alert-dismissible"
                    style="background:rgba(7,205,174,0.1);color:#07cdae;padding:15px;border-radius:10px;margin-bottom:20px;border:1px solid rgba(7,205,174,0.2); transition: opacity 0.5s ease;">
                    <i class="fas fa-check-circle"></i> <?= $msg_success ?>
                </div>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:300px 1fr;gap:2rem;" class="animate-up">
                <!-- FORM USER -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas <?= $edit_data ? 'fa-user-edit' : 'fa-user-plus' ?>"></i>
                        <?= $edit_data ? 'Edit Pengguna' : 'Tambah Pengguna Baru' ?>
                    </div>
                    <?php if ($edit_data): ?>
                        <div style="margin-bottom:1.5rem;"><a href="users.php" style="color:#d32f2f; font-size:0.85rem;"><i
                                    class="fas fa-times"></i> Batal Edit</a></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="id_user" value="<?= $edit_data['id'] ?? '' ?>">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= $edit_data['nama'] ?? '' ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control"
                                value="<?= $edit_data['username'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password <?= $edit_data ? '(Kosongkan jika tidak diubah)' : '(Wajib)' ?></label>
                            <input type="password" name="password" class="form-control" <?= $edit_data ? '' : 'required' ?>>
                        </div>
                        <div class="form-group">
                            <label>Role / Jabatan</label>
                            <select name="role" class="form-control">
                                <option value="kasir" <?= ($edit_data['role'] ?? '') == 'kasir' ? 'selected' : '' ?>>Kasir
                                </option>
                                <option value="owner" <?= ($edit_data['role'] ?? '') == 'owner' ? 'selected' : '' ?>>Owner
                                </option>
                            </select>
                        </div>
                        <button type="submit" name="simpan" class="btn-submit">
                            <i class="fas fa-save"></i> <?= $edit_data ? 'Simpan Perubahan' : 'Simpan User' ?>
                        </button>
                    </form>
                </div>

                <!-- TABLE USERS -->
                <div class="table-container">
                    <div class="card-header"
                        style="border:none; margin-bottom:0; background:rgba(46, 125, 50, 0.03); color:var(--primary); padding: 15px 25px;">
                        <h4
                            style="margin:0; font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-users"></i> Daftar Pengguna
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">NAMA</th>
                                    <th style="text-align:left;">USERNAME</th>
                                    <th style="text-align:center;">ROLE</th>
                                    <th style="text-align:center;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($u = mysqli_fetch_assoc($q_users)): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:700; color:var(--text-main); font-size:1rem;">
                                                <?= $u['nama'] ?>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-muted); font-weight:500;"><?= $u['username'] ?></td>
                                        <td align="center">
                                            <span class="badge-role <?= $u['role'] == 'owner' ? 'bg-owner' : 'bg-kasir' ?>"
                                                style="text-transform:uppercase; font-size:0.75rem; padding:5px 12px; border-radius:8px; font-weight:800; letter-spacing:0.5px; display:inline-block; min-width:80px;">
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td align="center">
                                            <div
                                                style="display:flex; gap:12px; justify-content:center; align-items:center;">
                                                <a href="?edit=<?= $u['id'] ?>"
                                                    style="color:var(--primary); font-size:1.2rem; transition:0.2s;"
                                                    title="Edit" onmouseover="this.style.transform='scale(1.2)'"
                                                    onmouseout="this.style.transform='scale(1)'">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php
                                                $current_user_id = $_SESSION['id_user'] ?? 0;
                                                if ($u['id'] != $current_user_id):
                                                    ?>
                                                    <a href="?hapus=<?= $u['id'] ?>" onclick="return confirm('Hapus user ini?')"
                                                        style="color:#d32f2f; font-size:1.2rem; transition:0.2s;" title="Hapus"
                                                        onmouseover="this.style.transform='scale(1.2)'"
                                                        onmouseout="this.style.transform='scale(1)'">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php endif; ?>
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
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Auto-hide alert
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