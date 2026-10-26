# LearnSphere – Online Course Enrollment Portal

![LearnSphere Logo](assets/images/logo.svg)

> **LearnSphere** is an online academic course enrollment portal built using **HTML5, CSS3, JavaScript, PHP 8, and MySQL**. It offers students a direct enrollment pipeline into accredited computer science and engineering programs without the friction of passwords or student dashboard logins.

---

## 🌟 Academic & Technical Highlights

- **5 Dedicated Pages**:
  1. [`index.php`](file:///d:/WDD%20PROJECT/index.php) – Home portal with hero section, portal metrics, popular disciplines, and featured courses.
  2. [`courses.php`](file:///d:/WDD%20PROJECT/courses.php) – Courses catalog featuring real-time keyword search, category filters, and responsive cards.
  3. [`course-details.php`](file:///d:/WDD%20PROJECT/course-details.php) – Deep syllabus breakdown, learning outcomes, instructor credentials, and sticky enrollment CTA.
  4. [`register.php`](file:///d:/WDD%20PROJECT/register.php) – Academic registration and course enrollment form with **17 HTML5 input types**, client/server validation, and instant confirmation receipt.
  5. [`about.php`](file:///d:/WDD%20PROJECT/about.php) – Mission statement, academic faculty showcase, and working MySQL contact inquiry form.
- **Modern UI/UX Design System**:
  - **Primary Palette**: Deep Navy Blue (`#0B192C` / `#1E3E62`).
  - **Accent Colors**: High-contrast Teal & Cyan (`#00ADB5` / `#0284C7`).
  - **Background**: Clean White (`#FFFFFF`) and Light Slate (`#F8FAFC`).
  - **Typography**: Crisp Google Font `'Poppins', sans-serif`.
  - **Scalable Vector Graphics**: Custom SVG illustrations for every course, hero section, and lead instructors.
- **Enterprise Security & Reliability**:
  - Parameterized MySQL queries via **PDO prepared statements** (100% SQL Injection prevention).
  - Sanitization against Cross-Site Scripting (XSS) via `htmlspecialchars()` escaping.
  - Duplicate enrollment detection preventing multiple enrollments for the same student/course pair.
  - Safe file upload handling for academic ID / certificate attachments.

---

## 📋 Academic Concepts Compliance Matrix

### 1. Semantic HTML5 Elements
| Concept | Implementation Location |
| :--- | :--- |
| `<header>` | Site navigation bar in [`includes/header.php`](file:///d:/WDD%20PROJECT/includes/header.php) and card headers |
| `<nav>` | Main navigation with ARIA label in [`includes/header.php`](file:///d:/WDD%20PROJECT/includes/header.php) |
| `<main>` | Core content containers in all 5 pages |
| `<section>` | Hero, metrics, categories, featured, catalog, mission, and contact sections |
| `<article>` | Individual course cards, metric boxes, and instructor cards |
| `<aside>` | Sticky enrollment sidebar in [`course-details.php`](file:///d:/WDD%20PROJECT/course-details.php) & contact panel in [`about.php`](file:///d:/WDD%20PROJECT/about.php) |
| `<footer>` | Comprehensive multi-column sitemap in [`includes/footer.php`](file:///d:/WDD%20PROJECT/includes/footer.php) |
| `<figure>`, `<figcaption>` | Technical blueprint figure in [`course-details.php`](file:///d:/WDD%20PROJECT/course-details.php) |
| `<form>`, `<fieldset>`, `<legend>` | Grouped form controls in [`register.php`](file:///d:/WDD%20PROJECT/register.php) and [`about.php`](file:///d:/WDD%20PROJECT/about.php) |
| `<mark>` | Search highlight badge in [`courses.php`](file:///d:/WDD%20PROJECT/courses.php) |

### 2. At Least 15 HTML Input Types Demonstrated
| # | Input Type | Usage in Portal | Location |
| :--- | :--- | :--- | :--- |
| 1 | `type="text"` | First Name, Last Name, Subject | [`register.php`](file:///d:/WDD%20PROJECT/register.php), [`about.php`](file:///d:/WDD%20PROJECT/about.php) |
| 2 | `type="email"` | Student Email & Contact Email | [`register.php`](file:///d:/WDD%20PROJECT/register.php), [`about.php`](file:///d:/WDD%20PROJECT/about.php) |
| 3 | `type="tel"` | Contact Telephone Number | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 4 | `type="date"` | Date of Birth (min/max age check) | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 5 | `type="number"` | Prior Technical Experience (Years) | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 6 | `type="radio"` | Gender & Learning Mode selection | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 7 | `type="checkbox"` | Elective Modules & Terms acceptance | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 8 | `type="file"` | Student ID / Academic Document upload | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 9 | `type="search"` | Course catalog keyword search | [`courses.php`](file:///d:/WDD%20PROJECT/courses.php) |
| 10 | `type="color"` | Personal Badge Accent Color Picker | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 11 | `type="range"` | Target Weekly Study Hours slider | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 12 | `type="time"` | Preferred Daily Webinar Time slot | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 13 | `type="url"` | Portfolio / GitHub / LinkedIn URL | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 14 | `type="month"` | Preferred Academic Intake Month | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 15 | `type="hidden"` | Form action token / course reference | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 16 | `type="submit"` | Registration & Enrollment submission | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |
| 17 | `type="reset"` | Form reset control | [`register.php`](file:///d:/WDD%20PROJECT/register.php) |

### 3. HTML Input Attributes Demonstrated
- `required` – Enforced on mandatory inputs (names, email, phone, course, terms).
- `placeholder` – Guiding example text across all input fields.
- `maxlength` & `minlength` – Character bounds on names, subjects, and textareas.
- `pattern` – Regex validation on telephone numbers (`[0-9+() -]{7,25}`).
- `readonly` – Applied to Academic Term display in [`register.php`](file:///d:/WDD%20PROJECT/register.php).
- `disabled` – Applied to Matriculation status and select placeholder options.
- `min` & `max` – Numerical and chronological boundaries for dates, experience, and range sliders.
- `accept` – Constraining file attachments to `.pdf, .jpg, .jpeg, .png`.
- `autocomplete` – Standardized accessibility attributes (`given-name`, `family-name`, `email`, `tel`).

### 4. CSS3 Concepts Demonstrated
- **Inline CSS**: Highlighted stat metrics and custom dynamic theme accents in [`index.php`](file:///d:/WDD%20PROJECT/index.php) and [`register.php`](file:///d:/WDD%20PROJECT/register.php).
- **Internal CSS**: Embedded `<style>` block in [`course-details.php`](file:///d:/WDD%20PROJECT/course-details.php) styling outcome cards, syllabus badge accents, and instructor cards.
- **External CSS**: Full design system in [`assets/css/style.css`](file:///d:/WDD%20PROJECT/assets/css/style.css).
- **Selectors**:
  - Universal (`*`, `*::before`, `*::after`)
  - Element (`body`, `h1`, `h2`, `p`, `a`, `table`, etc.)
  - Class (`.btn-primary`, `.course-card`, etc.)
  - ID (`#hero`, `#enrollmentForm`, `#coursesGrid`, etc.)
  - Grouping (`h1, h2, h3, h4`, `input, select, textarea`)
  - Descendant (`.hero-content h1 span`, `.course-card .course-card-desc`)
  - Attribute (`input[type="text"]`, `input[type="range"]`, `input[readonly]`)
  - Pseudo-classes (`:hover`, `:focus`, `:checked`, `:invalid`, `:nth-child()`, `:not()`)
  - Pseudo-elements (`::before`, `::after`, `::placeholder`)
- **Layout Engines**: CSS Flexbox (navigation, hero actions, footer, buttons) and CSS Grid (course cards, statistics bar, category grid, form fields).
- **Responsive Media Queries**: Breakpoints at `992px` (tablets), `768px` (mobile portrait), and `480px` (smartphones).
- **CSS Variables**: Global `:root` tokens for palette, spacing, radii, and elevations.

---

## 🗄️ Database Architecture (`learnsphere_db`)

The relational database schema is located at [`database/learnsphere_db.sql`](file:///d:/WDD%20PROJECT/database/learnsphere_db.sql).

### Table Overview:
1. **`categories`** – Course disciplinary departments (`category_id`, `name`, `slug`, `icon`, `description`).
2. **`courses`** – Comprehensive academic courses (`course_id`, `category_id`, `title`, `slug`, `short_description`, `full_description`, `syllabus`, `learning_outcomes`, `instructor_name`, `instructor_title`, `duration_weeks`, `difficulty`, `price`, `rating`, `image`, `featured`).
3. **`students`** – Student identity records (`student_id`, `first_name`, `last_name`, `email`, `phone`, `dob`, `gender`, `qualification`, `address`, `experience_years`, `portfolio_url`, `preferred_time`, `study_hours_week`, `intake_month`, `theme_color`, `id_document`).
4. **`enrollments`** – Course registration records (`enrollment_id`, `enrollment_code`, `student_id`, `course_id`, `study_mode`, `interests`, `special_notes`, `status`, `enrolled_at`). Has `UNIQUE(student_id, course_id)` constraint to prevent duplicate enrollments.
5. **`contact_messages`** – Inquiries submitted via admissions contact form (`message_id`, `full_name`, `email`, `subject`, `message`, `status`, `created_at`).

---

## 🚀 Setup & Installation Instructions (XAMPP & phpMyAdmin)

### Step 1: Place the Project in XAMPP `htdocs`
1. Copy or move this folder (`WDD PROJECT`) into your XAMPP installation directory:
   ```text
   C:\xampp\htdocs\learnsphere
   ```
   *(Or keep it at `C:\xampp\htdocs\WDD PROJECT`)*

### Step 2: Start Apache and MySQL in XAMPP
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Both services should display green indicator lights.

### Step 3: Import the Database in phpMyAdmin
1. Open your web browser and navigate to:
   ```text
   http://localhost/phpmyadmin/
   ```
2. Click on the **Import** tab at the top.
3. Click **Choose File** and select:
   ```text
   C:\xampp\htdocs\learnsphere\database\learnsphere_db.sql
   ```
4. Leave all settings at default (`utf-8`, SQL format) and click the **Import** button at the bottom.
5. phpMyAdmin will execute the script, creating the database `learnsphere_db` and populating all sample tables and records.

### Step 4: Verify Database Connection Configuration
Open [`includes/db.php`](file:///d:/WDD%20PROJECT/includes/db.php) and verify the credentials match your local MySQL configuration (standard XAMPP defaults):
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'learnsphere_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 5: Launch the Application
In your browser, visit:
```text
http://localhost/learnsphere/
```
*(Or `http://localhost/WDD%20PROJECT/` depending on your folder name)*

---

## 🧪 Testing Guide

### 1. Home Page Testing (`index.php`)
- Verify that the hero section loads with the SVG visual and stats counter.
- Click any of the **Popular Categories** cards; it will filter `courses.php` to that discipline.
- Check that the **Featured Courses** load dynamically from MySQL with prices and ratings.
- Test the responsive mobile menu toggle by resizing the browser window to mobile width.

### 2. Courses Catalog & Search Testing (`courses.php`)
- Enter keywords into the search bar (e.g. `Python`, `Web`, `Cloud`, `Design`) and submit; confirm only matching courses appear.
- Select a category from the dropdown or click one of the filter pills; verify filtered results.
- Test the **Reset Filters** button to restore the full catalog view.

### 3. Course Details Page Testing (`course-details.php`)
- Click **View Details** on any course card (e.g. `course-details.php?id=1`).
- Verify that the program overview, learning outcomes checklist, and structured syllabus modules load from the database.
- Click the **Enroll In This Course** button in the sticky sidebar; verify that it redirects to `register.php?course_id=1` with that specific course preselected in the dropdown!

### 4. Student Registration & Enrollment Testing (`register.php`)
- Fill out the registration form:
  - Test client-side and server-side validation by leaving mandatory fields blank or entering an invalid email.
  - Interact with the **Weekly Study Commitment** slider; verify the live hours badge updates.
  - Pick a custom **Badge Accent Theme Color** using the color input.
  - Optionally attach an ID document (`.pdf`, `.jpg`, or `.png`).
  - Select an academic course and choose a study mode.
  - Accept the Academic Integrity Policy and click **Complete Student Registration & Enroll**.
- Verify the **Success Confirmation Card**:
  - Displays the generated tracking code (e.g. `LS-2026-A1B2C`).
  - Summarizes the student name, email, course title, and timestamp.
  - Try submitting the same student email and course again to test **Duplicate Enrollment Prevention**.

### 5. Contact & Admissions Form Testing (`about.php`)
- Scroll to the **Contact Academic Support** section at the bottom of `about.php`.
- Fill in your name, email, subject, and message, and click **Submit Academic Inquiry**.
- Confirm the green success banner appears.
- Open phpMyAdmin, navigate to table `contact_messages`, and verify your message is stored in the database!

---

## 📁 Directory Structure

```text
learnsphere/
├── index.php                      # Home Portal Page
├── courses.php                    # Courses Catalog with Filter & Search
├── course-details.php             # Comprehensive Syllabus & Outcomes Page
├── register.php                   # Student Registration & Course Enrollment Form
├── about.php                      # Mission, Faculty, and Contact Inquiries Form
├── includes/
│   ├── db.php                     # PDO MySQL Database Connection & Helpers
│   ├── header.php                 # Semantic Site Header & Responsive Navigation
│   └── footer.php                 # Semantic Footer with Links and Accreditation
├── assets/
│   ├── css/
│   │   └── style.css              # Master Responsive CSS3 Stylesheet
│   ├── js/
│   │   └── script.js              # Mobile Toggle, Validation, and Dynamic Sliders
│   └── images/                    # Custom SVG Images & Course Illustrations
│       ├── logo.svg
│       ├── hero_illustration.svg
│       ├── about_hero.svg
│       ├── course_web_dev.svg
│       ├── course_ai_ml.svg
│       ├── course_cloud_devops.svg
│       ├── course_cybersecurity.svg
│       ├── course_ui_ux.svg
│       ├── course_mobile_dev.svg
│       ├── course_backend.svg
│       ├── course_data_analytics.svg
│       ├── avatar_alex.svg
│       ├── avatar_elena.svg
│       └── avatar_marcus.svg
├── database/
│   └── learnsphere_db.sql         # Database Schema & Seed Data Script
├── uploads/                       # Directory for Student Document Uploads
└── README.md                      # Complete Project Documentation & Setup Guide
```

---

## 🎓 Academic Integrity Notice
This web application was designed to meet and exceed all course requirements for web development projects. It features zero dependencies on bloated front-end CSS frameworks, cleanly structured vanilla CSS3, semantic HTML5 elements, and prepared SQL execution for rock-solid security.
