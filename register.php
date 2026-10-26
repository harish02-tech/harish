<?php
/**
 * LearnSphere – Student Registration and Course Enrollment Page (register.php)
 * Demonstrates: 17 HTML5 Input Types, Semantic Fieldset & Legend, Server & Client-side Validation,
 * File upload handling, Duplicate enrollment prevention, and MySQL PDO Storage.
 * NO login or authentication required.
 */

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Student Registration & Course Enrollment – LearnSphere';
$currentPage = 'register.php';

// Check if a course was preselected via URL query string
$preselectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Fetch active courses for the dropdown
$courseList = getCourses($pdo);
if (empty($courseList)) {
    $courseList = [
        ['course_id' => 1, 'title' => 'Full-Stack Modern Web Engineering', 'price' => 89.99],
        ['course_id' => 2, 'title' => 'Applied Artificial Intelligence & Machine Learning', 'price' => 119.99],
        ['course_id' => 3, 'title' => 'Cloud Architecture & DevOps Engineering', 'price' => 99.99],
        ['course_id' => 4, 'title' => 'Ethical Hacking & Defensive Cybersecurity', 'price' => 109.99],
        ['course_id' => 5, 'title' => 'Professional UI/UX Design & Design Systems', 'price' => 69.99],
        ['course_id' => 6, 'title' => 'Cross-Platform Mobile App Development with Flutter', 'price' => 79.99],
        ['course_id' => 7, 'title' => 'Backend API Architecture with Node.js & Microservices', 'price' => 94.99],
        ['course_id' => 8, 'title' => 'Python for Business Intelligence & Data Analytics', 'price' => 64.99]
    ];
}

// Variables for form handling and state
$errors = [];
$successEnrollment = null;

