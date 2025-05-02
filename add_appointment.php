<?php
session_start();
date_default_timezone_set('Asia/Manila'); // Set the correct timezone
include 'db.php'; // Ensure this file correctly initializes $conn

// Define the revenue for each appointment type
$appointment_revenues = [
    'General Checkup' => 500.00,
    'Teeth Cleaning' => 800.00,
    'Dental Crown' => 10000.00,
    'Tooth Filling' => 2000.00,
    'Root Canal' => 6500.00,
    'Tooth Extraction' => 1000.00,
    'Orthodontic Adjustment' => 2500.00,
    'Consultation' => 500.00,
];

// Fetching the count and revenue data based on appointment types
$sql = "SELECT appointment_type, COUNT(*) as count 
        FROM appointments 
        GROUP BY appointment_type";
$result = $conn->query($sql);

$appointments_data = [];
$totalRevenue = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointment_type = $row['appointment_type'];
        $count = $row['count'];
        $revenue = isset($appointment_revenues[$appointment_type]) ? $appointment_revenues[$appointment_type] * $count : 0;
        $appointments_data[] = [
            'appointment_type' => $appointment_type,
            'count' => $count,
            'total_revenue' => $revenue,
        ];
        $totalRevenue += $revenue;
    }
}

// Handle form submission for scheduling appointments
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $appointment_type = $conn->real_escape_string($_POST['appointment_type']);
    $appointment_date = $conn->real_escape_string($_POST['appointment_date']);
    $appointment_time = $conn->real_escape_string($_POST['appointment_time']);

    $sql = "INSERT INTO appointments (patient_name, appointment_type, appointment_date, appointment_time) 
            VALUES ('$patient_name', '$appointment_type', '$appointment_date', '$appointment_time')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Appointment scheduled successfully!'); window.location.href='receptionist_appointment.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients</title>
    <link rel="stylesheet" href="patient.css">
    
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="header">
        <!-- Logo Section -->
        <div class="logo-section">
            <i class="fa-solid fa-stethoscope"></i>
            <div class="logo-text">
                <h2>Bolasco-Servanez</h2>
                <p> Dental Clinic</p>
            </div>
        </div>

        <!-- Notification and Profile -->
        <div class="user-section">
            <div class="profile" onclick="toggleDropdown()">
                <img src="images/icon/1.jpg" alt="Profile Image">
                <div id="dropdown" class="dropdown-content">
                    <a href="index.html">Logout</a>
                </div>
            </div>        
        </div>
    </div>
    <div class="sidebar">
        <div>
            <div class="menu">
                <h3> <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dentist Dashboard</a></h3>
                <h3> <a href="add_patient.php"><i class="fas fa-user"></i> Patients</a> </h3>
                <h3> <a href="add_appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a> </h3>
                <h3>  <a href="dental_records.php"><i class="fa-solid fa-teeth"></i>Dental Records</a></h3>
            </div>
        </div>
        <div class="logout">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>
    </div>
    <div class="container">
        <div class="patientheader">
            <h1>Appointments</h1>
            <p>View Appointments</p>
        </div>
    
        <!-- <div class="controls">
            <input type="text" id="search" placeholder="Search patients...">
            
            <div class="filter-group">
                <select id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="active">Scheduled</option>
                    <option value="inactive">Done</option>
                </select>
        
                <select id="appointmentFilter">
                    <option value="all">All Appointments</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="past">Past</option>
                </select>
        
                <button id="openModalBtn">+ New Appointment</button>
            </div>
        </div>
    
       -->

        <div id="appointmentModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2>Schedule New Appointment</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Patient Name</label>
                        <input type="text" name="patient_name" required>
                    </div>
                    <div class="form-group">
                        <label>Appointment Type</label>
                        <select name="appointment_type" required>
                            <option value="General Checkup">General Checkup</option>
                            <option value="Teeth Cleaning">Teeth Cleaning</option>
                            <option value="Tooth Extraction">Tooth Extraction</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Appointment Date</label>
                        <input type="date" name="appointment_date" required>
                    </div>
                    <div class="form-group">
                        <label>Appointment Time</label>
                        <input type="time" name="appointment_time" required>
                    </div>
                    <div class="btn-group">
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="save-btn">Schedule Appointment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appointments Table -->
    <div class="table-wrapper">
      <table class="table-container">
        <tr>
          <th>Patient Name</th>
          <th>Type</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Fee</th>
        </tr>
        <!-- PHP-generated rows here -->
        <?php
        $sql = "SELECT * FROM appointments ORDER BY appointment_date, appointment_time";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $appointment_type = $row['appointment_type'];
            $fee = isset($appointment_revenues[$appointment_type]) ? $appointment_revenues[$appointment_type] : 0.00;
            echo "<tr>
                    <td>{$row['patient_name']}</td>
                    <td>{$row['appointment_type']}</td>
                    <td>{$row['appointment_date']}</td>
                    <td>{$row['appointment_time']}</td>
                    <td>{$row['status']}</td>
                    <td>₱" . number_format($fee, 2) . "</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='5'>No appointments found.</td></tr>";
        }
        ?>
      </table>
    </div>

    <script>
        function openModal() {
            document.getElementById("appointmentModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("appointmentModal").style.display = "none";
        }

        document.getElementById("openModalBtn").addEventListener("click", openModal);

        window.onclick = function(event) {
            var modal = document.getElementById("appointmentModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>

</body>
</html>
