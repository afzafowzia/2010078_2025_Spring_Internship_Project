<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lexask_db');


// File upload configuration
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

// Create database connection
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        die("Failed to create upload directory. Please manually create an 'uploads' folder in: " . __DIR__);
    }
    
    // Add security protection
    file_put_contents(UPLOAD_DIR . '.htaccess', 
        "Order deny,allow\nDeny from all\n<FilesMatch '\.(jpg|jpeg|png|gif)$'>\nAllow from all\n</FilesMatch>");
    
    // Add default profile picture
    $defaultImage = file_get_contents('https://via.placeholder.com/150');
    file_put_contents(UPLOAD_DIR . 'default.jpg', $defaultImage);
}

// Prevent session fixation
ini_set('session.use_strict_mode', 1);

// Set secure session cookie parameters
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
    // Initialize lawyer_id if not set
    if (!isset($_SESSION['lawyer_id'])) {
        $_SESSION['lawyer_id'] = null;
    }
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
    // Preserve lawyer_id during regeneration
    if (isset($_SESSION['lawyer_id'])) {
        $temp_lawyer_id = $_SESSION['lawyer_id'];
        session_regenerate_id(true);
        $_SESSION['lawyer_id'] = $temp_lawyer_id;
    }
}
?>