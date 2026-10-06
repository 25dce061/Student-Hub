<?php
// ==============================================================================
// StudentHub - Logout Script (pages/logout.php)
// Terminates active session, clears remember-me cookies, and redirects to login
// ==============================================================================

// 1. Initialize session
session_start();

// 2. Remove remember-me token from database if user was logged in
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . "/../config/db.php";
    $delStmt = $conn->prepare("DELETE FROM remember_tokens WHERE student_id = ?");
    if ($delStmt) {
        $delStmt->bind_param("i", $_SESSION['user_id']);
        $delStmt->execute();
        $delStmt->close();
    }
}

// 3. Delete the remember_token cookie from the client's browser
if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

// 4. Clear all session variables
session_unset();

// 5. Destroy the session on the server
session_destroy();

// 6. Send anti-caching headers so browser history cannot recall sensitive session pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// 7. Redirect back to login page with success notification
header("Location: login.php?logged_out=1");
exit();
?>
