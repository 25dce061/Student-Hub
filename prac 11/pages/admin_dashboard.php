<?php
// ==============================================================================
// StudentHub - Admin Dashboard (pages/admin_dashboard.php)
// Role-protected admin panel strictly reserved for role === 'admin'
// ==============================================================================

require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

// Strict Role Check: Non-admins cannot access admin dashboard
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$adminName = $_SESSION['name'] ?? $_SESSION['username'] ?? 'Administrator';

// Fetch quick summary stats
$totalStudents = 0;
$totalEvents   = 0;
$totalRegs     = 0;

$sRes = $conn->query("SELECT COUNT(*) AS count FROM students");
if ($sRes) $totalStudents = $sRes->fetch_assoc()['count'];

$eRes = $conn->query("SELECT COUNT(*) AS count FROM events");
if ($eRes) $totalEvents = $eRes->fetch_assoc()['count'];

$rRes = $conn->query("SELECT COUNT(*) AS count FROM registrations");
if ($rRes) $totalRegs = $rRes->fetch_assoc()['count'];

// Fetch recent students
$recentStudents = $conn->query("SELECT student_id, name, username, email, role FROM students ORDER BY student_id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Hub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../js/theme.js"></script>
    <style>
        .admin-container {
            max-width: 1050px;
            margin: 20px auto 40px;
            padding: 0 15px;
        }
        .admin-header-badge {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 16px 24px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 24px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid #315b7d;
        }
        .stat-card h3 {
            margin: 0 0 10px;
            font-size: 15px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #1e293b;
        }
        .admin-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .action-btn {
            background-color: #315b7d;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .action-btn:hover {
            background-color: #1f3d56;
        }
        .table-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 24px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .table-card h2 {
            margin-top: 0;
            color: #294d68;
            font-size: 20px;
            margin-bottom: 15px;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .styled-table th, .styled-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .styled-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
        }
        .badge-role {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-admin {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-student {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        /* Dark mode */
        body.dark .admin-header-badge {
            background-color: #0f172a;
        }
        body.dark .stat-card, body.dark .table-card {
            background-color: #1e293b;
            color: #f1f5f9;
        }
        body.dark .stat-card .stat-number {
            color: #93c5fd;
        }
        body.dark .styled-table th {
            background-color: #0f172a;
            color: #93c5fd;
        }
        body.dark .styled-table td {
            border-bottom-color: #334155;
        }
    </style>
</head>
<body>

    <iframe src="header.html" class="header-frame" title="Student Hub Header"></iframe>

    <div class="top-action-bar">
        <a href="../index.html" class="home-nav-btn">🏠 Home</a>
        <button id="theme-toggle" class="theme-btn-compact" type="button" aria-label="Switch to dark theme">🌙 Dark Mode</button>
    </div>

    <!-- Navigation Bar -->
    <nav id="main-nav" aria-label="Main navigation">
        <div id="nav-links">
            <a href="admin_dashboard.php" class="active">ADMIN DASHBOARD</a>
            <a href="dashboard.php">STUDENT VIEW</a>
            <a href="students.php">STUDENTS</a>
            <a href="events.php">EVENTS</a>
            <a href="registrations.php">REGISTRATIONS</a>
            <a href="logout.php" style="background-color: #b91c1c;">LOGOUT</a>
        </div>
    </nav>

    <h1 class="page-title">ADMINISTRATOR CONTROL PANEL</h1>

    <div class="admin-container">

        <!-- Welcome Banner -->
        <div class="admin-header-badge">
            <div>
                <h2 style="margin: 0; font-size: 20px;">🛡️ Welcome, <?php echo htmlspecialchars($adminName); ?></h2>
                <small style="opacity: 0.85;">Full administrative permissions granted.</small>
            </div>
            <div>
                <a href="logout.php" style="background-color: #ef4444; color: #ffffff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">🚪 Logout</a>
            </div>
        </div>

        <!-- Quick Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="stat-number"><?php echo $totalStudents; ?></div>
            </div>
            <div class="stat-card" style="border-top-color: #0284c7;">
                <h3>Campus Events</h3>
                <div class="stat-number"><?php echo $totalEvents; ?></div>
            </div>
            <div class="stat-card" style="border-top-color: #16a34a;">
                <h3>Event Registrations</h3>
                <div class="stat-number"><?php echo $totalRegs; ?></div>
            </div>
        </div>

        <!-- Administrative Shortcuts -->
        <div class="admin-actions">
            <a href="add_student.php" class="action-btn">➕ Add New Student</a>
            <a href="add_event.php" class="action-btn">📅 Add New Event</a>
            <a href="students.php" class="action-btn">👥 View All Students</a>
            <a href="registrations.php" class="action-btn">📝 Event Registrations</a>
            <a href="dashboard.php" class="action-btn" style="background-color: #475569;">👁️ View Student Dashboard</a>
        </div>

        <!-- Recent Registered Students Table -->
        <div class="table-card">
            <h2>Recently Registered Students</h2>
            <div style="overflow-x: auto;">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentStudents && $recentStudents->num_rows > 0): ?>
                            <?php while ($s = $recentStudents->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s['student_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($s['username']); ?></td>
                                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                                    <td>
                                        <span class="badge-role <?php echo ($s['role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                                            <?php echo htmlspecialchars(strtoupper($s['role'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">No student records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer>
        &copy; 2026 STUDENT HUB &bull; CHARUSAT University &bull; All Rights Reserved.
    </footer>

    <script src="../js/script.js" defer></script>
</body>
</html>
