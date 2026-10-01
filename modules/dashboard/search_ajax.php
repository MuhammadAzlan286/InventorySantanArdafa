<?php
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';

$search = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';

if (strlen($search) < 2) {
    echo json_encode([]);
    exit;
}

$query = "SELECT nama_barang, stok, posisi, harga_jual FROM barang WHERE nama_barang LIKE '%$search%' OR kode_barang LIKE '%$search%' LIMIT 5";
$result = mysqli_query($koneksi, $query);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode($items);
