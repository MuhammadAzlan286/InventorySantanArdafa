<?php
require_once '../../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| OWNER DASHBOARD MODEL - FINAL & STABLE
|--------------------------------------------------------------------------
| Menggunakan tabel: 'transaksi'
| Kolom: 'total_harga', 'tanggal'
*/

// ================= BARANG =================
function totalBarang()
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang");
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['total'] ?? 0);
}

// ================= STOK =================
function totalStok()
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT SUM(stok) AS total FROM barang");
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['total'] ?? 0);
}

// ================= TRANSAKSI =================
function totalTransaksi()
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi");
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['total'] ?? 0);
}

function totalTransaksiLunas()
{
    global $koneksi;
    $q = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total 
         FROM transaksi 
         WHERE status = 'lunas'"
    );
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['total'] ?? 0);
}

// ================= PENDAPATAN =================
function totalPendapatan()
{
    global $koneksi;
    $q = mysqli_query(
        $koneksi,
        "SELECT SUM(total_harga) AS total 
         FROM transaksi 
         WHERE status = 'lunas'"
    );
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['total'] ?? 0);
}

// ================= STOK MENIPIS =================
function countLowStock()
{
    global $koneksi;
    $q = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total 
         FROM barang 
         WHERE stok <= stok_min"
    );
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['total'] ?? 0);
}

// ================= GRAFIK 7 HARI TERAKHIR =================
function getSalesChartData()
{
    global $koneksi;

    $data = [
        'labels' => [],
        'values' => []
    ];

    for ($i = 6; $i >= 0; $i--) {
        $tanggal = date('Y-m-d', strtotime("-$i days"));
        $label = date('d M', strtotime($tanggal));

        $q = mysqli_query(
            $koneksi,
            "SELECT SUM(total_harga) AS total
             FROM transaksi
             WHERE status='lunas'
             AND DATE(tanggal) = '$tanggal'"
        );

        $r = mysqli_fetch_assoc($q);

        $data['labels'][] = $label;
        $data['values'][] = (int) ($r['total'] ?? 0);
    }

    return $data;
}

// ================= NOTIFIKASI TERBARU (Unread Only) =================
function getUnreadNotifications($limit = 5)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "
        SELECT t.*, b.nama_barang 
        FROM transaksi t 
        LEFT JOIN barang b ON t.id_barang = b.id 
        WHERE t.is_read = 0 
        ORDER BY t.tanggal DESC 
        LIMIT $limit
    ");
    $data = [];
    while ($row = mysqli_fetch_assoc($q)) {
        // Fallback if item deleted
        if (!$row['nama_barang']) {
            $row['nama_barang'] = "Item Tidak Dikenal (ID: {$row['id_barang']})";
        }
        $data[] = $row;
    }
    return $data;
}

function getLowStockItems($limit = 5)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok <= stok_min ORDER BY stok ASC LIMIT $limit");
    $data = [];
    while ($row = mysqli_fetch_assoc($q)) {
        $data[] = $row;
    }
    return $data;
}

function countUnreadNotifications()
{
    global $koneksi;
    // Count unread transactions
    $q_trans = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE is_read = 0");
    $r_trans = mysqli_fetch_assoc($q_trans);
    $unread_trans = (int) ($r_trans['total'] ?? 0);

    // Count low stock items
    $q_low = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE stok <= stok_min");
    $r_low = mysqli_fetch_assoc($q_low);
    $low_stock = (int) ($r_low['total'] ?? 0);

    return $unread_trans + $low_stock;
}

// ================= NEW ANALYTICS FUNCTIONS =================

function getBestSellingProducts($limit = 5)
{
    global $koneksi;
    $q = mysqli_query($koneksi, "
        SELECT b.nama_barang, SUM(t.qty) as total_qty
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        WHERE t.status = 'lunas'
        GROUP BY t.id_barang
        ORDER BY total_qty DESC
        LIMIT $limit
    ");
    $data = ['labels' => [], 'values' => []];
    while ($row = mysqli_fetch_assoc($q)) {
        $data['labels'][] = $row['nama_barang'];
        $data['values'][] = (int) $row['total_qty'];
    }
    return $data;
}

function getCategoryProfitDistribution()
{
    global $koneksi;
    $q = mysqli_query($koneksi, "
        SELECT b.kategori, SUM(t.total_harga - (b.harga_beli * t.qty)) as untung
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        WHERE t.status = 'lunas'
        GROUP BY b.kategori
    ");
    $data = ['labels' => [], 'values' => []];
    while ($row = mysqli_fetch_assoc($q)) {
        $data['labels'][] = $row['kategori'];
        $data['values'][] = (int) $row['untung'];
    }
    return $data;
}
?>