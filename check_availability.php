<?php
include 'db.php';

if (isset($_GET['date'])) {
    $date = $conn->real_escape_string($_GET['date']);

    $sql = "SELECT appointment_time FROM appointments WHERE appointment_date = '$date'";
    $result = $conn->query($sql);

    $unavailableSlots = [];

    while ($row = $result->fetch_assoc()) {
        $unavailableSlots[] = trim($row['appointment_time']);
    }

    echo json_encode(['unavailableSlots' => $unavailableSlots]);
}
?>
