<?php 
session_start();
include 'db.php'; // Ensure this file correctly initializes $conn


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $allergies = $conn->real_escape_string($_POST['allergies']);
    $medications = $conn->real_escape_string($_POST['medications']);
    $conditions = $conn->real_escape_string($_POST['conditions']);
    $history = $conn->real_escape_string($_POST['history']);
    $insurance_provider = $conn->real_escape_string($_POST['insurance_provider']);
    $policy_number = $conn->real_escape_string($_POST['policy_number']);
    $group_number = $conn->real_escape_string($_POST['group_number']);
    $policy_holder = $conn->real_escape_string($_POST['policy_holder']);
    $relationship = $conn->real_escape_string($_POST['relationship']);

    // Check if email already exists
    $check_email = "SELECT email FROM patients WHERE email='$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        echo "<script>alert('Error: A patient with this email already exists.');</script>";
    } else {
        // Insert patient data
        $sql = "INSERT INTO patients (first_name, last_name, dob, gender, address, email, phone, allergies, medications, conditions, history, insurance_provider, policy_number, group_number, policy_holder, relationship) 
                VALUES ('$first_name', '$last_name', '$dob', '$gender', '$address', '$email', '$phone', '$allergies', '$medications', '$conditions', '$history', '$insurance_provider', '$policy_number', '$group_number', '$policy_holder', '$relationship')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Patient added successfully!'); window.location.href='receptionist_patients.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM patients WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Patient deleted successfully!'); window.location.href='receptionist_patients.php';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
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
                </div>
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
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>
    </div>
    <div class="container">
        <div class="patientheader">
            <h1>Patients</h1>
            <p>View patients</p>
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
        </div>
       
         -->

        <div class="table-wrapper">
            <table class="table-container">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Condition</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                $sql = "SELECT id, first_name, last_name, phone, conditions FROM patients";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['first_name']} {$row['last_name']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['conditions']}</td>
                               <td><a href='?delete={$row['id']}' onclick='return confirm(\'Are you sure?\');'> <button type='submit' class='delete-btn'><i class='fa fa-trash'></i> Delete</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No patients found.</td></tr>";
                }
                ?>
            </table>
        </div>
        


<!-- Add Patient Modal -->
<div id="addPatientModal" class="modal">
    <div class="modal-content ">
        <span class="close-btn" onclick="closeModal()">&times;</span>

        <h2>Add New Patient</h2>
        <p>Enter the patient's information to create a new record</p>
        <div class="tab-navigation">
                    <button class="tab-btn active" onclick="showTab(event, 'personal')">Personal Information</button>
                    <button class="tab-btn" onclick="showTab(event, 'medical')">Medical History</button>    
                    <button class="tab-btn" onclick="showTab(event, 'insurance')">Insurance Details</button>
                </div>
        <form id="addPatientForm" action="add_patient.php" method="POST">
        <div id="personal" class="tab-content active">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input name="first_name" type="text" placeholder="John" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input name="last_name" type="text" placeholder="Smith" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input name="dob" type="date" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <label>Address</label>
            <textarea name="address" placeholder="123 Main St, City, State, ZIP" required></textarea>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input name="email" type="email" placeholder="john.smith@example.com" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input name="phone" type="tel" placeholder="(555) 123-4567" required>
                </div>
            </div>
</div>
<div id="medical" class="tab-content">
            <label>Allergies</label>
            <textarea name="allergies" placeholder="List any allergies or write 'None'"></textarea>

            <label>Current Medications</label>
            <textarea name="medications" placeholder="List current medications or write 'None'"></textarea>

            <label>Medical Conditions</label>
            <textarea name="conditions" placeholder="List any medical conditions or write 'None'"></textarea>
            
            <label>Dental History</label>
            <textarea name="history" placeholder="Previous dental procedures, issues, etc."></textarea>
</div>
<div id="insurance" class="tab-content ">
            <label>Insurance Provider</label>
            <input type="text" name="insurance_provider" placeholder="Provider name">

            <div class="form-row">
                <div class="form-group">
                    <label>Policy Number</label>
                    <input type="text" name="policy_number" placeholder="Policy #">
                </div>
                <div class="form-group">
                    <label>Group Number</label>
                    <input type="text" name="group_number" placeholder="Group #">
                </div>
            </div>

            <label>Policy Holder (if not patient)</label>
            <input type="text" name="policy_holder" placeholder="Full name">

            <label>Relationship to Patient</label>
            <select name="relationship">
                <option value="">Select relationship</option>
                <option value="self">Self</option>
                <option value="spouse">Spouse</option>
                <option value="child">Child</option>
                <option value="parent">Parent</option>
                <option value="other">Other</option>
            </select>
</div>
            <div class="btn-group">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="save-btn">Save Patient</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById("addPatientModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("addPatientModal").style.display = "none";
    }

    // Ensure button correctly triggers modal
    document.getElementById("add-btn").addEventListener("click", openModal);

    // Close modal if clicked outside content
    window.onclick = function(event) {
        var modal = document.getElementById("addPatientModal");
        if (event.target === modal) {
            closeModal();
        }
    };
</script>
<script src="dash.js"></script>
</body>
</html>
