<?php
include 'connect.php';

$sph = $_POST['sph'];

$lensQuery = "SELECT quantity FROM lens WHERE GRADE = ?";
$stmt = $conn->prepare($lensQuery);
$stmt->bind_param("s", $sph);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($quantity);

if ($stmt->num_rows > 0) {
    $stmt->fetch();
    if ($quantity >= 1) {
        $updateQuery = "UPDATE lens SET quantity = quantity - 1 WHERE GRADE = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("s", $sph);
        $updateStmt->execute();
        $updateStmt->close();
        echo 'true';
    } else {
        echo 'false';
    }
} else {
    echo 'false';
}

$stmt->close();
$conn->close();
?>
