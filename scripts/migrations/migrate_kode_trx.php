<?php
require '../../config/koneksi.php';

// Check if column exists
$res = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE 'kode_transaksi'");
if (mysqli_num_rows($res) == 0) {
    $sql = "ALTER TABLE transaksi ADD COLUMN kode_transaksi VARCHAR(20) DEFAULT NULL AFTER id";
    if (mysqli_query($koneksi, $sql)) {
        echo "Column 'kode_transaksi' added successfully.\n";
    } else {
        echo "Error adding column: " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "Column 'kode_transaksi' already exists.\n";
}
?>