<?php
// ==============================================================================
// StudentHub - Display All Events
// ==============================================================================

// Include database connection
require_once __DIR__ . "/../config/db.php";

// Fetch all events from the database
$sql = "SELECT event_id, event_name, event_date, description, location FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - StudentHub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        .top-toolbar {
            width: 95%;
            max-width: 1150px;
            margin: 20px auto 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-action {
            background-color: #315b7d;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }
        .btn-action:hover {
            background-color: #1f3d56;
        }
        .badge-count {
            background-color: #e2e8f0;
            color: #334155;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .quick-nav {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 15px auto 20px;
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
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
            font-size: 16px;
        }
        .btn-sm {
            display: inline-block;
            background-color: #16a34a;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-sm:hover {
            background-color: #15803d;
        }
        .date-badge {
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
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
            <a href="events.php" class="active">EVENTS</a>
            <a href="add_event.php">ADD EVENT</a>
            <a href="register_event.php">REGISTER FOR EVENT</a>
            <a href="registrations.php">REGISTRATIONS</a>
        </div>
    </nav>

    <!-- Page Title -->
    <h1 class="page-title">ALL COLLEGE EVENTS</h1>

    <!-- Quick Navigation Tabs -->
    <div class="quick-nav">
        <a href="students.php" class="quick-btn">👥 View Students</a>
        <a href="events.php" class="quick-btn active">📅 View Events</a>
        <a href="add_event.php" class="quick-btn">➕ Add Event</a>
        <a href="register_event.php" class="quick-btn">📝 Register for Event</a>
        <a href="registrations.php" class="quick-btn">📋 Registrations</a>
    </div>

    <!-- Toolbar: Total Count + Add Event Button -->
    <div class="top-toolbar">
        <span class="badge-count">
            Total Events: <strong><?php echo ($result) ? $result->num_rows : 0; ?></strong>
        </span>
        <div style="display: flex; gap: 10px;">
            <a href="add_event.php" class="btn-action">
                ➕ Add New Event
            </a>
            <a href="register_event.php" class="btn-action" style="background-color: #2563eb;">
                📝 Register Student
            </a>
        </div>
    </div>

    <!-- Events Table -->
    <main class="table-responsive">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($row['event_id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['event_name']); ?></strong></td>
                            <td>
                                <span class="date-badge">
                                    <?php echo htmlspecialchars(date("d M Y", strtotime($row['event_date']))); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td style="max-width: 300px; text-align: left; font-size: 14px;">
                                <?php echo htmlspecialchars($row['description'] ?: 'No description provided.'); ?>
                            </td>
                            <td>
                                <a href="register_event.php?event_id=<?php echo htmlspecialchars($row['event_id']); ?>" class="btn-sm">
                                    Register Student
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No college events scheduled at the moment.</p>
                <a href="add_event.php" class="btn-action" style="margin-top: 10px;">➕ Add First Event</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer style="margin-top: auto;">
        &copy; 2026 STUDENT HUB • CHARUSAT University • All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
