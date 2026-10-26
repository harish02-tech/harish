<?php
/**
 * LearnSphere – Database Connection & Utility Functions
 * Uses PHP Data Objects (PDO) for secure, prepared SQL execution.
 */

// Database Configuration Constants (Standard XAMPP default)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'learnsphere_db');
define('DB_USER', 'root');
define('DB_PASS', 'HARISH.K@#');
define('DB_CHARSET', 'utf8mb4');

$pdo = null;
$db_connected = false;
$db_error_message = '';

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $db_connected = true;
} catch (PDOException $e) {
    $db_connected = false;
    $db_error_message = $e->getMessage();
}

/**
 * Sanitize and clean string input
 * Prevents XSS attacks on user output
 */
function sanitize($data) {
    if (is_null($data)) return '';
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a unique, professional enrollment tracking code
 * Format: LS-2026-XXXXX
 */
function generateEnrollmentCode() {
    $year = date('Y');
    $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
    return "LS-{$year}-{$random}";
}

/**
 * Fetch all categories from the database
 */
function getCategories($pdo) {
    if (!$pdo) return [];
    try {
        $stmt = $pdo->query("SELECT * FROM `categories` ORDER BY `name` ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Fetch courses with optional filters for category, search query, and featured status
 */
function getCourses($pdo, $categoryId = null, $search = null, $limit = null, $featuredOnly = false) {
    if (!$pdo) return [];

    $sql = "SELECT c.*, cat.name AS category_name 
            FROM `courses` c 
            JOIN `categories` cat ON c.category_id = cat.category_id 
            WHERE 1=1";
    $params = [];

    if ($featuredOnly) {
        $sql .= " AND c.featured = 1";
    }

    if (!empty($categoryId) && is_numeric($categoryId)) {
        $sql .= " AND c.category_id = :category_id";
        $params[':category_id'] = (int)$categoryId;
    }

    if (!empty($search)) {
        $sql .= " AND (c.title LIKE :search OR c.short_description LIKE :search_desc OR c.instructor_name LIKE :search_inst)";
        $searchTerm = "%" . trim($search) . "%";
        $params[':search'] = $searchTerm;
        $params[':search_desc'] = $searchTerm;
        $params[':search_inst'] = $searchTerm;
    }

    $sql .= " ORDER BY c.featured DESC, c.created_at DESC";

    if (!empty($limit) && is_numeric($limit)) {
        $sql .= " LIMIT " . (int)$limit;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Fetch a single course by its ID
 */
function getCourseById($pdo, $courseId) {
    if (!$pdo || empty($courseId) || !is_numeric($courseId)) return null;

    try {
        $stmt = $pdo->prepare("SELECT c.*, cat.name AS category_name 
                               FROM `courses` c 
                               JOIN `categories` cat ON c.category_id = cat.category_id 
                               WHERE c.course_id = :id 
                               LIMIT 1");
        $stmt->execute([':id' => (int)$courseId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Format currency
 */
function formatPrice($amount) {
    return '$' . number_format((float)$amount, 2);
}
