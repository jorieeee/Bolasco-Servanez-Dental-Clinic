<?php
include 'db.php';
require 'vendor/autoload.php'; // PHPMailer
require 'book.php'; // wherever sendAppointmentReminders is declared

sendAppointmentReminders($conn);
?>
