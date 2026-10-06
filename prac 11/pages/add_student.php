<?php
// ==============================================================================
// StudentHub - Add Student
// ==============================================================================

// Include database connection
require_once __DIR__ . "/../config/db.php";

$message = "";
$message_type = ""; // "success" or "error"

$name = "";
$username = "";
$email = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name             = trim($_POST['name'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Step 1: Basic validation
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
        $message_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match. Please try again.";
        $message_type = "error";
    } else {
        // Step 2: Check if username or email is already taken (Unique check)
        $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Username or Email is already registered. Please choose another.";
            $message_type = "error";
        } else {
            // Step 3: Hash password using password_hash() for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Step 4: Insert student record using prepared statement
            $stmt = $conn->prepare("INSERT INTO students (name, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "Student registered successfully!";
                $message_type = "success";
                // Clear input values after successful registration
                $name = "";
                $username = "";
                $email = "";
            } else {
                $message = "Error registering student: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - StudentHub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        .form-card {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 35px 40px;
            box-shadow: 0 4px 18px rgba(40, 60, 80, 0.12);
        }
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #315b7d;
        }
        .btn-submit {
            background-color: #315b7d;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: #1f3d56;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .quick-nav {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 15px auto 25px;
            flex-wrap: wrap;
        }
        .quick-btn {
            background: #ffffff;
            color: #315b7d;
            border: 1px solid #315b7d;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .quick-btn:hover, .quick-btn.active {
            background: #315b7d;
            color: #ffffff;
        }
        body.dark-mode .form-card {
            background: #1e293b;
            color: #e2e8f0;
        }
        body.dark-mode .form-group label {
            color: #cbd5e1;
        }
        body.dark-mode .form-control {
            background: #0f172a;
            color: #ffffff;
            border-color: #475569;
        }
    </style>
</head>
<body>

    <!-- Header Frame -->
    <iframe src="header.html" class="header-frame" title="Student Hub Header"></iframe>

    <!-- Home & Dark Mode Bar -->
    <div class="top-action-bar">
        <a href="../index.html" class="home-nav-btn">🏠 Home</a>
        <button id="theme-toggle" class="theme-btn-compact" type="button">🌙 Dark Mode</button>
    </div>

    <!-- Main Navigation Bar -->
    <nav id="main-nav" aria-label="Main navigation">
        <button id="menu-toggle" class="mobile-menu-btn" type="button">☰ Menu</button>
        <div id="nav-links">
            <a href="dashboard.html">DASHBOARD</a>
            <a href="students.php">STUDENTS</a>
            <a href="add_student.php" class="active">ADD STUDENT</a>
            <a href="events.php">EVENTS</a>
            <a href="add_event.php">ADD EVENT</a>
            <a href="register_event.php">REGISTER FOR EVENT</a>
            <a href="registrations.php">REGISTRATIONS</a>
        </div>
    </nav>

    <!-- Page Title -->
    <h1 class="page-title">ADD NEW STUDENT</h1>

    <!-- Quick Navigation Tabs -->
    <div class="quick-nav">
        <a href="students.php" class="quick-btn">👥 View Students</a>
        <a href="add_student.php" class="quick-btn active">➕ Add Student</a>
        <a href="events.php" class="quick-btn">📅 View Events</a>
        <a href="register_event.php" class="quick-btn">📝 Register for Event</a>
        <a href="registrations.php" class="quick-btn">📋 Registrations</a>
    </div>

    <!-- Main Form Container -->
    <main class="form-card">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo ($message_type === 'success' ? '✅ ' : '⚠️ ') . htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="add_student.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name: *</label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="e.g. Rahul Sharma" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Username: *</label>
                <input type="text" id="username" name="username" class="form-control"
                       placeholder="e.g. rahul21" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address: *</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="e.g. rahul@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password: *</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="At least 6 characters" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password: *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                       placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="btn-submit">Register Student</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
            Want to see all registered students?
            <a href="students.php" style="color: #315b7d; font-weight: bold; text-decoration: none;">View Students List</a>
        </p>
    </main>

    <!-- Footer -->
    <footer style="margin-top: auto;">
        &copy; 2026 STUDENT HUB • CHARUSAT University • All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
