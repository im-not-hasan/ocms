<?php
include 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];
$patientid = $data['patientid'];

$query = "INSERT INTO cart (name, patientid) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $name, $patientid);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["error" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
