<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid CSRF token";
    header("Location: userdashboard.php");
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Process form data
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

// Validate inputs
$errors = [];

if (empty($full_name)) {
    $errors[] = "Full name is required";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}

// Check if email is already taken by another user
try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email is already in use by another account";
    }
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

// Handle profile picture upload
$profile_picture = $_SESSION['profile_picture'] ?? 'default.jpg';

if (!empty($_FILES['profile_picture']['name'])) {
    $upload_dir = 'uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    $file_info = $_FILES['profile_picture'];
    
    if (!in_array($file_info['type'], $allowed_types)) {
        $errors[] = "Only JPG, PNG, and GIF images are allowed";
    } elseif ($file_info['size'] > $max_size) {
        $errors[] = "Image size must be less than 2MB";
    } else {
        // Generate unique filename
        $ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
        $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
        $target_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file_info['tmp_name'], $target_path)) {
            // Delete old profile picture if it's not the default
            if ($profile_picture !== 'default.jpg' && file_exists($upload_dir . $profile_picture)) {
                unlink($upload_dir . $profile_picture);
            }
            $profile_picture = $new_filename;
        } else {
            $errors[] = "Failed to upload profile picture";
        }
    }
}

// If no errors, update database
if (empty($errors)) {
    try {
        // Prepare SQL based on whether password is being changed
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, 
                                   password = :password, profile_picture = :profile_picture 
                                   WHERE id = :user_id");
            $stmt->bindParam(':password', $hashed_password);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, 
                                   profile_picture = :profile_picture WHERE id = :user_id");
        }
        
        $stmt->bindParam(':name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':profile_picture', $profile_picture);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            // Update session variables
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['profile_picture'] = $profile_picture;
            
            $_SESSION['success'] = "Profile updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update profile";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = implode("<br>", $errors);
}

header("Location: userdashboard.php");
exit();
?>