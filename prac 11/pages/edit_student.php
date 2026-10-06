<?php
// ==============================================================================
// StudentHub - Edit Student Record (pages/edit_student.php)
// All Database Operations using MySQLi Prepared Statements
// Clear Success and Failure Messages
// ==============================================================================

require_once __DIR__ . "/../config/db.php";

$message = "";
$message_type = ""; // "success" or "error"

$student_id = intval($_GET['id'] ?? $_POST['student_id'] ?? 0);

if ($student_id <= 0) {
    header("Location: students.php?msg=not_found");
    exit();
}

// Fetch existing student using MySQLi prepared statement
$stmt = $conn->prepare("SELECT student_id, name, username, email, role FROM students WHERE student_id = ?");
if (!$stmt) {
    die("Database prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    header("Location: students.php?msg=not_found");
    exit();
}

$name     = $student['name'];
$username = $student['username'];
$email    = $student['email'];
$role     = $student['role'] ?? 'student';

// Handle POST update submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name             = trim($_POST['name'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $role             = trim($_POST['role'] ?? 'student');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Step 1: Input Validation
    if (empty($name) || empty($username) || empty($email) || empty($role)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "error";
    } elseif (!empty($password) && strlen($password) < 6) {
        $message = "New password must be at least 6 characters long.";
        $message_type = "error";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $message = "Passwords do not match. Please try again.";
        $message_type = "error";
    } else {
        // Step 2: Check for duplicate username or email among OTHER students (MySQLi Prepared Statement)
        $dupStmt = $conn->prepare("SELECT student_id, username, email FROM students WHERE (username = ? OR email = ?) AND student_id != ?");
        $dupStmt->bind_param("ssi", $username, $email, $student_id);
        $dupStmt->execute();
        $dupResult = $dupStmt->get_result();

        if ($dupResult->num_rows > 0) {
            $conflict = $dupResult->fetch_assoc();
            if (strcasecmp($conflict['username'], $username) === 0) {
                $message = "Username '" . htmlspecialchars($username) . "' is already taken by another student.";
            } else {
                $message = "Email '" . htmlspecialchars($email) . "' is already registered with another account.";
            }
            $message_type = "error";
            $dupStmt->close();
        } else {
            $dupStmt->close();

            // Step 3: Perform MySQLi Prepared Statement UPDATE
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $updStmt = $conn->prepare("UPDATE students SET name = ?, username = ?, email = ?, role = ?, password = ? WHERE student_id = ?");
                $updStmt->bind_param("sssssi", $name, $username, $email, $role, $hashed_password, $student_id);
            } else {
                $updStmt = $conn->prepare("UPDATE students SET name = ?, username = ?, email = ?, role = ? WHERE student_id = ?");
                $updStmt->bind_param("ssssi", $name, $username, $email, $role, $student_id);
            }

            if ($updStmt->execute()) {
                $message = "Student record #{$student_id} ('{$name}') updated successfully!";
                $message_type = "success";
                // Update local memory
                $student['name']     = $name;
                $student['username'] = $username;
                $student['email']    = $email;
                $student['role']     = $role;
            } else {
                $message = "Database Error: " . $conn->error;
                $message_type = "error";
            }
            $updStmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - StudentHub</title>
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
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: #1f3d56;
        }
        .btn-cancel {
            background-color: #64748b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }
        .btn-cancel:hover {
            background-color: #475569;
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
            <a href="students.php" class="active">STUDENTS</a>
            <a href="add_student.php">ADD STUDENT</a>
            <a href="events.php">EVENTS</a>
            <a href="add_event.php">ADD EVENT</a>
            <a href="register_event.php">REGISTER FOR EVENT</a>
            <a href="registrations.php">REGISTRATIONS</a>
        </div>
    </nav>

    <!-- Page Title -->
    <h1 class="page-title">EDIT STUDENT RECORD</h1>

    <!-- Quick Navigation Tabs -->
    <div class="quick-nav">
        <a href="students.php" class="quick-btn">👥 View Students</a>
        <a href="add_student.php" class="quick-btn">➕ Add Student</a>
        <a href="events.php" class="quick-btn">📅 View Events</a>
        <a href="register_event.php?student_id=<?php echo htmlspecialchars($student_id); ?>" class="quick-btn">📝 Register for Event</a>
        <a href="registrations.php" class="quick-btn">📋 Registrations</a>
    </div>

    <!-- Main Form Container -->
    <main class="form-card">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo ($message_type === 'success' ? '✅ ' : '⚠️ ') . htmlspecialchars($message); ?>
                <?php if ($message_type === 'success'): ?>
                    <div style="margin-top: 8px;">
                        <a href="students.php" style="color: #166534; font-weight: bold; text-decoration: underline;">
                            ← Return to Student Directory
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form action="edit_student.php?id=<?php echo htmlspecialchars($student_id); ?>" method="POST">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">

            <div class="form-group">
                <label for="name">Full Name: *</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Username: *</label>
                <input type="text" id="username" name="username" class="form-control"
                       value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address: *</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role: *</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="student" <?php echo ($role === 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                </select>
            </div>

            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <label style="font-weight: 600; font-size: 14px; color: #334155;">🔒 Change Password (Optional):</label>
                <p style="font-size: 13px; color: #64748b; margin: 4px 0 10px;">Leave blank if you do not want to change the existing password.</p>

                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-control" placeholder="New Password (min 6 characters)">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm New Password">
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-submit" style="flex: 1;">💾 Update Student</button>
                <a href="students.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer style="margin-top: auto;">
        &copy; 2026 STUDENT HUB • CHARUSAT University • All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
