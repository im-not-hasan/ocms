<?php
include 'connect.php';

$patientId = filter_input(INPUT_POST, 'patientId', FILTER_VALIDATE_INT);
$os_sph = filter_input(INPUT_POST, 'os_sph', FILTER_SANITIZE_STRING);
$os_cyl = filter_input(INPUT_POST, 'os_cyl', FILTER_SANITIZE_STRING);
$os_axis = filter_input(INPUT_POST, 'os_axis', FILTER_SANITIZE_STRING);
$os_add = filter_input(INPUT_POST, 'os_add', FILTER_SANITIZE_STRING);
$od_sph = filter_input(INPUT_POST, 'od_sph', FILTER_SANITIZE_STRING);
$od_cyl = filter_input(INPUT_POST, 'od_cyl', FILTER_SANITIZE_STRING);
$od_axis = filter_input(INPUT_POST, 'od_axis', FILTER_SANITIZE_STRING);
$od_add = filter_input(INPUT_POST, 'od_add', FILTER_SANITIZE_STRING);
$pd = filter_input(INPUT_POST, 'pd', FILTER_SANITIZE_STRING);
$lens = filter_input(INPUT_POST, 'lens', FILTER_SANITIZE_STRING);
$frame = filter_input(INPUT_POST, 'frame', FILTER_SANITIZE_STRING);

if ($patientId && $os_sph && $os_cyl && $os_axis && $os_add && $od_sph && $od_cyl && $od_axis && $od_add && $pd && $lens && $frame) {
    $prescription = "OS:\nSPH: $os_sph D\nCYL: $os_cyl\nAXIS: $os_axis\nADD: $os_add\n\nOD:\nSPH: $od_sph D\nCYL: $od_cyl\nAXIS: $od_axis\nADD: $od_add\n\nPD: $pd mm\nLens: $lens\nFrame: $frame";

    $query = $conn->prepare("UPDATE appointments SET prescription = ? WHERE id = ?");
    $query->bind_param("si", $prescription, $patientId);
    $query->execute();

    $insertQuery = $conn->prepare("INSERT INTO prescription (odsph, odcyl, odaxis, odadd, ossph, oscyl, osaxis, osadd, pd, frame, lens, id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertQuery->bind_param("sssssssssssi", $od_sph, $od_cyl, $od_axis, $od_add, $os_sph, $os_cyl, $os_axis, $os_add, $pd, $frame, $lens, $patientId);
    $insertQuery->execute();

    $query->close();
    $insertQuery->close();

    exit();
} else {
    echo "Invalid input detected.";
}

$conn->close();
?>
