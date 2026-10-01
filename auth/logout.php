<?php
// Script untuk menangani proses logout.
session_start();
session_unset();
session_destroy();

header("Location: ../index.php");
exit;
