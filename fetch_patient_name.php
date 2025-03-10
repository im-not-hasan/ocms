<?php
include 'connect.php';

$id = $_GET['id'];
$query = "SELECT Name FROM Patients WHERE ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode($name);
?>
