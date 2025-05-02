<?php 
session_start();
include 'db.php'; // Ensure this file correctly initializes $conn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['email'], $_POST['password'])) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header("Location: index.php");
        exit();
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password, $role);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;

                // Role-based redirection
                if ($role == 'dentist') {
                    header("Location: dashboard.php");
                    exit();
                } elseif ($role == 'receptionist') {
                    header("Location: receptionist_dash.php");
                    exit();
                } else {
                    // Default if no role matches
                    header("Location: index.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Invalid credentials. Please try again.";
            }
        } else {
            $_SESSION['error_message'] = "No user found with that email.";
        }
        
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Database error: " . $conn->error;
    }
    
    // Redirect back to the same page if login fails
    header("Location: index.php");
    exit();
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
        <h2>Welcome Back</h2>
        <p>Sign in to continue</p>
        
        <form action="index.php" method="POST">
            <?php
            if (!empty($_SESSION['error_message'])) {
                echo "<p class='error-message'>" . $_SESSION['error_message'] . "</p>";
                unset($_SESSION['error_message']); // Clear the error message after displaying
            }
            ?>
            <label for="email">Email</label>
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="you@example.com" required>
            </div>

            <label for="password">Password</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="********" required>
                <i class="fa-solid fa-eye password-toggle" onclick="togglePassword()"></i>
            </div>

            <a href="forgot_password.php" class="forgot">Forgot password?</a>
            <button type="submit" class="btn">Sign in</button>
        </form>

        <div class="register">
            <p>Don't have an account? <a href="register.php">Request Access</a></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
