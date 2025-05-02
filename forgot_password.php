<?php 
session_start();
ob_start(); // Start output buffering
include 'db.php'; // Ensure this file exists and connects to the database correctly
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

// If the user is already on verify_code.php, prevent further redirection
if (isset($_SESSION['email']) && basename($_SERVER['PHP_SELF']) !== 'verify_code.php') {
    // Redirect to 'verify_code.php' if the email session exists but we're not already on that page
    header('Location: verify_code.php');
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        die("Email field is required.");
    }

    // Save email in session to pass it to the verification page
    $_SESSION['email'] = $email;

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "dental_clinic_db");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<script>alert('Email not found! Please check your email or register.'); window.location.href='forgot_password.php';</script>";
        exit();
    }

    $stmt->close();

    // Generate a 6-digit verification code as a string
    $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

    // Save verification code to the database
    $stmt = $conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $verificationCode, $email);
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }
    $stmt->close();

    // Send verification email
    if (sendVerificationEmail($email, $verificationCode)) {
        // Redirect to 'verify_code.php' after sending the email successfully
        echo "<script>alert('A verification code has been sent to your email.'); window.location.href='verify_code.php';</script>";
        exit();  // Stop further script execution after redirect
    } else {
        echo "<script>alert('Failed to send email. Please try again.'); window.location.href='forgot_password.php';</script>";
        exit();  // Stop further script execution after the error
    }

    $conn->close();
}

// SMTP Email Sending Function
function sendVerificationEmail($recipientEmail, $verificationCode) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gallardojorie@gmail.com'; // Your Gmail
        $mail->Password = 'ewuwpuaureygsrck'; // App password (Not your actual Gmail password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('gallardojorie@gmail.com', 'Bolasco-Servanez Dental Clinic');
        $mail->addAddress($recipientEmail);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Verification Code';
        $mail->Body = "<h3>You only have 5 minutes to use this code to reset your password.</h3>
                       <p>Your verification code is: <strong>$verificationCode</strong></p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Logo and Clinic Name -->
    <div class="header">
        <img src="images/logo.png" alt="Clinic Logo">
        <h2 class="clinic-name">Bolasco-Servanez Dental Clinic</h2>
    </div>

    <!-- Forgot Password Form -->
    <div class="container">
        <h2>Reset Password</h2>
        <p>Enter your email address and we'll send you a code to reset your password</p>
        
        <form action="forgot_password.php" method="POST">
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="you@example.com" required>
            </div>
            <button type="submit" class="btn">Send verification Code</button>
        </form>

        <br>
    </div>
    <script src="script.js"></script>
</body>
</html>
