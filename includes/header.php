<?php
/**
 * LearnSphere – Semantic Header & Navigation Component
 */
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
if (!isset($pageTitle)) {
    $pageTitle = 'LearnSphere – Online Course Enrollment Portal';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LearnSphere is a premier online academic portal offering certified courses in Full-Stack Web, AI, DevOps, Cybersecurity, UI/UX, and Data Science.">
    <meta name="keywords" content="Online Courses, Course Enrollment, LearnSphere, Full-Stack, AI, Cybersecurity, Academic Portal">
    <meta name="author" content="LearnSphere Academic Team">
    <title><?php echo sanitize($pageTitle); ?></title>

    <!-- Google Fonts (Poppins & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons 6.5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- External Master CSS Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Optional custom page header injection (e.g. internal CSS) -->
    <?php if (isset($extraHeadContent)) echo $extraHeadContent; ?>
</head>
<body>

    <!-- Semantic Header Component -->
    <header class="site-header" id="siteHeader">
        <div class="container nav-container">
            <!-- Brand Logo -->
            <div class="nav-brand">
                <a href="index.php" title="LearnSphere Home">
                    <img src="assets/images/logo.svg" alt="LearnSphere Portal Logo" width="220" height="48">
                </a>
            </div>

            <!-- Semantic Main Navigation -->
            <nav class="main-nav" id="mainNav" aria-label="Main Navigation">
                <ul class="nav-menu">
                    <li>
                        <a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li>
                        <a href="courses.php" class="<?php echo ($currentPage == 'courses.php') ? 'active' : ''; ?>">
                            <i class="fas fa-book-open"></i> Courses
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i> About Us
                        </a>
                    </li>
                    <li>
                        <a href="about.php#contact" class="<?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                    <li>
                        <a href="register.php" class="<?php echo ($currentPage == 'register.php') ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                    <li>
                        <a href="admin.php" class="<?php echo ($currentPage == 'admin.php') ? 'active' : ''; ?>">
                            <i class="fas fa-user-shield"></i> Admin
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Header Quick Actions -->
            <div class="nav-actions">
                <a href="courses.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-compass"></i> Explore Courses
                </a>
                <a href="register.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-graduation-cap"></i> Enroll Now
                </a>
                <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <?php if (isset($db_connected) && !$db_connected): ?>
    <!-- Notice Banner if MySQL is not yet initialized -->
    <aside class="container" style="margin-top: 15px;">
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.4rem;"></i>
            <div>
                <strong>Notice: MySQL Database Not Connected</strong><br>
                <span>Please ensure Apache & MySQL are running in your XAMPP Control Panel and you have imported the schema from <code>database/learnsphere_db.sql</code> into phpMyAdmin.</span>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Semantic Main Content Wrapper Starts in Individual Pages -->
