<?php
// ==============================================================================
// StudentHub - Student Management Module (pages/students.php)
// Features: View, Search, Filter, Add, Edit, Delete, Event Registration
// All Database Operations using MySQLi Statements & Prepared Queries
// Clear Success / Failure Feedback Messages
// ==============================================================================

// Include database connection
require_once __DIR__ . "/../config/db.php";

// 1. Capture search and filter parameters
$search      = trim($_GET['search'] ?? '');
$filter_role = trim($_GET['role'] ?? 'all');
$sort        = trim($_GET['sort'] ?? 'id_desc');

// 2. Capture flash / feedback notification messages
$feedbackMsg  = "";
$feedbackType = ""; // "success" or "error"

if (isset($_GET['msg'])) {
    $msgKey = $_GET['msg'];
    $mName  = htmlspecialchars($_GET['name'] ?? 'Student');
    $mId    = intval($_GET['id'] ?? 0);
    $mErr   = htmlspecialchars($_GET['err'] ?? 'An error occurred.');

    if ($msgKey === 'added') {
        $feedbackMsg  = "Student record added successfully!";
        $feedbackType = "success";
    } elseif ($msgKey === 'updated') {
        $feedbackMsg  = "Student record for <strong>{$mName}</strong> updated successfully!";
        $feedbackType = "success";
    } elseif ($msgKey === 'deleted') {
        $feedbackMsg  = "Student record #{$mId} (<strong>{$mName}</strong>) deleted successfully.";
        $feedbackType = "success";
    } elseif ($msgKey === 'not_found') {
        $feedbackMsg  = "The requested student record could not be found.";
        $feedbackType = "error";
    } elseif ($msgKey === 'error') {
        $feedbackMsg  = "Database operation failed: {$mErr}";
        $feedbackType = "error";
    }
}

// 3. Count total students in database
$countRes = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = ($countRes) ? intval($countRes->fetch_assoc()['total']) : 0;

// 4. Fetch students using MySQLi Prepared Statement with Search & Filter
$conditions = [];
$params     = [];
$types      = "";

// Search filter (Name, Username, Email, ID)
if ($search !== '') {
    $conditions[] = "(name LIKE ? OR username LIKE ? OR email LIKE ? OR student_id = ?)";
    $like = "%" . $search . "%";
    $searchId = is_numeric($search) ? intval($search) : -1;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $searchId;
    $types .= "sssi";
}

// Role filter
if ($filter_role !== '' && $filter_role !== 'all') {
    $conditions[] = "role = ?";
    $params[] = $filter_role;
    $types .= "s";
}

// Sorting
$orderClause = "student_id DESC";
if ($sort === 'id_asc') {
    $orderClause = "student_id ASC";
} elseif ($sort === 'name_asc') {
    $orderClause = "name ASC";
} elseif ($sort === 'name_desc') {
    $orderClause = "name DESC";
}

$sql = "SELECT student_id, name, username, email, role FROM students";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY " . $orderClause;

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT student_id, name, username, email, role FROM students ORDER BY student_id DESC");
}

