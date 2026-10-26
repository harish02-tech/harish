<?php
/**
 * LearnSphere – Home Page (index.php)
 * Features Hero section, Course statistics, Popular categories, Featured courses, and CTAs.
 */

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'LearnSphere – Modern Online Course Enrollment Portal';
$currentPage = 'index.php';

// Fetch statistics
$totalCourses = 8;
$totalCategories = 6;
$totalStudents = 15400;

if ($pdo) {
    try {
        $courseCountStmt = $pdo->query("SELECT COUNT(*) FROM `courses`");
        $totalCourses = $courseCountStmt->fetchColumn() ?: 8;

        $catCountStmt = $pdo->query("SELECT COUNT(*) FROM `categories`");
        $totalCategories = $catCountStmt->fetchColumn() ?: 6;

        $studentCountStmt = $pdo->query("SELECT COUNT(*) FROM `students`");
        $dbStudentCount = $studentCountStmt->fetchColumn();
        if ($dbStudentCount > 0) {
            $totalStudents = 15400 + $dbStudentCount;
        }
    } catch (PDOException $e) {
        // Fallback to defaults
    }
}

// Fetch categories
$categories = getCategories($pdo);
if (empty($categories)) {
    // Graceful fallback sample categories if database not yet migrated
    $categories = [
        ['category_id' => 1, 'name' => 'Web Development', 'icon' => 'fas fa-code', 'description' => 'Master full-stack architectures, modern JavaScript, and backend frameworks.'],
        ['category_id' => 2, 'name' => 'Data Science & AI', 'icon' => 'fas fa-brain', 'description' => 'Machine learning, predictive models, and deep neural algorithms.'],
        ['category_id' => 3, 'name' => 'Cloud & DevOps', 'icon' => 'fas fa-cloud', 'description' => 'Kubernetes, AWS cloud scaling, and automated CI/CD pipelines.'],
        ['category_id' => 4, 'name' => 'Cybersecurity', 'icon' => 'fas fa-shield-alt', 'description' => 'Offensive ethical hacking, vulnerability scanning, and defense.'],
        ['category_id' => 5, 'name' => 'UI/UX Design', 'icon' => 'fas fa-paint-brush', 'description' => 'Figma design systems, wireframing, and interactive prototyping.'],
        ['category_id' => 6, 'name' => 'Mobile Engineering', 'icon' => 'fas fa-mobile-alt', 'description' => 'Cross-platform app development using Flutter and native SDKs.']
    ];
}

// Fetch featured courses
$featuredCourses = getCourses($pdo, null, null, 6, true);
if (empty($featuredCourses)) {
    // If not in database or database empty, fetch any courses
    $featuredCourses = getCourses($pdo, null, null, 6, false);
}

