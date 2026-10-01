<?php
require_once '../config/koneksi.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = isset($_POST['nama']) ? mysqli_real_escape_string($koneksi, $_POST['nama']) : '';
    $username = isset($_POST['username']) ? mysqli_real_escape_string($koneksi, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role     = isset($_POST['role']) ? mysqli_real_escape_string($koneksi, $_POST['role']) : '';

    // Simple validation
    if (empty($nama) || empty($username) || empty($password) || empty($role)) {
         header("Location: register.php?pesan=gagal");
         exit;
    }

    if (!in_array($role, ['owner','kasir'])) {
        header("Location: register.php?pesan=gagal");
        exit;
    }

    // Check if username already exists
    $check = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if(mysqli_num_rows($check) > 0){
        header("Location: register.php?pesan=duplikat");
        exit;
    }

    // SECURITY: Prevent multiple Owners
    if ($role === 'owner') {
        $checkOwner = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'owner' LIMIT 1");
        if(mysqli_num_rows($checkOwner) > 0) {
            header("Location: register.php?pesan=owner_exists");
            exit;
        }
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = mysqli_query($koneksi, "
        INSERT INTO users (nama, username, password, role)
        VALUES ('$nama', '$username', '$password_hash', '$role')
    ");

    if ($query) {
        header("Location: login.php?pesan=sukses");
        exit;
    } else {
        header("Location: register.php?pesan=gagal");
        exit;
    }
} else {
    // Direct access
    header("Location: register.php");
    exit;
}
