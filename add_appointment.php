<?php
include 'connect.php';

$id = $_POST['id'];
$patient_id = $_POST['patient_name']; 
$date = $_POST['date'];
$time = $_POST['time'] . ':00'; 
$contact_number = $_POST['contact_number'];

$patient_result = $conn->query("SELECT FName, LName FROM patient WHERE ID = '$patient_id'");
$patient_row = $patient_result->fetch_assoc();
$patient_name = $patient_row['FName'] . ' ' . $patient_row['LName'];

$prescription = '';

$query = "INSERT INTO appointments (ID, Name, Date, Time, Contact, Prescription) VALUES ('$id', '$patient_name', '$date', '$time', '$contact_number', '$prescription')";

if ($conn->query($query) === TRUE) {
    header("Location: appointments.php");
} else {
    echo "Error: " . $query . "<br>" . $conn->error;
}

$conn->close();
?>
