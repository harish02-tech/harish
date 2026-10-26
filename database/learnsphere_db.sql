
-- =====================================================================
-- LearnSphere – Online Course Enrollment Portal Database Schema
-- Database: learnsphere_db
-- Compatibility: MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.4+ (XAMPP default)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `learnsphere_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `learnsphere_db`;

-- Disable foreign key checks temporarily for clean initialization
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `categories`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. Categories Table
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. Courses Table
-- ---------------------------------------------------------------------
CREATE TABLE `courses` (
    `course_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `short_description` VARCHAR(300) NOT NULL,
    `full_description` TEXT NOT NULL,
    `syllabus` TEXT NOT NULL,
    `learning_outcomes` TEXT NOT NULL,
    `instructor_name` VARCHAR(150) NOT NULL,
    `instructor_title` VARCHAR(150) NOT NULL,
    `instructor_bio` TEXT NOT NULL,
    `duration_weeks` INT NOT NULL,
    `difficulty` ENUM('Beginner', 'Intermediate', 'Advanced', 'All Levels') NOT NULL DEFAULT 'All Levels',
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `original_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `rating` DECIMAL(2,1) NOT NULL DEFAULT 4.8,
    `reviews_count` INT NOT NULL DEFAULT 120,
    `image` VARCHAR(255) NOT NULL,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_course_category` 
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Students Table
-- ---------------------------------------------------------------------
CREATE TABLE `students` (
    `student_id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(30) NOT NULL,
    `dob` DATE NOT NULL,
    `gender` ENUM('Male', 'Female', 'Non-Binary', 'Prefer not to say') NOT NULL,
    `qualification` VARCHAR(100) NOT NULL,
    `address` TEXT NOT NULL,
    `experience_years` INT NOT NULL DEFAULT 0,
    `portfolio_url` VARCHAR(255) NULL,
    `preferred_time` TIME NULL,
    `study_hours_week` INT NOT NULL DEFAULT 10,
    `intake_month` VARCHAR(7) NULL,
    `theme_color` VARCHAR(20) DEFAULT '#00ADB5',
    `id_document` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. Enrollments Table
-- ---------------------------------------------------------------------
CREATE TABLE `enrollments` (
    `enrollment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `enrollment_code` VARCHAR(50) NOT NULL UNIQUE,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `study_mode` ENUM('Self-Paced Online', 'Instructor-Led Hybrid', 'Intensive Bootcamp') NOT NULL,
    `interests` VARCHAR(255) NULL,
    `special_notes` TEXT NULL,
    `status` ENUM('Confirmed', 'Pending Verification', 'Active') NOT NULL DEFAULT 'Confirmed',
    `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_enrollment_student` 
        FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_enrollment_course` 
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `unique_student_course` 
        UNIQUE (`student_id`, `course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. Contact Messages Table
-- ---------------------------------------------------------------------
CREATE TABLE `contact_messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('New', 'Read', 'Replied') NOT NULL DEFAULT 'New',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- POPULATE SEED DATA
-- =====================================================================

-- Categories Seed Data
INSERT INTO `categories` (`category_id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Web Development', 'web-development', 'fas fa-code', 'Modern frontend, backend, full-stack frameworks and APIs.'),
(2, 'Data Science & AI', 'data-science-ai', 'fas fa-brain', 'Machine learning, predictive analytics, deep neural networks.'),
(3, 'Cloud & DevOps', 'cloud-devops', 'fas fa-cloud', 'Kubernetes, AWS cloud architecture, CI/CD automated pipelines.'),
(4, 'Cybersecurity', 'cybersecurity', 'fas fa-shield-alt', 'Ethical hacking, network defense, penetration testing.'),
(5, 'UI/UX Design', 'ui-ux-design', 'fas fa-paint-brush', 'User research, wireframing, high-fidelity prototypes in Figma.'),
(6, 'Mobile Development', 'mobile-development', 'fas fa-mobile-alt', 'Cross-platform app development for iOS and Android.');

-- Courses Seed Data
INSERT INTO `courses` (
    `course_id`, `category_id`, `title`, `slug`, `short_description`, `full_description`, 
    `syllabus`, `learning_outcomes`, `instructor_name`, `instructor_title`, `instructor_bio`, 
    `duration_weeks`, `difficulty`, `price`, `original_price`, `rating`, `reviews_count`, `image`, `featured`
) VALUES
(
    1, 1, 
    'Full-Stack Modern Web Engineering', 
    'full-stack-modern-web-engineering',
    'Master HTML5, CSS3, modern JavaScript, PHP, MySQL, and RESTful architectures from scratch to deployment.',
    'This flagship program is engineered to take you from core programming fundamentals into enterprise-level web architecture. You will architect responsive, accessible client interfaces using semantic HTML5 and cutting-edge CSS3 grid systems, build scalable server-side systems with PHP 8, and manage robust relational data structures with MySQL. By the end of this course, you will possess real-world software engineering skills aligned with current industry standards.',
    'Module 1: Semantic HTML5, Core CSS3 & Responsive Architecture||Module 2: Advanced JavaScript (ES6+), DOM, and Async Fetch API||Module 3: Server-Side Logic with PHP 8, OOP & MVC Patterns||Module 4: Relational Database Modeling with MySQL & PDO Prepared Statements||Module 5: RESTful API Design, JWT Authentication & Security Hardening||Module 6: Production Deployment, CI/CD & Performance Optimization',
    'Design accessible, modern UI layouts with CSS Grid, Flexbox, and fluid typography.||Build secure, sanitized PHP applications with parameterized database queries.||Implement RESTful backend endpoints and consume external web services.||Deploy production-ready web applications on cloud and local web servers.',
    'Dr. Alexander Vance', 'Principal Software Architect & Academic Fellow',
    'Dr. Vance holds a Ph.D. in Computer Science with over 15 years of industry experience leading high-throughput distributed systems at top tech companies.',
    12, 'Intermediate', 89.99, 149.99, 4.9, 428, 'course_web_dev.svg', 1
),
(
    2, 2, 
    'Applied Artificial Intelligence & Machine Learning', 
    'applied-ai-machine-learning',
    'Hands-on machine learning, scikit-learn algorithms, deep neural networks, and modern generative AI models.',
    'Immerse yourself in modern computational data science. This comprehensive curriculum unpacks both foundational mathematical theory and real-world Python implementations. You will build classification algorithms, regression pipelines, natural language processors, and computer vision classifiers.',
    'Module 1: Foundations of Mathematical Computing & NumPy/Pandas||Module 2: Supervised Learning (Regression, Decision Trees, SVMs)||Module 3: Unsupervised Clustering, Dimensionality Reduction & PCA||Module 4: Neural Networks, Backpropagation & PyTorch Deep Learning||Module 5: Natural Language Processing (NLP) & Transformer Architecture||Module 6: AI Model Deployment with FastAPI & Docker Containers',
    'Formulate, clean, and analyze high-dimensional enterprise datasets.||Train, evaluate, and tune machine learning models for production accuracy.||Implement deep neural networks using PyTorch for computer vision and NLP.||Deploy trained models via lightweight microservices.',
    'Prof. Elena Rostova', 'Chief AI Researcher & Data Science Director',
    'Prof. Rostova has authored 30+ peer-reviewed papers on deep neural networks and consulted for leading healthcare and fintech enterprises.',
    14, 'Advanced', 119.99, 189.99, 4.9, 312, 'course_ai_ml.svg', 1
),
(
    3, 3, 
    'Cloud Architecture & DevOps Engineering', 
    'cloud-architecture-devops-engineering',
    'Architect resilient cloud solutions on AWS, manage Docker containers, and orchestrate with Kubernetes.',
    'Bridge software development and IT operations through robust automation. Learn Infrastructure as Code (IaC) using Terraform, continuous integration pipelines with GitHub Actions, container orchestration with Kubernetes, and enterprise monitoring with Prometheus and Grafana.',
    'Module 1: AWS Core Infrastructure (VPC, EC2, S3, IAM, Route53)||Module 2: Linux Systems Administration, Shell Scripting & Git Workflow||Module 3: Containerization with Docker & Multi-Stage Builds||Module 4: Container Orchestration at Scale with Kubernetes & Helm||Module 5: Infrastructure as Code (IaC) using HashiCorp Terraform||Module 6: Automated CI/CD Pipelines & Observability with Prometheus/Grafana',
    'Architect fault-tolerant, high-availability infrastructure on Amazon Web Services.||Automate continuous integration and continuous deployment pipelines.||Manage enterprise Kubernetes clusters and service mesh traffic.||Provision immutable infrastructure utilizing Terraform.',
    'Marcus Sterling', 'Cloud Solutions Architect (AWS Certified Master)',
    'Marcus has led cloud modernization migrations for Fortune 500 financial institutions and trains DevOps professionals globally.',
    10, 'Intermediate', 99.99, 159.99, 4.8, 254, 'course_cloud_devops.svg', 1
),
(
    4, 4, 
    'Ethical Hacking & Defensive Cybersecurity', 
    'ethical-hacking-cybersecurity',
    'Penetration testing methodologies, network defense, vulnerability assessment, and threat mitigation.',
    'Gain hands-on expertise in offensive vulnerability testing and defensive network hardening. Designed to provide real laboratory experience in safe, sandboxed environments, you will learn reconnaissance, network scanning, packet inspection, OWASP Top 10 web vulnerabilities, and incident response.',
    'Module 1: Cybersecurity Fundamentals & Network Protocol Analysis||Module 2: Penetration Testing Methodology & Reconnaissance Techniques||Module 3: Vulnerability Scanning & Exploitation with Metasploit||Module 4: OWASP Web Application Penetration Testing (SQLi, XSS, CSRF)||Module 5: Cryptography, PKI & Secure Network Architecture||Module 6: Incident Detection, Threat Hunting & Digital Forensics',
    'Identify and patch critical web security flaws such as SQL Injection and XSS.||Conduct systematic security audits and penetration tests on enterprise targets.||Inspect packet streams using Wireshark to locate malicious anomalies.||Formulate proactive incident response strategies and security policies.',
    'Sarah Jenkins, CISSP', 'Senior Cybersecurity Consultant & Ethical Hacker',
    'Sarah is an active security researcher, CEH and CISSP certified, with a decade of red-teaming and threat intelligence background.',
    12, 'Intermediate', 109.99, 179.99, 4.9, 189, 'course_cybersecurity.svg', 0
),
(
    5, 5, 
    'Professional UI/UX Design & Design Systems', 
    'professional-ui-ux-design-systems',
    'Master user research, wireframing, high-fidelity UI design in Figma, and interactive prototyping.',
    'Elevate your design career by mastering user-centered product design. From conducting deep qualitative user interviews to constructing atomic design systems in Figma, you will create frictionless interfaces that captivate users and drive measurable product engagement.',
    'Module 1: User Research, Empathy Mapping & Persona Creation||Module 2: Information Architecture, User Flows & Wireframing||Module 3: Figma Mastery: Auto-Layout, Components & Variables||Module 4: Design Systems, Accessibility (WCAG 2.1) & Tokens||Module 5: Micro-interactions, Advanced Prototyping & Motion Design||Module 6: Usability Testing, Developer Hand-off & Portfolio Building',
    'Conduct user testing sessions and derive actionable UX enhancements.||Build robust, scalable design systems featuring Figma tokens and variants.||Create high-fidelity interactive prototypes with complex animations.||Bridge the gap between product designers and engineering teams.',
    'Camilla Duarte', 'Product Design Lead & Design Educator',
    'Former lead UX designer at award-winning digital studios, Camilla specializes in design systems and human-computer interaction.',
    8, 'Beginner', 69.99, 119.99, 4.7, 210, 'course_ui_ux.svg', 1
),
(
    6, 6, 
    'Cross-Platform Mobile App Development with Flutter', 
    'cross-platform-mobile-app-development-flutter',
    'Build natively compiled applications for iOS and Android using Dart and Google Flutter framework.',
    'Harness the power of Flutter to develop beautiful, performant mobile applications with a single codebase. Learn state management using Riverpod and Bloc, integrate REST APIs and Firebase backends, and publish polished applications to both Google Play and Apple App Store.',
    'Module 1: Dart Programming Language: OOP, Streams & Asynchronous Logic||Module 2: Flutter Widget Tree, Layouts & Material 3 / Cupertino Design||Module 3: State Management with Riverpod and Provider||Module 4: Local Storage, SQLite & Secure Preferences||Module 5: REST API Integration, JSON Serialization & Firebase Cloud||Module 6: Native Device APIs (Camera, GPS) & Store Release Engineering',
    'Develop and compile production-ready iOS and Android apps from one codebase.||Implement scalable state architecture for complex user workflows.||Consume remote cloud services and persist offline relational data.||Prepare and deploy applications to Google Play and Apple App Store.',
    'David Chen', 'Senior Mobile Engineer & Flutter Specialist',
    'David has built and scaled mobile applications with over 5 million downloads across fintech and social media domains.',
    10, 'Beginner', 79.99, 129.99, 4.8, 175, 'course_mobile_dev.svg', 0
),
(
    7, 1, 
    'Backend API Architecture with Node.js & Microservices', 
    'backend-api-architecture-nodejs-microservices',
    'Construct high-throughput event-driven microservices, Redis caching layers, and asynchronous queues.',
    'Delve deep into backend scalability. Learn how top tech companies architect fault-tolerant distributed backends with Node.js, Express, Redis, Docker, and Apache Kafka for real-time data streaming.',
    'Module 1: Node.js Internals, Event Loop & Streams||Module 2: Modular API Design with Express & TypeScript||Module 3: Database Optimization (PostgreSQL & MongoDB)||Module 4: In-Memory Caching & Session Stores with Redis||Module 5: Asynchronous Message Queues with RabbitMQ/Kafka||Module 6: Microservice Communication, Gateway & Rate Limiting',
    'Engineer asynchronous, high-concurrency microservices.||Optimize database response latencies with multi-layer Redis caching.||Implement resilient message broker workflows for background tasks.||Monitor backend health and orchestrate containerized services.',
    'Liam O’Connor', 'Distributed Systems Architect',
    'Liam brings 12 years of distributed backend experience building payment processing engines handling millions of daily transactions.',
    10, 'Advanced', 94.99, 149.99, 4.9, 142, 'course_backend.svg', 0
),
(
    8, 2, 
    'Python for Business Intelligence & Data Analytics', 
    'python-business-intelligence-data-analytics',
    'Transform complex raw business data into actionable strategic insights with Python, SQL, and interactive dashboards.',
    'Designed for analysts and aspiring data professionals who want to automate reporting, construct interactive financial dashboards, and forecast business metrics using modern data pipelines.',
    'Module 1: Python Fundamentals for Business Analysts||Module 2: Advanced SQL Queries, Joins, Window Functions & CTEs||Module 3: Data Cleaning, Wrangling & Transformation with Pandas||Module 4: Exploratory Data Analysis & Statistical Modeling||Module 5: Interactive Visualizations with Seaborn & Plotly||Module 6: Automated KPI Dashboards & Executive Storytelling',
    'Automate manual spreadsheet workflows using elegant Python scripts.||Write performant SQL queries against high-volume relational databases.||Build interactive BI charts and executive dashboards with Plotly.||Synthesize statistical data into clear strategic business recommendations.',
    'Priya Sharma', 'Head of Business Analytics & BI Consultant',
    'Priya leads analytics teams for global retail corporations, specializing in churn forecasting and automated intelligence.',
    8, 'Beginner', 64.99, 99.99, 4.8, 280, 'course_data_analytics.svg', 1
);

-- Students Seed Data (Realistic sample record)
INSERT INTO `students` (
    `student_id`, `first_name`, `last_name`, `email`, `phone`, `dob`, `gender`, 
    `qualification`, `address`, `experience_years`, `portfolio_url`, `preferred_time`, 
    `study_hours_week`, `intake_month`, `theme_color`, `id_document`
) VALUES
(
    1, 'Jordan', 'Taylor', 'jordan.taylor@example.com', '+1 (555) 234-5678', 
    '2001-04-15', 'Female', 'Bachelor of Science (Computer Science)', 
    '742 Evergreen Terrace, Suite 4B, Springfield, OR 97477', 
    2, 'https://github.com/jordantaylor', '18:30:00', 12, '2026-11', '#00ADB5', NULL
);

-- Enrollments Seed Data (Realistic sample enrollment)
INSERT INTO `enrollments` (
    `enrollment_id`, `enrollment_code`, `student_id`, `course_id`, `study_mode`, 
    `interests`, `special_notes`, `status`, `enrolled_at`
) VALUES
(
    1, 'LS-2026-94812', 1, 1, 'Instructor-Led Hybrid', 
    'Web Architecture, Database Design', 'Eager to build full-stack web applications for academic and startup ventures.', 
    'Confirmed', '2026-10-20 10:15:00'
);

-- Contact Messages Seed Data
INSERT INTO `contact_messages` (`message_id`, `full_name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(
    1, 'Sophia Ramirez', 'sophia.ramirez@example.com', 'Syllabus Inquiry for Applied AI Course',
    'Hello LearnSphere team, I would like to know if GPU acceleration environments are provided for the deep learning modules in the Applied AI course. Thank you!',
    'Replied', '2026-10-21 14:22:00'
),
(
    2, 'Daniel Kim', 'daniel.kim@example.com', 'Corporate Batch Enrollment',
    'We are planning to sponsor 15 junior developers from our engineering team for the Full-Stack Modern Web Engineering program. Do you support customized corporate cohort schedules?',
    'New', '2026-10-24 09:40:00'
);
