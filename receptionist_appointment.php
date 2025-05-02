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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Patients</title>

  <!-- CSS -->
  <link rel="stylesheet" href="patient.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

  <!-- JS Libraries -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="https://rawgit.com/eKoopmans/html2pdf.js/master/dist/html2pdf.bundle.js"></script>

  <!-- Custom Script -->
  <script src="script.js" defer></script>

  <style>
  /* === Revenue Modal Button === */
  #openRevenueBtn {
    background: black;
    color: #fff;
    border: none;
    padding: 10px 40px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: background 0.3s;
  }

  #openRevenueBtn:hover {
    background: #ddd;
    color: black;
  }

  /* === Revenue Modal Styling === */
/* Revenue Modal Container */
#revenueModal {
  position: fixed;
  top: 61%;
  left: 57%;
  transform: translate(-50%, -50%);
  width: 120%;
  max-width: 1100px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  padding: 24px;
  font-family: 'Segoe UI', sans-serif;
}

/* Close Button */
.close-btn {
  float: right;
  font-size: 28px;
  cursor: pointer;
}

/* Headings */
.revenue-content h2 {
  margin-bottom: 0;
  font-size: 24px;
  font-weight: 600;
}

.sub-heading {
  font-size: 18px;
  color: #374151;
  margin-bottom: 20px;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}

.tab {
  padding: 6px 16px;
  background-color: #f3f4f6;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
}

.tab.selected {
  background-color: #fff;
  border: 1px solid #d1d5db;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Controls section */
.controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

/* Action buttons */
.actions .btn {
  padding: 6px 12px;
  margin-left: 8px;
  background-color: #f9fafb;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

/* Table Styling */
.table-wrapper {
  overflow-x: auto;
}

.revenue-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
}

.revenue-table thead {
  background-color: #f9fafb;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.revenue-table th, .revenue-table td {
  padding: 14px 16px;
  color: #1f2937;
}

.revenue-table tbody tr:hover {
  background-color: #f3f4f6;
}

.total-row {
  font-weight: bold;
  background-color: #f9fafb;
  border-top: 2px solid #e5e7eb;
}


  /* === Date Display === */
  #dateDisplay {
    font-weight: bold;
    font-size: 16px;
  }

  /* === Export Button === */
  #exportBtn {
    background: black;
    color: #fff;
    border: none;
    padding: 10px 40px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: background 0.3s;
  }

  #exportBtn:hover {
    background: #ddd;
    color: black;
  }

</style>

