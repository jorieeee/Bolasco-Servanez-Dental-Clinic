<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dental_clinic_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>