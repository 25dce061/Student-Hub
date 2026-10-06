<?php
// ==============================================================================
// StudentHub - Student Dashboard (pages/dashboard.php)
// Protected dashboard page requiring active authenticated student session
// ==============================================================================

// Enforce session check, anti-caching, and timeout
require_once __DIR__ . "/../config/auth.php";

$studentName = $_SESSION['name'] ?? $_SESSION['username'] ?? 'Student';
$userRole    = $_SESSION['role'] ?? 'student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Student Hub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../js/theme.js"></script>
    <style>
        .user-welcome-badge {
            max-width: 1000px;
            margin: 0 auto 15px;
            padding: 12px 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }
        body.dark .user-welcome-badge {
            background-color: #1e293b;
            color: #f1f5f9;
        }
        .user-welcome-badge strong {
            color: #294d68;
        }
        body.dark .user-welcome-badge strong {
            color: #93c5fd;
        }
        .admin-link-badge {
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
        }
        .admin-link-badge:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body class="dashboard-body">

    <iframe src="header.html" class="header-frame" title="Student Hub Header"></iframe>

    <div class="top-action-bar">
        <a href="../index.html" class="home-nav-btn">🏠 Home</a>
        <button id="theme-toggle" class="theme-btn-compact" type="button" aria-label="Switch to dark theme">🌙 Dark Mode</button>
    </div>

    <!-- Navigation Bar -->
    <nav id="main-nav" aria-label="Main navigation">
        <button id="menu-toggle" class="mobile-menu-btn" type="button" aria-expanded="false" aria-label="Toggle navigation">☰ Menu</button>
        <div id="nav-links">
            <a href="dashboard.php" class="active">DASHBOARD</a>
            <a href="course.html">COURSES</a>
            <a href="event.html">EVENTS</a>
            <a href="faq.html">FAQ</a>
            <a href="contact.html">CONTACT</a>
            <a href="profile.html">PROFILE</a>
            <a href="KYAAPPLICATION.html">KYA FORM</a>
            <a href="setting.html">SETTING</a>
            <?php if ($userRole === 'admin'): ?>
                <a href="admin_dashboard.php" style="background-color: #2563eb;">ADMIN PANEL</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Page Title -->
    <h1 class="page-title">STUDENT DASHBOARD</h1>

    <!-- Logged-in User Information Bar -->
    <div class="user-welcome-badge">
        <div>
            👋 Welcome, <strong><?php echo htmlspecialchars($studentName); ?></strong> 
            (Role: <em><?php echo htmlspecialchars($userRole); ?></em>)
        </div>
        <div>
            <a href="logout.php" style="color: #b91c1c; font-weight: bold; text-decoration: none;">🚪 Logout</a>
        </div>
    </div>

    <!-- Upcoming Campus Notice Bar -->
    <div class="quick-reminder-bar">
        <span>
            📢 <strong>Upcoming:</strong>
            Cognizance 2026 Tech Fest starts September 25. Check Events for registrations!
        </span>
        <a href="event.html">
            <button class="reminder-btn" type="button">View Events</button>
        </a>
    </div>

    <!-- Logout Action Button -->
    <div class="logout">
        <button id="open-logout-modal" type="button">LOGOUT</button>
    </div>

    <!-- Main Academic Cards Grid -->
    <main class="container">
        <a href="attendance.html">
            <button class="box" type="button">
                <span>📊 ATTENDANCE</span>
                <small style="font-size: 13px; font-weight: normal; opacity: 0.85;">View real-time course percentages</small>
            </button>
        </a>

        <a href="result.html">
            <button class="box" type="button">
                <span>🏆 RESULT</span>
                <small style="font-size: 13px; font-weight: normal; opacity: 0.85;">View exam grades &amp; marks</small>
            </button>
        </a>

        <a href="fees.html">
            <button class="box" type="button">
                <span>💳 FEES</span>
                <small style="font-size: 13px; font-weight: normal; opacity: 0.85;">View tuition fee records</small>
            </button>
        </a>

        <a href="timetable.html">
            <button class="box" type="button">
                <span>📅 TIME TABLE</span>
                <small style="font-size: 13px; font-weight: normal; opacity: 0.85;">View weekly lecture schedule</small>
            </button>
        </a>
    </main>

    <!-- Logout Confirmation Modal Dialog -->
    <dialog id="logout-modal" class="event-modal">
        <div class="modal-content">
            <button id="cancel-logout-x" type="button" class="modal-close-btn" aria-label="Close modal">&times;</button>
            <h2 style="margin-top: 0; color: #294d68;">Confirm Logout</h2>
            <p style="color: #475569; line-height: 1.6; margin-bottom: 24px;">
                Are you sure you want to end your active session and log out of the Student Hub portal?
            </p>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button id="cancel-logout" type="button" style="background-color: #64748b;">Cancel</button>
                <a href="logout.php" class="event-btn" style="background-color: #b91c1c; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Yes, Logout</a>
            </div>
        </div>
    </dialog>

    <footer>
        &copy; 2026 STUDENT HUB &bull; CHARUSAT University &bull; All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
