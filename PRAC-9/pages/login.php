<?php
// ==============================================================================
// StudentHub - Student Portal Login Page (pages/login.php)
// Handles login form display, error alerts, and session redirection
// ==============================================================================

session_start();

// If user is already logged in, redirect directly to their dashboard
if (isset($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? 'student') === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        header("Location: dashboard.php");
        exit();
    }
}

// Check for feedback messages via GET parameters
$alertMessage = "";
$alertClass   = "";

if (isset($_GET['timeout'])) {
    $alertMessage = "Your session has expired. Please login again.";
    $alertClass   = "login-alert-warning";
} elseif (isset($_GET['logged_out'])) {
    $alertMessage = "You have been logged out successfully.";
    $alertClass   = "login-alert-success";
} elseif (isset($_GET['error'])) {
    if ($_GET['error'] === 'empty') {
        $alertMessage = "Please enter username/email and password.";
        $alertClass   = "login-alert-error";
    } else {
        // Generic error message for security
        $alertMessage = "Invalid username/email or password.";
        $alertClass   = "login-alert-error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Login - Student Hub</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/login.css">
</head>

<body>

    <h1 class="page-title">STUDENT PORTAL LOGIN</h1>

    <main class="login-container">

        <h2>LOGIN</h2>

        <?php if (!empty($alertMessage)): ?>
            <div class="login-alert <?php echo $alertClass; ?>">
                <?php echo htmlspecialchars($alertMessage); ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">

            <div class="login-row">
                <label for="login_id">User / Email:</label>
                <input
                    type="text"
                    id="login_id"
                    name="login_id"
                    placeholder="Enter username or email"
                    required
                >
            </div>

            <div class="login-row">
                <label for="password">Password:</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >
            </div>

            <label class="login-remember">
                <input type="checkbox" name="remember" value="1">
                Remember Me
            </label>

            <button type="submit">Login</button>

        </form>

        <div class="login-links">
            <a href="changepassword.html">Forgot Password?</a>
            <a href="register.html">Create Account</a>
        </div>

    </main>

    <script src="../js/script.js" defer></script>
</body>
</html>
