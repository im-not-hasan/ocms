<?php
include 'connectlogin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    if ($id && $username && $password) {
        $sql = "INSERT INTO login (ID, Username, Password) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('iss', $id, $username, $hashed_password);

            if ($stmt->execute()) {
                echo "<script>alert('Account added successfully!'); window.location.href='accounts.php';</script>";
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Invalid input detected.</p>";
    }

    $conn->close();
} else {
    echo "<p>Invalid request.</p>";
}
?>