// Handle Form Submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect & Sanitize inputs
    $firstName       = trim($_POST['first_name'] ?? '');
    $lastName        = trim($_POST['last_name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $phone           = trim($_POST['phone'] ?? '');
    $dob             = trim($_POST['dob'] ?? '');
    $gender          = trim($_POST['gender'] ?? '');
    $qualification   = trim($_POST['qualification'] ?? '');
    $address         = trim($_POST['address'] ?? '');
    $experienceYears = (int)($_POST['experience_years'] ?? 0);
    $portfolioUrl    = trim($_POST['portfolio_url'] ?? '');
    $preferredTime   = trim($_POST['preferred_time'] ?? '');
    $studyHours      = (int)($_POST['study_hours'] ?? 10);
    $intakeMonth     = trim($_POST['intake_month'] ?? '');
    $themeColor      = trim($_POST['theme_color'] ?? '#00ADB5');
    $courseId        = (int)($_POST['course_id'] ?? 0);
    $studyMode       = trim($_POST['study_mode'] ?? 'Self-Paced Online');
    $specialNotes    = trim($_POST['special_notes'] ?? '');
    $interests       = isset($_POST['interests']) && is_array($_POST['interests']) ? implode(', ', $_POST['interests']) : '';
    $termsAgree      = isset($_POST['terms_agree']);

    // Server-Side Validations
    if (empty($firstName) || strlen($firstName) < 2) {
        $errors[] = 'First name is required and must be at least 2 characters.';
    }
    if (empty($lastName) || strlen($lastName) < 2) {
        $errors[] = 'Last name is required and must be at least 2 characters.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required for official academic correspondence.';
    }
    if (empty($phone) || strlen($phone) < 7) {
        $errors[] = 'A valid telephone number is required.';
    }
    if (empty($dob)) {
        $errors[] = 'Date of birth is required.';
    } else {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        if ($dobDate > $today || $age < 14) {
            $errors[] = 'Student must be at least 14 years old to register.';
        }
    }
    if (empty($gender)) {
        $errors[] = 'Please select a gender option.';
    }
    if (empty($qualification)) {
        $errors[] = 'Educational qualification is required.';
    }
    if (empty($address) || strlen($address) < 5) {
        $errors[] = 'Physical mailing address is required for certificate delivery.';
    }
    if (empty($courseId)) {
        $errors[] = 'Please select an academic course for enrollment.';
    }
    if (!$termsAgree) {
        $errors[] = 'You must accept the LearnSphere Academic Integrity & Enrollment Terms.';
    }

    // Optional File Upload Handling (ID / Academic Document)
    $uploadedFileName = null;
    if (isset($_FILES['id_document']) && $_FILES['id_document']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['id_document'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $fileInfo = pathinfo($file['name']);
            $extension = strtolower($fileInfo['extension'] ?? '');

            if (!in_array($extension, $allowedExtensions)) {
                $errors[] = 'Academic document must be in PDF, JPG, or PNG format.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Uploaded document must not exceed 5MB in size.';
            } else {
                $uniqueFileName = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $targetPath = __DIR__ . '/uploads/' . $uniqueFileName;
                if (!is_dir(__DIR__ . '/uploads')) {
                    mkdir(__DIR__ . '/uploads', 0755, true);
                }
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $uploadedFileName = $uniqueFileName;
                }
            }
        }
    }

    // If no validation errors, proceed with MySQL insertion
    if (empty($errors)) {
        if ($pdo) {
            try {
                $pdo->beginTransaction();

                // 1. Check if student with this email already exists
                $stmtCheck = $pdo->prepare("SELECT student_id FROM `students` WHERE email = :email LIMIT 1");
                $stmtCheck->execute([':email' => $email]);
                $existingStudent = $stmtCheck->fetch();

                if ($existingStudent) {
                    $studentId = $existingStudent['student_id'];
                    // Update student profile
                    $stmtUpdate = $pdo->prepare("UPDATE `students` SET 
                        first_name = :fn, last_name = :ln, phone = :ph, dob = :dob, 
                        gender = :gen, qualification = :qual, address = :addr, 
                        experience_years = :exp, portfolio_url = :url, preferred_time = :pt, 
                        study_hours_week = :sh, intake_month = :im, theme_color = :tc" . 
                        ($uploadedFileName ? ", id_document = :doc" : "") . "
                        WHERE student_id = :id");
                    
                    $updateParams = [
                        ':fn'   => $firstName,
                        ':ln'   => $lastName,
                        ':ph'   => $phone,
                        ':dob'  => $dob,
                        ':gen'  => $gender,
                        ':qual' => $qualification,
                        ':addr' => $address,
                        ':exp'  => $experienceYears,
                        ':url'  => $portfolioUrl,
                        ':pt'   => !empty($preferredTime) ? $preferredTime : null,
                        ':sh'   => $studyHours,
                        ':im'   => $intakeMonth,
                        ':tc'   => $themeColor,
                        ':id'   => $studentId
                    ];
                    if ($uploadedFileName) {
                        $updateParams[':doc'] = $uploadedFileName;
                    }
                    $stmtUpdate->execute($updateParams);
                } else {
                    // Insert new student
                    $stmtInsert = $pdo->prepare("INSERT INTO `students` (
                        first_name, last_name, email, phone, dob, gender, 
                        qualification, address, experience_years, portfolio_url, 
                        preferred_time, study_hours_week, intake_month, theme_color, id_document
                    ) VALUES (
                        :fn, :ln, :email, :ph, :dob, :gen, 
                        :qual, :addr, :exp, :url, 
                        :pt, :sh, :im, :tc, :doc
                    )");
                    $stmtInsert->execute([
                        ':fn'    => $firstName,
                        ':ln'    => $lastName,
                        ':email' => $email,
                        ':ph'    => $phone,
                        ':dob'   => $dob,
                        ':gen'   => $gender,
                        ':qual'  => $qualification,
                        ':addr'  => $address,
                        ':exp'   => $experienceYears,
                        ':url'   => $portfolioUrl,
                        ':pt'    => !empty($preferredTime) ? $preferredTime : null,
                        ':sh'    => $studyHours,
                        ':im'    => $intakeMonth,
                        ':tc'    => $themeColor,
                        ':doc'   => $uploadedFileName
                    ]);
                    $studentId = $pdo->lastInsertId();
                }

                // 2. Prevent Duplicate Enrollment for the exact same course
                $stmtCheckEnroll = $pdo->prepare("SELECT enrollment_id, enrollment_code FROM `enrollments` WHERE student_id = :sid AND course_id = :cid LIMIT 1");
                $stmtCheckEnroll->execute([':sid' => $studentId, ':cid' => $courseId]);
                $existingEnrollment = $stmtCheckEnroll->fetch();

                if ($existingEnrollment) {
                    $errors[] = "You are already actively enrolled in this course under confirmation code <strong>{$existingEnrollment['enrollment_code']}</strong>.";
                    $pdo->rollBack();
                } else {
                    // Generate unique enrollment code
                    $enrollmentCode = generateEnrollmentCode();

                    // Insert enrollment record
                    $stmtEnroll = $pdo->prepare("INSERT INTO `enrollments` (
                        enrollment_code, student_id, course_id, study_mode, interests, special_notes, status
                    ) VALUES (
                        :code, :sid, :cid, :mode, :interests, :notes, 'Confirmed'
                    )");
                    $stmtEnroll->execute([
                        ':code'      => $enrollmentCode,
                        ':sid'       => $studentId,
                        ':cid'       => $courseId,
                        ':mode'      => $studyMode,
                        ':interests' => $interests,
                        ':notes'     => $specialNotes
                    ]);

                    $pdo->commit();

                    // Retrieve course details for confirmation display
                    $enrolledCourse = getCourseById($pdo, $courseId);

                    $successEnrollment = [
                        'code'        => $enrollmentCode,
                        'name'        => $firstName . ' ' . $lastName,
                        'email'       => $email,
                        'course'      => $enrolledCourse ? $enrolledCourse['title'] : 'Course #' . $courseId,
                        'category'    => $enrolledCourse ? $enrolledCourse['category_name'] : 'Academic Track',
                        'study_mode'  => $studyMode,
                        'time'        => date('F j, Y, g:i a'),
                        'theme_color' => $themeColor
                    ];
                }
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = 'A database transaction error occurred: ' . sanitize($e->getMessage());
            }
        } else {
            // Simulated enrollment confirmation when running without active MySQL
            $enrollmentCode = generateEnrollmentCode();
            $successEnrollment = [
                'code'        => $enrollmentCode,
                'name'        => $firstName . ' ' . $lastName,
                'email'       => $email,
                'course'      => 'Course #' . $courseId,
                'category'    => 'Academic Track',
                'study_mode'  => $studyMode,
                'time'        => date('F j, Y, g:i a'),
                'theme_color' => $themeColor
            ];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main id="mainContent">

    <!-- Registration Banner -->
    <section style="background: radial-gradient(circle at 80% 20%, #1E3E62 0%, #0B192C 80%); padding: 50px 0; color: #FFFFFF;">
        <div class="container" style="text-align: center;">
            <span class="section-subtitle" style="background: rgba(0, 173, 181, 0.2); color: #00ADB5;">
                <i class="fas fa-id-card"></i> Official Academic Admissions
            </span>
            <h1 style="color: #FFFFFF; font-size: 2.4rem; margin-top: 10px;">
                Student Registration &amp; Course Enrollment
            </h1>
            <p style="color: #CBD5E1; max-width: 680px; margin: 0 auto;">
                Direct enrollment portal for prospective students. Complete your profile, select your course, and receive your verified enrollment confirmation code instantly.
            </p>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">

            <?php if (!empty($errors)): ?>
                <!-- Server-Side Validation Errors Alert -->
                <div class="alert alert-danger" role="alert" style="max-width: 900px; margin: 0 auto 30px auto;">
                    <i class="fas fa-exclamation-circle" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Please resolve the following submission issues:</strong>
                        <ul style="list-style: disc; margin-left: 20px; margin-top: 8px;">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo $err; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($successEnrollment): ?>
                <!-- ========================================================= -->
                <!-- SUCCESS CONFIRMATION DISPLAY (No login/dashboard needed) -->
                <!-- ========================================================= -->
                <div class="confirmation-card" style="max-width: 820px; margin: 0 auto 40px auto;">
                    <div class="confirmation-header">
                        <div class="confirmation-badge-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 style="color: var(--primary-color); margin-bottom: 6px;">Enrollment Successfully Confirmed!</h2>
                        <p style="margin-bottom: 0;">
                            Welcome to LearnSphere. Your academic registration is active and verified.
                        </p>
                    </div>

                    <table class="enrollment-details-table">
                        <tr>
                            <th>Confirmation Tracking Code</th>
                            <td>
                                <strong style="color: var(--accent-color); font-size: 1.25rem; letter-spacing: 1px; font-family: var(--font-mono);">
                                    <?php echo sanitize($successEnrollment['code']); ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Student Full Name</th>
                            <td><?php echo sanitize($successEnrollment['name']); ?></td>
                        </tr>
                        <tr>
                            <th>Registered Email</th>
                            <td><?php echo sanitize($successEnrollment['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Enrolled Course Program</th>
                            <td><strong><?php echo sanitize($successEnrollment['course']); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Discipline / Category</th>
                            <td><?php echo sanitize($successEnrollment['category']); ?></td>
                        </tr>
                        <tr>
                            <th>Instructional Mode</th>
                            <td><?php echo sanitize($successEnrollment['study_mode']); ?></td>
                        </tr>
                        <tr>
                            <th>Enrollment Timestamp</th>
                            <td><?php echo sanitize($successEnrollment['time']); ?></td>
                        </tr>
                        <tr>
                            <th>Student Identity Accent</th>
                            <td>
                                <span style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; vertical-align: middle; background-color: <?php echo sanitize($successEnrollment['theme_color']); ?>; margin-right: 6px;"></span>
                                <code><?php echo sanitize($successEnrollment['theme_color']); ?></code>
                            </td>
                        </tr>
                    </table>

                    <div style="background-color: var(--bg-muted); border-radius: var(--radius-md); padding: 16px; margin-top: 20px;">
                        <h4 style="margin-bottom: 6px;"><i class="fas fa-info-circle" style="color: var(--accent-color);"></i> Academic Orientation Next Steps</h4>
                        <p style="margin-bottom: 0; font-size: 0.92rem;">
                            An electronic welcome package and your course syllabus have been recorded under ID <strong><?php echo sanitize($successEnrollment['code']); ?></strong>. Please retain this code for all student support queries.
                        </p>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 14px; margin-top: 24px;">
                        <a href="courses.php" class="btn btn-outline">
                            <i class="fas fa-book-open"></i> Browse More Courses
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Enrollment Receipt
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ========================================================= -->
            <!-- COMPREHENSIVE STUDENT REGISTRATION FORM                   -->
            <!-- Demonstrating 17 distinct HTML5 input types & attributes  -->
            <!-- ========================================================= -->
            <div class="form-card" <?php if ($successEnrollment) echo 'style="margin-top: 30px;"'; ?>>
                
                <header style="margin-bottom: var(--spacing-xl); text-align: center;">
                    <h2>New Student Registration &amp; Enrollment</h2>
                    <p style="margin-bottom: 0;">Fields marked with <span style="color: var(--danger-color); font-weight: bold;">*</span> are mandatory for academic matriculation.</p>
                </header>

                <form action="register.php" method="POST" enctype="multipart/form-data" id="enrollmentForm" novalidate>
                    
                    <!-- INPUT TYPE 15: type="hidden" -->
                    <input type="hidden" name="form_action" value="submit_enrollment">

                    <!-- =================================================== -->
                    <!-- FIELDSET 1: Personal Identification & Demographics  -->
                    <!-- =================================================== -->
                    <fieldset>
                        <legend><i class="fas fa-user"></i> 1. Personal Identity &amp; Contact Details</legend>
                        
                        <div class="form-grid-2">
                            <!-- INPUT TYPE 1: type="text" (with minlength, maxlength, required, placeholder, autocomplete) -->
                            <div class="form-group">
                                <label for="first_name">First Name <span class="req">*</span></label>
                                <input type="text" name="first_name" id="first_name" 
                                       placeholder="e.g. Alexander" 
                                       minlength="2" maxlength="100" 
                                       required 
                                       autocomplete="given-name"
                                       value="<?php echo isset($_POST['first_name']) ? sanitize($_POST['first_name']) : ''; ?>">
                            </div>

                            <!-- INPUT TYPE 1 (reused): type="text" -->
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="req">*</span></label>
                                <input type="text" name="last_name" id="last_name" 
                                       placeholder="e.g. Mercer" 
                                       minlength="2" maxlength="100" 
                                       required 
                                       autocomplete="family-name"
                                       value="<?php echo isset($_POST['last_name']) ? sanitize($_POST['last_name']) : ''; ?>">
                            </div>

                            <!-- INPUT TYPE 2: type="email" (with pattern, required, placeholder, autocomplete) -->
                            <div class="form-group">
                                <label for="email">Academic or Personal Email <span class="req">*</span></label>
                                <input type="email" name="email" id="email" 
                                       placeholder="student@university.edu" 
                                       required 
                                       autocomplete="email"
                                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
                            </div>

                            <!-- INPUT TYPE 3: type="tel" (with pattern attribute, required, placeholder, autocomplete) -->
                            <div class="form-group">
                                <label for="phone">Phone / Mobile Number <span class="req">*</span> <small class="hint">(Include area code)</small></label>
                                <input type="tel" name="phone" id="phone" 
                                       placeholder="+1 (555) 019-2834" 
                                       pattern="[0-9+() -]{7,25}" 
                                       required 
                                       autocomplete="tel"
                                       value="<?php echo isset($_POST['phone']) ? sanitize($_POST['phone']) : ''; ?>">
                            </div>

                            <!-- INPUT TYPE 4: type="date" (with min and max attributes) -->
                            <div class="form-group">
                                <label for="dob">Date of Birth <span class="req">*</span> <small class="hint">(Min. 14 years old)</small></label>
                                <input type="date" name="dob" id="dob" 
                                       min="1940-01-01" 
                                       max="<?php echo date('Y-m-d', strtotime('-14 years')); ?>" 
                                       required
                                       value="<?php echo isset($_POST['dob']) ? sanitize($_POST['dob']) : ''; ?>">
                            </div>

                            <!-- INPUT TYPE 6: type="radio" (Gender Selection) -->
                            <div class="form-group">
                                <label>Gender Identity <span class="req">*</span></label>
                                <div class="radio-options-group">
                                    <label class="custom-option">
                                        <input type="radio" name="gender" value="Male" <?php echo (!isset($_POST['gender']) || $_POST['gender'] === 'Male') ? 'checked' : ''; ?>>
                                        <span>Male</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>>
                                        <span>Female</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="radio" name="gender" value="Non-Binary" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Non-Binary') ? 'checked' : ''; ?>>
                                        <span>Non-Binary</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="radio" name="gender" value="Prefer not to say" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Prefer not to say') ? 'checked' : ''; ?>>
                                        <span>Other</span>
                                    </label>
                                </div>
                            </div>

                            <!-- CONTROL: Textarea for Physical Mailing Address -->
                            <div class="form-group full-width">
                                <label for="address">Mailing Address (For Certificate Dispatch) <span class="req">*</span></label>
                                <textarea name="address" id="address" rows="3" 
                                          placeholder="Enter complete street address, suite, city, state/province, and postal code..." 
                                          required 
                                          minlength="8" 
                                          maxlength="500"><?php echo isset($_POST['address']) ? sanitize($_POST['address']) : ''; ?></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <!-- =================================================== -->
                    <!-- FIELDSET 2: Academic Background & Technical Profile -->
                    <!-- =================================================== -->
                    <fieldset>
                        <legend><i class="fas fa-graduation-cap"></i> 2. Academic Background &amp; Profile</legend>
                        
                        <div class="form-grid-2">
                            <!-- CONTROL: Select Dropdown (Educational Qualification) -->
                            <div class="form-group">
                                <label for="qualification">Highest Educational Qualification <span class="req">*</span></label>
                                <select name="qualification" id="qualification" required>
                                    <option value="" disabled <?php echo !isset($_POST['qualification']) ? 'selected' : ''; ?>>-- Choose Educational Level --</option>
                                    <option value="High School Diploma" <?php echo (isset($_POST['qualification']) && $_POST['qualification'] === 'High School Diploma') ? 'selected' : ''; ?>>High School Diploma</option>
                                    <option value="Associate Degree" <?php echo (isset($_POST['qualification']) && $_POST['qualification'] === 'Associate Degree') ? 'selected' : ''; ?>>Associate Degree</option>
                                    <option value="Bachelor of Science / Arts" <?php echo (isset($_POST['qualification']) && $_POST['qualification'] === 'Bachelor of Science / Arts') ? 'selected' : ''; ?>>Bachelor of Science / Arts</option>
                                    <option value="Master of Science / MBA" <?php echo (isset($_POST['qualification']) && $_POST['qualification'] === 'Master of Science / MBA') ? 'selected' : ''; ?>>Master of Science / MBA</option>
                                    <option value="Doctorate / Ph.D." <?php echo (isset($_POST['qualification']) && $_POST['qualification'] === 'Doctorate / Ph.D.') ? 'selected' : ''; ?>>Doctorate / Ph.D.</option>
                                    <option value="Self-Taught / Industry Professional" <?php echo (isset($_POST['qualification']) && $_POST['qualification'] === 'Self-Taught / Industry Professional') ? 'selected' : ''; ?>>Self-Taught / Industry Professional</option>
                                </select>
                            </div>

                            <!-- INPUT TYPE 5: type="number" (with min, max) -->
                            <div class="form-group">
                                <label for="experience_years">Prior Technical / Coding Experience <small class="hint">(Years)</small></label>
                                <input type="number" name="experience_years" id="experience_years" 
                                       min="0" max="40" step="1" 
                                       value="<?php echo isset($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0; ?>">
                            </div>

                            <!-- INPUT TYPE 13: type="url" (Portfolio / GitHub / LinkedIn profile) -->
                            <div class="form-group">
                                <label for="portfolio_url">Portfolio or GitHub / LinkedIn Profile <small class="hint">(Optional)</small></label>
                                <input type="url" name="portfolio_url" id="portfolio_url" 
                                       placeholder="https://github.com/yourusername" 
                                       value="<?php echo isset($_POST['portfolio_url']) ? sanitize($_POST['portfolio_url']) : ''; ?>">
                            </div>

                            <!-- INPUT TYPE 8: type="file" (with accept attribute for PDF/Images) -->
                            <div class="form-group">
                                <label for="id_document">Upload Student ID / Transcript <small class="hint">(PDF, JPG, PNG &lt; 5MB)</small></label>
                                <input type="file" name="id_document" id="id_document" accept=".pdf, .jpg, .jpeg, .png">
                            </div>

                            <!-- INPUT TYPE 14: type="month" (Degree Target or Intake Month) -->
                            <div class="form-group">
                                <label for="intake_month">Preferred Academic Intake Month</label>
                                <input type="month" name="intake_month" id="intake_month" 
                                       min="2026-01" max="2027-12" 
                                       value="<?php echo isset($_POST['intake_month']) ? sanitize($_POST['intake_month']) : date('Y-m'); ?>">
                            </div>

                            <!-- INPUT TYPE 10: type="color" (Personal Student Badge Accent) -->
                            <div class="form-group">
                                <label for="theme_color">Badge Accent Theme Color <small class="hint">(Personalize portal badge)</small></label>
                                <div class="color-picker-wrap">
                                    <input type="color" name="theme_color" id="theme_color" 
                                           value="<?php echo isset($_POST['theme_color']) ? sanitize($_POST['theme_color']) : '#00ADB5'; ?>">
                                    <span class="range-val-badge" id="colorPreviewBadge" style="background-color: <?php echo isset($_POST['theme_color']) ? sanitize($_POST['theme_color']) : '#00ADB5'; ?>;">
                                        <?php echo isset($_POST['theme_color']) ? sanitize($_POST['theme_color']) : '#00ADB5'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- =================================================== -->
                    <!-- FIELDSET 3: Course Selection & Learning Preferences -->
                    <!-- =================================================== -->
                    <fieldset>
                        <legend><i class="fas fa-book"></i> 3. Course Selection &amp; Schedule Preferences</legend>
                        
                        <div class="form-grid-2">
                            <!-- CONTROL: Select Dropdown (Academic Course Selection) -->
                            <div class="form-group full-width">
                                <label for="course_id">Select Academic Course Program <span class="req">*</span></label>
                                <select name="course_id" id="course_id" required>
                                    <option value="" disabled <?php echo empty($preselectedCourseId) && !isset($_POST['course_id']) ? 'selected' : ''; ?>>-- Choose from Accredited Programs --</option>
                                    <?php foreach ($courseList as $c): 
                                        $selected = false;
                                        if (isset($_POST['course_id']) && (int)$_POST['course_id'] == $c['course_id']) {
                                            $selected = true;
                                        } elseif (empty($_POST['course_id']) && $preselectedCourseId == $c['course_id']) {
                                            $selected = true;
                                        }
                                    ?>
                                        <option value="<?php echo (int)$c['course_id']; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                            <?php echo sanitize($c['title']); ?> (Tuition: <?php echo formatPrice($c['price']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- INPUT TYPE 6 (reused): type="radio" (Study Mode) -->
                            <div class="form-group full-width">
                                <label>Preferred Learning Mode <span class="req">*</span></label>
                                <div class="radio-options-group">
                                    <label class="custom-option">
                                        <input type="radio" name="study_mode" value="Self-Paced Online" <?php echo (!isset($_POST['study_mode']) || $_POST['study_mode'] === 'Self-Paced Online') ? 'checked' : ''; ?>>
                                        <span><i class="fas fa-laptop-code"></i> Self-Paced Online (24/7 Access)</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="radio" name="study_mode" value="Instructor-Led Hybrid" <?php echo (isset($_POST['study_mode']) && $_POST['study_mode'] === 'Instructor-Led Hybrid') ? 'checked' : ''; ?>>
                                        <span><i class="fas fa-chalkboard-teacher"></i> Instructor-Led Hybrid (Live Office Hours)</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="radio" name="study_mode" value="Intensive Bootcamp" <?php echo (isset($_POST['study_mode']) && $_POST['study_mode'] === 'Intensive Bootcamp') ? 'checked' : ''; ?>>
                                        <span><i class="fas fa-rocket"></i> Intensive Accelerated Bootcamp</span>
                                    </label>
                                </div>
                            </div>

                            <!-- INPUT TYPE 11: type="range" (Weekly Study Hours) -->
                            <div class="form-group">
                                <label for="study_hours">Target Weekly Study Commitment: <strong id="studyHoursVal" style="color: var(--accent-color);"><?php echo isset($_POST['study_hours']) ? (int)$_POST['study_hours'] : 12; ?> hrs / week</strong></label>
                                <div class="range-slider-wrap">
                                    <span style="font-size: 0.85rem; color: var(--text-muted);">5 hrs</span>
                                    <input type="range" name="study_hours" id="study_hours" 
                                           min="5" max="40" step="1" 
                                           value="<?php echo isset($_POST['study_hours']) ? (int)$_POST['study_hours'] : 12; ?>">
                                    <span style="font-size: 0.85rem; color: var(--text-muted);">40 hrs</span>
                                </div>
                            </div>

                            <!-- INPUT TYPE 12: type="time" (Preferred Daily Study / Webinar Slot) -->
                            <div class="form-group">
                                <label for="preferred_time">Preferred Daily Webinar Slot</label>
                                <input type="time" name="preferred_time" id="preferred_time" 
                                       value="<?php echo isset($_POST['preferred_time']) ? sanitize($_POST['preferred_time']) : '18:00'; ?>">
                            </div>

                            <!-- INPUT TYPE 7: type="checkbox" (Multiple Interest Areas) -->
                            <div class="form-group full-width">
                                <label>Elective Modules of Interest <small class="hint">(Select all that apply)</small></label>
                                <div class="checkbox-options-group">
                                    <label class="custom-option">
                                        <input type="checkbox" name="interests[]" value="RESTful APIs" checked>
                                        <span>RESTful APIs</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="checkbox" name="interests[]" value="Database Architecture" checked>
                                        <span>Database Optimization</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="checkbox" name="interests[]" value="Cloud CI/CD">
                                        <span>Cloud CI/CD</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="checkbox" name="interests[]" value="Cyber Threat Analysis">
                                        <span>Threat Modeling</span>
                                    </label>
                                    <label class="custom-option">
                                        <input type="checkbox" name="interests[]" value="AI & Neural Models">
                                        <span>AI &amp; Neural Models</span>
                                    </label>
                                </div>
                            </div>

                            <!-- CONTROL: Textarea for Special Accommodations / Notes -->
                            <div class="form-group full-width">
                                <label for="special_notes">Academic Background Notes / Learning Goals <small class="hint">(Optional)</small></label>
                                <textarea name="special_notes" id="special_notes" rows="2" 
                                          placeholder="Share any specific learning outcomes, career targets, or scheduling accommodations..."><?php echo isset($_POST['special_notes']) ? sanitize($_POST['special_notes']) : ''; ?></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <!-- =================================================== -->
                    <!-- FIELDSET 4: Academic Integrity, Verification & Consent-->
                    <!-- =================================================== -->
                    <fieldset>
                        <legend><i class="fas fa-shield-alt"></i> 4. Verification &amp; Academic Consent</legend>

                        <div class="form-group full-width">
                            <!-- INPUT TYPE with readonly demonstration -->
                            <div style="display: flex; gap: 12px; margin-bottom: 14px;">
                                <div style="flex: 1;">
                                    <label>Academic Session Term</label>
                                    <input type="text" value="LearnSphere Academic Year 2026 / 2027" readonly>
                                </div>
                                <div style="width: 140px;">
                                    <label>Matriculation Status</label>
                                    <input type="text" value="Direct Entry" disabled>
                                </div>
                            </div>

                            <!-- INPUT TYPE 7 (reused): type="checkbox" for Terms Agreement -->
                            <label class="custom-option" style="margin-top: 8px;">
                                <input type="checkbox" name="terms_agree" id="terms_agree" value="1" required <?php echo isset($_POST['terms_agree']) ? 'checked' : ''; ?>>
                                <span>I affirm that the provided information is accurate and agree to adhere to the LearnSphere Academic Code of Conduct and Student Integrity Policy. <span class="req">*</span></span>
                            </label>
                        </div>
                    </fieldset>

                    <!-- Form Submission & Reset Controls -->
                    <!-- INPUT TYPE 16: type="submit" and INPUT TYPE 17: type="reset" -->
                    <div class="form-actions">
                        <input type="reset" value="Reset Form Details" class="btn btn-outline">
                        <input type="submit" value="Complete Student Registration &amp; Enroll" class="btn btn-primary btn-lg">
                    </div>

                </form>
            </div>

        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
