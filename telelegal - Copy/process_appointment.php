<?php
// process_appointment.php
require_once 'config.php'; // Your database connection file
session_start(); // Add this at the top to access session data

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in");
}

// In your insert query
$stmt = $conn->prepare("INSERT INTO appointments 
    (first_name, last_name, email, phone, address, 
     service_type, appointment_date, appointment_time, 
     notes, user_id)
    VALUES 
    (:first_name, :last_name, :email, :phone, :address, 
     :service_type, :appointment_date, :appointment_time, 
     :notes, :user_id)");

// Bind all parameters including user_id
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

// Check if form is submitted AND user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    // Sanitize and validate input
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $appointmentDate = filter_input(INPUT_POST, 'appointmentDate', FILTER_SANITIZE_STRING);
    $appointmentTime = filter_input(INPUT_POST, 'appointmentTime', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $userId = $_SESSION['user_id']; // Get the logged-in user's ID

    // Basic validation
    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($service)) $errors[] = "Service type is required";
    if (empty($appointmentDate)) $errors[] = "Appointment date is required";
    if (empty($appointmentTime)) $errors[] = "Appointment time is required";

    if (empty($errors)) {
        try {
            // Combine date and time
            $datetime = $appointmentDate . ' ' . $appointmentTime . ':00';
            
            // Modified INSERT statement to include user_id
            $stmt = $conn->prepare("INSERT INTO appointments 
                                  (first_name, last_name, email, phone, address, service_type, 
                                  appointment_date, appointment_time, notes, user_id) 
                                  VALUES (:first_name, :last_name, :email, :phone, :address, 
                                  :service_type, :appointment_date, :appointment_time, :notes, :user_id)");
            
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':service_type', $service);
            $stmt->bindParam(':appointment_date', $appointmentDate);
            $stmt->bindParam(':appointment_time', $appointmentTime);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $userId); // Add this line
            
            if ($stmt->execute()) {
                // Success - redirect to thank you page
                header("Location: appointment_success.php");
                exit();
            } else {
                $errors[] = "Error saving appointment. Please try again.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // If we got here, there were errors
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: appointment.php");
    exit();

} else {
    // Not a POST request or user not logged in - redirect back
    $_SESSION['errors'] = ["You must be logged in to book an appointment"];
    header("Location: appointment.php");
    exit();
}