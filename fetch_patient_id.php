<?php
include 'connect.php';

$fname = $_GET['fname'];
$lname = $_GET['lname'];

$query = "SELECT id FROM patient WHERE FName = ? AND LName = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $fname, $lname);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$stmt->close();
$conn->close();

echo json_encode($patient);
?>