</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="logo-section">
      <i class="fa-solid fa-stethoscope"></i>
      <div class="logo-text">
        <h2>Bolasco-Servanez</h2>
        <p>Dental Clinic</p>
      </div>
    </div>
    <div class="user-section">
            <div class="profile" onclick="toggleDropdown()">
                <img src="images/icon/1.jpg" alt="Profile Image">

            </div>        
        </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="menu">
      <h3><a href="receptionist_dash.php"><i class="fas fa-chart-line"></i> Receptionist Dashboard</a></h3>
      <h3><a href="receptionist_patients.php"><i class="fas fa-user"></i> Patients</a></h3>
      <h3><a href="receptionist_appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a></h3>
      <h3><a href="receptionist_records.php"><i class="fas fa-file-alt"></i> Records</a></h3>
    </div>
    <div class="logout">
      <h3><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></h3>
    </div>
  </div>

  <!-- Main Container -->
  <div class="container">
    <div class="patientheader">
      <h1>Appointments</h1>
      <p>Manage Appointments</p>
    </div>

    <div class="controls">
      <input type="text" id="search" placeholder="Search patients..." />
      <div class="filter-group">
        <select id="statusFilter">
          <option value="all">All Statuses</option>
          <option value="active">Scheduled</option>
          <option value="inactive">Done</option>
        </select>
        <select id="appointmentFilter">
          <option value="all">All Appointments</option>
          <option value="upcoming">Upcoming</option>
        </select>
        <button id="exportBtn"><i class="fas fa-file-export"></i> Export</button>
        <button id="openModalBtn">+ New Appointment</button>
        <button id="openRevenueBtn">₱ Revenue</button>
      </div>
    </div>

    <!-- Appointment Modal -->
    <div id="appointmentModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Schedule New Appointment</h2>
        <form method="POST">
          <div class="form-group">
            <label>Patient Name</label>
            <input type="text" name="patient_name" required />
          </div>
          <div class="form-group">
            <label>Appointment Type</label>
            <select name="appointment_type" required>
              <option value="General Checkup">General Checkup</option>
              <option value="Teeth Cleaning">Teeth Cleaning</option>
              <option value="Dental Crown">Dental Crown</option>
              <option value="Tooth Filling">Tooth Filling</option>
              <option value="Root Canal">Root Canal</option>
              <option value="Tooth Extraction">Tooth Extraction</option>
              <option value="Orthodontic Adjustment">Orthodontic Adjustment</option>
              <option value="Consultation">Consultation</option>
            </select>
          </div>
          <div class="form-group">
            <label>Appointment Date</label>
            <input type="date" name="appointment_date" required />
          </div>
          <div class="form-group">
            <label>Appointment Time</label>
            <input type="time" name="appointment_time" required />
          </div>
          <div class="btn-group">
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            <button type="submit" class="save-btn">Schedule Appointment</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Revenue Modal -->
<div id="revenueModal" class="modal">
    <div class="revenue-content">
        <span class="close-btn" onclick="closeRevenueModal()">&times;</span>
        <h2>Appointments</h2>
        <div class="sub-heading">Revenue by <strong>Appointment Type</strong></div>
        <div class="controls">
            <div class="actions">
            <button class="btn" id="revenueExportBtn"><span class="icon">⬇️</span> Revenue Export</button>            </div>
            </div>
        <div class="table-wrapper">
            <table class="revenue-table">
                <thead>
                    <tr>
                        <th>Appointment Type</th>
                        <th>Count</th>
                        <th>Revenue</th>
                        <th>Avg. Revenue</th>
                        <th>% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through appointment types and display data
                    foreach ($appointments_data as $appointment) {
                        $count = $appointment['count'];
                        $revenue = $appointment['total_revenue'];
                        $avgRevenue = $count > 0 ? $revenue / $count : 0;
                        $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;
                        echo "<tr>
                                <td>{$appointment['appointment_type']}</td>
                                <td>{$count}</td>
                                <td>₱" . number_format($revenue, 2) . "</td>
                                <td>₱" . number_format($avgRevenue, 2) . "</td>
                                <td>" . round($percentage, 2) . "%</td>
                              </tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>Total</td>
                        <td><?php echo array_sum(array_column($appointments_data, 'count')); ?></td>
                        <td>₱<?php echo number_format($totalRevenue, 2); ?></td>
                        <td>₱<?php echo $totalRevenue > 0 ? number_format($totalRevenue / array_sum(array_column($appointments_data, 'count')), 2) : '0.00'; ?></td>
                        <td>100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
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
        $appointment_date = $row['appointment_date'];
        $appointment_time = $row['appointment_time'];
        $status = $row['status'];
        $appointment_type = $row['appointment_type'];
        $fee = isset($appointment_revenues[$appointment_type]) ? $appointment_revenues[$appointment_type] : 0.00;
        
        // Get current date and time
        $current_date = new DateTime();
        $appointment_datetime = new DateTime($appointment_date . ' ' . $appointment_time);

        // Check if the appointment date has passed
        if ($appointment_datetime < $current_date && $status != 'DONE') {
          $status = 'DONE';
        }

        echo "<tr>
                <td>{$row['patient_name']}</td>
                <td>{$row['appointment_type']}</td>
                <td>{$row['appointment_date']}</td>
                <td>{$row['appointment_time']}</td>
                <td>{$status}</td>
                <td>₱" . number_format($fee, 2) . "</td>
              </tr>";
      }
    } else {
      echo "<tr><td colspan='6'>No appointments found.</td></tr>";
    }
    ?>
  </table>
