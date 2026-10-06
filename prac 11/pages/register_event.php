<?php
// ==============================================================================
// StudentHub - Register Student for Event
// ==============================================================================

// Include database connection
require_once __DIR__ . "/../config/db.php";

$message = "";
$message_type = ""; // "success" or "error"

// Read any pre-selected IDs from GET parameters (e.g. from Students or Events page)
$selected_student_id = intval($_GET['student_id'] ?? ($_POST['student_id'] ?? 0));
$selected_event_id   = intval($_GET['event_id'] ?? ($_POST['event_id'] ?? 0));
$registration_date   = $_POST['registration_date'] ?? date('Y-m-d');

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id        = intval($_POST['student_id'] ?? 0);
    $event_id          = intval($_POST['event_id'] ?? 0);
    $registration_date = trim($_POST['registration_date'] ?? date('Y-m-d'));

    // Step 1: Basic validation
    if ($student_id <= 0 || $event_id <= 0) {
        $message = "Please select both a student and an event.";
        $message_type = "error";
    } elseif (empty($registration_date)) {
        $message = "Please provide a valid registration date.";
        $message_type = "error";
    } else {
        // Step 2: Check if student has already registered for this event (prevent duplicate)
        $check_stmt = $conn->prepare("SELECT registration_id FROM registrations WHERE student_id = ? AND event_id = ?");
        $check_stmt->bind_param("ii", $student_id, $event_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "This student is already registered for this event!";
            $message_type = "error";
        } else {
            // Step 3: Insert registration using prepared statement
            $stmt = $conn->prepare("INSERT INTO registrations (student_id, event_id, registration_date) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $student_id, $event_id, $registration_date);

            if ($stmt->execute()) {
                $message = "Student successfully registered for the event!";
                $message_type = "success";
            } else {
                // If unique constraint catches duplicate registration
                if ($conn->errno == 1062) {
                    $message = "This student is already registered for this event!";
                } else {
                    $message = "Registration failed: " . $conn->error;
                }
                $message_type = "error";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// Fetch all students for the dropdown
$students_result = $conn->query("SELECT student_id, name, username FROM students ORDER BY name ASC");

// Fetch all events for the dropdown
$events_result = $conn->query("SELECT event_id, event_name, event_date FROM events ORDER BY event_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Event - StudentHub</title>
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
            background-color: #ffffff;
            transition: border-color 0.2s;
            font-family: inherit;
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
            <a href="add_student.php">ADD STUDENT</a>
            <a href="events.php">EVENTS</a>
            <a href="add_event.php">ADD EVENT</a>
            <a href="register_event.php" class="active">REGISTER FOR EVENT</a>
            <a href="registrations.php">REGISTRATIONS</a>
        </div>
    </nav>

    <!-- Page Title -->
    <h1 class="page-title">EVENT REGISTRATION</h1>

    <!-- Quick Navigation Tabs -->
    <div class="quick-nav">
        <a href="students.php" class="quick-btn">👥 View Students</a>
        <a href="events.php" class="quick-btn">📅 View Events</a>
        <a href="register_event.php" class="quick-btn active">📝 Register for Event</a>
        <a href="registrations.php" class="quick-btn">📋 View Registrations</a>
    </div>

    <!-- Main Form Container -->
    <main class="form-card">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo ($message_type === 'success' ? '✅ ' : '⚠️ ') . htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="register_event.php" method="POST">
            <!-- Student Selection Dropdown -->
            <div class="form-group">
                <label for="student_id">Select Student: *</label>
                <select id="student_id" name="student_id" class="form-control" required>
                    <option value="">-- Choose a Student --</option>
                    <?php if ($students_result && $students_result->num_rows > 0): ?>
                        <?php while ($student = $students_result->fetch_assoc()): ?>
                            <option value="<?php echo $student['student_id']; ?>"
                                <?php echo ($selected_student_id == $student['student_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($student['name']) . " (" . htmlspecialchars($student['username']) . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>No students found in database</option>
                    <?php endif; ?>
                </select>
                <?php if (!$students_result || $students_result->num_rows == 0): ?>
                    <small style="color: #b91c1c;">No students found. <a href="add_student.php">Add a student first</a>.</small>
                <?php endif; ?>
            </div>

            <!-- Event Selection Dropdown -->
            <div class="form-group">
                <label for="event_id">Select Event: *</label>
                <select id="event_id" name="event_id" class="form-control" required>
                    <option value="">-- Choose an Event --</option>
                    <?php if ($events_result && $events_result->num_rows > 0): ?>
                        <?php while ($ev = $events_result->fetch_assoc()): ?>
                            <option value="<?php echo $ev['event_id']; ?>"
                                <?php echo ($selected_event_id == $ev['event_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ev['event_name']) . " [" . htmlspecialchars(date("d M Y", strtotime($ev['event_date']))) . "]"; ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>No events found in database</option>
                    <?php endif; ?>
                </select>
                <?php if (!$events_result || $events_result->num_rows == 0): ?>
                    <small style="color: #b91c1c;">No events found. <a href="add_event.php">Add an event first</a>.</small>
                <?php endif; ?>
            </div>

            <!-- Registration Date -->
            <div class="form-group">
                <label for="registration_date">Registration Date: *</label>
                <input type="date" id="registration_date" name="registration_date" class="form-control"
                       value="<?php echo htmlspecialchars($registration_date); ?>" required>
            </div>

            <button type="submit" class="btn-submit">Confirm Registration</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
            Want to see which students have registered?
            <a href="registrations.php" style="color: #315b7d; font-weight: bold; text-decoration: none;">View All Registrations</a>
        </p>
    </main>

    <!-- Footer -->
    <footer style="margin-top: auto;">
        &copy; 2026 STUDENT HUB • CHARUSAT University • All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
