<?php
include 'db.php';
require 'vendor/autoload.php'; // PHPMailer


function sendAppointmentConfirmations($conn) {
    $today = date('Y-m-d');
  
    $sql = "SELECT patient_name, email, appointment_type, appointment_date, appointment_time, dentist_name 
            FROM appointments 
            WHERE appointment_date = ? AND status = 'Pending Confirmation'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any appointments exist for today
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recipientEmail = $row['email'];
            $clientName = $row['patient_name'];
            $appointmentType = $row['appointment_type'];
            $appointmentDate = date("F j, Y", strtotime($row['appointment_date']));
            $appointmentTime = date("g:i A", strtotime($row['appointment_time']));
            $dentistName = $row['dentist_name'] ?: 'Dr. Bolasco or assigned dental specialist';
  
            $subject = 'Appointment Confirmation – Bolasco-Servanez Dental Clinic';
            $body = "
                <p>Dear <strong>$clientName</strong>,</p>
    
                <p>We are pleased to confirm your upcoming dental appointment at <strong>Bolasco-Servanez Dental Clinic</strong>.</p>
    
                <p>📅 <strong>Appointment Date:</strong> $appointmentDate<br>
                🕒 <strong>Time:</strong> $appointmentTime<br>
                📍 <strong>Location:</strong> Bolasco-Servanez Dental Clinic</p>
    
                <p><strong>Service:</strong> $appointmentType<br>
                <strong>Dentist:</strong> $dentistName</p>
    
                <p>If you have any questions or need to reschedule, please contact us at <strong>0912-345-6789</strong> or reply to this email at your earliest convenience.</p>
    
                <p>Kindly arrive 10–15 minutes before your appointment time. Don’t forget to bring any necessary documents or identification.</p>
    
                <p>We look forward to seeing you and providing excellent care for your smile!</p>
    
                <br>
                <p>Warm regards,<br>
                <strong>Bolasco-Servanez Dental Clinic Team</strong><br>
                0912-345-6789 | gallardojorie@gmail.com<br>
                <a href='http://localhost/DI%20PA%20RIN%20SURE/dental.php'>Website</a></p>
            ";

            // Initialize PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'gallardojorie@gmail.com';
                $mail->Password = 'ewuwpuaureygsrck'; // Reminder: Store this securely in production
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
  
                $mail->setFrom('gallardojorie@gmail.com', 'Bolasco-Servanez Dental Clinic');
                $mail->addAddress($recipientEmail, $clientName);
  
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body;
  
                $mail->send();
                echo "Confirmation email sent to $recipientEmail<br>";
            } catch (Exception $e) {
                echo "Failed to send to $recipientEmail: {$mail->ErrorInfo}<br>";
            }
        }
    } else {
        echo "No appointments to confirm today.<br>";
    }
}

sendAppointmentConfirmations($conn);
?>
