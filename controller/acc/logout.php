<?php
// logout.php — Logout controller.
// Destroys the session cleanly and redirects to the login page.
// No HTML output — redirect only.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables.
$_SESSION = [];

// Invalidate the session cookie.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ../../index.php');
exit;
