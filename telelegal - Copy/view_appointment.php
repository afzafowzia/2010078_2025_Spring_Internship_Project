<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if appointment ID is provided
if (!isset($_GET['id'])) {
    header("Location: userdashboard.php");
    exit();
}

$appointmentId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
$userId = $_SESSION['user_id'];

// Fetch appointment details
try {
    $stmt = $conn->prepare("SELECT a.*, l.name AS lawyer_name, l.specialization, l.image AS lawyer_image, l.bio AS lawyer_bio
                          FROM appointments a
                          JOIN lawyers l ON a.lawyer_id = l.id
                          WHERE a.id = :id AND a.user_id = :user_id");
    $stmt->bindParam(':id', $appointmentId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        $_SESSION['error'] = "Appointment not found";
        header("Location: userdashboard.php");
        exit();
    }
    
    $appointmentDateTime = new DateTime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching appointment details";
    header("Location: userdashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <!-- Include your CSS files -->
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container py-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Appointment Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="<?= htmlspecialchars($appointment['lawyer_image']) ?>" 
                             class="img-fluid rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover;"
                             alt="<?= htmlspecialchars($appointment['lawyer_name']) ?>">
                        <h4><?= htmlspecialchars($appointment['lawyer_name']) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($appointment['specialization']) ?></p>
                        <hr>
                        <p><?= htmlspecialchars($appointment['lawyer_bio']) ?></p>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5><i class="far fa-calendar me-2"></i> Date</h5>
                                <p><?= $appointmentDateTime->format('l, F j, Y') ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="far fa-clock me-2"></i> Time</h5>
                                <p><?= $appointmentDateTime->format('g:i A') ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5><i class="fas fa-video me-2"></i> Meeting Type</h5>
                                <span class="badge bg-<?= $appointment['meeting_type'] == 'video' ? 'primary' : 
                                                       ($appointment['meeting_type'] == 'phone' ? 'success' : 'info') ?>">
                                    <?= ucfirst($appointment['meeting_type']) ?>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle me-2"></i> Status</h5>
                                <span class="badge bg-<?= $appointment['status'] == 'confirmed' ? 'success' : 
                                                      ($appointment['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($appointment['legal_issue'])): ?>
                            <div class="mb-4">
                                <h5><i class="fas fa-file-alt me-2"></i> Legal Issue</h5>
                                <div class="border p-3 rounded bg-light">
                                    <?= nl2br(htmlspecialchars($appointment['legal_issue'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($appointment['meeting_type'] == 'video' && $appointment['status'] == 'confirmed'): ?>
                            <div class="alert alert-info">
                                <h5><i class="fas fa-video me-2"></i> Meeting Instructions</h5>
                                <p>Your video consultation will be conducted via Zoom. Click the button below to join the meeting 5 minutes before your scheduled time.</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-video me-2"></i> Join Video Meeting
                                </a>
                                <p class="small mt-2">Meeting ID: <?= $appointment['id'] ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                            <?php if ($appointment['status'] != 'cancelled'): ?>
                                <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-outline-danger">
                                    <i class="far fa-calendar-times me-2"></i> Cancel Appointment
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include your JS files -->
</body>
</html>