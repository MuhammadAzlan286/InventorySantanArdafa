<?php
require_once '../../config/koneksi.php';
$res = mysqli_query($koneksi, "SELECT DATABASE()");
$row = mysqli_fetch_row($res);
echo "Current DB: " . $row[0] . "\n";
echo "Host info: " . mysqli_get_host_info($koneksi) . "\n";

$q = mysqli_query($koneksi, "SELECT id, nama_barang, gambar FROM barang WHERE nama_barang LIKE '%Gula Pasir Kristal%'");
while ($r = mysqli_fetch_assoc($q)) {
    echo "ID: {$r['id']} | Name: {$r['nama_barang']} | Gambar: [{$r['gambar']}]\n";
}
?>