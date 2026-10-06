<?php
// ==============================================================================
// StudentHub - Authentication & Session Protection (config/auth.php)
// Simple, beginner-friendly session verification & access control
// ==============================================================================

// 1. Start PHP session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Prevent browser from caching protected pages after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 3. Optional Remember Me Auto-Login: Check if user has a valid remember-me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once __DIR__ . "/db.php";

    $cookieData = explode(":", $_COOKIE['remember_token'], 2);
    if (count($cookieData) === 2) {
        $cookieStudentId = intval($cookieData[0]);
        $cookieRawToken  = $cookieData[1];
        $cookieTokenHash = hash('sha256', $cookieRawToken);

        $autoLoginSql = "SELECT s.student_id, s.name, s.username, s.email, s.role 
                         FROM remember_tokens rt 
                         JOIN students s ON rt.student_id = s.student_id 
                         WHERE rt.student_id = ? AND rt.token_hash = ? AND rt.expires_at > NOW() 
                         LIMIT 1";

        $autoStmt = $conn->prepare($autoLoginSql);
        if ($autoStmt) {
            $autoStmt->bind_param("is", $cookieStudentId, $cookieTokenHash);
            $autoStmt->execute();
            $autoResult = $autoStmt->get_result();

            if ($autoUser = $autoResult->fetch_assoc()) {
                // Token is valid! Restore session
                session_regenerate_id(true);
                $_SESSION['user_id']       = $autoUser['student_id'];
                $_SESSION['name']          = $autoUser['name'];
                $_SESSION['username']      = $autoUser['username'];
                $_SESSION['email']         = $autoUser['email'];
                $_SESSION['role']          = $autoUser['role'];
                $_SESSION['last_activity'] = time();
            } else {
                // Invalid or expired token: clear cookie
                setcookie("remember_token", "", time() - 3600, "/");
            }
            $autoStmt->close();
        }
    }
}

// 4. Check whether user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 5. Session Timeout: Check for 15 minutes (900 seconds) of inactivity
$timeout = 900; // 900 seconds = 15 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Session has expired due to inactivity
    session_unset();
    session_destroy();

    // Clear remember me cookie on timeout if desired
    if (isset($_COOKIE['remember_token'])) {
        setcookie("remember_token", "", time() - 3600, "/");
    }

    header("Location: login.php?timeout=1");
    exit();
}

// Update last activity timestamp to current time
$_SESSION['last_activity'] = time();
?>
