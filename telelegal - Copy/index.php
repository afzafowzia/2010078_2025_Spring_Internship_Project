<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: userDashboard.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    $user = login_user($email, $password);
    
    if ($user) {
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        
        // Redirect to dashboard
        header("Location: userDashboard.php");
        exit();
    } else {
        $login_error = "Invalid email or password";
    }
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $signup_error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format";
    } elseif (strlen($password) < 8) {
        $signup_error = "Password must be at least 8 characters";
    } elseif (email_exists($email)) {
        $signup_error = "Email already registered";
    } else {
        if (register_user($name, $email, $password)) {
            $_SESSION['signup_success'] = true;
            header("Location: userDashboard.php");
            exit();
        } else {
            $signup_error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEXASK - Your Legal Solution</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(to right,rgb(73, 134, 225),rgb(148, 116, 200));
        }

        .navbar-brand, .nav-link, .navbar-text {
            color: white !important;
        }

        .hero-section {
            background: linear-gradient(to right, #6610f2, #6f42c1);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        .hero-section img {
           max-height: 350px;
           object-fit: cover;
        }

        .hero-section h1 {
            font-size: 3rem;
        }

        .feature-card {
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .legal-tips img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        footer {
            background-color: #343a40;
            color: white;
        }
        .footer-section a {
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #0d6efd;
        }

        .footer-section input.form-control {
            background-color: #f1f1f1;
            border: none;
        }

        .hover-effect {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .hover-effect:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        .lawyer-signup-section {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            border-top: 1px solid #ccc;
        }
        .legal-tips-section {
            background-color: #f9f9ff;
        }

        .tip-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .tip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">LEXASK</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon text-white"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="features.php">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="tips.php">Tips</a></li>
                </ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- Text Section -->
                <div class="col-md-6 text-start">
                    <h1>Welcome to LEXASK</h1>
                    <p>Your one-stop platform for legal services and consultations.</p>
                </div>
                <!-- Image Section -->
                <div class="col-md-6 text-center">
                    <img src="https://media.istockphoto.com/id/1491771681/photo/lady-justice-in-law-office.jpg?s=612x612&w=0&k=20&c=nW9rwSkAQXB0xELIdxXxvInqE7zzg_QDFMCYR-N0KZQ=" alt="Legal Help" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container my-5">
        <div class="row g-4 text-center">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 p-3 hover-effect">
                    <div class="mb-3">
                        <i class="bi bi-calendar-check-fill fs-1 text-primary"></i>
                    </div>
                    <h5 class="card-title">Book Lawyers Easily</h5>
                    <p class="card-text">Schedule appointments with trusted legal professionals.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 p-3 hover-effect">
                    <div class="mb-3">
                        <i class="bi bi-camera-video-fill fs-1 text-success"></i>
                    </div>
                    <h5 class="card-title">Video/Audio Consultations</h5>
                    <p class="card-text">Connect with lawyers via secure calls and chat.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 p-3 hover-effect">
                    <div class="mb-3">
                        <i class="bi bi-robot fs-1 text-warning"></i>
                    </div>
                    <h5 class="card-title">AI Legal Assistance</h5>
                    <p class="card-text">Get instant help from our intelligent legal chatbot.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lawyer Signup Section -->
    <section class="lawyer-signup-section py-5 text-center bg-light">
        <div class="container">
            <h2 class="mb-4">Are You a Lawyer?</h2>
            <p class="mb-4 lead">Join our platform to connect with clients and offer legal services online. It's easy and fast to get started!</p>
            <a href="lawyer-register.php" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus-fill me-2"></i>Join as a Lawyer
            </a>
        </div>
    </section>

    <!-- Legal Tips Section -->
    <div class="container my-5 legal-tips">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="https://images.unsplash.com/photo-1555374018-13a8994ab246" alt="Legal Tips" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h3 class="mb-3">Helpful Legal Tips</h3>
                <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Know when to consult a lawyer.</li>
                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Always check a lawyer's credentials.</li>
                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Use LEXASK to book a consultation in minutes.</li>
                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Keep your documents ready and organized.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Legal Tips Cards Section -->
    <section class="legal-tips-section py-5 bg-white">
        <div class="container">
            <h2 class="text-center mb-5">📝 Legal Tips</h2>
            <div class="row g-4">
                <!-- Tip 1 -->
                <div class="col-md-6">
                    <div class="tip-card p-4 h-100 shadow-sm border rounded hover-effect">
                        <h4 class="d-flex align-items-center"><i class="bi bi-clock-fill text-warning me-2"></i> When to Find a Lawyer?</h4>
                        <p class="mt-3">
                            You should consult a lawyer when you're facing legal trouble, signing contracts, going through divorce, starting a business, or experiencing workplace issues.
                        </p>
                    </div>
                </div>
                <!-- Tip 2 -->
                <div class="col-md-6">
                    <div class="tip-card p-4 h-100 shadow-sm border rounded hover-effect">
                        <h4 class="d-flex align-items-center"><i class="bi bi-search text-info me-2"></i> How to Find a Lawyer?</h4>
                        <p class="mt-3">
                            Use our platform to browse verified lawyers, check their expertise and availability, and book a consultation in minutes—all online and hassle-free.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($login_error)): ?>
                        <div class="alert alert-danger"><?php echo $login_error; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="login" value="1">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberLogin" name="remember">
                            <label class="form-check-label" for="rememberLogin">Remember me</label>
                        </div>
                        <a href="forgot-password.php" class="d-block mb-3 text-decoration-none">Forgot password?</a>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($signup_error)): ?>
                        <div class="alert alert-danger"><?php echo $signup_error; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="signup" value="1">
                        <div class="mb-3">
                            <label for="signupName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="signupName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="signupEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="signupPassword" name="password" required minlength="8">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="termsCheck" name="terms" required>
                            <label class="form-check-label" for="termsCheck">I agree to the <a href="terms.php">terms and conditions</a></label>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-section bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <!-- Newsletter -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">📬 Subscribe to our Newsletter</h5>
                    <form class="d-flex flex-column">
                        <input type="email" class="form-control mb-2" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">📞 Contact Us</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Legal St, Dhaka, Bangladesh</p>
                    <p><i class="fas fa-phone me-2"></i> +880 123-456-789</p>
                    <p><i class="fas fa-envelope me-2"></i> support@lexask.com</p>
                </div>

                <!-- Social Media -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">🌐 Follow Us</h5>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <hr class="bg-secondary">

            <div class="text-center">
                <p class="mb-0">&copy; 2025 LexAsk. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>