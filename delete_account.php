<?php
// CRITICAL: Must be the very first thing in the file
session_start();

// Check if the user is logged in and we have a user_id
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    // If not logged in, redirect them immediately
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// --- 1. Prepare and Execute the DELETE Statement ---
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Log error and redirect if statement preparation fails
    error_log("DB Prepare Error on account deletion: " . $conn->error);
    $conn->close();
    // Redirect back to profile with an error message
    header("Location: profile.php?error=deletion_failed");
    exit();
}

$stmt->bind_param("i", $userId);
$success = $stmt->execute();

$stmt->close();
$conn->close();

if ($success) {
    // --- 2. DESTROY THE SESSION (Logout user completely) ---
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // --- 3. Redirect to confirm deletion ---
    // Redirect to home page with a success status
    header("Location: index.php?status=account_deleted");
    exit();
} else {
    // Deletion failed (e.g., integrity constraint failure, though unlikely for a primary key)
    header("Location: profile.php?error=deletion_failed");
    exit();
}

// Closing ?> tag is intentionally omitted for best practices