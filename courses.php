<?php
/**
 * LearnSphere – Courses Catalog Page (courses.php)
 * Search, filter by category, and explore all courses loaded dynamically from MySQL.
 */

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Academic Courses Catalog – LearnSphere';
$currentPage = 'courses.php';

// Capture search and category filter inputs
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch all categories for filter dropdown & pills
$categories = getCategories($pdo);
if (empty($categories)) {
    $categories = [
        ['category_id' => 1, 'name' => 'Web Development'],
        ['category_id' => 2, 'name' => 'Data Science & AI'],
        ['category_id' => 3, 'name' => 'Cloud & DevOps'],
        ['category_id' => 4, 'name' => 'Cybersecurity'],
        ['category_id' => 5, 'name' => 'UI/UX Design'],
        ['category_id' => 6, 'name' => 'Mobile Engineering']
    ];
}

// Fetch courses from MySQL based on filters
$courses = getCourses($pdo, $selectedCategory ?: null, $searchQuery ?: null);

// Fallback course catalog if MySQL is not yet migrated
if (empty($courses) && empty($searchQuery) && empty($selectedCategory)) {
    $courses = [
        [
            'course_id' => 1,
            'category_id' => 1,
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
            'category_id' => 2,
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
            'category_id' => 3,
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
            'course_id' => 4,
            'category_id' => 4,
            'title' => 'Ethical Hacking & Defensive Cybersecurity',
            'category_name' => 'Cybersecurity',
            'short_description' => 'Penetration testing methodologies, network defense, vulnerability assessment, and threat mitigation.',
            'instructor_name' => 'Sarah Jenkins, CISSP',
            'duration_weeks' => 12,
            'difficulty' => 'Intermediate',
            'price' => 109.99,
            'original_price' => 179.99,
            'rating' => 4.9,
            'reviews_count' => 189,
            'image' => 'course_cybersecurity.svg'
        ],
        [
            'course_id' => 5,
            'category_id' => 5,
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
        ],
        [
            'course_id' => 6,
            'category_id' => 6,
            'title' => 'Cross-Platform Mobile App Development with Flutter',
            'category_name' => 'Mobile Engineering',
            'short_description' => 'Build natively compiled applications for iOS and Android using Dart and Google Flutter framework.',
            'instructor_name' => 'David Chen',
            'duration_weeks' => 10,
            'difficulty' => 'Beginner',
            'price' => 79.99,
            'original_price' => 129.99,
            'rating' => 4.8,
            'reviews_count' => 175,
            'image' => 'course_mobile_dev.svg'
        ],
        [
            'course_id' => 7,
            'category_id' => 1,
            'title' => 'Backend API Architecture with Node.js & Microservices',
            'category_name' => 'Web Development',
            'short_description' => 'Construct high-throughput event-driven microservices, Redis caching layers, and asynchronous queues.',
            'instructor_name' => 'Liam O’Connor',
            'duration_weeks' => 10,
            'difficulty' => 'Advanced',
            'price' => 94.99,
            'original_price' => 149.99,
            'rating' => 4.9,
            'reviews_count' => 142,
            'image' => 'course_backend.svg'
        ],
        [
            'course_id' => 8,
            'category_id' => 2,
            'title' => 'Python for Business Intelligence & Data Analytics',
            'category_name' => 'Data Science & AI',
            'short_description' => 'Transform complex raw business data into actionable strategic insights with Python, SQL, and interactive dashboards.',
            'instructor_name' => 'Priya Sharma',
            'duration_weeks' => 8,
            'difficulty' => 'Beginner',
            'price' => 64.99,
            'original_price' => 99.99,
            'rating' => 4.8,
            'reviews_count' => 280,
            'image' => 'course_data_analytics.svg'
        ]
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="mainContent">

    <!-- Page Banner -->
    <section style="background: radial-gradient(circle at 90% 10%, #1E3E62 0%, #0B192C 80%); padding: 50px 0; color: #FFFFFF;">
        <div class="container">
            <span class="section-subtitle" style="background: rgba(0, 173, 181, 0.2); color: #00ADB5;">
                <i class="fas fa-graduation-cap"></i> Academic Curriculum
            </span>
            <h1 style="color: #FFFFFF; font-size: 2.5rem; margin-top: 10px;">
                Explore All Academic Courses
            </h1>
            <p style="color: #CBD5E1; max-width: 650px; margin-bottom: 0;">
                Discover accredited engineering and computing programs. Filter by discipline or search for specific technologies to find your optimal career path.
            </p>
        </div>
    </section>

    <!-- Main Course Listing & Filter Section -->
    <section class="section-padding">
        <div class="container">

            <!-- Search & Filter Bar Component -->
            <div class="filter-search-bar">
                <form action="courses.php" method="GET" class="filter-form" role="search">
                    <!-- Search Input (HTML5 Input Type: search) -->
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="search" name="search" id="courseSearch" 
                               placeholder="Search courses by title, topic, or instructor..." 
                               value="<?php echo sanitize($searchQuery); ?>"
                               autocomplete="off">
                    </div>

                    <!-- Category Dropdown Filter -->
                    <select name="category" id="categoryFilter" class="filter-select" onchange="this.form.submit()">
                        <option value="0">All Disciplines (All Categories)</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat['category_id']; ?>" <?php echo ($selectedCategory == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo sanitize($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Filter Action Buttons -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <?php if (!empty($searchQuery) || !empty($selectedCategory)): ?>
                        <a href="courses.php" class="btn btn-outline" title="Clear all filters">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Category Quick Pills -->
                <div class="filter-pills">
                    <a href="courses.php" class="filter-pill <?php echo ($selectedCategory == 0) ? 'active' : ''; ?>">
                        All Tracks
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="courses.php?category=<?php echo (int)$cat['category_id']; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?>" 
                           class="filter-pill <?php echo ($selectedCategory == $cat['category_id']) ? 'active' : ''; ?>">
                            <?php echo sanitize($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Results Counter & Status -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                <p style="margin-bottom: 0; font-weight: 600; color: var(--primary-color);">
                    Showing <span style="color: var(--accent-color); font-weight: 700;"><?php echo count($courses); ?></span> Courses
                    <?php if (!empty($searchQuery)): ?>
                        for <mark style="background-color: var(--accent-light); padding: 2px 8px; border-radius: 4px;">"<?php echo sanitize($searchQuery); ?>"</mark>
                    <?php endif; ?>
                </p>
                <span style="font-size: 0.85rem; color: var(--text-muted);">
                    <i class="fas fa-check-circle" style="color: var(--success-color);"></i> Database Synced
                </span>
            </div>

            <?php if (empty($courses)): ?>
                <!-- No Results State -->
                <div style="text-align: center; padding: 60px 20px; background: #FFFFFF; border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
                    <i class="fas fa-search" style="font-size: 3rem; color: var(--text-light); margin-bottom: 16px;"></i>
                    <h3 style="color: var(--primary-color);">No Courses Found</h3>
                    <p style="max-width: 480px; margin: 0 auto 20px auto;">
                        We couldn't find any courses matching your specific query. Try searching with different keywords or clear the category filters.
                    </p>
                    <a href="courses.php" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> View All Courses
                    </a>
                </div>
            <?php else: ?>
                <!-- Courses Grid -->
                <div class="courses-grid" id="coursesGrid">
                    <?php foreach ($courses as $course): 
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
                                <img src="assets/images/avatar_alex.svg" alt="Instructor" class="instructor-avatar-sm">
                                <div class="instructor-text">
                                    <strong><?php echo sanitize($course['instructor_name']); ?></strong>
                                    <span>Faculty Instructor</span>
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
                                        View Details
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
            <?php endif; ?>

        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
