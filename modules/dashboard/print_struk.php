<?php
// Layout untuk mencetak struk transaksi.
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';

if (!isset($_GET['kode_trx']) && !isset($_GET['last']) && !isset($_GET['id'])) {
    exit("Data tidak ditemukan");
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sql = "SELECT t.*, b.nama_barang, b.kode_barang, b.harga_jual 
            FROM transaksi t 
            JOIN barang b ON t.id_barang = b.id 
            WHERE t.id = $id";
} elseif (isset($_GET['kode_trx'])) {
    $kode_trx = mysqli_real_escape_string($koneksi, $_GET['kode_trx']);
    $sql = "SELECT t.*, b.nama_barang, b.kode_barang, b.harga_jual 
            FROM transaksi t 
            JOIN barang b ON t.id_barang = b.id 
            WHERE t.kode_transaksi = '$kode_trx'
            ORDER BY t.id ASC";
} elseif (isset($_GET['last'])) {
    // Ambil kode_transaksi terakhir yang lunas
    $q_last = mysqli_query($koneksi, "SELECT kode_transaksi FROM transaksi WHERE status='lunas' ORDER BY tanggal DESC, id DESC LIMIT 1");
    $d_last = mysqli_fetch_assoc($q_last);
    $kode_trx = $d_last['kode_transaksi'];

    if (!$kode_trx)
        exit("Tidak ada transaksi terakhir yang ditemukan.");

    $sql = "SELECT t.*, b.nama_barang, b.kode_barang, b.harga_jual 
            FROM transaksi t 
            JOIN barang b ON t.id_barang = b.id 
            WHERE t.kode_transaksi = '$kode_trx'
            ORDER BY t.id ASC";
} else {
    exit("Data tidak ditemukan");
}

$result = mysqli_query($koneksi, $sql);
$items = [];
$total_belanja = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
    $total_belanja += $row['total_harga'];
    $tanggal_print = $row['tanggal'];
}

if (empty($items))
    exit("Tidak ada item untuk dicetak");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Belanja - Santan Ardafa</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=1.6">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            background: #f0f0f0;
            padding: 20px;
            margin: 0;
        }

        .invoice-container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #1a4d2e;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 15px;
        }

        .store-info h2 {
            margin: 0;
            color: #1a4d2e;
            font-size: 20px;
            font-weight: bold;
        }

        .store-info p {
            margin: 3px 0;
            color: #555;
            font-size: 12px;
        }

        .trx-info {
            margin-bottom: 15px;
            font-size: 12px;
            line-height: 1.6;
        }

        .trx-info p {
            margin: 2px 0;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
            padding: 8px 0;
            font-size: 12px;
            text-align: left;
        }

        td {
            padding: 8px 0;
            font-size: 12px;
            vertical-align: top;
        }

        .total-section {
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
            padding: 5px 0;
        }

        .total-label {
            color: #333;
        }

        .total-amount {
            color: #1a4d2e;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 100%;
                border-top: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="no-print" style="text-align:center; marginBottom:20px;">
        <button onclick="window.print()"
            style="background:#07cdae; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight:bold;">
            <i class="fas fa-print"></i> Cetak Sekarang
        </button>
        <button onclick="window.close()"
            style="background:#fe7096; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight:bold; margin-left:10px;">
            Tutup
        </button>
    </div>

    <div class="invoice-container">
        <div class="header">
            <div class="store-info">
                <h2>SANTAN ARDAFA</h2>
                <p>Pasar Kunjungan Blok A No. 15</p>
                <p>Telp: 0812-3456-7890</p>
            </div>
        </div>

        <div class="trx-info">
            <p><span>ID Trx:</span>
                <strong>#<?= isset($items[0]['kode_transaksi']) ? $items[0]['kode_transaksi'] : (isset($kode_trx) ? $kode_trx : '---') ?></strong>
            </p>
            <p><span>Tanggal:</span> <span><?= date('d/m/Y H:i', strtotime($tanggal_print)) ?></span></p>
            <p><span>Kasir:</span> <span><?= $_SESSION['nama'] ?></span></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="50%">Barang</th>
                    <th width="20%" style="text-align:center;">Qty</th>
                    <th width="30%" style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($items as $item): ?>
                    <tr>
                        <td><?= $item['nama_barang'] ?> <br> <small
                                style="color:#666;">@<?= number_format($item['harga_jual'], 0, ',', '.') ?></small></td>
                        <td style="text-align:center;"><?= $item['qty'] ?></td>
                        <td style="text-align:right;">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">TOTAL BELANJA</span>
                <span class="total-amount">Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di Santan Ardafa.</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</p>
        </div>
    </div>

</body>

</html>