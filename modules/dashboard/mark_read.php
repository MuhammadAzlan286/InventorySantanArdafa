<?php
require_once '../../config/auth.php';
require_once '../../config/koneksi.php';

header('Content-Type: application/json');

// Check if user is logged in is already handled by auth.php
// Allow all roles to mark as read to prevent "unclickable" notifications

if (isset($_GET['all'])) {
    $query = "UPDATE transaksi SET is_read = 1 WHERE is_read = 0";
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "UPDATE transaksi SET is_read = 1 WHERE id = $id";

    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
}
?>