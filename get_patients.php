<?php
include 'connect.php';

$query = "SELECT CONCAT(FName, ' ', LName) AS Name FROM patient";
$result = $conn->query($query);

$patients = array();
while($row = $result->fetch_assoc()) {
    $patients[] = $row;
}

echo json_encode($patients);

$conn->close();
?>
