<?php
session_start();

if (!isset($_SESSION['role'])) {
    die("Silakan login terlebih dahulu");
}

if ($_SESSION['role'] === 'owner') {
    require 'owner.php';
} elseif ($_SESSION['role'] === 'kasir') {
    require 'kasir.php';
} else {
    echo "Role tidak dikenali";
}
