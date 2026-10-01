<?php
require_once '../../config/koneksi.php';

echo "Memulai migrasi data gambar barang...\n";

// Ambil semua barang
$q = mysqli_query($koneksi, "SELECT id, nama_barang, gambar FROM barang");
$count = 0;
$updated = 0;

while ($r = mysqli_fetch_assoc($q)) {
    $count++;
    $id = $r['id'];
    $nama = $r['nama_barang'];
    $current_img = $r['gambar'];

    // Prioritas nama file yang cocok dengan nama barang
    $extensions = ['jpeg', 'jpg', 'png'];
    $found_file = null;

    // Coba cari file yang namanya persis dengan nama barang
    // Path updated to go up two levels to root
    foreach ($extensions as $ext) {
        $filename = $nama . "." . $ext;
        $path = "../../assets/images/" . $filename;
        if (file_exists($path)) {
            $found_file = $filename;
            break;
        }
    }

    // Jika tidak ditemukan dengan nama persis, coba cari file IMG_ yang ada di folder (jika ada)
    if (!$found_file && !empty($current_img)) {
        if (file_exists("../../assets/images/" . $current_img)) {
            $found_file = $current_img;
        } elseif (file_exists("../../assets/images/barang/" . $current_img)) {
            // Jika ada di folder barang, biarkan saja (sudah benar secara struktur baru)
            continue;
        }
    }

    if ($found_file) {
        // Update database
        $sql = "UPDATE barang SET gambar = '" . mysqli_real_escape_string($koneksi, $found_file) . "' WHERE id = $id";
        if (mysqli_query($koneksi, $sql)) {
            echo "[OK] ID $id: $nama -> $found_file\n";
            $updated++;
        } else {
            echo "[FAIL] ID $id: " . mysqli_error($koneksi) . "\n";
        }
    }
}

echo "\nMigrasi Selesai!\n";
echo "Total barang dicek: $count\n";
echo "Total barang diperbarui: $updated\n";
?>