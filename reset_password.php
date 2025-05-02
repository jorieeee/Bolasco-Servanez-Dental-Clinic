<?php
session_start();
include 'db.php'; // Ensure this file connects to the database

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the password and confirm password from the form
    $newPassword = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Check if both passwords match
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error_message'] = "Passwords do not match!";
        header("Location: reset_password.php"); // Redirect back to the form
        exit();
    }

    // Validate password criteria (at least 8 characters, includes uppercase, lowercase, number, and special character)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!$%^&*()_+|~=`{}\[\]:";\'<>,.?\/])(?=.*[A-Z]).{8,}$/', $newPassword)) {
        $_SESSION['error_message'] = "Password must be at least 8 characters, include an uppercase letter, a number, and a special character.";
        header("Location: reset_password.php"); // Redirect back to the form
        exit();
    }

    // Hash the new password securely
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "dental_clinic_db");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Ensure session email exists
    if (!isset($_SESSION['email'])) {
        $_SESSION['error_message'] = "Session expired. Please request a new verification code.";
        header("Location: forgot_password.php");
        exit();
    }

    $email = $_SESSION['email'];  // Use the email stored in session

    // Update password in the database
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ss", $hashedPassword, $email);
if ($stmt->execute()) {
    // Password successfully updated
    echo "<script>alert('Your password has been updated successfully!'); window.location.href='index.php';</script>";
    exit();
} else {
    $_SESSION['error_message'] = "Failed to update password. Please try again.";
    header("Location: reset_password.php"); // Redirect back to the form
    exit();
}


    $stmt->close();
    $conn->close();
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

    <!-- Login Container -->
    <div class="container">
        <h2>Create New Password</h2>
        <p>Your password must be at least 8 characters and include uppercase, lowercase, number, and special character.</p>
        
        <?php
        if (isset($_SESSION['error_message'])) {
            echo "<p style='color:red;'>".$_SESSION['error_message']."</p>";
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo "<p style='color:green;'>".$_SESSION['success_message']."</p>";
            unset($_SESSION['success_message']);
        }
        ?>
        
        <form action="reset_password.php" method="POST">
            <label for="password">New Password</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="********" required>
                <i class="fa-solid fa-eye password-toggle" id="togglePassword1"></i>
            </div>

            <label for="confirm_password">Confirm Password</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="********" required>
                <i class="fa-solid fa-eye password-toggle" id="togglePassword2"></i>
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>
        <a href="index.php" class="back-to-login"><i class="fas fa-lock"></i> Back to Login</a>
    </div>

    <script src="script.js"></script>
    <script>
        document.getElementById('togglePassword1').addEventListener('click', function () {
            togglePasswordVisibility('password');
        });

        document.getElementById('togglePassword2').addEventListener('click', function () {
            togglePasswordVisibility('confirm_password');
        });

        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
        }
    </script>
</body>
</html>
