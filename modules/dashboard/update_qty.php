<?php
require_once '../../config/auth.php';
require_once 'transaksi_model.php';

header('Content-Type: application/json');

if ($_SESSION['role'] !== 'kasir') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_POST['id']) && isset($_POST['qty'])) {
    $id = intval($_POST['id']);
    $qty = intval($_POST['qty']);

    if (updateTransaksi($id, $qty)) {
        // Fetch updated data to return
        $q = mysqli_query($koneksi, "SELECT t.total_harga, b.harga_jual FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE t.id = $id");
        $data = mysqli_fetch_assoc($q);

        // Calculate new grand total and total items
        $q_grand = mysqli_query($koneksi, "SELECT SUM(total_harga) as grand, SUM(qty) as total_qty FROM transaksi WHERE status = 'belum_bayar'");
        $grand = mysqli_fetch_assoc($q_grand);

        echo json_encode([
            'success' => true,
            'subtotal' => (int) $data['total_harga'],
            'grand_total' => (int) $grand['grand'],
            'total_items' => (int) $grand['total_qty']
        ]);
    } else {
        // Fetch Max Available Qty for capping
        $q_max = mysqli_query($koneksi, "SELECT t.qty as cart_qty, b.stok as stock_avail FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE t.id = $id");
        $m = mysqli_fetch_assoc($q_max);
        $max_avail = (int) $m['cart_qty'] + (int) $m['stock_avail'];

        echo json_encode([
            'success' => false,
            'message' => 'Stok tidak mencukupi',
            'max_qty' => $max_avail
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>