<?php
/**
 * LearnSphere – Course Details Page (course-details.php)
 * Displays in-depth curriculum syllabus, learning outcomes, instructor credentials,
 * and an Enroll Now CTA linking directly to student registration.
 * Also demonstrates Academic Requirement: "Internal CSS inside a style block on one page".
 */

require_once __DIR__ . '/includes/db.php';

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$course = getCourseById($pdo, $courseId);

// Fallback course data if MySQL database not yet populated
if (!$course) {
    // Attempt course 1 from sample list
    $fallbackList = [
        1 => [
            'course_id' => 1,
            'category_name' => 'Web Development',
            'title' => 'Full-Stack Modern Web Engineering',
            'short_description' => 'Master HTML5, CSS3, modern JavaScript, PHP, MySQL, and RESTful architectures from scratch to deployment.',
            'full_description' => 'This flagship program is engineered to take you from core programming fundamentals into enterprise-level web architecture. You will architect responsive, accessible client interfaces using semantic HTML5 and cutting-edge CSS3 grid systems, build scalable server-side systems with PHP 8, and manage robust relational data structures with MySQL. By the end of this course, you will possess real-world software engineering skills aligned with current industry standards.',
            'syllabus' => 'Module 1: Semantic HTML5, Core CSS3 & Responsive Architecture||Module 2: Advanced JavaScript (ES6+), DOM, and Async Fetch API||Module 3: Server-Side Logic with PHP 8, OOP & MVC Patterns||Module 4: Relational Database Modeling with MySQL & PDO Prepared Statements||Module 5: RESTful API Design, JWT Authentication & Security Hardening||Module 6: Production Deployment, CI/CD & Performance Optimization',
            'learning_outcomes' => 'Design accessible, modern UI layouts with CSS Grid, Flexbox, and fluid typography.||Build secure, sanitized PHP applications with parameterized database queries.||Implement RESTful backend endpoints and consume external web services.||Deploy production-ready web applications on cloud and local web servers.',
            'instructor_name' => 'Dr. Alexander Vance',
            'instructor_title' => 'Principal Software Architect & Academic Fellow',
            'instructor_bio' => 'Dr. Vance holds a Ph.D. in Computer Science with over 15 years of industry experience leading high-throughput distributed systems at top tech companies.',
            'duration_weeks' => 12,
            'difficulty' => 'Intermediate',
            'price' => 89.99,
            'original_price' => 149.99,
            'rating' => 4.9,
            'reviews_count' => 428,
            'image' => 'course_web_dev.svg'
        ]
    ];
    $course = $fallbackList[$courseId] ?? $fallbackList[1];
}

$pageTitle = sanitize($course['title']) . ' – Course Details | LearnSphere';
$currentPage = 'courses.php';

// Parse syllabus and learning outcomes separated by delimiter
$syllabusModules = explode('||', $course['syllabus']);
$learningOutcomes = explode('||', $course['learning_outcomes']);

