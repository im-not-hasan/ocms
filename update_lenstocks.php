<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grade']) && isset($_POST['newQuantity'])) {
    $grade = $_POST['grade'];
    $newQuantity = $_POST['newQuantity'];

    $sql = "UPDATE lens SET Quantity = ? WHERE Grade = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('is', $newQuantity, $grade); 

        if ($stmt->execute()) {
            echo "<script>alert('Stock updated successfully!'); window.location.href='lenstock.php';</script>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }

    $conn->close();
} else {
    echo "<p>Invalid request.</p>";
}
?>
