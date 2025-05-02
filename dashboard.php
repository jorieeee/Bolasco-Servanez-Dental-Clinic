<?php 
session_start();
date_default_timezone_set('Asia/Manila'); // Set the correct timezone
include 'db.php';

$today = date('Y-m-d');

// Fix: JOIN with patients to get names and other data
$sql = "SELECT 
            appointments.*, 
            patients.first_name, 
            patients.last_name 
        FROM appointments 
        JOIN patients ON appointments.patient_id = patients.id 
        WHERE appointment_date = '$today' 
        ORDER BY appointment_time";

$appointments_result = $conn->query($sql);

// Fetch other necessary data
$recent_patients = $conn->query("SELECT * FROM patients ORDER BY id DESC LIMIT 5");
$total_patients = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
$activities = $conn->query("SELECT patients.first_name, patients.last_name, dental_records.date_of_visit 
                            FROM dental_records 
                            JOIN patients ON dental_records.patient_id = patients.id 
                            ORDER BY dental_records.date_of_visit DESC LIMIT 5");
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

// Calculate total revenue for the current month
$start_of_month = date('Y-m-01');
$end_of_month = date('Y-m-t');

$revenue_query = "SELECT SUM(fee) AS total_revenue FROM appointments 
                  WHERE appointment_date BETWEEN '$start_of_month' AND '$end_of_month'";
$revenue_result = $conn->query($revenue_query);
$total_revenue_month = $revenue_result->fetch_assoc()['total_revenue'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentalCare Dashboard</title>
    <link rel="stylesheet" href="dash.css">
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .wrapper {
    display: flex;
    flex-direction: column;
    margin-left: 250px; /* Leave space for sidebar */
    margin-top: 40px; /* Leave space for header */
    min-height: 100vh;
    overflow-y: auto;
}
</style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <i class="fa-solid fa-stethoscope"></i>
            <div class="logo-text">
                <h2>Bolasco-Servanez</h2>
                <p> Dental Clinic</p>
            </div>
        </div>

        <div class="user-section">
            <div class="profile" onclick="toggleDropdown()">
                <img src="images/icon/1.jpg" alt="Profile Image">
                <div id="dropdown" class="dropdown-content">

                    <a href="index.php">Logout</a>
                </div>
            </div>        
        </div>
    </div>

    <div class="sidebar">
        <div class="menu">
            <h3> <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dentist Dashboard</a></h3>
            <h3> <a href="add_patient.php"><i class="fas fa-user"></i> Patients</a> </h3>
            <h3> <a href="add_appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a> </h3>
            <h3>  <a href="dental_records.php"><i class="fa-solid fa-teeth"></i>Dental Records</a></h3>
        </div>
        <div class="logout">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>
    </div>

    <div class="wrapper">
        <div class="dashContainer">
            <div class="dashHeader">
                <h1>Dashboard</h1>
                <p>Overview of appointments, patients, and activity</p>
            </div>
            <div class="stats">
                <div class="card">
                    <div class="title">
                        <h3>Total Patients</h3>
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <p><?php echo $total_patients; ?></p>
                </div>
                
                <div class="card">
                    <div class="title">
                        <h3>Appointments</h3>
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <p><?php echo $total_appointments; ?></p>
                </div>
                
                <div class="card">
                    <div class="title">
                        <h3>Patients</h3>
                        <i class="fas fa-users"></i>
                    </div>
                    <p><?php echo $total_patients; ?></p>
                </div>

                <div class="card">
                    <div class="title">
                        <h3>Revenue</h3>
                    </div>
                    <p><i>&#8369;<?php echo number_format($totalRevenue, 2); ?></i></p>
                </div>

                <div class="main-content">
                    <div class="top-section">
                    <div class="appointments">
    <h2>Today's Appointments</h2>
    <ul>
        <?php
        // SQL query to fetch today's appointments
        $sql = "SELECT * FROM appointments WHERE appointment_date = '$today'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            // Output each appointment
            while($row = $result->fetch_assoc()) {
                // Convert appointment time from 24-hour to 12-hour format
                $formatted_time = date("g:i A", strtotime($row['appointment_time']));
                
                echo "<li>";
                echo "<div class='info'>";
                echo "<strong>" . htmlspecialchars($row['patient_name']) . " <span style='color: #2b8a3e;'>(Scheduled)</span></strong><br>";
                echo "<p>" . $formatted_time . " - " . htmlspecialchars($row['appointment_type']) . "</p>";
                echo "</div>";
                echo "<button class='view-btn'>";
                echo "<i class='fa-solid fa-file'></i> View";
                echo "</button>";
                echo "</li>";
            }
        } else {
            echo "<li>No appointments for today.</li>";
        }
        
        // Close the database connection
        $conn->close();
        ?>
    </ul>

    <br>
    <a href="add_appointment.php">
        <button class="view-all">View All Appointments →</button>
    </a>
</div>



                        <div class="calendar-container">
                            <h2>Calendar</h2>
                            <div class="calendar-box">
                                <div class="calendar-header">
                                    <button id="prev-btn">❮</button>
                                    <h2 id="month-year"></h2>
                                    <button id="next-btn">❯</button>
                                </div>
                                <div class="day-names">
                                    <div class="day-name">Sun</div>
                                    <div class="day-name">Mon</div>
                                    <div class="day-name">Tue</div>
                                    <div class="day-name">Wed</div>
                                    <div class="day-name">Thu</div>
                                    <div class="day-name">Fri</div>
                                    <div class="day-name">Sat</div>
                                </div>
                                <div class="calendar-grid" id="calendar-days"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bottom-section">
                        <div class="recent-patients">
                            <h2>Recent Patients</h2>
                            <ul>
                                <?php while($p = $recent_patients->fetch_assoc()): ?>
                                    <li>
                                        <div class="info">
                                            <strong><?php echo $p['first_name'] . ' ' . $p['last_name']; ?></strong>
                                            <p>Email: <?php echo $p['email']; ?></p>
                                        </div>
                                        <span class="status active">Active</span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <a href="add_patient.php">
                                <button class="view-all">View All Patients →</button>
                            </a>
                        </div>

                        <div class="activity">
                            <h2>Activity</h2>
                            <ul>
                                <?php while($a = $activities->fetch_assoc()): ?>
                                    <li>
                                        <span class="icon">🦷</span>
                                        <div>
                                            <strong>Dentist update</strong>
                                            <p><?php echo $a['first_name'] . ' ' . $a['last_name']; ?> visited on <?php echo $a['date_of_visit']; ?></p>
                                            <small>Recently</small>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="dash.js"></script>
</body>
</html>
