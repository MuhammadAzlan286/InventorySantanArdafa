<?php
// Halaman riwayat transaksi penjualan.
require_once '../../config/auth.php';
require_once 'transaksi_model.php';

if ($_SESSION['role'] !== 'kasir' && $_SESSION['role'] !== 'owner') {
    exit("Akses ditolak");
}

$error = null;
$success = null;

// Tambah transaksi
if (isset($_POST['tambah'])) {
    $id_barang = $_POST['id_barang'];
    $qty = $_POST['qty'];
    if (tambahTransaksi($id_barang, $qty)) {
        $success = "Item berhasil ditambahkan!";
    } else {
        $error = "Gagal! Stok barang tidak mencukupi.";
    }
}

// Update transaksi
if (isset($_POST['update'])) {
    $id = $_POST['id_transaksi'];
    $new_qty = $_POST['qty'];
    if (updateTransaksi($id, $new_qty)) {
        $success = "Jumlah item diperbarui!";
    } else {
        $error = "Gagal update! Stok tidak cukup.";
    }
}

// Bayar SATU transaksi
if (isset($_GET['bayar'])) {
    bayarTransaksi($_GET['bayar']);
    header("Location: transaksi.php");
    exit;
}

// Bayar SEMUA (Checkout)
if (isset($_POST['bayar_semua'])) {
    $kode_trx = bayarSemua();
    if ($kode_trx) {
        header("Location: transaksi.php?print_success=true&kode_trx=" . $kode_trx);
        exit;
    }
}

// Reset / Clear Cart
if (isset($_POST['reset'])) {
    // First, return stock for all pending transactions
    global $koneksi;
    $q_pending = mysqli_query($koneksi, "SELECT id_barang, qty FROM transaksi WHERE status = 'belum_bayar'");
    while ($item = mysqli_fetch_assoc($q_pending)) {
        mysqli_query($koneksi, "UPDATE barang SET stok = stok + {$item['qty']} WHERE id = {$item['id_barang']}");
    }
    // Then delete all pending transactions
    mysqli_query($koneksi, "DELETE FROM transaksi WHERE status = 'belum_bayar'");
    header("Location: transaksi.php");
    exit;
}

// Hapus transaksi
if (isset($_GET['hapus'])) {
    hapusTransaksi($_GET['hapus']);
    header("Location: transaksi.php");
    exit;
}

