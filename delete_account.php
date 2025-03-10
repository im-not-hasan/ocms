<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM login WHERE ID = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo "<script>alert('Account deleted successfully!'); window.location.href='accounts.php';</script>";
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
