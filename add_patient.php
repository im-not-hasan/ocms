<?php
include 'connect.php';
session_start();

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($id !== false && $id !== null) {
        $stmt = $conn->prepare("SELECT * FROM patient WHERE ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "<script>alert('Invalid ID.'); window.location.href = 'patients.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['number']) && preg_match('/^09[0-9]{9}$/', $_POST['number'])) {
    include 'connect.php';

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $mname = strtoupper(filter_input(INPUT_POST, 'mname', FILTER_SANITIZE_STRING));
    $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);

    if ($id && $fname && $lname && $mname && $dob && $gender && $address && $number) {
        $stmt = $conn->prepare("INSERT INTO patient (ID, FName, LName, MName, DOB, Gender, Address, Number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $id, $fname, $lname, $mname, $dob, $gender, $address, $number);
        
        if ($stmt->execute()) {
            echo "<script>alert('Patient added successfully!'); window.location.href = 'patients.php';</script>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid input detected.'); window.history.back();</script>";
        exit;
    }

    $conn->close();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<script>alert('Invalid phone number format.'); window.history.back();</script>";
    exit;
}
?>
