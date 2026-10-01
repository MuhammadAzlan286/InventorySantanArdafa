<?php
require_once '../../config/koneksi.php';
$res = mysqli_query($koneksi, "DESCRIBE pengeluaran");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>