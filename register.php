<?php
include 'connectlogin.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['new-username'];
    $password = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-password'];

    if ($password !== $confirmPassword) {
        echo "<script>
            alert('Passwords do not match');
            window.location.href = 'index.php';
        </script>";
        exit();
    }
    else{
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];
    $stmt = $conn->prepare("INSERT INTO login (username, password, fullname, address, contact, dob) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $hashed_password, $fullname, $address, $contact, $dob);

    if ($stmt->execute()) {
        echo "<script>
            alert('Successfully registered!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Failed to register');
            window.location.href = 'index.php';
        </script>";
    }

    $stmt->close();
}
}
$conn->close();
?>
