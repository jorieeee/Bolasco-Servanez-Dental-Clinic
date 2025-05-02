<?php
require 'vendor/autoload.php'; // Adjust path if you installed Dompdf manually
include 'db.php';   // Your DB connection

use Dompdf\Dompdf;

// Fetch patient data
$patients = $conn->query("
    SELECT p.first_name, p.last_name, 
           d.procedures_done, d.date_of_visit, d.next_appointment 
    FROM patients p 
    LEFT JOIN dental_records d ON p.id = d.patient_id
    ORDER BY d.date_of_visit DESC
");

// Build HTML content
$html = '
<h1 style="font-size: 20px; text-align:center;">Bolasco-Servanez Dental Clinic</h1>
<p style="font-size: 12px; text-align:center;">Appointments (' . date('Y-m-d') . ')</p>
<table border="1" cellspacing="0" cellpadding="8" style="width:90%; font-size: 12px; margin: auto;">
    <thead>
        <tr>
            <th>Patient Name</th>
            <th>Procedures Done</th>
            <th>Date of Visit</th>
            <th>Next Appointment</th>
        </tr>
    </thead>
    <tbody>';

while ($row = $patients->fetch_assoc()) {
    $html .= '<tr>
                <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>
                <td>' . htmlspecialchars($row['procedures_done'] ?: 'N/A') . '</td>
                <td>' . htmlspecialchars($row['date_of_visit'] ?: 'N/A') . '</td>
                <td>' . htmlspecialchars($row['next_appointment'] ?: 'N/A') . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF file
$dompdf->stream('Patient_Appointments_' . date('Ymd') . '.pdf', ["Attachment" => 1]);
exit;
?>
