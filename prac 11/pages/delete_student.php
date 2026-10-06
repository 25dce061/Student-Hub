<?php
// ==============================================================================
// StudentHub - Delete Student Record (pages/delete_student.php)
// Uses MySQLi Prepared Statements with Clear Success/Failure Feedback
// ==============================================================================

require_once __DIR__ . "/../config/db.php";

$student_id = intval($_POST['student_id'] ?? $_GET['id'] ?? 0);
$confirm    = $_POST['confirm'] ?? $_GET['confirm'] ?? '';

// Validate ID
if ($student_id <= 0) {
    header("Location: students.php?msg=not_found");
    exit();
}

// Check if student exists using MySQLi prepared statement
$checkStmt = $conn->prepare("SELECT student_id, name, username, email, role FROM students WHERE student_id = ?");
if (!$checkStmt) {
    header("Location: students.php?msg=error&err=" . urlencode("Database prepare error: " . $conn->error));
    exit();
}
$checkStmt->bind_param("i", $student_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$student = $checkResult->fetch_assoc();
$checkStmt->close();

if (!$student) {
    header("Location: students.php?msg=not_found");
    exit();
}

// If form is submitted via POST or confirmed via GET, execute deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" || $confirm === "yes") {
    $studentName = $student['name'];

    // Execute MySQLi Prepared Statement for DELETE
    $delStmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    if (!$delStmt) {
        header("Location: students.php?msg=error&err=" . urlencode("Failed to prepare delete statement: " . $conn->error));
        exit();
    }

    $delStmt->bind_param("i", $student_id);
    if ($delStmt->execute()) {
        $delStmt->close();
        header("Location: students.php?msg=deleted&name=" . urlencode($studentName) . "&id=" . $student_id);
        exit();
    } else {
        $errorMsg = $conn->error;
        $delStmt->close();
        header("Location: students.php?msg=error&err=" . urlencode("Delete operation failed: " . $errorMsg));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete Student - StudentHub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        .confirm-card {
            max-width: 520px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 35px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            text-align: center;
        }
        .warning-icon {
            font-size: 50px;
            margin-bottom: 12px;
            color: #ef4444;
        }
        .confirm-card h2 {
            margin: 0 0 12px;
            color: #991b1b;
            font-size: 22px;
        }
        .student-details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0;
            text-align: left;
            font-size: 14px;
            line-height: 1.8;
        }
        .student-details-box strong {
            color: #1e293b;
        }
        .btn-confirm-delete {
            background-color: #dc2626;
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-confirm-delete:hover {
            background-color: #b91c1c;
        }
        .btn-cancel {
            background-color: #e2e8f0;
            color: #334155;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-cancel:hover {
            background-color: #cbd5e1;
        }
        .btn-group-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        body.dark-mode .confirm-card {
            background: #1e293b;
            color: #f1f5f9;
        }
        body.dark-mode .student-details-box {
            background: #0f172a;
            border-color: #334155;
            color: #cbd5e1;
        }
        body.dark-mode .student-details-box strong {
            color: #f8fafc;
        }
        body.dark-mode .btn-cancel {
            background-color: #334155;
            color: #f8fafc;
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

    <main class="confirm-card">
        <div class="warning-icon">⚠️</div>
        <h2>Confirm Student Deletion</h2>
        <p style="color: #64748b; font-size: 14px;">
            Are you sure you want to permanently delete this student record?
        </p>

        <div class="student-details-box">
            <div><strong>Student ID:</strong> #<?php echo htmlspecialchars($student['student_id']); ?></div>
            <div><strong>Full Name:</strong> <?php echo htmlspecialchars($student['name']); ?></div>
            <div><strong>Username:</strong> <?php echo htmlspecialchars($student['username']); ?></div>
            <div><strong>Email Address:</strong> <?php echo htmlspecialchars($student['email']); ?></div>
            <div><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($student['role'] ?? 'student')); ?></div>
        </div>

        <form action="delete_student.php" method="POST">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
            <input type="hidden" name="confirm" value="yes">

            <div class="btn-group-actions">
                <a href="students.php" class="btn-cancel">← Cancel</a>
                <button type="submit" class="btn-confirm-delete">🗑️ Delete Student</button>
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
