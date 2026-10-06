<?php
// ==============================================================================
// StudentHub - Login Processing Script (pages/login_process.php)
// Secure authentication using MySQLi, password_verify, and PHP sessions
// ==============================================================================

// 1. Always start the session first
session_start();

// 2. Include existing database connection
require_once __DIR__ . "/../config/db.php";

// 3. Ensure form was submitted via HTTP POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve and trim inputs
    $loginId  = trim($_POST['login_id'] ?? $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // 4. Validate non-empty inputs
    if (empty($loginId) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    // 5. Query user by Username OR Email using MySQLi Prepared Statement
    $sql = "SELECT student_id, name, username, email, password, role FROM students WHERE username = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $loginId, $loginId);
        $stmt->execute();
        $result = $stmt->get_result();

        // 6. Check if user exists and verify hashed password
        if ($user = $result->fetch_assoc()) {

            // Use password_verify() to test against bcrypt hash stored in DB
            if (password_verify($password, $user['password'])) {

                // 7. Security: Regenerate session ID to prevent Session Fixation attacks
                session_regenerate_id(true);

                // 8. Store authenticated user info in session
                $_SESSION['user_id']       = $user['student_id'];
                $_SESSION['name']          = $user['name'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['email']         = $user['email'];
                $_SESSION['role']          = $user['role'] ?? 'student';
                $_SESSION['last_activity'] = time();

                // 9. Optional Remember Me: Secure token generation
                if ($remember) {
                    $rawToken   = bin2hex(random_bytes(32));
                    $tokenHash  = hash('sha256', $rawToken);
                    $expiresAt  = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60)); // 30 days

                    $tokenSql = "INSERT INTO remember_tokens (student_id, token_hash, expires_at) VALUES (?, ?, ?)";
                    $tokenStmt = $conn->prepare($tokenSql);
                    if ($tokenStmt) {
                        $tokenStmt->bind_param("iss", $user['student_id'], $tokenHash, $expiresAt);
                        $tokenStmt->execute();
                        $tokenStmt->close();

                        // Store student_id:rawToken in an HttpOnly cookie
                        $cookieVal = $user['student_id'] . ":" . $rawToken;
                        setcookie("remember_token", $cookieVal, time() + (30 * 24 * 60 * 60), "/", "", false, true);
                    }
                }

                $stmt->close();

                // 10. Role-based Redirection
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    header("Location: dashboard.php");
                    exit();
                }

            }
        }

        $stmt->close();
    }

    // 11. Generic error for both invalid username/email AND incorrect password
    // (Never reveal whether the username or email exists to prevent enumeration)
    header("Location: login.php?error=invalid");
    exit();

} else {
    // If accessed directly via GET, redirect back to login page
    header("Location: login.php");
    exit();
}
?>
