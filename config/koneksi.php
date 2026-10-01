<?php
// Konfigurasi koneksi ke database.
$host = "localhost";
$user = "root";
$pass = "";
$db = "santanardafa";
$port = 3307;
// Menambahkan variabel port

// Menambahkan parameter $port di akhir fungsi mysqli_connect
$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

require_once 'functions.php';
?>