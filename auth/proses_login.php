<?php
// Logika backend untuk memproses login.
// Configure session before starting
session_set_cookie_params([
    'lifetime' => 2592000,  // 30 days
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
require_once '../config/koneksi.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? mysqli_real_escape_string($koneksi, $_POST['username']) : '';
    $password = isset($_POST['password']) ? mysqli_real_escape_string($koneksi, $_POST['password']) : '';

    if (empty($username) || empty($password)) {
        header("Location: login.php?pesan=gagal");
        exit;
    }

    $query = mysqli_query($koneksi, "
        SELECT * FROM users 
        WHERE username='$username'
    ");

    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        $role = $data['role']; // Get role from database

        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $data['id']; // Standardized to user_id
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $role;

        writeLog("Login Success", "User logged into the system as " . $role);

        // Check if there's a redirect URL stored (from session expiry)
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect_url = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // Clear it
            header("Location: .." . $redirect_url);
        } else {
            header("Location: ../modules/dashboard/");
        }
        exit;
    } else {
        writeLog("Login Failed", "Attempted login for username: " . $username . " with role: " . $role);
        header("Location: login.php?pesan=gagal");
        exit;
    }
} else {
    // If not POST, redirect to login
    header("Location: login.php");
    exit;
}
