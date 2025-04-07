<?php
// Configure session settings
if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    // Configure session settings
    ini_set('session.gc_maxlifetime', 3600); // Set session timeout to 1 hour
    ini_set('session.use_strict_mode', 1); // Enable strict mode
    ini_set('session.use_only_cookies', 1); // Force use of cookies only
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie

    // Start session
    session_start();
} else if (headers_sent()) {
    error_log('Headers already sent in ' . __FILE__ . ' on line ' . __LINE__);
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_attendance";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
