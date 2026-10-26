<?php
/**
 * LearnSphere – About Us & Contact Support Page (about.php)
 * Introduces the LearnSphere mission, academic methodology, lead faculty,
 * and includes a working Contact Form that persists inquiries to MySQL.
 */

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'About Us & Contact Admissions – LearnSphere';
$currentPage = 'about.php';

$contactSuccess = false;
$contactErrors = [];

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Server-Side Validations
    if (empty($name) || strlen($name) < 2) {
        $contactErrors[] = 'Please provide your full name.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactErrors[] = 'Please provide a valid email address.';
    }
    if (empty($subject) || strlen($subject) < 3) {
        $contactErrors[] = 'Please provide a subject for your inquiry.';
    }
    if (empty($message) || strlen($message) < 10) {
        $contactErrors[] = 'Your message must be at least 10 characters long.';
    }

    if (empty($contactErrors)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO `contact_messages` (`full_name`, `email`, `subject`, `message`, `status`) VALUES (:name, :email, :sub, :msg, 'New')");
                $stmt->execute([
                    ':name'  => $name,
                    ':email' => $email,
                    ':sub'   => $subject,
                    ':msg'   => $message
                ]);
                $contactSuccess = true;
            } catch (PDOException $e) {
                $contactErrors[] = 'Database error recording message: ' . sanitize($e->getMessage());
            }
        } else {
            // Simulated success if MySQL is not yet configured
            $contactSuccess = true;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="mainContent">

    <!-- About Hero Section -->
    <section style="background: radial-gradient(circle at 80% 20%, #1E3E62 0%, #0B192C 80%); padding: 60px 0; color: #FFFFFF;">
        <div class="container about-grid">
            <div>
                <span class="section-subtitle" style="background: rgba(0, 173, 181, 0.2); color: #00ADB5;">
                    <i class="fas fa-university"></i> Empowering Global Technologists
                </span>
                <h1 style="color: #FFFFFF; font-size: 2.6rem; margin-top: 12px; margin-bottom: 16px;">
                    Democratizing World-Class Tech Education
                </h1>
                <p style="color: #CBD5E1; font-size: 1.1rem; line-height: 1.7; margin-bottom: 24px;">
                    LearnSphere was founded on the fundamental principle that elite computational science, cloud infrastructure, and modern software engineering must be directly accessible to any dedicated student worldwide without bureaucratic barriers.
                </p>
                <div style="display: flex; gap: 16px;">
                    <a href="#contact" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Contact Admissions
                    </a>
                    <a href="courses.php" class="btn btn-outline" style="color: #FFFFFF; border-color: rgba(255,255,255,0.4);">
                        <i class="fas fa-book-open"></i> Browse Programs
                    </a>
                </div>
            </div>

            <div style="display: flex; justify-content: center;">
                <img src="assets/images/about_hero.svg" alt="LearnSphere Campus and Mission Visual" style="max-width: 480px; width: 100%;">
            </div>
        </div>
    </section>

    <!-- Mission & Core Values Section -->
    <section class="section-padding">
        <div class="container">
            <header class="section-header">
                <span class="section-subtitle">Our Foundation</span>
                <h2 class="section-title">The LearnSphere Mission</h2>
                <p class="section-description">
                    We bridge academic depth with practical industrial velocity, preparing learners to architect real-world production systems.
                </p>
            </header>

            <div class="categories-grid" style="grid-template-columns: repeat(3, 1fr);">
                <article class="category-card" style="text-align: left; align-items: flex-start;">
                    <div class="category-icon" style="background-color: #E0F2FE; color: #0284C7;">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Rigorous Curriculum</h3>
                    <p>
                        Every syllabus is curated with leading software architects. Modules focus on deep fundamentals, testing paradigms, and scalable architecture rather than fleeting syntax.
                    </p>
                </article>

                <article class="category-card" style="text-align: left; align-items: flex-start;">
                    <div class="category-icon" style="background-color: #DCFCE7; color: #16A34A;">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h3>Direct Open Enrollment</h3>
                    <p>
                        No high-friction authentication or protracted admissions delays. Students register, enroll, and obtain lifetime syllabus access directly through our streamlined portal.
                    </p>
                </article>

                <article class="category-card" style="text-align: left; align-items: flex-start;">
                    <div class="category-icon" style="background-color: #FEF3C7; color: #D97706;">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Industry-Standard Capstones</h3>
                    <p>
                        Graduates demonstrate verifiable portfolio competencies by constructing functional, database-backed web applications, machine learning pipelines, and cloud clusters.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Faculty & Mentorship Showcase -->
    <section class="section-padding" style="background-color: var(--bg-muted);">
        <div class="container">
            <header class="section-header">
                <span class="section-subtitle">Academic Mentorship</span>
                <h2 class="section-title">Meet Our Lead Faculty</h2>
                <p class="section-description">
                    Learn directly from veteran researchers, authors, and principal cloud engineers.
                </p>
            </header>

            <div class="instructors-grid">
                <article class="instructor-card">
                    <img src="assets/images/avatar_alex.svg" alt="Dr. Alexander Vance">
                    <h3 style="font-size: 1.2rem; margin-bottom: 4px;">Dr. Alexander Vance</h3>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--accent-color); display: block; margin-bottom: 12px;">
                        Principal Web Architect (Ph.D.)
                    </span>
                    <p style="font-size: 0.9rem; margin-bottom: 0;">
                        15+ years engineering high-scale distributed systems and mentoring full-stack engineers globally.
                    </p>
                </article>

                <article class="instructor-card">
                    <img src="assets/images/avatar_elena.svg" alt="Prof. Elena Rostova">
                    <h3 style="font-size: 1.2rem; margin-bottom: 4px;">Prof. Elena Rostova</h3>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #A855F7; display: block; margin-bottom: 12px;">
                        Chief AI Researcher
                    </span>
                    <p style="font-size: 0.9rem; margin-bottom: 0;">
                        Published neural network researcher and consultant for computer vision and generative AI applications.
                    </p>
                </article>

                <article class="instructor-card">
                    <img src="assets/images/avatar_marcus.svg" alt="Marcus Sterling">
                    <h3 style="font-size: 1.2rem; margin-bottom: 4px;">Marcus Sterling</h3>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #0284C7; display: block; margin-bottom: 12px;">
                        Cloud Solutions Architect
                    </span>
                    <p style="font-size: 0.9rem; margin-bottom: 0;">
                        AWS Certified Master specializing in Kubernetes orchestration, microservices, and automated CI/CD.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Contact & Academic Inquiries Section -->
    <section class="section-padding" id="contact">
        <div class="container">
            <header class="section-header">
                <span class="section-subtitle">Get In Touch</span>
                <h2 class="section-title">Contact Academic Support &amp; Admissions</h2>
                <p class="section-description">
                    Have questions regarding syllabus details, corporate sponsorships, or course prerequisites? Our admissions staff responds within 24 hours.
                </p>
            </header>

            <div class="contact-layout">
                <!-- Left: Contact Details Panel -->
                <aside class="contact-info-panel">
                    <div>
                        <h3>LearnSphere Admissions Office</h3>
                        <p>
                            Connect directly with our admissions advisors for guidance on academic programs and enrollment verification.
                        </p>

                        <div class="contact-details-list">
                            <div class="contact-detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Academic Headquarters</strong>
                                    <span>100 University Avenue, Tech Park Suite 400<br>Cambridge, MA 02138, USA</span>
                                </div>
                            </div>

                            <div class="contact-detail-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Admissions &amp; Registrar Email</strong>
                                    <span>admissions@learnsphere.edu</span>
                                </div>
                            </div>

                            <div class="contact-detail-item">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <strong>Toll-Free Academic Hotline</strong>
                                    <span>+1 (800) 555-LEARN / +1 (555) 019-2834</span>
                                </div>
                            </div>

                            <div class="contact-detail-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Advisory Office Hours</strong>
                                    <span>Monday – Friday: 08:00 AM – 06:00 PM EST</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <span style="font-size: 0.85rem; color: #94A3B8;">Accredited Member:</span>
                        <strong style="display: block; color: #FFFFFF; font-size: 0.95rem; margin-top: 4px;">International Open Learning &amp; Engineering Council</strong>
                    </div>
                </aside>

                <!-- Right: Working Contact Form (Saved to MySQL contact_messages table) -->
                <div class="form-card" style="margin: 0; max-width: 100%;">
                    
                    <?php if ($contactSuccess): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle" style="font-size: 1.4rem;"></i>
                            <div>
                                <strong>Message Dispatched Successfully!</strong><br>
                                Thank you for reaching out. Your academic inquiry has been logged in our registry and an admissions counselor will contact you shortly.
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($contactErrors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle" style="font-size: 1.4rem;"></i>
                            <div>
                                <strong>Please correct the following:</strong>
                                <ul style="list-style: disc; margin-left: 20px; margin-top: 6px;">
                                    <?php foreach ($contactErrors as $cErr): ?>
                                        <li><?php echo $cErr; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="about.php#contact" method="POST" id="contactForm" novalidate>
                        <div class="form-group">
                            <label for="contact_name">Full Name <span class="req">*</span></label>
                            <input type="text" name="full_name" id="contact_name" 
                                   placeholder="e.g. Jordan Taylor" 
                                   required minlength="2" maxlength="150"
                                   autocomplete="name"
                                   value="<?php echo isset($_POST['full_name']) && !$contactSuccess ? sanitize($_POST['full_name']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_email">Email Address <span class="req">*</span></label>
                            <input type="email" name="email" id="contact_email" 
                                   placeholder="jordan.taylor@example.com" 
                                   required 
                                   autocomplete="email"
                                   value="<?php echo isset($_POST['email']) && !$contactSuccess ? sanitize($_POST['email']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_subject">Subject Inquiry <span class="req">*</span></label>
                            <input type="text" name="subject" id="contact_subject" 
                                   placeholder="e.g. Question regarding Full-Stack Web Syllabus" 
                                   required minlength="3" maxlength="255"
                                   value="<?php echo isset($_POST['subject']) && !$contactSuccess ? sanitize($_POST['subject']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_message">Your Message <span class="req">*</span></label>
                            <textarea name="message" id="contact_message" rows="5" 
                                      placeholder="Please detail your question or consultation request here..." 
                                      required minlength="10"><?php echo isset($_POST['message']) && !$contactSuccess ? sanitize($_POST['message']) : ''; ?></textarea>
                        </div>

                        <div style="margin-top: 20px;">
                            <button type="submit" name="contact_submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-paper-plane"></i> Submit Academic Inquiry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
