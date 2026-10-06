<?php
// ==============================================================================
// StudentHub - Add Event
// ==============================================================================

// Include database connection
require_once __DIR__ . "/../config/db.php";

$message = "";
$message_type = ""; // "success" or "error"

$event_name  = "";
$event_date  = "";
$location    = "";
$description = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name  = trim($_POST['event_name'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Step 1: Basic validation
    if (empty($event_name) || empty($event_date) || empty($location)) {
        $message = "Please fill in Event Name, Date, and Location.";
        $message_type = "error";
    } else {
        // Step 2: Insert event into database using prepared statement
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, description, location) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $event_name, $event_date, $description, $location);

        if ($stmt->execute()) {
            $message = "Event created successfully!";
            $message_type = "success";
            // Clear inputs after successful insert
            $event_name  = "";
            $event_date  = "";
            $location    = "";
            $description = "";
        } else {
            $message = "Error creating event: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - StudentHub</title>
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
            font-family: inherit;
        }
        .form-control:focus {
            outline: none;
            border-color: #315b7d;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 90px;
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
            <a href="add_event.php" class="active">ADD EVENT</a>
            <a href="register_event.php">REGISTER FOR EVENT</a>
            <a href="registrations.php">REGISTRATIONS</a>
        </div>
    </nav>

    <!-- Page Title -->
    <h1 class="page-title">ADD NEW COLLEGE EVENT</h1>

    <!-- Quick Navigation Tabs -->
    <div class="quick-nav">
        <a href="students.php" class="quick-btn">👥 View Students</a>
        <a href="events.php" class="quick-btn">📅 View Events</a>
        <a href="add_event.php" class="quick-btn active">➕ Add Event</a>
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

        <form action="add_event.php" method="POST">
            <div class="form-group">
                <label for="event_name">Event Name: *</label>
                <input type="text" id="event_name" name="event_name" class="form-control"
                       placeholder="e.g. Annual Tech Fest 2026" value="<?php echo htmlspecialchars($event_name); ?>" required>
            </div>

            <div class="form-group">
                <label for="event_date">Event Date: *</label>
                <input type="date" id="event_date" name="event_date" class="form-control"
                       value="<?php echo htmlspecialchars($event_date); ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location / Venue: *</label>
                <input type="text" id="location" name="location" class="form-control"
                       placeholder="e.g. Auditorium Hall A" value="<?php echo htmlspecialchars($location); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Event Description:</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          placeholder="Brief description about the event..."><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <button type="submit" class="btn-submit">Create Event</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
            Want to see all scheduled events?
            <a href="events.php" style="color: #315b7d; font-weight: bold; text-decoration: none;">View Events List</a>
        </p>
    </main>

    <!-- Footer -->
    <footer style="margin-top: auto;">
        &copy; 2026 STUDENT HUB • CHARUSAT University • All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
