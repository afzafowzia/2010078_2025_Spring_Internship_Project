<?php
require_once 'config.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Strict ID validation
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error'] = "Invalid appointment ID";
    header("Location: userDashboard.php");
    exit();
}

$appointmentId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

try {
    // Explicitly check for NOT NULL
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE id = :id AND id IS NOT NULL AND user_id = :user_id AND status != 'cancelled'");
    $stmt->bindParam(':id', $appointmentId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "Appointment not found or already cancelled";
        header("Location: userDashboard.php");
        exit();
    }
    
    // Add NULL check in UPDATE
    $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = :id AND id IS NOT NULL AND user_id = :user_id");
    $stmt->bindParam(':id', $appointmentId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $_SESSION['success'] = "Appointment cancelled successfully";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error cancelling appointment: " . $e->getMessage();
}

header("Location: userDashboard.php");
exit();
?>