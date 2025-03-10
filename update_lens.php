<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grade']) && isset($_POST['quantity']) && isset($_POST['lifespan']) && isset($_POST['price'])) {
    $grade = $_POST['grade'];
    $quantity = $_POST['quantity'];
    $lifespan = $_POST['lifespan'];
    $price = $_POST['price'];

    $sql = "UPDATE lens SET Quantity = ?, Lifespan = ?, Price = ? WHERE Grade = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('issi', $quantity, $lifespan, $price, $grade); 

        if ($stmt->execute()) {
            echo "<script>alert('Lens details updated successfully!'); window.location.href='lenstock.php';</script>";
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
