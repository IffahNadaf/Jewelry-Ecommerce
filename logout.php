<?php
// Start the session to access session variables
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Destroy the session cookie on the client side
// This is required to force the browser to forget the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session data on the server side
session_destroy();

// 4. Redirect the user back to the home page or login page
header("Location: index.php");
exit();