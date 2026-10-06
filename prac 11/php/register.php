<?php
// ==============================================================================
// StudentHub - Student Registration Processing Script (php/register.php)
// Secure MySQLi + password_hash implementation
// ==============================================================================

// Reuse existing database connection
require_once __DIR__ . "/../config/db.php";

$errors = [];
$isSuccess = false;

// Ensure form was submitted using HTTP POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Receive and trim inputs
    $name             = trim($_POST['name'] ?? $_POST['fullname'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Backend validation
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required. Please fill in all fields.";
    } else {
        if (!preg_match("/^[A-Za-z ]{3,30}$/", $name)) {
            $errors[] = "Full Name must contain only letters and spaces (3 to 30 characters).";
        }
        if (!preg_match("/^[A-Za-z0-9]{3,15}$/", $username)) {
            $errors[] = "Username must be 3 to 15 characters long (letters and numbers only).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^[A-Za-z0-9._%+-]+@gmail\.com$/i", $email)) {
            $errors[] = "Email must be a valid Gmail address (e.g., student@gmail.com).";
        }
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
            $errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.";
        }
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }

    // Duplicate username and email check using MySQLi prepared statement
    if (empty($errors)) {
        $checkSql = "SELECT username, email FROM students WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);

        if ($checkStmt) {
            $checkStmt->bind_param("ss", $username, $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            $usernameExists = false;
            $emailExists    = false;

            while ($row = $checkResult->fetch_assoc()) {
                if (strcasecmp($row['username'], $username) === 0) {
                    $usernameExists = true;
                }
                if (strcasecmp($row['email'], $email) === 0) {
                    $emailExists = true;
                }
            }

            $checkStmt->close();

            if ($usernameExists && $emailExists) {
                $errors[] = "Username and email already exist.";
            } elseif ($usernameExists) {
                $errors[] = "Username already exists.";
            } elseif ($emailExists) {
                $errors[] = "Email already exists.";
            }
        } else {
            $errors[] = "Database error: unable to check existing accounts.";
        }
    }

    // Hash password and insert student
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertSql = "INSERT INTO students (name, username, email, password) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);

        if ($insertStmt) {
            $insertStmt->bind_param("ssss", $name, $username, $email, $hashedPassword);

            if ($insertStmt->execute()) {
                $isSuccess = true;
            } else {
                $errors[] = "Database error: registration failed. Please try again.";
            }

            $insertStmt->close();
        } else {
            $errors[] = "Database error: failed to prepare statement.";
        }
    }

} else {
    header("Location: ../pages/register.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status - Student Hub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/register.css">
</head>

<body>

    <iframe src="../pages/header.html" class="header-frame" title="Student Hub Header"></iframe>

    <div class="top-action-bar">
        <a href="../index.html" class="home-nav-btn">🏠 Home</a>
        <button id="theme-toggle" class="theme-btn-compact" type="button">🌙 Dark Mode</button>
    </div>

    <nav id="main-nav">
        <div id="nav-links">
            <a href="../pages/dashboard.html">DASHBOARD</a>
            <a href="../pages/course.html">COURSES</a>
            <a href="../pages/event.html">EVENTS</a>
            <a href="../pages/faq.html">FAQ</a>
            <a href="../pages/contact.html">CONTACT</a>
            <a href="../pages/profile.html">PROFILE</a>
            <a href="../pages/register.html">REGISTER</a>
            <a href="../pages/login.html">LOGIN</a>
        </div>
    </nav>

    <h1 class="page-title">REGISTRATION STATUS</h1>

    <main class="register-main">
        <div class="result-card">
            <?php if ($isSuccess): ?>
                <div class="result-title success">🎉 Registration Successful!</div>
                <div class="msg-box-success">
                    Registration successful!<br>
                    You can now login.
                </div>
                <p style="color: #64748b; margin-bottom: 25px; font-size: 15px;">
                    Welcome to StudentHub, <strong><?php echo htmlspecialchars($name); ?></strong>! Your account has been securely created.
                </p>
                <div class="btn-container">
                    <a href="../pages/login.html" class="btn-primary">Go to Login</a>
                    <a href="../index.html" class="btn-secondary">Back to Home</a>
                </div>
            <?php else: ?>
                <div class="result-title error">⚠️ Registration Failed</div>
                <div class="msg-box-error">
                    <strong>Please resolve the following:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="btn-container">
                    <a href="../pages/register.html" class="btn-primary">Try Again</a>
                    <a href="javascript:history.back()" class="btn-secondary">← Go Back</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        &copy; 2026 STUDENT HUB &bull; CHARUSAT University &bull; All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>