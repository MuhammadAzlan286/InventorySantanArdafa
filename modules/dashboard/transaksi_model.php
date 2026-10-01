<?php
require_once '../../config/koneksi.php';

// ==========================================================================
// TRANSAKSI MODEL - STANDARDISASI FINAL
// Menggunakan tabel: 'transaksi'
// Kolom inti: id_barang, qty, total_harga, status, tanggal
// ==========================================================================

function getTransaksi()
{
    global $koneksi;
    return mysqli_query($koneksi, "
        SELECT t.id, b.nama_barang, t.qty, t.total_harga, t.status, t.tanggal
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        ORDER BY t.tanggal DESC
    ");
}

function getKeranjang()
{
    global $koneksi;
    return mysqli_query($koneksi, "
        SELECT t.id, b.nama_barang, b.gambar, t.qty, t.total_harga, t.status, t.tanggal
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        WHERE t.status = 'belum_bayar'
        ORDER BY t.tanggal DESC
    ");
}

function tambahTransaksi($id_barang, $qty)
{
    global $koneksi;

    // Ambil Data Barang
    $res = mysqli_query($koneksi, "SELECT stok, harga_jual FROM barang WHERE id='$id_barang'");
    $b = mysqli_fetch_assoc($res);

    if (!$b || $qty > $b['stok'])
        return false;

    // Harga Jual
    $harga_satuan = $b['harga_jual'];

    // Cek apakah barang sudah ada di keranjang (status: belum_bayar)
    $q_check = mysqli_query($koneksi, "SELECT id, qty FROM transaksi WHERE id_barang='$id_barang' AND status='belum_bayar' LIMIT 1");

    if (mysqli_num_rows($q_check) > 0) {
        // Jika ADA, panggil updateTransaksi agar stok & total_harga benar-benar tersinkron
        $t_existing = mysqli_fetch_assoc($q_check);
        $total_qty = $t_existing['qty'] + $qty;
        return updateTransaksi($t_existing['id'], $total_qty);
    } else {
        // Jika BELUM ADA, Insert baru
        $total = $harga_satuan * $qty;

        // Kurangi Stok
        mysqli_query($koneksi, "UPDATE barang SET stok=stok-$qty WHERE id='$id_barang'");

        $sql = "INSERT INTO transaksi (id_barang, qty, total_harga, status, tanggal) 
                VALUES ('$id_barang','$qty','$total','belum_bayar', NOW())";

        return mysqli_query($koneksi, $sql);
    }
}

function updateTransaksi($id, $new_qty)
{
    global $koneksi;

    // Ambil Data Lama
    $q_old = mysqli_query($koneksi, "SELECT id_barang, qty FROM transaksi WHERE id='$id'");
    $t_old = mysqli_fetch_assoc($q_old);

    // Ambil Data Barang
    $res = mysqli_query($koneksi, "SELECT stok, harga_jual FROM barang WHERE id='{$t_old['id_barang']}'");
    $b = mysqli_fetch_assoc($res);

    if (!$b)
        return false;

    $harga_satuan = $b['harga_jual'];

    // Hitung Revert Stok
    // Stok Nyata = Stok Sekarang + Stok Lama
    $current_stock_real = $b['stok'] + $t_old['qty'];

    if ($new_qty > $current_stock_real)
        return false;

    $new_total = $harga_satuan * $new_qty;

    // Update Stok Baru
    $final_stock = $current_stock_real - $new_qty;
    mysqli_query($koneksi, "UPDATE barang SET stok=$final_stock WHERE id='{$t_old['id_barang']}'");

    // Update Transaksi
    return mysqli_query($koneksi, "UPDATE transaksi SET qty='$new_qty', total_harga='$new_total' WHERE id='$id'");
}

function bayarTransaksi($id)
{
    global $koneksi;
    // Update status AND timestamp to mark the actual sale time
    $res = mysqli_query($koneksi, "UPDATE transaksi SET status='lunas', tanggal=NOW() WHERE id='$id'");
    if ($res) {
        writeLog("Payment Success", "Single item payment for transaction ID: " . $id);
    }
    return $res;
}

function bayarSemua()
{
    global $koneksi;

    // Generate unique code: TRX + DateHash + Random
    $kode_trx = "TRX-" . date('ymd') . "-" . strtoupper(substr(uniqid(), -4));

    $q = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM transaksi WHERE status='belum_bayar'");
    $data = mysqli_fetch_assoc($q);
    $total = $data['total'] ? $data['total'] : 0;

    if ($total == 0)
        return false;

    // Update status, timestamp, AND kode_transaksi for all pending items
    $res = mysqli_query($koneksi, "UPDATE transaksi SET status='lunas', tanggal=NOW(), kode_transaksi='$kode_trx' WHERE status='belum_bayar'");

    if ($res) {
        writeLog("Checkout Success", "Batch payment completed for total: Rp " . number_format($total, 0, ',', '.') . " (Code: $kode_trx)");
        return $kode_trx; // Return the code instead of just true
    }
    return false;
}

function hapusTransaksi($id)
{
    global $koneksi;
    $t = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_barang, qty FROM transaksi WHERE id='$id'"));

    // Kembalikan Stok
    if ($t) {
        mysqli_query($koneksi, "UPDATE barang SET stok=stok+{$t['qty']} WHERE id={$t['id_barang']}");
    }

    return mysqli_query($koneksi, "DELETE FROM transaksi WHERE id='$id'");
}
?>