// Fallback courses if DB not yet initialized
if (empty($featuredCourses)) {
    $featuredCourses = [
        [
            'course_id' => 1,
            'title' => 'Full-Stack Modern Web Engineering',
            'category_name' => 'Web Development',
            'short_description' => 'Master HTML5, CSS3, modern JavaScript, PHP, MySQL, and RESTful architectures from scratch to deployment.',
            'instructor_name' => 'Dr. Alexander Vance',
            'duration_weeks' => 12,
            'difficulty' => 'Intermediate',
            'price' => 89.99,
            'original_price' => 149.99,
            'rating' => 4.9,
            'reviews_count' => 428,
            'image' => 'course_web_dev.svg'
        ],
        [
            'course_id' => 2,
            'title' => 'Applied Artificial Intelligence & Machine Learning',
            'category_name' => 'Data Science & AI',
            'short_description' => 'Hands-on machine learning, scikit-learn algorithms, deep neural networks, and modern generative AI models.',
            'instructor_name' => 'Prof. Elena Rostova',
            'duration_weeks' => 14,
            'difficulty' => 'Advanced',
            'price' => 119.99,
            'original_price' => 189.99,
            'rating' => 4.9,
            'reviews_count' => 312,
            'image' => 'course_ai_ml.svg'
        ],
        [
            'course_id' => 3,
            'title' => 'Cloud Architecture & DevOps Engineering',
            'category_name' => 'Cloud & DevOps',
            'short_description' => 'Architect resilient cloud solutions on AWS, manage Docker containers, and orchestrate with Kubernetes.',
            'instructor_name' => 'Marcus Sterling',
            'duration_weeks' => 10,
            'difficulty' => 'Intermediate',
            'price' => 99.99,
            'original_price' => 159.99,
            'rating' => 4.8,
            'reviews_count' => 254,
            'image' => 'course_cloud_devops.svg'
        ],
        [
            'course_id' => 5,
            'title' => 'Professional UI/UX Design & Design Systems',
            'category_name' => 'UI/UX Design',
            'short_description' => 'Master user research, wireframing, high-fidelity UI design in Figma, and interactive prototyping.',
            'instructor_name' => 'Camilla Duarte',
            'duration_weeks' => 8,
            'difficulty' => 'Beginner',
            'price' => 69.99,
            'original_price' => 119.99,
            'rating' => 4.7,
            'reviews_count' => 210,
            'image' => 'course_ui_ux.svg'
        ]
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Semantic Main Element -->
<main id="mainContent">

    <!-- 1. Hero Section -->
    <section class="hero-section" id="hero">
        <div class="container hero-grid">
            <!-- Left Hero Content -->
            <div class="hero-content">
                <span class="section-subtitle" style="background: rgba(0, 173, 181, 0.18); color: #00ADB5; border: 1px solid rgba(0, 173, 181, 0.35);">
                    <i class="fas fa-certificate"></i> Accredited Higher-Education Portal
                </span>
                <h1>
                    Unlock Next-Generation <span class="highlight">Knowledge</span> &amp; Digital Engineering
                </h1>
                <p>
                    LearnSphere provides rigorous, curriculum-aligned academic courses in software engineering, applied artificial intelligence, cloud systems, and cybersecurity. Enroll today to earn verifiable career credentials.
                </p>

                <!-- Hero CTAs -->
                <div class="hero-cta-group">
                    <a href="courses.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-compass"></i> Explore Courses
                    </a>
                    <a href="register.php" class="btn btn-accent-outline btn-lg" style="background-color: rgba(255, 255, 255, 0.08); color: #FFFFFF; border-color: rgba(255, 255, 255, 0.4);">
                        <i class="fas fa-user-graduate"></i> Register Now
                    </a>
                </div>

                <!-- Hero Inline Key Statistics -->
                <div class="hero-stats-inline">
                    <div class="stat-inline-item">
                        <strong>98.7%</strong>
                        <span>Course Satisfaction</span>
                    </div>
                    <div class="stat-inline-item">
                        <strong>50+</strong>
                        <span>Industry Projects</span>
                    </div>
                    <div class="stat-inline-item">
                        <!-- Inline CSS Demonstration -->
                        <strong style="color: #38BDF8;">100%</strong>
                        <span>Accredited Content</span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual Graphic -->
            <div class="hero-visual">
                <img src="assets/images/hero_illustration.svg" alt="Interactive Digital Learning Lab Visual" width="600" height="460">
            </div>
        </div>
    </section>

    <!-- 2. Course Statistics Bar (Semantic Section with CSS Grid) -->
    <section class="container" aria-label="Portal Metrics">
        <div class="stats-section">
            <div class="stats-grid">
                <article class="stat-box">
                    <div class="stat-icon"><i class="fas fa-book-reader"></i></div>
                    <div class="stat-info">
                        <h3><?php echo (int)$totalCourses; ?>+</h3>
                        <p>Specialized Courses</p>
                    </div>
                </article>

                <article class="stat-box">
                    <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($totalStudents); ?>+</h3>
                        <p>Global Enrolled Students</p>
                    </div>
                </article>

                <article class="stat-box">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-info">
                        <h3>24+</h3>
                        <p>Distinguished Mentors</p>
                    </div>
                </article>

                <article class="stat-box">
                    <div class="stat-icon"><i class="fas fa-award"></i></div>
                    <div class="stat-info">
                        <h3>4.9 / 5.0</h3>
                        <p>Accredited Quality Rating</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- 3. Popular Categories Section -->
    <section class="section-padding" id="categoriesSection">
        <div class="container">
            <header class="section-header">
                <span class="section-subtitle">Academic Disciplines</span>
                <h2 class="section-title">Explore Popular Categories</h2>
                <p class="section-description">
                    Choose from specialized tracks designed by university scholars and software engineering leads to prepare you for the technological landscape.
                </p>
            </header>

            <div class="categories-grid">
                <?php foreach ($categories as $cat): ?>
                <a href="courses.php?category=<?php echo (int)$cat['category_id']; ?>" class="category-card" title="Browse <?php echo sanitize($cat['name']); ?> Courses">
                    <div class="category-icon">
                        <i class="<?php echo sanitize($cat['icon']); ?>"></i>
                    </div>
                    <h3><?php echo sanitize($cat['name']); ?></h3>
                    <p><?php echo sanitize($cat['description']); ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 4. Featured Courses Section -->
    <section class="section-padding" style="background-color: var(--bg-muted);" id="featuredCourses">
        <div class="container">
            <header class="section-header">
                <span class="section-subtitle">Curated Programs</span>
                <h2 class="section-title">Featured Academic Courses</h2>
                <p class="section-description">
                    High-demand professional courses complete with hands-on capstones, live instructor office hours, and accredited certification.
                </p>
            </header>

            <div class="courses-grid">
                <?php foreach ($featuredCourses as $course): 
                    $diffClass = 'badge-all-levels';
                    if (strtolower($course['difficulty']) == 'beginner') $diffClass = 'badge-beginner';
                    if (strtolower($course['difficulty']) == 'intermediate') $diffClass = 'badge-intermediate';
                    if (strtolower($course['difficulty']) == 'advanced') $diffClass = 'badge-advanced';
                ?>
                <article class="course-card">
                    <div class="course-card-image">
                        <img src="assets/images/<?php echo sanitize($course['image']); ?>" alt="<?php echo sanitize($course['title']); ?>" loading="lazy">
                        <span class="course-category-badge"><?php echo sanitize($course['category_name']); ?></span>
                        <span class="course-difficulty-badge <?php echo $diffClass; ?>"><?php echo sanitize($course['difficulty']); ?></span>
                    </div>

                    <div class="course-card-body">
                        <div class="course-meta-top">
                            <span class="course-rating">
                                <i class="fas fa-star"></i> <?php echo number_format((float)$course['rating'], 1); ?>
                                <small style="color: var(--text-light);">(<?php echo (int)$course['reviews_count']; ?>)</small>
                            </span>
                            <span><i class="far fa-clock"></i> <?php echo (int)$course['duration_weeks']; ?> Weeks</span>
                        </div>

                        <h3 class="course-card-title">
                            <a href="course-details.php?id=<?php echo (int)$course['course_id']; ?>">
                                <?php echo sanitize($course['title']); ?>
                            </a>
                        </h3>

                        <p class="course-card-desc">
                            <?php echo sanitize($course['short_description']); ?>
                        </p>

                        <div class="course-instructor-info">
                            <img src="assets/images/avatar_alex.svg" alt="Instructor Avatar" class="instructor-avatar-sm">
                            <div class="instructor-text">
                                <strong><?php echo sanitize($course['instructor_name']); ?></strong>
                                <span>Academic Lead</span>
                            </div>
                        </div>

                        <div class="course-card-footer">
                            <div class="course-price-wrap">
                                <span class="course-price"><?php echo formatPrice($course['price']); ?></span>
                                <?php if (!empty($course['original_price']) && $course['original_price'] > $course['price']): ?>
                                <span class="course-original-price"><?php echo formatPrice($course['original_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="course-card-actions">
                                <a href="course-details.php?id=<?php echo (int)$course['course_id']; ?>" class="btn btn-outline btn-sm">
                                    Details
                                </a>
                                <a href="register.php?course_id=<?php echo (int)$course['course_id']; ?>" class="btn btn-primary btn-sm">
                                    Enroll Now
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- View All Courses Button -->
            <div style="text-align: center; margin-top: var(--spacing-2xl);">
                <a href="courses.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-layer-group"></i> View All Available Courses
                </a>
            </div>
        </div>
    </section>

    <!-- 5. Academic Excellence Call To Action Banner -->
    <section class="section-padding">
        <div class="container">
            <div style="background: radial-gradient(circle at 10% 20%, #1E3E62 0%, #0B192C 100%); border-radius: var(--radius-lg); padding: var(--spacing-2xl); color: #FFFFFF; text-align: center; position: relative; overflow: hidden; box-shadow: var(--shadow-lg);">
                <span class="section-subtitle" style="background: rgba(0, 173, 181, 0.2); color: #38BDF8; margin-bottom: 14px;">Direct Enrollment Open</span>
                <h2 style="color: #FFFFFF; font-size: 2.3rem; max-width: 720px; margin: 0 auto 16px auto;">
                    Ready to Accelerate Your Academic &amp; Engineering Trajectory?
                </h2>
                <p style="color: #CBD5E1; max-width: 600px; margin: 0 auto 28px auto; font-size: 1.05rem;">
                    No complex login procedures required. Select your course, complete the verified academic enrollment form, and embark on your learning journey today.
                </p>
                <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Begin Registration
                    </a>
                    <a href="about.php" class="btn btn-outline btn-lg" style="color: #FFFFFF; border-color: rgba(255,255,255,0.4);">
                        <i class="fas fa-university"></i> Learn About LearnSphere
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
