<?php
include 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$query = $data['query'];

if ($conn->query($query) === TRUE) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["error" => $conn->error]);
}

$conn->close();
?>
