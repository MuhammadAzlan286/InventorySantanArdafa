<?php
require_once '../../config/koneksi.php';

$sql = "ALTER TABLE transaksi ADD COLUMN is_read TINYINT(1) DEFAULT 0";

if (mysqli_query($koneksi, $sql)) {
    echo "SUCCESS: Column 'is_read' added to 'transaksi' table.";
} else {
    echo "ERROR: " . mysqli_error($koneksi);
}
?>
