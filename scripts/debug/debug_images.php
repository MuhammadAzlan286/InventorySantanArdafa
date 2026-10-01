<?php
require_once '../../config/koneksi.php';
$q = mysqli_query($koneksi, "SELECT id, nama_barang, gambar FROM barang WHERE id BETWEEN 140 AND 146");
while ($r = mysqli_fetch_assoc($q)) {
    echo "ID: {$r['id']} | Name: {$r['nama_barang']} | Gambar: [{$r['gambar']}] | Empty: " . (empty($r['gambar']) ? 'YES' : 'NO') . "\n";
}
?>