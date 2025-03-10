<?php
include 'connect.php';

$name = $_GET['name'];
$nameParts = explode(' ', $name);
$fname = $nameParts[0];
$lname = $nameParts[1];

$query = "SELECT * FROM patient WHERE FName = ? AND LName = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $fname, $lname);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode($details);
?>
