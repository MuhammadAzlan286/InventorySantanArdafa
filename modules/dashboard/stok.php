<?php
// Halaman untuk melihat dan mengelola stok barang.
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';
require_once 'owner_model.php';

// Data untuk Navbar Notifikasi
$recent_notif = getUnreadNotifications(5);
$unread_count = countUnreadNotifications();
$low_stock_items = getLowStockItems(5);

// Proteksi akses
if ($_SESSION['role'] != 'owner') {
    exit("Akses ditolak");
}

/* ================================
   FILTER KATEGORI
================================ */
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

$where = "";
if (count($whereClauses) > 0) {
    $where = "WHERE " . implode(" AND ", $whereClauses);
}

// Ambil kategori unik
$kategoriList = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM barang");

// Ambil data barang (READ ONLY)
$barang = mysqli_query($koneksi, "
    SELECT * FROM barang
    $where
    ORDER BY kategori ASC, nama_barang ASC
");

/* ================================
   STATISTIK & NOTIFIKASI STOK
================================ */
$stok_habis = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM barang WHERE stok <= 0"));
$stok_low = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM barang WHERE stok > 0 AND stok <= stok_min"));
$stok_aman = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM barang WHERE stok > stok_min"));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Inventaris Stok | Santan Ardafa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../assets/css/style.css?v=1.10">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Highlight Animation for Search Results */
        .search-highlight {
            animation: highlightPulse 2s infinite;
            background-color: rgba(182, 109, 255, 0.1) !important;
            border-left: 4px solid #b66dff;
        }

        @keyframes highlightPulse {
            0% {
                background-color: rgba(182, 109, 255, 0.1);
            }

            50% {
                background-color: rgba(182, 109, 255, 0.25);
            }

            100% {
                background-color: rgba(182, 109, 255, 0.1);
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
            <a href="stok.php" class="active"><i class="fas fa-cubes"></i> <span>Daftar Stok</span></a>
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
            <div></div> <!-- Spacer -->

            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h3 style="font-size:1.5rem; font-weight:700; margin:0;">Inventory / Stok</h3>
                    <p style="color:var(--text-muted); margin-top:5px;">Monitoring stok barang dan ketersediaan gudang
                    </p>
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
                        <div class="notification-dropdown">
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
                        <div style="position:relative; flex: 2;">
                            <input type="text" id="navbarSearch" class="form-control"
                                placeholder="Cari barang / kode..." style="padding-left: 40px;"
                                value="<?= htmlspecialchars($filterSearch) ?>">
                            <i class="fas fa-search"
                                style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:var(--primary); opacity:0.6;"></i>
                        </div>
                        <div style="flex: 1;">
                            <select id="categoryFilter" class="form-control category-select">
                                <option value="">Semua Kategori</option>
                                <?php
                                mysqli_data_seek($kategoriList, 0); // Reset pointer
                                while ($k = mysqli_fetch_assoc($kategoriList)):
                                    ?>
                                    <option value="<?= $k['kategori'] ?>" <?= ($filterKategori == $k['kategori'] ? 'selected' : '') ?>>
                                        <?= $k['kategori'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL INVENTARIS -->
            <div class="table-container animate-up">
                <div class="card-header"
                    style="border:none; margin-bottom:0; background:rgba(46, 125, 50, 0.03); color:var(--primary); padding: 15px 25px;">
                    <h4
                        style="margin:0; font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-cubes"></i> Manajemen Inventaris & Stok
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 100px; text-align: center;">KODE</th>
                                <th style="text-align: left;">BARANG</th>
                                <th style="text-align: center;">KATEGORI</th>
                                <th style="text-align: right;">HARGA JUAL</th>
                                <th style="text-align: center;">STOK</th>
                                <th style="text-align: center;">POSISI</th>
                                <th style="text-align: center;">MIN. STOK</th>
                                <th style="text-align: center;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
                            ?>
                            <?php while ($b = mysqli_fetch_assoc($barang)):
                                $stok_now = $b['stok'];
                                $stok_min = $b['stok_min'];

                                if ($stok_now <= 0) {
                                    $st = "Habis";
                                    $badge_cls = "badge-danger";
                                    $row_cls = "row-danger";
                                } elseif ($stok_now <= $stok_min) {
                                    $st = "Menipis";
                                    $badge_cls = "badge-warning";
                                    $row_cls = "row-warning";
                                } else {
                                    $st = "Aman";
                                    $badge_cls = "badge-success";
                                    $row_cls = "";
                                }

                                // Search Highlighting Logic
                                $is_match = false;
                                if ($search != '' && stripos($b['nama_barang'], $search) !== false) {
                                    $is_match = true;
                                    $row_cls .= " search-highlight";
                                }
                                ?>
                                <tr class="<?= $row_cls ?>" <?= $is_match ? 'data-found="true"' : '' ?>>
                                    <td align="center">
                                        <span
                                            style="font-weight: 700; color: var(--primary); font-size: 0.85rem; letter-spacing: 0.5px;"><?= $b['kode_barang'] ?></span>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <?php
                                            $image_path = "../../assets/images/barang/" . $b['gambar'];
                                            $old_path = "../../assets/images/" . $b['gambar'];

                                            if (!empty($b['gambar']) && file_exists($image_path)): ?>
                                                <img src="<?= $image_path ?>"
                                                    style="width:40px; height:40px; object-fit:cover; border-radius:8px; border: 1.5px solid var(--border-color);">
                                            <?php elseif (!empty($b['gambar']) && file_exists($old_path)): ?>
                                                <img src="<?= $old_path ?>"
                                                    style="width:40px; height:40px; object-fit:cover; border-radius:8px; border: 1.5px solid var(--border-color);">
                                            <?php else: ?>
                                                <div
                                                    style="width:40px; height:40px; background:rgba(255,255,255,0.05); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#666; border: 1.5px solid var(--border-color);">
                                                    <i class="fas fa-image" style="opacity:0.3;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div style="font-weight:700; color:var(--text-main); font-size:0.95rem;">
                                                <?= ucwords($b['nama_barang']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <span
                                            style="background:rgba(255,255,255,0.1); padding:4px 10px; border-radius:6px; font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">
                                            <?= $b['kategori'] ?>
                                        </span>
                                    </td>
                                    <td align="right">
                                        <span style="color:var(--text-main); font-weight:700; font-size:0.9rem;">Rp
                                            <?= number_format($b['harga_jual'], 0, ',', '.') ?></span>
                                    </td>
                                    <td align="center">
                                        <span
                                            style="background:rgba(46,125,50,0.08); color:var(--primary); padding:4px 12px; border-radius:6px; font-weight:800; font-size:0.85rem; min-width:40px; display:inline-block;">
                                            <?= $stok_now ?>
                                        </span>
                                    </td>
                                    <td align="center" style="color:var(--text-muted); font-size:0.85rem; font-weight:500;">
                                        <?= $b['posisi'] ?: '<span style="opacity:0.3">-</span>' ?>
                                    </td>
                                    <td align="center" style="color:var(--text-muted); font-size:0.85rem; font-weight:500;">
                                        <?= $stok_min ?>
                                    </td>
                                    <td align="center">
                                        <span class="status-badge <?= $badge_cls ?>"
                                            style="min-width:80px; text-align:center;">
                                            <?= $st ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($barang) == 0): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:3rem;color:#94a3b8;">
                                        <i class="fas fa-box-open fa-3x"
                                            style="margin-bottom:1rem;display:block;opacity:0.5;"></i>
                                        Tidak ada data barang ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>



    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const foundItem = document.querySelector('[data-found="true"]');
            if (foundItem) {
                // Scroll to the element
                foundItem.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Optional: flash effect via class is already active, 
                // but let's ensure the table is visible if inside a scroll container
            }

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
                                <div style="font-size:0.8rem; background:rgba(182,109,255,0.1); color:var(--primary); padding:2px 8px; border-radius:4px;">Stock: ${item.stok}</div>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">
                                <i class="fas fa-map-marker-alt" style="margin-right:5px;"></i> Posisi: ${item.posisi || '-'}
                            </div>
                        `;
                                div.addEventListener('click', () => {
                                    const category = document.getElementById('categoryFilter').value;
                                    window.location.href = `stok.php?search=${encodeURIComponent(item.nama_barang)}&kategori=${encodeURIComponent(category)}`;
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
                        // Pre-check existence before redirecting
                        fetch(`search_ajax.php?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    const category = document.getElementById('categoryFilter').value;
                                    window.location.href = `stok.php?search=${encodeURIComponent(query)}&kategori=${encodeURIComponent(category)}`;
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

            // NOTIFICATION LOGIC (Moved to notifications.js)

            document.getElementById('categoryFilter').addEventListener('change', function () {
                const search = searchInput.value.trim();
                const category = this.value;
                window.location.href = `stok.php?search=${encodeURIComponent(search)}&kategori=${encodeURIComponent(category)}`;
            });

            // Check if search was performed and no items found
            <?php if (isset($_GET['search']) && mysqli_num_rows($barang) == 0): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang tidak tersedia',
                    text: 'Maaf, barang yang Anda cari tidak dapat ditemukan dalam database kami.',
                    confirmButtonColor: '#b66dff',
                    background: '#1e212b',
                    color: '#e2e8f0'
                });
            <?php endif; ?>
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