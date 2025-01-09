<?php
// File: views/auth/logout.php

require_once __DIR__ . '/../../helpers/SessionHelper.php';

// Start the session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Determine the redirect URL based on the user's role
$redirectUrl = '/'; // Default redirect
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if ($role === 'merchant') {
    $redirectUrl = '/merchant/login';
} elseif ($role === 'user') {
    $redirectUrl = '/';
}

// Clear all session data
$_SESSION = []; // Clear session variables
session_destroy(); // Destroy the session

// Unset the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Debugging step (remove in production)
print_r($_SESSION); // Should show an empty array

// Redirect to the appropriate page
header("Location: $redirectUrl");
exit();
