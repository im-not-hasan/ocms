<?php
include 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];

$query = "UPDATE eyewear SET quantity = (quantity - 1) WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $name);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["error" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
