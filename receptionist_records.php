<?php 
session_start();
include 'db.php'; // Ensure this file correctly initializes $conn

// Fetch patient records
$patients = $conn->query("SELECT id, first_name, last_name FROM patients");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $conn->real_escape_string($_POST['patient_id']);
    $date_of_visit = $conn->real_escape_string($_POST['date_of_visit']);
    $chief_complaint = $conn->real_escape_string($_POST['chief_complaint']);
    $oral_hygiene = $conn->real_escape_string($_POST['oral_hygiene']);
    $gum_condition = $conn->real_escape_string($_POST['gum_condition']);
    $cavities_detected = $conn->real_escape_string($_POST['cavities_detected']);
    $missing_teeth = $conn->real_escape_string($_POST['missing_teeth']);
    $plaque_tartar = $conn->real_escape_string($_POST['plaque_tartar']);
    $other_observations = $conn->real_escape_string($_POST['other_observations']);
    $recommended_treatment = $conn->real_escape_string($_POST['recommended_treatment']);
    $procedures_done = $conn->real_escape_string($_POST['procedures_done']);
    $next_appointment = $conn->real_escape_string($_POST['next_appointment']);
    $prescribed_medications = $conn->real_escape_string($_POST['prescribed_medications']);
    $dentist_name = $conn->real_escape_string($_POST['dentist_name']);
    $license_number = $conn->real_escape_string($_POST['license_number']);
    
    $sql = "INSERT INTO dental_records (patient_id, date_of_visit, chief_complaint, oral_hygiene, gum_condition, cavities_detected, missing_teeth, plaque_tartar, other_observations, recommended_treatment, procedures_done, next_appointment, prescribed_medications, dentist_name, license_number) 
            VALUES ('$patient_id', '$date_of_visit', '$chief_complaint', '$oral_hygiene', '$gum_condition', '$cavities_detected', '$missing_teeth', '$plaque_tartar', '$other_observations', '$recommended_treatment', '$procedures_done', '$next_appointment', '$prescribed_medications', '$dentist_name', '$license_number')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Dental record added successfully!'); window.location.href='receptionist_records.php';</script>";
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
          /* === Export Button === */
  #exportBtn {
    background: green;
    color: white;
    border: none;
    padding: 10px 40px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: background 0.3s;
    border-radius: 5px;
  }

  #exportBtn:hover {
    background: #ddd;
    color: black;
  }
    </style>
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
            </div>        
        </div>
    </div>
    <div class="sidebar">
        <div>
        <div class="menu">
        <div class="menu">
        <h3>  <a href="receptionist_dash.php"><i class="fas fa-chart-line"></i> Receptionist Dashboard</a></h3>
              <h3>  <a href="receptionist_patients.php"><i class="fas fa-user"></i> Patients</a></h3>
              <h3>  <a href="receptionist_appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a></h3>
              <h3>  <a href="receptionist_records.php"><i class="fas fa-file-alt"></i> Records</a></h3>
            </div>
            </div>
        </div>
        <div class="logout">
        <h3>  <a href="login.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a> </h3>
        </div>
    </div>
    <div class="container">
        <div class="patientheader">
            <h1>Dental Records</h1>
            <p>Manage your patient records</p>
        </div>
    
        <!-- <div class="controls">
            <input type="text" id="search" placeholder="Search patients...">
            
            <div class="filter-group">
                <select id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
        
                <select id="appointmentFilter">
                    <option value="all">All Appointments</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="past">Past</option>
                </select>
        
                <button id="add-btn">+ Add Patient</button>
            </div>
        </div> -->
  
        <div class="table-wrapper">
            <table class="table-container">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Procedures Done Today</th>
                        <th>Date of Visit</th>
                        <th>Next Appointment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $patients = $conn->query("
                        SELECT p.id, p.first_name, p.last_name, 
                           d.procedures_done, d.date_of_visit, d.next_appointment 
                            FROM patients p 
                            LEFT JOIN dental_records d ON p.id = d.patient_id
                            ORDER BY d.date_of_visit DESC
                        ");

                        while ($row = $patients->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <a href="#" onclick="openModal(<?php echo $row['id']; ?>)">
                                <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>
                            </a>
                        </td>
                        <td><?php echo $row['procedures_done'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['date_of_visit'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['next_appointment'] ?: 'N/A'; ?></td>
                        <td>
                            <button id="exportBtn"><i class="fas fa-download"></i> Download</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>    

<!-- Hidden Export Content -->
<div id="contentToExport" style="display:none; font-family: Arial, sans-serif; text-align: center;">
    <h1 style="font-size: 24px; margin-bottom: 20px; font-weight: bold;">Bolasco-Servanez Dental Clinic</h1>
    <p style="font-size: 14px; margin-bottom: 30px;">Appointments and Dental Records (<span id="currentDate"></span>)</p>
    
    <div style="width: 80%; margin: 0 auto; text-align: left;">
        <?php
        // Query to fetch patients, their appointments, and dental records
        $patients = $conn->query("
            SELECT p.first_name, p.last_name, 
                   a.appointment_type, a.appointment_date, a.appointment_time, a.status,
                   d.procedures_done, d.date_of_visit, d.next_appointment, 
                   a.fee 
            FROM patients p
            LEFT JOIN appointments a ON p.id = a.patient_id
            LEFT JOIN dental_records d ON p.id = d.patient_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");

        while ($row = $patients->fetch_assoc()) {
            ?>
            <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 8px; background-color: #f9f9f9;">
                <h3 style="font-size: 18px; margin-bottom: 10px; font-weight: bold;">
                    <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>
                </h3>
                <div style="margin-bottom: 8px;">
                    <strong>Appointment Date:</strong> <?php echo ($row['appointment_date'] ? date('F d, Y', strtotime($row['appointment_date'])) : 'N/A'); ?>
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Appointment Time:</strong> <?php echo ($row['appointment_time'] ?: 'N/A'); ?>
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Status:</strong> <?php echo ($row['status'] ?: 'N/A'); ?>
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Procedures Done:</strong> <?php echo ($row['procedures_done'] ?: 'N/A'); ?>
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Date of Visit:</strong> <?php echo ($row['date_of_visit'] ? date('F d, Y', strtotime($row['date_of_visit'])) : 'N/A'); ?>
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>Next Appointment:</strong> <?php echo ($row['next_appointment'] ? date('F d, Y', strtotime($row['next_appointment'])) : 'N/A'); ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>



<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Edit Patient Record</h2>
        <form id="editForm" action="" method="POST">
            <input type="hidden" name="patient_id" id="patient_id">

            <!-- Row 1: Basic Patient Details -->
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Visit</label>
                    <input type="date" name="date_of_visit" required>
                </div>
                <div class="form-group">
                    <label>Chief Complaint</label>
                    <input type="text" name="chief_complaint" required>
                </div>
            
            </div>

            <!-- Row 2: Oral Health Condition -->
            <div class="form-row">
                <div class="form-group">
                    <label>Oral Hygiene Status</label>
                    <select name="oral_hygiene">
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Gum Condition</label>
                    <select name="gum_condition">
                        <option value="Healthy">Healthy</option>
                        <option value="Inflamed">Inflamed</option>
                        <option value="Bleeding">Bleeding</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cavities Detected</label>
                    <input type="text" name="cavities_detected">
                </div>
                <div class="form-group">
                    <label>Missing Teeth</label>
                    <input type="text" name="missing_teeth">
                </div>
            </div>

            <!-- Row 3: Additional Observations -->
            <div class="form-row">
            <div class="form-group">
                    <label>Procedures Done Today</label>
                    <textarea name="procedures_done"></textarea>
                </div>
               
                <div class="form-group">
                    <label>Other Observations</label>
                    <textarea name="other_observations"></textarea>
                </div>
                <div class="form-group">
                    <label>Recommended Treatment</label>
                    <textarea name="recommended_treatment"></textarea>
                </div>
                <div class="form-group">
                    <label>Prescribed Medications</label>
                    <textarea name="prescribed_medications"></textarea>
                </div>
            </div>

            <!-- Row 4: Treatment & Next Steps -->
            <div class="form-row">
            <div class="form-group">
                    
                    <label>Plaque/Tartar Buildup</label>
                    <select name="plaque_tartar">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Next Appointment Date</label>
                    <input type="date" name="next_appointment">
                </div>
                
                <div class="form-group">
                    <label>Dentist Name</label>
                    <input type="text" name="dentist_name" required>
                </div>
                <div class="form-group">
                    <label>License Number</label>
                    <input type="text" name="license_number" required>
                </div>
            </div>
            <div class="btn-group">
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            <button type="submit" class="save-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

    <script>
        function openModal(id) {
            document.getElementById('patient_id').value = id;
            document.getElementById('modal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }


        function openModal(id) {
    document.getElementById('patient_id').value = id;
    document.getElementById('modal').style.display = 'block';

    // Fetch patient details using AJAX
    fetchPatientData(id);
}

function fetchPatientData(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_patient_data.php?id=' + id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const patientData = JSON.parse(xhr.responseText);
            
            // Populate the modal with the fetched data
            document.querySelector('[name="date_of_visit"]').value = patientData.date_of_visit;
            document.querySelector('[name="chief_complaint"]').value = patientData.chief_complaint;
            document.querySelector('[name="oral_hygiene"]').value = patientData.oral_hygiene;
            document.querySelector('[name="gum_condition"]').value = patientData.gum_condition;
            document.querySelector('[name="cavities_detected"]').value = patientData.cavities_detected;
            document.querySelector('[name="missing_teeth"]').value = patientData.missing_teeth;
            document.querySelector('[name="plaque_tartar"]').value = patientData.plaque_tartar;
            document.querySelector('[name="other_observations"]').value = patientData.other_observations;
            document.querySelector('[name="recommended_treatment"]').value = patientData.recommended_treatment;
            document.querySelector('[name="procedures_done"]').value = patientData.procedures_done;
            document.querySelector('[name="next_appointment"]').value = patientData.next_appointment;
            document.querySelector('[name="prescribed_medications"]').value = patientData.prescribed_medications;
            document.querySelector('[name="dentist_name"]').value = patientData.dentist_name;
            document.querySelector('[name="license_number"]').value = patientData.license_number;
        }
    };
    xhr.send();
}

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

    </script>
</body>
</html>