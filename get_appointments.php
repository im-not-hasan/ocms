<?php
include 'connect.php';

$query = "SELECT * FROM appointments";
$result = $conn->query($query);

$appointments = array();
while($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);

$conn->close();
?>
