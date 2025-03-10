<?php
include 'connect.php';

$name = $_GET['name'];
$query = "SELECT Quantity FROM Eyewear WHERE Name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($quantity);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode($quantity);
?>
