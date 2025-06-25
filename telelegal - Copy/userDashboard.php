<?php
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .hero {
      background: linear-gradient(to right, rgba(0,0,0,0.6), rgba(0,0,0,0.2)), url('https://source.unsplash.com/1600x600/?law,legal,books') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 50px 20px;
    }
    .hero .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      margin-bottom: 15px;
    }
    .card {
      border-radius: 15px;
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: scale(1.03);
    }
    .profile-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    }
    .hero form input, .hero form select {
    border-radius: 10px;
    }
    .modal-content {
    border-radius: 15px;
    }
    .modal-body input {
    border-radius: 8px;
    }


  </style>
</head>
<body>
  <!-- Navbar -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
        <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.jpg'); ?>" 
             class="rounded-circle me-2" 
             width="30" 
             height="30" 
             alt="Profile">
        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
    </ul>
</li>

<!-- Hero with User Profile -->
    <!-- Hero Section with User Info & Lawyer Search -->
    <section class="hero">
    <div class="container text-center">
      
        <h2>Welcome</h2>
        <p class="mb-3">Search and connect with expert lawyers for your legal needs.</p>
        <button class="btn btn-outline-light mb-4" data-bs-toggle="modal" data-bs-target="#userProfileModal">Edit Profile</button>

        <!-- Lawyer Search Form -->

    </div>
    </section>

<!-- Edit Profile Modal -->
<div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="userProfileModalLabel">Edit Your Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        <?php if (!empty($_SESSION['profile_picture'])): ?>
                            <small>Current: <?php echo htmlspecialchars($_SESSION['profile_picture']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Main Dashboard Cards -->
<div class="container mt-4">
    <div class="row text-center">
        <div class="col-md-4 mb-3">
            <div class="card p-3 shadow-sm">
                <h5>📄 Find a Lawyer</h5>
                <p>Browse and connect with qualified legal professionals.</p>
                <a href="findlawyer.php" class="btn btn-primary w-100">Find Lawyers</a>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-3 shadow-sm">
                <h5>📁 My Cases</h5>
                <p>Track the progress and status of your legal cases.</p>
                <a href="mycases.php" class="btn btn-success w-100">View Cases</a>
            </div>
        </div>
        
    </div>
</div>


<!-- Upcoming Appointments Section -->
<!-- Upcoming Appointments Section -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="far fa-calendar-alt me-2"></i> Upcoming Appointments</h4>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($appointments)): ?>
        <div class="row">
            <?php foreach ($appointments as $appointment): 
                $appointmentDateTime = new DateTime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
                $formattedDate = $appointmentDateTime->format('l, F j, Y');
                $formattedTime = $appointmentDateTime->format('g:i A');
                $isToday = $appointment['appointment_date'] == date('Y-m-d');
            ?>
                <div class="col-md-6 mb-4">
                    <div class="card appointment-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <!-- REPLACE THIS SECTION -->
                                <h5 class="card-title">Appointment for <?= htmlspecialchars($appointment['service_type']) ?></h5>
                                <span class="badge bg-<?= $isToday ? 'warning text-dark' : 'primary' ?>">
                                    <?= $isToday ? 'Today' : $formattedDate ?>
                                </span>
                            </div>
                            
                            <!-- You can remove or modify this line since we don't have specialization -->
                            <!-- <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($appointment['specialization']) ?></h6> -->
                            
                            <div class="d-flex align-items-center my-3">
                                <i class="far fa-clock fa-lg me-2"></i>
                                <span class="h5 mb-0"><?= $formattedTime ?></span>
                                <!-- You can keep or modify this badge based on available data -->
                                <span class="badge ms-3 bg-info">
                                    <?= ucfirst($appointment['status'] ?? 'scheduled') ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($appointment['notes'])): ?>
                                <p class="card-text"><strong>Notes:</strong> <?= htmlspecialchars($appointment['notes']) ?></p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <a href="view_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="far fa-eye me-1"></i> Details
                                </a>
                                <?php if (($appointment['status'] ?? '') != 'cancelled'): ?>
                                    <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-outline-danger">
                                        <i class="far fa-calendar-times me-1"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="far fa-calendar-alt fa-4x text-muted mb-4"></i>
                <h5>No Upcoming Appointments</h5>
                <p class="text-muted">You don't have any scheduled appointments yet.</p>
                <a href="findlawyer.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i> Book an Appointment
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
  <!-- Legal Tips Section -->
  <div class="container mt-5">
    <h4 class="mb-3">🧠 Helpful Legal Tips</h4>
    <div class="row">
      <div class="col-md-6">
        <div class="alert alert-info shadow-sm">
          <h5>📌 When to Find a Lawyer?</h5>
          <p>If you're facing legal trouble, signing a contract, or handling disputes, it’s time to consult a lawyer.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-secondary shadow-sm">
          <h5>🔎 How to Find a Lawyer?</h5>
          <p>Use our platform to explore verified legal professionals by expertise, rating, and availability.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white mt-5 pt-4 pb-2">
    <div class="container">
      <div class="row text-center text-md-start">
        <div class="col-md-4 mb-3">
          <h5>Newsletter</h5>
          <input type="email" class="form-control mb-2" placeholder="Your email address">
          <button class="btn btn-primary w-100">Subscribe</button>
        </div>
        <div class="col-md-4 mb-3">
          <h5>Contact</h5>
          <p>Email: support@lexask.com</p>
          <p>Phone: +8801XXXXXXXXX</p>
        </div>
        <div class="col-md-4 mb-3">
          <h5>Follow Us</h5>
          <a href="#" class="text-white me-2">Facebook</a>
          <a href="#" class="text-white me-2">Twitter</a>
          <a href="#" class="text-white">LinkedIn</a>
        </div>
      </div>
      <div class="text-center mt-3">
        <small>© 2025 LexAsk. All Rights Reserved.</small>
      </div>
    </div>
  </footer>

  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form>
            <input type="text" class="form-control mb-2" placeholder="Full Name" value="John Doe">
            <input type="email" class="form-control mb-2" placeholder="Email" value="john@example.com">
            <input type="tel" class="form-control mb-2" placeholder="Phone Number">
            <input type="file" class="form-control mb-2">
            <button class="btn btn-success w-100">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