// Ambil data KERANJANG (Status: belum_bayar)
$keranjang = getKeranjang();
$total_belanja = 0;
$total_items = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir | Santan Ardafa</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.12">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Ubuntu', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
        }

        .main-content {
            padding: 30px;
        }

        /* Override container for dashboard fit */
        .container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Form Elements */
        label {
            display: block;
            color: #4a7c59;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #d4f1d4;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: white;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        @media (min-width: 1200px) {
            .transaksi-grid {
                display: grid;
                grid-template-columns: 380px 1fr;
                gap: 25px;
                align-items: start;
            }
        }

        .form-group {
            margin-bottom: 1rem;
        }

        /* Buttons */
        .btn {
            padding: 0.85rem 1.75rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            white-space: nowrap;
        }

        .btn-primary {
            background: #4a7c59;
            color: white;
        }

        .btn-primary:hover {
            background: #1a4d2e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(74, 124, 89, 0.3);
        }

        .btn-success {
            background: #4a7c59;
            color: white;
        }

        .btn-success:hover {
            background: #1a4d2e;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #4a7c59;
            color: white;
        }

        th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e8f5e8;
            font-size: 0.9rem;
        }

        tbody tr:hover {
            background: #f8fdf8;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #a0a0a0;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Total Section */
        .total-section {
            display: flex;
            flex-direction: column;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            margin-bottom: 2rem;
            border: 1px solid #e8f5e8;
        }

        .total-info {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .total-item:not(:last-child) {
            border-bottom: 1px dashed #e8f5e8;
        }

        .total-label {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .total-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a4d2e;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .action-buttons form {
            display: block;
            width: 100%;
        }

        .action-buttons .btn {
            width: 100%;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 1.5rem 1rem;
            color: #666;
            font-size: 0.85rem;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            border: 2px solid #d4f1d4;
            border-radius: 10px;
            height: 50px;
            display: flex;
            align-items: center;
            background: white;
            transition: all 0.2s ease;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #4a7c59;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            padding-left: 15px;
            padding-right: 30px;
            color: #1a4d2e;
            font-weight: 500;
            font-size: 0.95rem;
        }


        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
            right: 12px;
            display: flex;
            align-items: center;
        }

        .select2-dropdown {
            border: 2px solid #d4f1d4;
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            margin-top: 5px;
        }

        .select2-results__option {
            padding: 12px 15px;
            font-size: 0.9rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #4a7c59;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Action Icons */
        .action-icon {
            color: #dc3545;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.1rem;
        }

        .action-icon:hover {
            color: #c82333;
            transform: scale(1.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .total-section {
                flex-direction: column;
                gap: 1.5rem;
            }

            .total-info {
                flex-direction: column;
                gap: 1rem;
                width: 100%;
            }

            .action-buttons {
                width: 100%;
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
            <a href="kasir.php">
                <i class="fas fa-home"></i> <span>Beranda</span>
            </a>
            <a href="transaksi.php" class="active">
                <i class="fas fa-cash-register"></i> <span>Mulai Transaksi</span>
            </a>

            <div style="margin-top:auto; padding:20px 15px;">
                <a href="../../auth/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- HEADER -->
            <div class="dashboard-header animate-up">
                <div class="header-left">
                    <h3 style="font-size:1.5rem; font-weight:700; margin:0;">Sistem Kasir</h3>
                    <p style="color:var(--text-muted); margin-top:5px;">SANTAN ARDAFA POS</p>
                </div>
                <div class="header-right" style="display:flex; align-items:center;">
                    <div
                        style="background:white; padding:10px 20px; border-radius:12px; color:var(--primary); font-size:0.9rem; font-weight:600; box-shadow:var(--shadow-soft); border:1.5px solid var(--border-color); display:flex; align-items:center; height:45px;">
                        <i class="far fa-calendar-alt" style="margin-right:8px;"></i> <?= date('d F Y') ?>
                    </div>
                </div>
            </div>

            <!-- Main Container -->
            <div class="container">
                <div class="transaksi-grid">
                    <!-- Left Column: Tambah Item -->
                    <div class="left-column">
                        <div class="card animate-up">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="fa-solid fa-plus-circle"></i> Tambah Item
                                </h4>
                            </div>
                            <div class="card-body">

                                <?php if ($error): ?>
                                    <div class="alert alert-error alert-dismissible">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <?= $error ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($success): ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <i class="fas fa-check-circle"></i>
                                        <?= $success ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Nama / Cari Barang</label>
                                            <select name="id_barang" id="select_barang" required>
                                                <option value="">Contoh: Beras 5kg</option>
                                                <?php
                                                $q_barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nama_barang ASC");
                                                while ($b = mysqli_fetch_assoc($q_barang)) {
                                                    $stok_info = ($b['stok'] <= 0) ? "(HABIS)" : "(Stok: {$b['stok']})";
                                                    $disabled = ($b['stok'] <= 0) ? "disabled" : "";
                                                    echo "<option value='{$b['id']}' $disabled data-price='{$b['harga_jual']}'>{$b['nama_barang']} - Rp " . number_format($b['harga_jual']) . " $stok_info</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Harga</label>
                                            <input type="text" id="harga_display" placeholder="Harga sesuai" readonly
                                                style="background: #f8fdf8;">
                                        </div>
                                        <div class="form-group">
                                            <label>Jumlah (Qty)</label>
                                            <input type="number" name="qty" value="1" min="1" placeholder="Jumlah">
                                        </div>
                                    </div>
                                    <button type="submit" name="tambah" class="btn btn-primary" style="width: 100%;">
                                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </form>
                            </div> <!-- Close card-body -->
                        </div>
                    </div> <!-- Close left-column -->

                    <!-- Right Column: Keranjang & Total -->
                    <div class="right-column">
                        <div class="card animate-up">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="fa-solid fa-shopping-cart"></i> Keranjang Belanja
                                </h4>
                            </div>
                            <div class="card-body">

                                <div class="table-container">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Barang</th>
                                                <th style="width: 120px;">Qty</th>
                                                <th style="width: 150px;">Harga</th>
                                                <th style="width: 150px;">Subtotal</th>
                                                <th style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($keranjang) > 0):
                                                while ($t = mysqli_fetch_assoc($keranjang)):
                                                    $total_belanja += $t['total_harga'];
                                                    $total_items += $t['qty'];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div style="font-weight: 600; color: #333;">
                                                                <?= $t['nama_barang'] ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="qty-input" data-id="<?= $t['id'] ?>"
                                                                value="<?= $t['qty'] ?>" min="1"
                                                                style="width: 80px; padding: 0.5rem; border: 2px solid #d4f1d4; border-radius: 6px; text-align: center;">
                                                        </td>
                                                        <td>Rp <?= number_format($t['total_harga'] / $t['qty'], 0, ',', '.') ?>
                                                        </td>
                                                        <td>
                                                            <span id="subtotal-<?= $t['id'] ?>"
                                                                style="font-weight: 700; color: #1a4d2e;">
                                                                Rp <?= number_format($t['total_harga'], 0, ',', '.') ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="?hapus=<?= $t['id'] ?>"
                                                                onclick="return confirm('Hapus item ini?')" class="action-icon">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                endwhile;
                                            else:
                                                ?>
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="empty-state">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            <div>Keranjang masih kosong</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- Close card-body -->
                        </div>

                        <!-- Total and Actions -->
                        <div class="total-section animate-up">
                            <div class="total-info">
                                <div class="total-item">
                                    <span class="total-label">Total Item</span>
                                    <span class="total-value" id="total_items"><?= $total_items ?></span>
                                </div>
                                <div class="total-item">
                                    <span class="total-label">Total Belanja</span>
                                    <span class="total-value" id="total_belanja">Rp
                                        <?= number_format($total_belanja, 0, ',', '.') ?></span>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <?php if (mysqli_num_rows($keranjang) > 0):
                                    mysqli_data_seek($keranjang, 0); ?>
                                    <form method="post" onsubmit="return confirm('Selesaikan transaksi?');"
                                        style="display: inline;">
                                        <button type="submit" name="bayar_semua" class="btn btn-success">
                                            <i class="fas fa-credit-card"></i> Proses Pembayaran
                                        </button>
                                    </form>

                                    <form method="post" onsubmit="return confirm('Reset semua item?');"
                                        style="display: inline;">
                                        <button type="submit" name="reset" class="btn btn-danger">
                                            <i class="fas fa-redo"></i> Reset
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php if (isset($_GET['print_success'])): ?>
                                        <button type="button"
                                            onclick="window.open('print_struk.php?kode_trx=<?= $_GET['kode_trx'] ?>', '_blank', 'width=400,height=600')"
                                            class="btn btn-primary"
                                            style="background:var(--primary); font-size:1.1rem; padding:15px 30px; box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);">
                                            <i class="fas fa-print"></i> Cetak Struk
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div> <!-- Close right-column -->
                </div> <!-- Close transaksi-grid -->

            </div> <!-- Close container -->

            <script>
                $(document).ready(function () {
                    // Select2 initialization
                    $('#select_barang').select2({
                        placeholder: "Pilih Barang..."
                    });

                    // Display price on selection
                    $('#select_barang').on('change', function () {
                        var price = $(this).find(':selected').data('price');
                        if (price) {
                            $('#harga_display').val('Rp ' + parseInt(price).toLocaleString('id-ID'));
                        } else {
                            $('#harga_display').val('');
                        }
                    });

                    // AJAX Qty Update
                    function updateQtyAjax(id, newQty) {
                        if (newQty < 1) return;

                        $.ajax({
                            url: 'update_qty.php',
                            type: 'POST',
                            data: { id: id, qty: newQty },
                            success: function (response) {
                                if (response.success) {
                                    $(`#subtotal-${id}`).text('Rp ' + response.subtotal.toLocaleString('id-ID'));
                                    $('#total_belanja').text('Rp ' + response.grand_total.toLocaleString('id-ID'));
                                    $('#total_items').text(response.total_items);
                                } else {
                                    if (response.max_qty !== undefined) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Stok tidak mencukupi',
                                            text: `Maaf, stok yang tersedia hanya ${response.max_qty} unit.`,
                                            confirmButtonColor: '#4a7c59'
                                        });
                                        $(`.qty-input[data-id="${id}"]`).val(response.max_qty);
                                        updateQtyAjax(id, response.max_qty);
                                    } else {
                                        alert(response.message || 'Gagal update');
                                        location.reload();
                                    }
                                }
                            },
                            error: function () {
                                alert('Terjadi kesalahan koneksi');
                            }
                        });
                    }

                    // Qty input change
                    $('.qty-input').on('change', function () {
                        updateQtyAjax($(this).data('id'), $(this).val());
                    });

                    // Print success
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('print_success')) {
                        const kodeTrx = urlParams.get('kode_trx');
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            text: 'Mencetak struk...',
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonColor: '#4a7c59'
                        }).then(() => {
                            if (kodeTrx) {
                                window.open('print_struk.php?kode_trx=' + kodeTrx, '_blank', 'width=400,height=600');
                            } else {
                                window.open('print_struk.php?last=true', '_blank', 'width=400,height=600');
                            }
                            window.history.replaceState({}, document.title, window.location.pathname);
                        });
                    }

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
            <!-- Footer -->
            <div class="footer">
                <span class="highlight">Santan Ardafa</span> ✨ Excellence in Every Transaction • © 2026 Crafted with
                Passion
            </div>
        </div> <!-- Close main-content -->
    </div> <!-- Close wrapper -->
</body>

</html>