// Demonstration of Internal CSS Requirement via $extraHeadContent
$extraHeadContent = '
<style>
    /* Academic Requirement: Internal CSS block demonstrated on course-details.php */
    .course-details-badge-accent {
        background: linear-gradient(135deg, rgba(0, 173, 181, 0.2) 0%, rgba(2, 132, 199, 0.2) 100%);
        border: 1px solid var(--accent-color);
        color: var(--accent-color);
        font-weight: 700;
        padding: 6px 16px;
        border-radius: var(--radius-full);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .outcome-card {
        background-color: #FFFFFF;
        border-left: 4px solid var(--accent-color);
        padding: 14px 18px;
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        box-shadow: var(--shadow-sm);
        margin-bottom: 12px;
        transition: transform 0.2s ease;
    }
    .outcome-card:hover {
        transform: translateX(6px);
        box-shadow: var(--shadow-md);
    }
    
    .syllabus-module-number {
        background-color: var(--primary-color);
        color: #FFFFFF;
        font-size: 0.8rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: var(--radius-sm);
        margin-right: 12px;
        flex-shrink: 0;
    }

    .instructor-bio-card {
        background: linear-gradient(to right, #F8FAFC, #FFFFFF);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        display: flex;
        gap: var(--spacing-lg);
        align-items: center;
    }
</style>
';

require_once __DIR__ . '/includes/header.php';
?>

<main id="mainContent">

    <!-- Course Details Hero Banner -->
    <section class="course-details-hero">
        <div class="container">
            <div style="max-width: 850px;">
                <!-- Internal CSS Styled Badge -->
                <span class="course-details-badge-accent">
                    <i class="fas fa-layer-group"></i> <?php echo sanitize($course['category_name']); ?>
                </span>

                <h1 style="margin-top: 16px;"><?php echo sanitize($course['title']); ?></h1>
                
                <p><?php echo sanitize($course['short_description']); ?></p>

                <!-- Course Meta Overview -->
                <div class="details-meta-bar">
                    <div class="details-meta-item">
                        <i class="fas fa-user-tie"></i>
                        <span>Instructor: <strong><?php echo sanitize($course['instructor_name']); ?></strong></span>
                    </div>
                    <div class="details-meta-item">
                        <i class="far fa-clock"></i>
                        <span>Duration: <strong><?php echo (int)$course['duration_weeks']; ?> Weeks</strong></span>
                    </div>
                    <div class="details-meta-item">
                        <i class="fas fa-signal"></i>
                        <span>Level: <strong><?php echo sanitize($course['difficulty']); ?></strong></span>
                    </div>
                    <div class="details-meta-item">
                        <i class="fas fa-star" style="color: #FBBF24;"></i>
                        <span>Rating: <strong><?php echo number_format((float)$course['rating'], 1); ?></strong> (<?php echo (int)$course['reviews_count']; ?> reviews)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content & Sidebar Grid Layout -->
    <section class="section-padding">
        <div class="container course-details-layout">

            <!-- Left: Main Course Syllabus & Outcomes -->
            <article class="details-content-main">
                
                <!-- Course Visual Banner -->
                <figure style="margin-bottom: var(--spacing-xl); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-color);">
                    <img src="assets/images/<?php echo sanitize($course['image']); ?>" alt="<?php echo sanitize($course['title']); ?>" style="width: 100%; max-height: 380px; object-fit: cover;">
                    <figcaption style="padding: 10px 16px; background-color: var(--bg-muted); font-size: 0.85rem; color: var(--text-muted); font-style: italic;">
                        Figure 1: Program architecture &amp; technical blueprint for <?php echo sanitize($course['title']); ?>.
                    </figcaption>
                </figure>

                <!-- Section: Comprehensive Overview -->
                <section style="margin-bottom: var(--spacing-2xl);">
                    <h2>Program Overview</h2>
                    <p style="font-size: 1.05rem; line-height: 1.8;">
                        <?php echo nl2br(sanitize($course['full_description'])); ?>
                    </p>
                </section>

                <!-- Section: Learning Outcomes -->
                <section style="margin-bottom: var(--spacing-2xl);">
                    <h2>What You Will Master</h2>
                    <p>Upon satisfactory completion of all modules and laboratory evaluations, students will be able to:</p>
                    
                    <div>
                        <?php foreach ($learningOutcomes as $index => $outcome): ?>
                            <?php if (!empty(trim($outcome))): ?>
                            <div class="outcome-card">
                                <i class="fas fa-check-circle" style="color: var(--accent-color); margin-right: 8px;"></i>
                                <span style="font-size: 0.95rem; font-weight: 500; color: var(--primary-color);">
                                    <?php echo sanitize(trim($outcome)); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Section: Curriculum & Syllabus -->
                <section style="margin-bottom: var(--spacing-2xl);">
                    <h2>Structured Syllabus &amp; Modules</h2>
                    <p>A rigorous sequential roadmap covering foundational principles through to advanced production deployment.</p>

                    <div class="syllabus-list">
                        <?php foreach ($syllabusModules as $index => $module): ?>
                            <?php if (!empty(trim($module))): ?>
                            <div class="syllabus-module-item">
                                <div style="display: flex; align-items: center;">
                                    <span class="syllabus-module-number">0<?php echo $index + 1; ?></span>
                                    <strong><?php echo sanitize(trim($module)); ?></strong>
                                </div>
                                <span style="font-size: 0.85rem; color: var(--text-light);"><i class="fas fa-lock-open" style="color: var(--success-color); margin-right: 4px;"></i> Available</span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Section: Lead Faculty Instructor -->
                <section>
                    <h2>Lead Faculty &amp; Mentor</h2>
                    <div class="instructor-bio-card">
                        <img src="assets/images/avatar_alex.svg" alt="<?php echo sanitize($course['instructor_name']); ?>" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--accent-color); flex-shrink: 0;">
                        <div>
                            <h3 style="margin-bottom: 4px;"><?php echo sanitize($course['instructor_name']); ?></h3>
                            <span style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--accent-color); margin-bottom: 10px;">
                                <?php echo sanitize($course['instructor_title']); ?>
                            </span>
                            <p style="margin-bottom: 0; font-size: 0.92rem;">
                                <?php echo sanitize($course['instructor_bio']); ?>
                            </p>
                        </div>
                    </div>
                </section>

            </article>

            <!-- Right: Sticky Aside Enrollment Summary -->
            <aside class="details-sidebar" aria-label="Course Enrollment Sidebar">
                <div class="enrollment-card-sticky">
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 1px;">
                        Tuition &amp; Enrollment
                    </span>

                    <div class="enroll-price-display">
                        <span class="price-main"><?php echo formatPrice($course['price']); ?></span>
                        <?php if (!empty($course['original_price']) && $course['original_price'] > $course['price']): ?>
                        <span style="color: var(--text-light); text-decoration: line-through; font-size: 1.1rem;">
                            <?php echo formatPrice($course['original_price']); ?>
                        </span>
                        <span style="background-color: var(--success-light); color: var(--success-color); font-size: 0.8rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                            SAVE <?php echo round((($course['original_price'] - $course['price']) / $course['original_price']) * 100); ?>%
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Direct Enroll CTA (links to register.php with preselected course_id) -->
                    <a href="register.php?course_id=<?php echo (int)$course['course_id']; ?>" class="btn btn-primary btn-block btn-lg" style="margin-bottom: var(--spacing-md);">
                        <i class="fas fa-graduation-cap"></i> Enroll In This Course
                    </a>

                    <p style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-bottom: var(--spacing-lg);">
                        <i class="fas fa-shield-alt" style="color: var(--success-color);"></i> Instant Academic Confirmation • No Login Needed
                    </p>

                    <!-- Course Included Features Checklist -->
                    <h4 style="font-size: 1.05rem; margin-bottom: 12px;">This Program Includes:</h4>
                    <ul class="features-check-list">
                        <li><i class="fas fa-video"></i> <?php echo (int)$course['duration_weeks'] * 4; ?>+ Hours of Directed Lab Work</li>
                        <li><i class="fas fa-file-code"></i> Comprehensive Source Repositories</li>
                        <li><i class="fas fa-certificate"></i> Accredited Certificate of Completion</li>
                        <li><i class="fas fa-infinity"></i> Lifetime Access to Updated Syllabus</li>
                        <li><i class="fas fa-comments"></i> 1-on-1 Faculty Mentorship Reviews</li>
                    </ul>

                    <div style="background-color: var(--bg-muted); border-radius: var(--radius-md); padding: 14px; text-align: center; margin-top: 18px;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">Have questions about cohort dates?</span><br>
                        <a href="about.php#contact" style="font-weight: 600; font-size: 0.9rem;">Contact Admissions &rarr;</a>
                    </div>
                </div>
            </aside>

        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
