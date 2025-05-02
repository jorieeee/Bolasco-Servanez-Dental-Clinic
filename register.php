<?php
session_start();
include 'db.php'; // Make sure this file exists

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    // Check if all fields are filled
    if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
        die("Some fields are missing. Please fill out all fields.");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "dental_clinic_db");

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Email already exists
        echo "<script>alert('This email is already in use. Please use another email.'); window.location.href='register.php';</script>";
        exit();
    }
    $stmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Redirect to index.php on success
        header("Location: index.php");
        exit();
    } else {
        die("Error inserting record: " . $stmt->error);
    }

    // Close connections
    $stmt->close();
    $conn->close();
}



// SMTP Email Sending Function
function sendVerificationEmail($recipientEmail, $verificationCode) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change to your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'gallardojorie@gmail.com'; // SMTP username
        $mail->Password = 'ewuwpuaureygsrck'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('gallardo@gmail.com', 'Bolasco-Servanez Dental Clinic');
        $mail->addAddress($recipientEmail);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Verification Code';
        $mail->Body = "<h3>Your verification code is: <strong>$verificationCode</strong></h3><p>Use this code to reset your password.</p>";

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
    <div class="requestHeader">
        <img src="images/logo.png" alt="Clinic Logo">
        <h2 class="clinic-name">Bolasco-Servanez Dental Clinic</h2>
    </div>

    <!-- Register Container -->
    <div class="requestContainer">
        <h2>Request System Access</h2>
        <p>Complete this form to request access to the Bolasco-Servanez Dental Clinic. 
        Your request will be reviewed by an administrator.
        </p>
    
        <form id="requestForm" action="register.php" method="POST">
            <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
            <input type="text" name="full_name" id="full_name" required>
        
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email" id="email" required>
        
            <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" required>
                <i class="fa-solid fa-eye password-toggle" onclick="togglePassword()"></i>
            </div>
        
            <label for="role"><i class="fas fa-user-tag"></i> Role</label>
            <select name="role" id="role" required>
                <option value="">Select your role</option>
                <option value="dentist">Dentist</option>
                <option value="receptionist">Receptionist</option>
            </select>
        
            <button type="submit" class="btn">Submit</button>
        </form>
        
        
        
        

        <p class="terms">By submitting this request, you agree to our privacy policy and terms of service. All requests are subject to verification and approval.</p>
        <a href="index.php" class="back-to-login"><i class="fas fa-lock"></i> Back to Login</a>
        
    </div>
    
    <script src="script.js"></script>
</body>
</html>
