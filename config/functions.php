<?php
// Kumpulan fungsi bantuan (helper) umum.

/**
 * Helper to record system activities
 */
function writeLog($action, $details = "")
{
    global $koneksi;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'System';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Guest';

    // Sanitize
    $action = mysqli_real_escape_string($koneksi, $action);
    $details = mysqli_real_escape_string($koneksi, $details);

    $sql = "INSERT INTO activity_logs (user_id, nama_user, role, action, details) 
            VALUES ('$user_id', '$nama_user', '$role', '$action', '$details')";

    return mysqli_query($koneksi, $sql);
}
?>