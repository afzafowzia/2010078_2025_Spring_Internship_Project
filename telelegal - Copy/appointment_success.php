<?php
// appointment_success.php
require_once 'config.php';
require_once 'functions.php';

// Instead of directly accessing $_SESSION
if (isset($_SESSION['user_id'])) {
    // User is logged in
} else {
    // Redirect to login
    header("Location: login.php");
    exit();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Example: Get appointments for the user
// Fetch upcoming appointments (today and future)
try {
    $currentDate = date('Y-m-d');
    $stmt = $conn->prepare("SELECT * 
                          FROM appointments
                          WHERE user_id = :user_id 
                          AND appointment_date >= :current_date
                          AND status != 'cancelled'
                          ORDER BY appointment_date ASC, appointment_time ASC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':current_date', $currentDate);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $appointments = [];
    $error = "Error fetching appointments: " . $e->getMessage();
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Appointment Booked Successfully!</h2>
            <p class="lead">Thank you for booking with us. We've sent a confirmation to your email.</p>
            <a href="userDashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>