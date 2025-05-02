<?php
include 'db.php';

if (isset($_GET['id'])) {
    $patient_id = $conn->real_escape_string($_GET['id']);

    // Fetch patient data
    $sql = "SELECT * FROM dental_records WHERE patient_id = '$patient_id' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        echo json_encode($patient);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
