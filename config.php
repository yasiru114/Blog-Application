<?php
/**
 * Database Configuration & Global Helper Functions
 * Automatically adapts between Local (XAMPP) and Live (InfinityFree) environments.
 */

// 1. Detect Environment (Local vs Live)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$is_local = (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    php_sapi_name() === 'cli-server'
);

if ($is_local) {
    // Localhost (XAMPP) Configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'blog_app');
    
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    define('APP_URL', 'http://' . $host . rtrim($script_dir, '/'));
} else {
    // Live Server (InfinityFree) Configuration
    define('DB_HOST', 'sql312.infinityfree.com');
    define('DB_USER', 'if0_42658676');
    define('DB_PASS', 'mBpdP3063hpjuk');
    define('DB_NAME', 'if0_42658676_db_yasiru');
    
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    define('APP_URL', $protocol . $host);
}

// 2. Application Constants
define('APP_NAME', 'TechFlow');

// 3. Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Singleton Database Connection
 */
function getDBConnection() {
    static $conn = null;

    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Database Connection Failed: " . $conn->connect_error);
        }

        $conn->set_charset("utf8mb4");
    }

    return $conn;
}

// Initialize global connection instance for direct $conn usages
$conn = getDBConnection();

/**
 * Close database connection safely
 */
function closeDBConnection() {
    global $conn;
    if (isset($conn) && $conn) {
        $conn->close();
    }
}

/**
 * Get all available topics (for dropdowns and select fields)
 */
function getAllTopics() {
    $conn = getDBConnection();
    $sql = "SELECT * FROM topic ORDER BY sort_order ASC, name ASC";
    $result = $conn->query($sql);
    $topics = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
    }

    return $topics;
}

/**
 * Get all topics alongside the count of associated blog posts
 */
function getAllTopicsWithCounts() {
    $conn = getDBConnection();
    $sql = "SELECT t.*, COUNT(b.id) AS post_count 
            FROM topic t 
            LEFT JOIN blogpost b ON t.id = b.topic_id 
            GROUP BY t.id 
            ORDER BY t.sort_order ASC, t.name ASC";
    
    $result = $conn->query($sql);
    $topics = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
    }

    return $topics;
}

/**
 * Fetch a single topic by slug
 */
function getTopicBySlug($slug) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM topic WHERE slug = ? LIMIT 1");
    if (!$stmt) return null;
    
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Fetch a single topic by ID
 */
function getTopicById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM topic WHERE id = ? LIMIT 1");
    if (!$stmt) return null;
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>