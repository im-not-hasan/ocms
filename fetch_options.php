<?php
include 'connect.php';

$options = array();
$brandResult = $conn->query("SELECT * FROM brand");
while ($row = $brandResult->fetch_assoc()) {
  $options['brand'][] = $row['brandName'];
}

$materialResult = $conn->query("SELECT * FROM material");
while ($row = $materialResult->fetch_assoc()) {
  $options['material'][] = $row['materialName'];
}

$patientResult = $conn->query("SELECT ID FROM patients");
while ($row = $patientResult->fetch_assoc()) {
  $options['patients'][] = ['id' => $row['ID']];
}

$conn->close();

echo json_encode($options);
?>