$hasFilter = ($search !== '' || ($filter_role !== '' && $filter_role !== 'all') || $sort !== 'id_desc');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students - StudentHub</title>
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
            background-color: #2563eb;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-sm:hover {
            opacity: 0.9;
        }
        .search-filter-bar {
            width: 95%;
            max-width: 1150px;
            margin: 10px auto 15px;
            background: #ffffff;
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 2px 8px rgba(40, 60, 80, 0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .search-filter-bar input[type="text"] {
            flex: 1 1 220px;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        .search-filter-bar select {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            background: #ffffff;
            color: #1e293b;
            outline: none;
        }
        .btn-search {
            background-color: #315b7d;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-search:hover {
            background-color: #1f3d56;
        }
        .btn-reset {
            background-color: #e2e8f0;
            color: #475569;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        .btn-reset:hover {
            background-color: #cbd5e1;
        }
        .alert-banner {
            width: 95%;
            max-width: 1150px;
            margin: 15px auto 5px;
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .badge-role {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-role-admin {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-role-student {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        body.dark-mode .search-filter-bar {
            background: #1e293b;
        }
        body.dark-mode .search-filter-bar input,
        body.dark-mode .search-filter-bar select {
            background: #0f172a;
            color: #f1f5f9;
            border-color: #475569;
        }
        body.dark-mode .btn-reset {
            background: #334155;
            color: #f1f5f9;
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
    <h1 class="page-title">ALL REGISTERED STUDENTS</h1>

    <!-- Quick Navigation Tabs -->
    <div class="quick-nav">
        <a href="students.php" class="quick-btn active">👥 View Students</a>
        <a href="add_student.php" class="quick-btn">➕ Add Student</a>
        <a href="events.php" class="quick-btn">📅 View Events</a>
        <a href="register_event.php" class="quick-btn">📝 Register for Event</a>
        <a href="registrations.php" class="quick-btn">📋 Registrations</a>
    </div>

    <!-- Clear Success / Failure Message Banner -->
    <?php if (!empty($feedbackMsg)): ?>
        <div class="alert-banner alert-<?php echo $feedbackType; ?>" id="feedbackBanner">
            <div>
                <?php echo ($feedbackType === 'success' ? '✅ ' : '⚠️ '); ?>
                <?php echo $feedbackMsg; ?>
            </div>
            <button type="button" onclick="document.getElementById('feedbackBanner').style.display='none';" style="background: none; border: none; font-size: 16px; cursor: pointer; color: inherit;">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Search & Filter Controls Bar -->
    <form action="students.php" method="GET" class="search-filter-bar">
        <input type="text"
               name="search"
               placeholder="🔍 Search student by Name, Username, Email, or ID..."
               value="<?php echo htmlspecialchars($search); ?>">

        <select name="role" aria-label="Filter by Role">
            <option value="all">All Roles</option>
            <option value="student" <?php echo ($filter_role === 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="admin" <?php echo ($filter_role === 'admin') ? 'selected' : ''; ?>>Administrator</option>
        </select>

        <select name="sort" aria-label="Sort by">
            <option value="id_desc" <?php echo ($sort === 'id_desc') ? 'selected' : ''; ?>>ID: Newest First</option>
            <option value="id_asc" <?php echo ($sort === 'id_asc') ? 'selected' : ''; ?>>ID: Oldest First</option>
            <option value="name_asc" <?php echo ($sort === 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
            <option value="name_desc" <?php echo ($sort === 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
        </select>

        <button type="submit" class="btn-search">Filter</button>

        <?php if ($hasFilter): ?>
            <a href="students.php" class="btn-reset">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Toolbar: Total Count + Add Student Button -->
    <div class="top-toolbar">
        <span class="badge-count">
            Total Students: <strong><?php echo $totalStudents; ?></strong>
            <?php if ($hasFilter && $result): ?>
                (Showing: <strong><?php echo $result->num_rows; ?></strong>)
            <?php endif; ?>
        </span>
        <a href="add_student.php" class="btn-action">
            ➕ Add New Student
        </a>
    </div>

    <!-- Students Table -->
    <main class="table-responsive">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $stId   = htmlspecialchars($row['student_id']);
                            $stName = htmlspecialchars($row['name']);
                            $stUser = htmlspecialchars($row['username']);
                            $stMail = htmlspecialchars($row['email']);
                            $stRole = htmlspecialchars($row['role'] ?? 'student');
                        ?>
                        <tr>
                            <td>#<?php echo $stId; ?></td>
                            <td><strong><?php echo $stName; ?></strong></td>
                            <td><?php echo $stUser; ?></td>
                            <td><?php echo $stMail; ?></td>
                            <td>
                                <span class="badge-role <?php echo ($stRole === 'admin') ? 'badge-role-admin' : 'badge-role-student'; ?>">
                                    <?php echo strtoupper($stRole); ?>
                                </span>
                            </td>
                            <td style="white-space: nowrap;">
                                <!-- Edit Student -->
                                <a href="edit_student.php?id=<?php echo $stId; ?>"
                                   class="btn-sm"
                                   style="background-color: #d97706; margin-right: 4px;"
                                   title="Edit Student">
                                    Edit
                                </a>

                                <!-- Delete Student -->
                                <a href="delete_student.php?id=<?php echo $stId; ?>"
                                   class="btn-sm"
                                   style="background-color: #dc2626; margin-right: 4px;"
                                   onclick="return confirm('Are you sure you want to delete student #<?php echo $stId; ?> (<?php echo addslashes($stName); ?>)?');"
                                   title="Delete Student">
                                    Delete
                                </a>

                                <!-- Previous Existing Function Intact: Register for Event -->
                                <a href="register_event.php?student_id=<?php echo $stId; ?>"
                                   class="btn-sm"
                                   title="Register for Event">
                                    Register for Event
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No students found matching your criteria.</p>
                <div style="margin-top: 10px;">
                    <?php if ($hasFilter): ?>
                        <a href="students.php" class="btn-action" style="margin-right: 8px;">View All Students</a>
                    <?php endif; ?>
                    <a href="add_student.php" class="btn-action">➕ Add First Student</a>
                </div>
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
