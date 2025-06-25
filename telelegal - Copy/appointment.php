<?php
// Database connection and form processing logic would go here

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
    <title>Tele-legal Appointment System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --accent-color: #ff6b6b;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .appointment-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .appointment-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .appointment-header h2 {
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .appointment-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .appointment-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .btn-book {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
        }
        
        .btn-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .service-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .service-card:hover {
            border-color: var(--secondary-color);
            transform: translateY(-5px);
        }
        
        .service-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(106, 17, 203, 0.05);
        }
        
        .service-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .time-slot {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .time-slot:hover {
            background-color: #f8f9fa;
        }
        
        .time-slot.selected {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .step {
            text-align: center;
            position: relative;
            flex: 1;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .step.active .step-number {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .step.completed .step-number {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .step-title {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .step.active .step-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .step.completed .step-title {
            color: var(--secondary-color);
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 60%;
            width: 80%;
            height: 2px;
            background-color: #e9ecef;
            z-index: -1;
        }
        
        .step.completed:not(:last-child)::after {
            background-color: var(--secondary-color);
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container appointment-container">
        <div class="appointment-header">
            <h2><i class="fas fa-gavel me-2"></i> Tele-legal Consultation</h2>
            <p>Book your online legal appointment with certified professionals</p>
        </div>
        
        <div class="appointment-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1-indicator">
                    <div class="step-number">1</div>
                    <div class="step-title">Personal Info</div>
                </div>
                <div class="step" id="step2-indicator">
                    <div class="step-number">2</div>
                    <div class="step-title">Service</div>
                </div>
                <div class="step" id="step3-indicator">
                    <div class="step-number">3</div>
                    <div class="step-title">Schedule</div>
                </div>
                <div class="step" id="step4-indicator">
                    <div class="step-number">4</div>
                    <div class="step-title">Confirm</div>
                </div>
            </div>
            
            <form id="appointmentForm" action="process_appointment.php" method="POST">
                <!-- Step 1: Personal Information -->
                <div class="form-section active" id="step1">
                    <h4 class="mb-4"><i class="fas fa-user-circle me-2 text-primary"></i> Personal Information</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    <div class="nav-buttons">
                        <div></div> <!-- Empty div for spacing -->
                        <button type="button" class="btn btn-book next-step" data-next="step2">Next <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>
                
                <!-- Step 2: Service Selection -->
                <div class="form-section" id="step2">
                    <h4 class="mb-4"><i class="fas fa-balance-scale me-2 text-primary"></i> Select Legal Service</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="service-card" data-service="consultation">
                                <div class="service-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h5>Legal Consultation</h5>
                                <p>General legal advice and guidance</p>
                                <div class="text-primary fw-bold">$50</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-card" data-service="document-review">
                                <div class="service-icon">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <h5>Document Review</h5>
                                <p>Contract or legal document analysis</p>
                                <div class="text-primary fw-bold">$75</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-card" data-service="case-evaluation">
                                <div class="service-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h5>Case Evaluation</h5>
                                <p>Detailed analysis of your legal case</p>
                                <div class="text-primary fw-bold">$100</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="service" name="service" value="">
                    <div class="nav-buttons">
                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="step1"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="button" class="btn btn-book next-step" data-next="step3">Next <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>
                
                <!-- Step 3: Schedule Appointment -->
                <div class="form-section" id="step3">
                    <h4 class="mb-4"><i class="far fa-calendar-alt me-2 text-primary"></i> Schedule Your Appointment</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointmentDate" class="form-label">Select Date</label>
                            <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Available Time Slots</label>
                            <div class="time-slots-container">
                                <div class="time-slot" data-time="09:00">9:00 AM</div>
                                <div class="time-slot" data-time="10:30">10:30 AM</div>
                                <div class="time-slot" data-time="12:00">12:00 PM</div>
                                <div class="time-slot" data-time="14:00">2:00 PM</div>
                                <div class="time-slot" data-time="15:30">3:30 PM</div>
                                <div class="time-slot" data-time="17:00">5:00 PM</div>
                            </div>
                            <input type="hidden" id="appointmentTime" name="appointmentTime" value="">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Briefly describe your legal concern..."></textarea>
                    </div>
                    <div class="nav-buttons">
                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="step2"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="button" class="btn btn-book next-step" data-next="step4">Next <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>
                
                <!-- Step 4: Confirmation -->
                <div class="form-section" id="step4">
                    <h4 class="mb-4"><i class="fas fa-check-circle me-2 text-primary"></i> Confirm Your Appointment</h4>
                    <div class="confirmation-details p-4 mb-4" style="background-color: #f8f9fa; border-radius: 10px;">
                        <h5 class="mb-3">Appointment Summary</h5>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Name:</div>
                            <div class="col-8" id="confirm-name"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Email:</div>
                            <div class="col-8" id="confirm-email"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Phone:</div>
                            <div class="col-8" id="confirm-phone"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Service:</div>
                            <div class="col-8" id="confirm-service"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Date & Time:</div>
                            <div class="col-8" id="confirm-datetime"></div>
                        </div>
                        <div class="row">
                            <div class="col-4 fw-bold">Notes:</div>
                            <div class="col-8" id="confirm-notes"></div>
                        </div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="termsAgreement" required>
                        <label class="form-check-label" for="termsAgreement">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                        </label>
                    </div>
                    <div class="nav-buttons">
                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="step3"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="submit" class="btn btn-book"><i class="fas fa-calendar-check me-2"></i> Confirm Booking</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Terms of Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>This is where your terms of service content would go. Include information about:</p>
                    <ul>
                        <li>Service expectations</li>
                        <li>Payment terms</li>
                        <li>Cancellation policy</li>
                        <li>Attorney-client relationship</li>
                        <li>Limitations of tele-legal services</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Privacy Policy</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>This is where your privacy policy content would go. Include information about:</p>
                    <ul>
                        <li>Data collection practices</li>
                        <li>How client information is used</li>
                        <li>Confidentiality protections</li>
                        <li>Data security measures</li>
                        <li>Cookies and tracking</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Service selection
            $('.service-card').click(function() {
                $('.service-card').removeClass('selected');
                $(this).addClass('selected');
                $('#service').val($(this).data('service'));
            });
            
            // Time slot selection
            $('.time-slot').click(function() {
                $('.time-slot').removeClass('selected');
                $(this).addClass('selected');
                $('#appointmentTime').val($(this).data('time'));
            });
            
            // Step navigation
            $('.next-step').click(function() {
                var currentSection = $(this).closest('.form-section');
                var nextSectionId = $(this).data('next');
                
                // Validate before proceeding
                if (validateStep(currentSection.attr('id'))) {
                    currentSection.removeClass('active');
                    $('#' + nextSectionId).addClass('active');
                    
                    // Update step indicator
                    $('.step').removeClass('active completed');
                    $('#' + currentSection.attr('id') + '-indicator').addClass('completed');
                    $('#' + nextSectionId + '-indicator').addClass('active');
                    
                    // Update confirmation details if we're on the last step
                    if (nextSectionId === 'step4') {
                        updateConfirmationDetails();
                    }
                }
            });
            
            $('.prev-step').click(function() {
                var currentSection = $(this).closest('.form-section');
                var prevSectionId = $(this).data('prev');
                
                currentSection.removeClass('active');
                $('#' + prevSectionId).addClass('active');
                
                // Update step indicator
                $('.step').removeClass('active completed');
                $('#' + prevSectionId + '-indicator').addClass('active');
                $('#' + currentSection.attr('id') + '-indicator').removeClass('completed');
            });
            
            // Form validation for each step
            function validateStep(stepId) {
                var isValid = true;
                
                if (stepId === 'step1') {
                    $('#step1 input[required]').each(function() {
                        if (!$(this).val()) {
                            $(this).addClass('is-invalid');
                            isValid = false;
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });
                    
                    // Validate email format
                    var email = $('#email').val();
                    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        $('#email').addClass('is-invalid');
                        isValid = false;
                    }
                } else if (stepId === 'step2') {
                    if (!$('#service').val()) {
                        alert('Please select a legal service');
                        isValid = false;
                    }
                } else if (stepId === 'step3') {
                    if (!$('#appointmentDate').val()) {
                        $('#appointmentDate').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#appointmentDate').removeClass('is-invalid');
                    }
                    
                    if (!$('#appointmentTime').val()) {
                        alert('Please select a time slot');
                        isValid = false;
                    }
                }
                
                return isValid;
            }
            
            // Update confirmation details
            function updateConfirmationDetails() {
                $('#confirm-name').text($('#firstName').val() + ' ' + $('#lastName').val());
                $('#confirm-email').text($('#email').val());
                $('#confirm-phone').text($('#phone').val());
                
                var serviceText = '';
                var service = $('#service').val();
                if (service === 'consultation') serviceText = 'Legal Consultation ($50)';
                else if (service === 'document-review') serviceText = 'Document Review ($75)';
                else if (service === 'case-evaluation') serviceText = 'Case Evaluation ($100)';
                $('#confirm-service').text(serviceText);
                
                var date = new Date($('#appointmentDate').val());
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                var formattedDate = date.toLocaleDateString('en-US', options);
                $('#confirm-datetime').text(formattedDate + ' at ' + $('#appointmentTime').val());
                
                var notes = $('#notes').val();
                $('#confirm-notes').text(notes ? notes : 'None');
            }
        });
    </script>
</body>
</html>