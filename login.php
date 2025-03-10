<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT fullname, username, password FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['fullname'] = $row['fullname'];
            header("Location: welcome.php");
            exit();
        } else {
            header("Location: index.php?error=Invalid%20password");
            exit();
        }
    } else {
        header("Location: index.php?error=No%20user%20found%20with%20this%20username");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
