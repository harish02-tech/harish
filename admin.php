<?php
/**
 * LearnSphere – Academic Admin Portal (admin.php)
 * Complete management system for Enrollments, Courses, Students, and Inquiries.
 */

session_start();
require_once __DIR__ . '/includes/db.php';

// Flash Message Helpers
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ---------------------------------------------------------------------
// 1. Authentication Handlers (Login / Logout)
// ---------------------------------------------------------------------
$adminUser = 'admin';
$adminPass = '6380716612'; // Updated admin portal credential

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
    setFlash('success', 'You have been successfully signed out.');
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = 'Academic Dean';
        setFlash('success', 'Welcome back, Dean! Admin portal authenticated.');
        header('Location: admin.php');
        exit;
    } else {
        $loginError = 'Invalid credentials. Default: Username: <strong>admin</strong> | Password: <strong>6380716612</strong>';
    }
}

$isLoggedIn = !empty($_SESSION['admin_logged_in']);

// ---------------------------------------------------------------------
// 2. Process POST Administrative Actions (Only if Logged In)
// ---------------------------------------------------------------------
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];

    // Action: Update Enrollment Status
    if ($action === 'update_enrollment_status') {
        $enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $allowedStatuses = ['Confirmed', 'Pending Verification', 'Active'];

        if ($enrollmentId && in_array($newStatus, $allowedStatuses) && $pdo) {
            $stmt = $pdo->prepare("UPDATE `enrollments` SET `status` = :status WHERE `enrollment_id` = :id");
            $stmt->execute([':status' => $newStatus, ':id' => $enrollmentId]);
            setFlash('success', "Enrollment #{$enrollmentId} status updated to <strong>{$newStatus}</strong>.");
        }
        header('Location: admin.php#enrollments');
        exit;
    }

    // Action: Delete Enrollment
    if ($action === 'delete_enrollment') {
        $enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
        if ($enrollmentId && $pdo) {
            $stmt = $pdo->prepare("DELETE FROM `enrollments` WHERE `enrollment_id` = :id");
            $stmt->execute([':id' => $enrollmentId]);
            setFlash('success', "Enrollment #{$enrollmentId} has been successfully removed.");
        }
        header('Location: admin.php#enrollments');
        exit;
    }

    // Action: Add New Course
    if ($action === 'add_course') {
        $categoryId       = (int)($_POST['category_id'] ?? 1);
        $title            = trim($_POST['title'] ?? '');
        $shortDesc        = trim($_POST['short_description'] ?? '');
        $fullDesc         = trim($_POST['full_description'] ?? '');
        $syllabus         = trim($_POST['syllabus'] ?? '');
        $outcomes         = trim($_POST['learning_outcomes'] ?? '');
        $instructorName   = trim($_POST['instructor_name'] ?? '');
        $instructorTitle  = trim($_POST['instructor_title'] ?? 'Lead Instructor');
        $instructorBio    = trim($_POST['instructor_bio'] ?? 'Industry expert and senior educator.');
        $durationWeeks    = (int)($_POST['duration_weeks'] ?? 8);
        $difficulty       = trim($_POST['difficulty'] ?? 'All Levels');
        $price            = (float)($_POST['price'] ?? 89.99);
        $origPrice        = (float)($_POST['original_price'] ?? ($price * 1.4));
        $image            = trim($_POST['image'] ?? 'assets/images/course_web_dev.svg');
        $featured         = isset($_POST['featured']) ? 1 : 0;

        if (!empty($title) && $pdo) {
            // Generate clean URL slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
            $slugCheck = $pdo->prepare("SELECT COUNT(*) FROM `courses` WHERE `slug` = :slug");
            $slugCheck->execute([':slug' => $slug]);
            if ($slugCheck->fetchColumn() > 0) {
                $slug .= '-' . time();
            }

            $sql = "INSERT INTO `courses` (
                        `category_id`, `title`, `slug`, `short_description`, `full_description`, 
                        `syllabus`, `learning_outcomes`, `instructor_name`, `instructor_title`, `instructor_bio`, 
                        `duration_weeks`, `difficulty`, `price`, `original_price`, `rating`, `reviews_count`, `image`, `featured`
                    ) VALUES (
                        :cat_id, :title, :slug, :short_desc, :full_desc, 
                        :syllabus, :outcomes, :inst_name, :inst_title, :inst_bio, 
                        :duration, :difficulty, :price, :orig_price, 4.9, 25, :image, :featured
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cat_id'      => $categoryId,
                ':title'       => $title,
                ':slug'        => $slug,
                ':short_desc'  => $shortDesc ?: $title,
                ':full_desc'   => $fullDesc ?: $shortDesc,
                ':syllabus'    => $syllabus ?: 'Module 1: Introduction||Module 2: Advanced Topics||Module 3: Capstone Project',
                ':outcomes'    => $outcomes ?: 'Gain enterprise skills||Build production-grade projects',
                ':inst_name'   => $instructorName ?: 'LearnSphere Faculty',
                ':inst_title'  => $instructorTitle,
                ':inst_bio'    => $instructorBio,
                ':duration'    => $durationWeeks,
                ':difficulty'  => $difficulty,
                ':price'       => $price,
                ':orig_price'  => $origPrice,
                ':image'       => $image,
                ':featured'    => $featured
            ]);

            setFlash('success', "Course <strong>" . htmlspecialchars($title) . "</strong> created successfully!");
        } else {
            setFlash('error', "Course title cannot be empty.");
        }
        header('Location: admin.php#courses');
        exit;
    }

    // Action: Toggle Featured Course
    if ($action === 'toggle_featured') {
        $courseId = (int)($_POST['course_id'] ?? 0);
        if ($courseId && $pdo) {
            $stmt = $pdo->prepare("UPDATE `courses` SET `featured` = 1 - `featured` WHERE `course_id` = :id");
            $stmt->execute([':id' => $courseId]);
            setFlash('success', "Course #{$courseId} featured status toggled.");
        }
        header('Location: admin.php#courses');
        exit;
    }

    // Action: Delete Course
    if ($action === 'delete_course') {
        $courseId = (int)($_POST['course_id'] ?? 0);
        if ($courseId && $pdo) {
            $stmt = $pdo->prepare("DELETE FROM `courses` WHERE `course_id` = :id");
            $stmt->execute([':id' => $courseId]);
            setFlash('success', "Course #{$courseId} deleted from database.");
        }
        header('Location: admin.php#courses');
        exit;
    }

    // Action: Delete Student
    if ($action === 'delete_student') {
        $studentId = (int)($_POST['student_id'] ?? 0);
        if ($studentId && $pdo) {
            $stmt = $pdo->prepare("DELETE FROM `students` WHERE `student_id` = :id");
            $stmt->execute([':id' => $studentId]);
            setFlash('success', "Student #{$studentId} and associated records removed.");
        }
        header('Location: admin.php#students');
        exit;
    }

    // Action: Update Message Status
    if ($action === 'update_message_status') {
        $messageId = (int)($_POST['message_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $allowed = ['New', 'Read', 'Replied'];
        if ($messageId && in_array($newStatus, $allowed) && $pdo) {
            $stmt = $pdo->prepare("UPDATE `contact_messages` SET `status` = :status WHERE `message_id` = :id");
            $stmt->execute([':status' => $newStatus, ':id' => $messageId]);
            setFlash('success', "Inquiry #{$messageId} marked as {$newStatus}.");
        }
        header('Location: admin.php#messages');
        exit;
    }

    // Action: Delete Message
    if ($action === 'delete_message') {
        $messageId = (int)($_POST['message_id'] ?? 0);
        if ($messageId && $pdo) {
            $stmt = $pdo->prepare("DELETE FROM `contact_messages` WHERE `message_id` = :id");
            $stmt->execute([':id' => $messageId]);
            setFlash('success', "Message #{$messageId} permanently deleted.");
        }
        header('Location: admin.php#messages');
        exit;
    }
}

// ---------------------------------------------------------------------
// 3. Fetch Data for Dashboard (If Logged In)
// ---------------------------------------------------------------------
$stats = [
    'students' => 0,
    'courses' => 0,
    'enrollments' => 0,
    'messages_new' => 0,
    'revenue' => 0.00
];

$enrollments = [];
$courses = [];
$students = [];
$categories = [];
$messages = [];

if ($isLoggedIn && $pdo) {
    try {
        // Statistics
        $stats['students'] = (int)$pdo->query("SELECT COUNT(*) FROM `students`")->fetchColumn();
        $stats['courses'] = (int)$pdo->query("SELECT COUNT(*) FROM `courses`")->fetchColumn();
        $stats['enrollments'] = (int)$pdo->query("SELECT COUNT(*) FROM `enrollments`")->fetchColumn();
        $stats['messages_new'] = (int)$pdo->query("SELECT COUNT(*) FROM `contact_messages` WHERE `status` = 'New'")->fetchColumn();
        $stats['revenue'] = (float)$pdo->query("SELECT COALESCE(SUM(c.price), 0) FROM `enrollments` e JOIN `courses` c ON e.course_id = c.course_id")->fetchColumn();

        // Categories
        $categories = $pdo->query("SELECT * FROM `categories` ORDER BY `name` ASC")->fetchAll();

        // Enrollments with student and course details
        $enrollQuery = "SELECT e.*, 
                               s.first_name, s.last_name, s.email AS student_email, s.phone AS student_phone,
                               c.title AS course_title, c.price AS course_price, c.image AS course_image
                        FROM `enrollments` e
                        JOIN `students` s ON e.student_id = s.student_id
                        JOIN `courses` c ON e.course_id = c.course_id
                        ORDER BY e.enrolled_at DESC";
        $enrollments = $pdo->query($enrollQuery)->fetchAll();

        // Courses with category name & enrolled count
        $courseQuery = "SELECT c.*, cat.name AS category_name,
                               (SELECT COUNT(*) FROM `enrollments` WHERE course_id = c.course_id) AS enrolled_students
                        FROM `courses` c
                        JOIN `categories` cat ON c.category_id = cat.category_id
                        ORDER BY c.created_at DESC";
        $courses = $pdo->query($courseQuery)->fetchAll();

        // Students with enrollment counts
        $studentQuery = "SELECT s.*,
                                (SELECT COUNT(*) FROM `enrollments` WHERE student_id = s.student_id) AS enrollment_count
                         FROM `students` s
                         ORDER BY s.created_at DESC";
        $students = $pdo->query($studentQuery)->fetchAll();

        // Contact inquiries
        $messages = $pdo->query("SELECT * FROM `contact_messages` ORDER BY `created_at` DESC")->fetchAll();

    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}

$flash = getFlash();
$pageTitle = 'Academic Admin Portal – LearnSphere';
$currentPage = 'admin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Base & Admin Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">

<?php if (!$isLoggedIn): ?>
    <!-- ========================================================= -->
    <!-- ADMIN LOGIN VIEW                                         -->
    <!-- ========================================================= -->
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <a href="index.php">
                    <img src="assets/images/logo.svg" alt="LearnSphere Logo">
                </a>
                <div class="admin-badge-role">
                    <i class="fas fa-shield-alt"></i> Executive Portal Access
                </div>
                <h1>Academic Portal Admin</h1>
                <p>Sign in to manage courses, enrollments, and academic operations.</p>
            </div>

            <?php if (!empty($loginError)): ?>
                <div class="admin-alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?php echo $loginError; ?></div>
                </div>
            <?php endif; ?>

            <?php if ($flash): ?>
                <div class="admin-alert <?php echo $flash['type']; ?>">
                    <i class="fas fa-info-circle"></i>
                    <div><?php echo $flash['message']; ?></div>
                </div>
            <?php endif; ?>

            <form action="admin.php" method="POST" id="adminLoginForm">
                <input type="hidden" name="form_action" value="login">

                <div class="admin-input-group">
                    <label for="adminUsername">Admin Username</label>
                    <div class="admin-input-box">
                        <i class="fas fa-user-shield"></i>
                        <input type="text" id="adminUsername" name="username" placeholder="Username" required autofocus value="admin">
                    </div>
                </div>

                <div class="admin-input-group">
                    <label for="adminPassword">Security Key / Password</label>
                    <div class="admin-input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="adminPassword" name="password" placeholder="Password" required value="6380716612">
                    </div>
                </div>

                <button type="submit" class="btn-admin-submit">
                    <i class="fas fa-sign-in-alt"></i> Authenticate & Enter Dashboard
                </button>

                <button type="button" class="btn-admin-quick" id="btnQuickLogin">
                    <i class="fas fa-bolt" style="color: #F59E0B;"></i> 1-Click Instant Demo Login (admin / 6380716612)
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.75rem;">
                <a href="index.php" style="color: #64748B; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-arrow-left"></i> Return to Main Academic Site
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnQuickLogin').addEventListener('click', function() {
            document.getElementById('adminUsername').value = 'admin';
            document.getElementById('adminPassword').value = '6380716612';
            document.getElementById('adminLoginForm').submit();
        });
    </script>

