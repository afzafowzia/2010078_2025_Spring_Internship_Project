<?php
require_once 'config.php';

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// User registration function
function register_user($name, $email, $password, $user_type = 'client') {
    global $conn;
    
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (:name, :email, :password, :user_type)");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':user_type', $user_type);
        
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

// User login function
function login_user($email, $password) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    } catch(PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to check if email exists
function email_exists($email) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        error_log("Email check error: " . $e->getMessage());
        return false;
    }
}

function handleProfileUpload($file, $userId) {
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error'];
    }

    if (!in_array($file['type'], ALLOWED_TYPES)) {
        return ['error' => 'Only JPG, PNG, and GIF images are allowed'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'File size must be less than 2MB'];
    }

function getUpcomingAppointments($userId, $conn) {
    $currentDate = date('Y-m-d');
    try {
        $stmt = $conn->prepare("SELECT a.*, l.name AS lawyer_name, l.specialization, l.image AS lawyer_image
                              FROM appointments a
                              JOIN lawyers l ON a.lawyer_id = l.id
                              WHERE a.user_id = :user_id
                              AND a.appointment_date >= :current_date
                              AND a.status != 'cancelled'
                              ORDER BY a.appointment_date ASC, a.appointment_time ASC
                              LIMIT 10");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':current_date', $currentDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching appointments: " . $e->getMessage());
        return [];
    }
}

    // Generate secure filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['error' => 'Failed to save file'];
}

?>