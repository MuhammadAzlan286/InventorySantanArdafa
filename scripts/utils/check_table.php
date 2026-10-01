<?php
require '../../config/koneksi.php';
$res = mysqli_query($koneksi, "DESCRIBE transaksi");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>