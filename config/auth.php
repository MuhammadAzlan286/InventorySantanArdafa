<?php
// Fungsi bantuan untuk autentikasi dan pengecekan sesi login.
// Start session with custom configuration
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters (30 days = 2592000 seconds)
    session_set_cookie_params([
        'lifetime' => 2592000,  // 30 days
        'path' => '/',
        'secure' => false,      // Set to true if using HTTPS
        'httponly' => true,     // Prevent JavaScript access
        'samesite' => 'Lax'     // CSRF protection
    ]);

    // Configure session garbage collection
    ini_set('session.gc_maxlifetime', 2592000); // 30 days
    ini_set('session.cookie_lifetime', 2592000); // 30 days

    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login'])) {
    // Store the current page for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    // Redirect to login page with friendly message
    header("Location: ../../auth/login.php?session_expired=1");
    exit;
}

// Optional: Refresh session activity timestamp
$_SESSION['last_activity'] = time();