<?php else: ?>
    <!-- ========================================================= -->
    <!-- ADMIN DASHBOARD VIEW                                      -->
    <!-- ========================================================= -->

    <!-- Top Admin Bar -->
    <header class="admin-navbar">
        <div class="admin-nav-container">
            <div class="admin-nav-left">
                <a href="index.php" title="Return to Portal">
                    <img src="assets/images/logo.svg" alt="LearnSphere Admin">
                </a>
                <span class="admin-portal-tag"><i class="fas fa-lock"></i> Admin Console</span>
            </div>

            <div class="admin-nav-right">
                <a href="index.php" class="btn-view-site" target="_blank" title="Preview public website in new tab">
                    <i class="fas fa-external-link-alt"></i> View Public Site
                </a>

                <div class="admin-user-pill">
                    <div class="admin-avatar">AD</div>
                    <div class="admin-user-info">
                        <div class="admin-user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                        <div class="admin-user-role">Super Administrator</div>
                    </div>
                </div>

                <a href="admin.php?action=logout" class="btn-logout" title="Sign out of Admin Portal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="admin-main-container">
        <!-- Dashboard Header -->
        <div class="admin-header-title">
            <div>
                <h2><i class="fas fa-tachometer-alt" style="color: #00ADB5;"></i> Academic Management Dashboard</h2>
                <p>Real-time telemetry, student roster tracking, and catalog controls.</p>
            </div>
            <div>
                <button type="button" class="btn-primary-action" onclick="openAddCourseModal()">
                    <i class="fas fa-plus-circle"></i> + Add New Course
                </button>
            </div>
        </div>

        <!-- Flash Notice Messages -->
        <?php if ($flash): ?>
            <div class="admin-alert <?php echo $flash['type']; ?>">
                <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="font-size: 1.2rem;"></i>
                <div><?php echo $flash['message']; ?></div>
            </div>
        <?php endif; ?>

        <!-- KPI Statistic Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-info">
                    <span>Enrolled Students</span>
                    <h3><?php echo number_format($stats['students']); ?></h3>
                </div>
                <div class="kpi-icon teal">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-info">
                    <span>Active Courses</span>
                    <h3><?php echo number_format($stats['courses']); ?></h3>
                </div>
                <div class="kpi-icon blue">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-info">
                    <span>Total Registrations</span>
                    <h3><?php echo number_format($stats['enrollments']); ?></h3>
                </div>
                <div class="kpi-icon emerald">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-info">
                    <span>Tuition Volume</span>
                    <h3>$<?php echo number_format($stats['revenue'], 2); ?></h3>
                </div>
                <div class="kpi-icon purple">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-info">
                    <span>New Inquiries</span>
                    <h3><?php echo number_format($stats['messages_new']); ?></h3>
                </div>
                <div class="kpi-icon amber">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
            </div>
        </div>

        <!-- Main Tabbed Panel -->
        <div class="admin-tabs-card">
            <nav class="admin-tabs-nav" aria-label="Admin Navigation Tabs">
                <button class="admin-tab-btn active" data-tab="tab-enrollments">
                    <i class="fas fa-clipboard-list"></i> Course Enrollments
                    <span class="tab-badge"><?php echo count($enrollments); ?></span>
                </button>
                <button class="admin-tab-btn" data-tab="tab-courses">
                    <i class="fas fa-graduation-cap"></i> Course Catalog
                    <span class="tab-badge"><?php echo count($courses); ?></span>
                </button>
                <button class="admin-tab-btn" data-tab="tab-students">
                    <i class="fas fa-users"></i> Registered Students
                    <span class="tab-badge"><?php echo count($students); ?></span>
                </button>
                <button class="admin-tab-btn" data-tab="tab-messages">
                    <i class="fas fa-inbox"></i> Inquiries & Contact
                    <span class="tab-badge"><?php echo count($messages); ?></span>
                </button>
            </nav>

            <!-- ===================================================== -->
            <!-- TAB 1: ENROLLMENTS                                    -->
            <!-- ===================================================== -->
            <section class="tab-content active" id="tab-enrollments">
                <div class="table-toolbar">
                    <div class="toolbar-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="enrollmentSearch" placeholder="Filter by Student, Code, or Course..." onkeyup="filterTable('enrollmentTable', this.value)">
                    </div>
                    <div class="toolbar-actions">
                        <select id="statusFilter" onchange="filterEnrollmentStatus(this.value)" style="padding: 0.55rem 0.85rem; border: 1.5px solid #E2E8F0; border-radius: 8px; font-size: 0.85rem;">
                            <option value="ALL">All Statuses</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Pending Verification">Pending Verification</option>
                            <option value="Active">Active</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" id="enrollmentTable">
                        <thead>
                            <tr>
                                <th>Tracking Code</th>
                                <th>Student</th>
                                <th>Course Title</th>
                                <th>Study Mode</th>
                                <th>Tuition</th>
                                <th>Status</th>
                                <th>Enrolled Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($enrollments)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                        <i class="fas fa-folder-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                        No course enrollments found yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($enrollments as $e): ?>
                                    <tr data-status="<?php echo htmlspecialchars($e['status']); ?>">
                                        <td>
                                            <strong style="color: #00ADB5; font-family: monospace; font-size: 0.95rem;">
                                                <?php echo htmlspecialchars($e['enrollment_code']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0B192C;">
                                                <?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?>
                                            </div>
                                            <div style="font-size: 0.78rem; color: #64748B;">
                                                <i class="fas fa-envelope" style="font-size: 0.7rem;"></i> <?php echo htmlspecialchars($e['student_email']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-weight: 500;"><?php echo htmlspecialchars($e['course_title']); ?></span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.82rem; background: #F1F5F9; padding: 2px 8px; border-radius: 4px;">
                                                <?php echo htmlspecialchars($e['study_mode']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>$<?php echo number_format($e['course_price'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'badge-confirmed';
                                            if ($e['status'] === 'Pending Verification') $badgeClass = 'badge-pending';
                                            if ($e['status'] === 'Active') $badgeClass = 'badge-active';
                                            ?>
                                            <span class="badge-status <?php echo $badgeClass; ?>">
                                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                <?php echo htmlspecialchars($e['status']); ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.82rem; color: #64748B;">
                                            <?php echo date('M d, Y', strtotime($e['enrolled_at'])); ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="action-btn-group" style="justify-content: flex-end;">
                                                <!-- Quick Status Dropdown Form -->
                                                <form action="admin.php" method="POST" style="display: inline-block;">
                                                    <input type="hidden" name="admin_action" value="update_enrollment_status">
                                                    <input type="hidden" name="enrollment_id" value="<?php echo $e['enrollment_id']; ?>">
                                                    <select name="status" onchange="this.form.submit()" title="Change enrollment status" style="padding: 3px 6px; font-size: 0.78rem; border-radius: 6px; border: 1px solid #CBD5E1; background: #FFF;">
                                                        <option value="Confirmed" <?php if ($e['status'] === 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                                                        <option value="Active" <?php if ($e['status'] === 'Active') echo 'selected'; ?>>Active</option>
                                                        <option value="Pending Verification" <?php if ($e['status'] === 'Pending Verification') echo 'selected'; ?>>Pending</option>
                                                    </select>
                                                </form>

                                                <!-- Delete Enrollment -->
                                                <form action="admin.php" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to cancel and remove this enrollment?');">
                                                    <input type="hidden" name="admin_action" value="delete_enrollment">
                                                    <input type="hidden" name="enrollment_id" value="<?php echo $e['enrollment_id']; ?>">
                                                    <button type="submit" class="btn-icon-action danger" title="Delete Enrollment">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ===================================================== -->
            <!-- TAB 2: COURSES                                        -->
            <!-- ===================================================== -->
            <section class="tab-content" id="tab-courses">
                <div class="table-toolbar">
                    <div class="toolbar-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search courses by title, instructor..." onkeyup="filterTable('coursesTable', this.value)">
                    </div>
                    <div class="toolbar-actions">
                        <button type="button" class="btn-primary-action" onclick="openAddCourseModal()">
                            <i class="fas fa-plus"></i> Add New Course
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" id="coursesTable">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Category</th>
                                <th>Instructor</th>
                                <th>Duration / Level</th>
                                <th>Tuition</th>
                                <th>Enrollments</th>
                                <th>Featured</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($courses)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                        No courses available.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <?php 
                                                $imgSrc = (strpos($c['image'], 'assets/') === 0 || strpos($c['image'], 'http') === 0) 
                                                    ? $c['image'] 
                                                    : 'assets/images/' . $c['image']; 
                                                ?>
                                                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Course Thumbnail" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; background: #F1F5F9; border: 1px solid #E2E8F0;">
                                                <div>
                                                    <div style="font-weight: 600; color: #0B192C;">
                                                        <?php echo htmlspecialchars($c['title']); ?>
                                                    </div>
                                                    <div style="font-size: 0.78rem; color: #64748B;">
                                                        <i class="fas fa-star" style="color: #F59E0B;"></i> <?php echo $c['rating']; ?> (<?php echo $c['reviews_count']; ?> reviews)
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="background: #E0F7F8; color: #008B94; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500;">
                                                <?php echo htmlspecialchars($c['category_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 500;"><?php echo htmlspecialchars($c['instructor_name']); ?></span>
                                            <div style="font-size: 0.75rem; color: #94A3B8;"><?php echo htmlspecialchars($c['instructor_title']); ?></div>
                                        </td>
                                        <td>
                                            <div><?php echo $c['duration_weeks']; ?> Weeks</div>
                                            <span style="font-size: 0.75rem; color: #64748B;"><?php echo htmlspecialchars($c['difficulty']); ?></span>
                                        </td>
                                        <td>
                                            <strong style="color: #0F172A;">$<?php echo number_format($c['price'], 2); ?></strong>
                                            <?php if ($c['original_price'] > $c['price']): ?>
                                                <div style="font-size: 0.75rem; text-decoration: line-through; color: #94A3B8;">
                                                    $<?php echo number_format($c['original_price'], 2); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #00ADB5;">
                                                <?php echo $c['enrolled_students']; ?>
                                            </span> students
                                        </td>
                                        <td>
                                            <form action="admin.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="admin_action" value="toggle_featured">
                                                <input type="hidden" name="course_id" value="<?php echo $c['course_id']; ?>">
                                                <button type="submit" class="btn-icon-action" title="Toggle featured state" style="color: <?php echo $c['featured'] ? '#F59E0B' : '#CBD5E1'; ?>;">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="action-btn-group" style="justify-content: flex-end;">
                                                <a href="course-details.php?id=<?php echo $c['course_id']; ?>" target="_blank" class="btn-icon-action" title="View Public Course Page">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <form action="admin.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course? Warning: Associated enrollments will also be removed!');">
                                                    <input type="hidden" name="admin_action" value="delete_course">
                                                    <input type="hidden" name="course_id" value="<?php echo $c['course_id']; ?>">
                                                    <button type="submit" class="btn-icon-action danger" title="Delete Course">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ===================================================== -->
            <!-- TAB 3: STUDENTS                                       -->
            <!-- ===================================================== -->
            <section class="tab-content" id="tab-students">
                <div class="table-toolbar">
                    <div class="toolbar-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search by name, email, qualification..." onkeyup="filterTable('studentsTable', this.value)">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" id="studentsTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Qualification</th>
                                <th>Total Enrollments</th>
                                <th>Registered</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                        No registered students found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td><span style="font-family: monospace; font-weight: 600;">#STU-<?php echo str_pad($s['student_id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                        <td>
                                            <div style="font-weight: 600; color: #0B192C;">
                                                <?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B;">
                                                Gender: <?php echo htmlspecialchars($s['gender']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                                        <td><?php echo htmlspecialchars($s['phone']); ?></td>
                                        <td>
                                            <span style="background: #F1F5F9; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                                <?php echo htmlspecialchars($s['qualification']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #0284C7;"><?php echo $s['enrollment_count']; ?></span> courses
                                        </td>
                                        <td style="font-size: 0.82rem; color: #64748B;">
                                            <?php echo date('M d, Y', strtotime($s['created_at'])); ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="action-btn-group" style="justify-content: flex-end;">
                                                <button type="button" class="btn-icon-action" title="View Full Profile Details" onclick="viewStudentDetails(<?php echo htmlspecialchars(json_encode($s)); ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <form action="admin.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this student and their academic records?');">
                                                    <input type="hidden" name="admin_action" value="delete_student">
                                                    <input type="hidden" name="student_id" value="<?php echo $s['student_id']; ?>">
                                                    <button type="submit" class="btn-icon-action danger" title="Delete Student Record">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ===================================================== -->
            <!-- TAB 4: MESSAGES & INQUIRIES                           -->
            <!-- ===================================================== -->
            <section class="tab-content" id="tab-messages">
                <div class="table-toolbar">
                    <div class="toolbar-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search inquiries by sender, subject..." onkeyup="filterTable('messagesTable', this.value)">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" id="messagesTable">
                        <thead>
                            <tr>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                        No messages or inquiries found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($messages as $m): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: #0B192C;">
                                                <?php echo htmlspecialchars($m['full_name']); ?>
                                            </div>
                                            <div style="font-size: 0.78rem; color: #64748B;">
                                                <?php echo htmlspecialchars($m['email']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong style="color: #1E293B;"><?php echo htmlspecialchars($m['subject']); ?></strong>
                                        </td>
                                        <td>
                                            <div style="max-width: 320px; font-size: 0.84rem; color: #475569; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($m['message']); ?>">
                                                <?php echo htmlspecialchars($m['message']); ?>
                                            </div>
                                        </td>
                                        <td style="font-size: 0.8rem; color: #64748B; white-space: nowrap;">
                                            <?php echo date('M d, Y h:i A', strtotime($m['created_at'])); ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $mBadge = 'badge-new';
                                            if ($m['status'] === 'Read') $mBadge = 'badge-read';
                                            if ($m['status'] === 'Replied') $mBadge = 'badge-replied';
                                            ?>
                                            <span class="badge-status <?php echo $mBadge; ?>">
                                                <?php echo htmlspecialchars($m['status']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="action-btn-group" style="justify-content: flex-end;">
                                                <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>?subject=RE: <?php echo urlencode($m['subject']); ?>&body=Dear <?php echo urlencode($m['full_name']); ?>,%0D%0A%0D%0A" class="btn-icon-action" title="Reply via Email">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                                
                                                <form action="admin.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="admin_action" value="update_message_status">
                                                    <input type="hidden" name="message_id" value="<?php echo $m['message_id']; ?>">
                                                    <input type="hidden" name="status" value="<?php echo ($m['status'] === 'New') ? 'Read' : 'Replied'; ?>">
                                                    <button type="submit" class="btn-icon-action success" title="Mark Status">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <form action="admin.php" method="POST" style="display: inline;" onsubmit="return confirm('Delete this inquiry?');">
                                                    <input type="hidden" name="admin_action" value="delete_message">
                                                    <input type="hidden" name="message_id" value="<?php echo $m['message_id']; ?>">
                                                    <button type="submit" class="btn-icon-action danger" title="Delete Inquiry">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- ========================================================= -->
    <!-- MODAL: ADD NEW COURSE                                     -->
    <!-- ========================================================= -->
    <div class="admin-modal-overlay" id="addCourseModal">
        <div class="admin-modal-box">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle" style="color: #00ADB5;"></i> Add New Academic Course</h3>
                <button type="button" class="modal-close-btn" onclick="closeAddCourseModal()">&times;</button>
            </div>
            <form action="admin.php" method="POST">
                <input type="hidden" name="admin_action" value="add_course">
                <div class="modal-body">
                    <div class="form-grid-2">
                        <div class="modal-input-group">
                            <label for="courseTitle">Course Title *</label>
                            <input type="text" id="courseTitle" name="title" required placeholder="e.g. Advanced Deep Learning & NLP">
                        </div>
                        <div class="modal-input-group">
                            <label for="courseCategory">Category Track *</label>
                            <select id="courseCategory" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="modal-input-group">
                            <label for="instructorName">Lead Instructor *</label>
                            <input type="text" id="instructorName" name="instructor_name" required placeholder="e.g. Dr. Jane Doe">
                        </div>
                        <div class="modal-input-group">
                            <label for="instructorTitle">Instructor Title</label>
                            <input type="text" id="instructorTitle" name="instructor_title" placeholder="e.g. Principal AI Scientist">
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="modal-input-group">
                            <label for="durationWeeks">Duration (Weeks)</label>
                            <input type="number" id="durationWeeks" name="duration_weeks" min="1" max="52" value="10">
                        </div>
                        <div class="modal-input-group">
                            <label for="difficulty">Difficulty Level</label>
                            <select id="difficulty" name="difficulty">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="All Levels" selected>All Levels</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="modal-input-group">
                            <label for="price">Tuition Price ($) *</label>
                            <input type="number" step="0.01" id="price" name="price" value="89.99" required>
                        </div>
                        <div class="modal-input-group">
                            <label for="originalPrice">Original Price ($)</label>
                            <input type="number" step="0.01" id="originalPrice" name="original_price" value="129.99">
                        </div>
                    </div>

                    <div class="modal-input-group">
                        <label for="shortDesc">Short Summary *</label>
                        <textarea id="shortDesc" name="short_description" rows="2" required placeholder="Concise overview of what this course delivers..."></textarea>
                    </div>

                    <div class="modal-input-group">
                        <label for="syllabus">Syllabus Modules (Separate with || )</label>
                        <input type="text" id="syllabus" name="syllabus" value="Module 1: Foundations & Architecture||Module 2: Practical Engineering Labs||Module 3: Production Deployment">
                    </div>

                    <div class="modal-input-group">
                        <label for="courseImage">Cover Image Asset</label>
                        <select id="courseImage" name="image">
                            <option value="assets/images/course_web_dev.svg">Web Engineering (course_web_dev.svg)</option>
                            <option value="assets/images/course_ai_ml.svg">AI & Machine Learning (course_ai_ml.svg)</option>
                            <option value="assets/images/course_cloud_devops.svg">Cloud & DevOps (course_cloud_devops.svg)</option>
                            <option value="assets/images/course_cybersecurity.svg">Cybersecurity (course_cybersecurity.svg)</option>
                            <option value="assets/images/course_ui_ux.svg">UI/UX Design (course_ui_ux.svg)</option>
                            <option value="assets/images/course_data_analytics.svg">Data Analytics (course_data_analytics.svg)</option>
                            <option value="assets/images/course_mobile_dev.svg">Mobile Development (course_mobile_dev.svg)</option>
                            <option value="assets/images/course_backend.svg">Backend APIs (course_backend.svg)</option>
                        </select>
                    </div>

                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="featuredCheck" name="featured" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        <label for="featuredCheck" style="margin: 0; font-size: 0.88rem; font-weight: 500; cursor: pointer;">Feature this course on the homepage</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline btn-sm" onclick="closeAddCourseModal()">Cancel</button>
                    <button type="submit" class="btn-primary-action">
                        <i class="fas fa-check"></i> Save & Publish Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: STUDENT PROFILE DETAILS                            -->
    <!-- ========================================================= -->
    <div class="admin-modal-overlay" id="studentModal">
        <div class="admin-modal-box">
            <div class="modal-header">
                <h3><i class="fas fa-id-card" style="color: #00ADB5;"></i> Student Academic Record</h3>
                <button type="button" class="modal-close-btn" onclick="closeStudentModal()">&times;</button>
            </div>
            <div class="modal-body" id="studentModalContent">
                <!-- Injected via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="closeStudentModal()">Close Window</button>
            </div>
        </div>
    </div>

    <!-- Interactive Client Scripts for Tab Navigation, Search & Modals -->
    <script>
        // Tab switching
        const tabBtns = document.querySelectorAll('.admin-tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-tab');
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                const content = document.getElementById(target);
                if (content) content.classList.add('active');

                // Update hash in URL
                window.location.hash = target.replace('tab-', '');
            });
        });

        // Restore active tab from URL hash on load
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.replace('#', '');
            if (hash) {
                const matchingBtn = document.querySelector(`.admin-tab-btn[data-tab="tab-${hash}"]`);
                if (matchingBtn) matchingBtn.click();
            }
        });

        // Table search filter
        function filterTable(tableId, query) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            const term = query.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const text = rows[i].innerText.toLowerCase();
                rows[i].style.display = text.indexOf(term) > -1 ? '' : 'none';
            }
        }

        // Enrollment Status Filter
        function filterEnrollmentStatus(status) {
            const table = document.getElementById('enrollmentTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const rowStatus = rows[i].getAttribute('data-status');
                if (status === 'ALL' || rowStatus === status) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        // Modals
        function openAddCourseModal() {
            document.getElementById('addCourseModal').classList.add('active');
        }

        function closeAddCourseModal() {
            document.getElementById('addCourseModal').classList.remove('active');
        }

        function closeStudentModal() {
            document.getElementById('studentModal').classList.remove('active');
        }

        function viewStudentDetails(student) {
            const content = document.getElementById('studentModalContent');
            content.innerHTML = `
                <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 1.25rem;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: ${student.theme_color || '#00ADB5'}; color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                        ${student.first_name ? student.first_name[0] : 'S'}
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1.2rem; color: #0B192C;">${escapeHtml(student.first_name)} ${escapeHtml(student.last_name)}</h4>
                        <div style="font-size: 0.85rem; color: #64748B;">Student Registration: #${student.student_id} &bull; ${escapeHtml(student.gender)}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.88rem; background: #F8FAFC; padding: 1rem; border-radius: 10px; border: 1px solid #E2E8F0;">
                    <div><strong>Email:</strong><br><a href="mailto:${escapeHtml(student.email)}">${escapeHtml(student.email)}</a></div>
                    <div><strong>Telephone:</strong><br>${escapeHtml(student.phone)}</div>
                    <div><strong>Date of Birth:</strong><br>${escapeHtml(student.dob)}</div>
                    <div><strong>Academic Qualification:</strong><br>${escapeHtml(student.qualification)}</div>
                    <div><strong>Experience:</strong><br>${student.experience_years} years in industry</div>
                    <div><strong>Preferred Study Time:</strong><br>${escapeHtml(student.preferred_time || 'Flexible')}</div>
                    <div><strong>Weekly Study Hours:</strong><br>${student.study_hours_week} hrs/week</div>
                    <div><strong>Intake Term:</strong><br>${escapeHtml(student.intake_month || 'Immediate')}</div>
                </div>

                <div style="margin-top: 1rem; font-size: 0.88rem;">
                    <strong>Physical Mailing Address:</strong>
                    <p style="margin: 4px 0 0 0; color: #475569;">${escapeHtml(student.address)}</p>
                </div>

                ${student.portfolio_url ? `
                <div style="margin-top: 0.75rem; font-size: 0.88rem;">
                    <strong>Portfolio / GitHub:</strong><br>
                    <a href="${escapeHtml(student.portfolio_url)}" target="_blank" rel="noopener noreferrer">${escapeHtml(student.portfolio_url)} <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i></a>
                </div>
                ` : ''}

                ${student.id_document ? `
                <div style="margin-top: 0.75rem; font-size: 0.88rem;">
                    <strong>Identification Document:</strong><br>
                    <span style="font-family: monospace; background: #E2E8F0; padding: 2px 6px; border-radius: 4px;">${escapeHtml(student.id_document)}</span>
                </div>
                ` : ''}
            `;
            document.getElementById('studentModal').classList.add('active');
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Close modals on clicking outside box
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('admin-modal-overlay')) {
                e.target.classList.remove('active');
            }
        });
    </script>
<?php endif; ?>

</body>
</html>