</div>


    <!-- Hidden Export Content -->
    <div id="contentToExport" style="display:none; text-align:center;">
      <h1 style="font-size: 20px;">Bolasco-Servanez Dental Clinic</h1>
      <p style="font-size: 12px;">Appointments (<span id="currentDate"></span>)</p>
      <table border="1" cellspacing="0" cellpadding="8" style="width:90%; font-size: 12px; margin: auto;">
        <thead>
          <tr>
            <th>Patient Name</th>
            <th>Type</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Fee</th>
          </tr>
        </thead>
        <tbody>
          <?php
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
        </tbody>
      </table>
    </div>
  </div>
  <!-- Hidden Export Content -->
<div id="revenueExport" style="display:none; text-align:center;">
  <h1 style="font-size: 20px;">Bolasco-Servanez Dental Clinic</h1>
  <p style="font-size: 12px;">Revenue (<span id="revcurrentDate"></span>)</p>
  <table border="1" cellspacing="0" cellpadding="8" style="width:90%; font-size: 12px; margin: auto;">
    <thead>
      <tr>
        <th>Appointment Type</th>
        <th>Count</th>
        <th>Revenue</th>
        <th>Avg. Revenue</th>
        <th>% of Total</th>
      </tr>
    </thead>
    <tbody>
      <?php
        // Loop through appointment types and display data
        foreach ($appointments_data as $appointment) {
          $count = $appointment['count'];
          $revenue = $appointment['total_revenue'];
          $avgRevenue = $count > 0 ? $revenue / $count : 0;
          $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;
          echo "<tr>
            <td>{$appointment['appointment_type']}</td>
            <td>{$count}</td>
            <td>₱" . number_format($revenue, 2) . "</td>
            <td>₱" . number_format($avgRevenue, 2) . "</td>
            <td>" . round($percentage, 2) . "%</td>
          </tr>";
        }
      ?>
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td>Total</td>
        <td><?php echo array_sum(array_column($appointments_data, 'count')); ?></td>
        <td>₱<?php echo number_format($totalRevenue, 2); ?></td>
        <td>₱<?php echo $totalRevenue > 0 ? number_format($totalRevenue / array_sum(array_column($appointments_data, 'count')), 2) : '0.00'; ?></td>
        <td>100%</td>
      </tr>
    </tfoot>
  </table>
</div>

  <!-- JS Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="appointment.js"></script>
  <script>

document.getElementById("exportBtn").addEventListener("click", function () {
const element = document.getElementById("contentToExport");

// Make the content temporarily visible for PDF generation
element.style.display = 'block';

const opt = {
margin:       0.5,
filename:     'Bolasco-Servanez Dental Clinic_Appointment.pdf',
image:        { type: 'jpeg', quality: 0.98 },
html2canvas:  { scale: 2 },
jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
};

// Generate the PDF
html2pdf().set(opt).from(element).save().then(function() {
// After saving the PDF, hide the content again
element.style.display = 'none';
});
});
document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

function updateDate() {
const options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };
const today = new Date().toLocaleDateString('en-PH', options);
document.getElementById('currentDate').textContent = today;
}

updateDate(); // Call on load

document.getElementById("revenueExportBtn").addEventListener("click", function () {
    const element = document.getElementById("revenueExport");

    // Update the date
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById("revcurrentDate").innerText = formattedDate;

    // Temporarily show the content
    element.style.display = 'block';

    const opt = {
        margin:       0.5,
        filename:     'Bolasco-Servanez Dental Clinic_Revenue.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // Generate the PDF
    html2pdf().set(opt).from(element).save().then(function () {
        // Hide the content again after export
        element.style.display = 'none';
    });
});
document.getElementById('revcurrentDate').textContent = new Date().toLocaleDateString();

function updateDate() {
const options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };
const today = new Date().toLocaleDateString('en-PH', options);
document.getElementById('currentDate').textContent = today;
}

updateDate(); // Call on load

</script>

</body>
</html>