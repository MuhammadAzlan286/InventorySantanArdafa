<?php
require_once '../../config/koneksi.php';

// 1. Table Activity Logs
$q1 = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_user VARCHAR(100),
    role VARCHAR(50),
    action VARCHAR(255),
    details TEXT,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
)";

// 2. Table Suppliers
$q2 = "CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(255) NOT NULL,
    kontak VARCHAR(100),
    alamat TEXT,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
)";

// 3. Table Pengeluaran (Operational Costs)
$q3 = "CREATE TABLE IF NOT EXISTS pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengeluaran VARCHAR(255) NOT NULL,
    nominal DECIMAL(15,2) NOT NULL,
    kategori VARCHAR(100),
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
)";

// 4. Add supplier_id to barang
$q4 = "ALTER TABLE barang ADD COLUMN IF NOT EXISTS supplier_id INT AFTER kategori";

$queries = [$q1, $q2, $q3, $q4];

foreach ($queries as $sql) {
    if (mysqli_query($koneksi, $sql)) {
        echo "Success executing: " . substr($sql, 0, 50) . "...\n";
    } else {
        echo "Error: " . mysqli_error($koneksi) . "\n";
    }
}
?>