<?php
session_start();
include 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is loaded



function sendAppointmentReminders($conn) {
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    $sql = "SELECT patient_name, email, appointment_type, appointment_date, appointment_time 
            FROM appointments 
            WHERE appointment_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tomorrow);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $recipientEmail = $row['email'];
        $clientName = $row['patient_name'];
        $appointmentType = $row['appointment_type'];

        // Format date and time (24-hour military time)
        $formattedDate = date("F j, Y", strtotime($row['appointment_date']));
        $formattedTime = date("H:i", strtotime($row['appointment_time'])); // 24-hour military time
        $dateTime = $formattedDate . " at " . $formattedTime;

        $subject = 'Your Dental Care Update from Bolasco-Servanez Dental Clinic';
        $body = "
            <p>Dear <strong>$clientName</strong>,</p>
            <p>We hope this message finds you well!</p>
            <p>We’re reaching out with a quick update regarding your dental care at <strong>Bolasco-Servanez Dental Clinic</strong>:</p>
            <p>✅ <strong>$appointmentType</strong> scheduled for <strong>$dateTime</strong></p>
            <p><strong>Here are the details:</strong></p>
            <ul>
                <li><strong>Date/Time:</strong> $dateTime</li>
                <li><strong>Location:</strong> Bolasco-Servanez Dental Clinic</li>
                <li><strong>Dentist:</strong> Dr. Bolasco or assigned dental specialist</li>
            </ul>
            <p>If you have any questions or would like to reschedule, feel free to reply to this email. 
            You can also manage your appointment and view updates through our 
            <a href='http://localhost/DI%20PA%20RIN%20SURE/dental.php'>online portal</a>.</p>
            <br>
            <p>Thank you for trusting us with your smile! 😁<br>
            We look forward to seeing you soon.</p>
            <br>
            <p>Warm regards,<br>
            Marjorie Gallardo<br>
            Administrator<br>
            Bolasco-Servanez Dental Clinic<br>
            0912-345-6789 | gallardojorie@gmail.com<br>
            <a href='http://localhost/DI%20PA%20RIN%20SURE/dental.php'>Website</a></p>
        ";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gallardojorie@gmail.com';
            $mail->Password = 'ewuwpuaureygsrck'; // Make sure your App Password is correct
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('gallardojorie@gmail.com', 'Bolasco-Servanez Dental Clinic');
            $mail->addAddress($recipientEmail, $clientName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {

        }
    }
}





if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $patient_name = $conn->real_escape_string($_POST['patient_name']);
  $email = $conn->real_escape_string($_POST['email']);
  $phone = $conn->real_escape_string($_POST['phone']);
  $appointment_type = $conn->real_escape_string($_POST['appointment_type']);
  $appointment_date = $conn->real_escape_string($_POST['appointment_date']);
  $appointment_time = $conn->real_escape_string($_POST['appointment_time']);
  $appointment_notes = $conn->real_escape_string($_POST['appointment_notes']);

  // Convert appointment_time to 24-hour military time
  $appointment_time_24h = date("H:i", strtotime($appointment_time));

  $check_sql = "SELECT COUNT(*) FROM appointments WHERE appointment_date = '$appointment_date' AND appointment_time = '$appointment_time_24h'";
  $result = $conn->query($check_sql);
  $row = $result->fetch_row();

  if ($row[0] > 0) {
      echo "<script>alert('This time slot is already booked. Please choose another time.'); window.history.back();</script>";
  } else {
      $sql = "INSERT INTO appointments (patient_name, email, phone, appointment_type, appointment_date, appointment_time, appointment_notes) 
              VALUES ('$patient_name', '$email', '$phone', '$appointment_type', '$appointment_date', '$appointment_time_24h', '$appointment_notes')";

      if ($conn->query($sql) === TRUE) {
          echo "<script>alert('Appointment scheduled successfully!'); window.location.href='book.php';</script>";
      } else {
          echo "<script>alert('Error: " . $conn->error . "');</script>";
      }
  }
}




