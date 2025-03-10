<?php
include 'connect.php';

$id = $_POST['id'];
$name = $_POST['name'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];
$brand = $_POST['brand'];
$frame = $_POST['frame'];

if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['image']['tmp_name'];
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = $name . "." . $file_extension;

    $all_path = "Eyewear/All/" . $file_name;
    $frame_path = "Eyewear/All/$frame/" . $file_name;
    $brand_path = "Eyewear/$brand/" . $file_name;
    $brand_frame_path = "Eyewear/$brand/$frame/" . $file_name;

    move_uploaded_file($tmp_name, $all_path);
    copy($all_path, $frame_path);
    copy($all_path, $brand_path);
    copy($all_path, $brand_frame_path);
} else {
    echo "Error uploading file.";
    exit;
}

$query = "INSERT INTO eyewear (ID, Name, Quantity, Price) VALUES ('$id', '$name', '$quantity', '$price')";

if ($conn->query($query) === TRUE) {
    echo "<script>alert('Eyewear added successfully!'); window.location.href='eyewearstock.php';</script>";
} else {
    echo "Error: " . $query . "<br>" . $conn->error;
}

$conn->close();
?>