sendAppointmentReminders($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Schedule Appointment</title>
  <link rel="stylesheet" href="bookstyle.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
.slot-btn.disabled {
    background-color: gray;
    color: #666;
    pointer-events: none;
    opacity: 0.6;
}

.slot-btn.selected {
    background-color: black;
    color: white;
}

    
  </style>
</head>
<body>
  <!-- Back to Home Button -->
  <div class="back-to-home">
    <a href="dental.php" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</a>
  </div>
  <div class="container">
    <h1>Schedule New Appointment</h1>
    <p>Fill in the details to book a new appointment</p>

    <div class="tabs">
      <button class="tab active" onclick="switchTab('details')">Appointment Details</button>
    </div>

    <form action="book.php" method="POST">
      <div class="form-section" id="details-tab">
        <!-- Row 1 -->
        <div class="form-row">
          <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="patient_name" placeholder="Enter your full name" required />
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email address" required />
          </div>
        </div>

        <!-- Row 2 -->
        <div class="form-row">
          <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" placeholder="Enter your phone number" required />
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
        </div>

        <!-- Date -->
        <div class="form-row">
          <div class="form-group full-width">
            <label>Date</label>
            <input type="date" name="appointment_date" id="appointment-date" required />
          </div>
        </div>

        <!-- Time Slots -->
        <div class="slots-container">
          <label>Available Time Slots</label>
          <div class="slots-grid">
            <button type="button" class="slot-btn" onclick="setAppointmentTime('9:00 AM')">9:00 AM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('9:30 AM')">9:30 AM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('10:00 AM')">10:00 AM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('10:30 AM')">10:30 AM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('11:00 AM')">11:00 AM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('11:30 AM')">11:30 AM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('1:00 PM')">1:00 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('1:30 PM')">1:30 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('2:00 PM')">2:00 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('2:30 PM')">2:30 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('3:00 PM')">3:00 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('3:30 PM')">3:30 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('4:00 PM')">4:00 PM</button>
            <button type="button" class="slot-btn" onclick="setAppointmentTime('4:30 PM')">4:30 PM</button>
          </div>
        </div>

        <br>
        <div class="form-row">
          <div class="form-group full-width">
            <label>Appointment Notes</label>
            <textarea name="appointment_notes" placeholder="Enter any special instructions or notes for this appointment" rows="4"></textarea>
          </div>
        </div>

        <!-- Submit Section with Icon and Instructions -->
<div class="submit-section">
  <div class="notice">
    <svg class="icon" xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512" fill="#facc15">
      <path d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm0 312c-13.3 0-24-10.7-24-24s10.7-24 
      24-24 24 10.7 24 24-10.7 24-24 24zm24-88c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 
      10.7-24 24-24s24 10.7 24 24v120z"/>
    </svg>
    <span>Please make sure all fields are filled correctly before submitting.</span>
  </div>

  <ul class="appointment-instructions">
    <li>Please arrive 15 minutes before your appointment time</li>
    <li>Bring your insurance card and ID</li>
    <li>Complete any required forms before arrival</li>
    <li>Notify us of any medication changes</li>
  </ul>


</div>
        <!-- Hidden Input for Appointment Time -->
        <input type="hidden" name="appointment_time" id="appointment-time">

        <div class="submit-btn-container">
          <button type="submit" class="submit-btn">Schedule Appointment</button>
        </div>
      </div>
    </form>
  </div>

  <script>
// Disable past dates
const dateInput = document.getElementById('appointment-date');
const today = new Date().toISOString().split('T')[0];
dateInput.min = today;

document.getElementById('appointment-date').addEventListener('change', function () {
    const selectedDate = this.value;
    fetchAvailableSlots(selectedDate);
});

function fetchAvailableSlots(date) {
    fetch(`check_availability.php?date=${date}`)
        .then(response => response.json())
        .then(data => {
            const unavailableSlots = data.unavailableSlots ? data.unavailableSlots.map(slot => slot.trim()) : [];

            document.querySelectorAll('.slot-btn').forEach(button => {
                const time = button.textContent.trim();
                if (unavailableSlots.includes(time)) {
                    button.disabled = true;
                    button.classList.add('disabled');
                    button.classList.remove('selected');
                } else {
                    button.disabled = false;
                    button.classList.remove('disabled');
                }
            });

            // Clear selected time if the selected button is now disabled
            const selectedBtn = document.querySelector('.slot-btn.selected');
            if (selectedBtn && selectedBtn.disabled) {
                selectedBtn.classList.remove('selected');
                document.getElementById('appointment-time').value = '';
            }
        })
        .catch(error => {
            console.error('Error fetching available slots:', error);
        });
}

function setAppointmentTime(time) {
    const clickedButton = Array.from(document.querySelectorAll('.slot-btn')).find(btn => btn.textContent.trim() === time);

    if (!clickedButton.disabled) {
        document.getElementById('appointment-time').value = time;
        document.querySelectorAll('.slot-btn').forEach(btn => btn.classList.remove('selected'));
        clickedButton.classList.add('selected');
    }
}

// Optional: Prevent form submission without selecting a slot
document.querySelector('form').addEventListener('submit', function (e) {
    const selectedTime = document.getElementById('appointment-time').value;
    if (!selectedTime) {
        alert('Please select a time slot before submitting.');
        e.preventDefault();
    }
});
</script>






</body>
</html>
