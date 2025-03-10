
<?php

      if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $result = $conn->query("SELECT * FROM patient WHERE ID = $id");
         $patient = $result->fetch_assoc();
      }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['number']) && preg_match('/^09[0-9]{9}$/', $_POST['number'])) {
        include 'connect.php';
        echo "<script>var number = document.getElementById('number').value; 
        if (number.length !== 11 || !number.startsWith('09')) {
        alert('Phone number must be 11 digits and start with 09.');
        } </script>";
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
        $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
        $mname = strtoupper(filter_input(INPUT_POST, 'mname', FILTER_SANITIZE_STRING));
        $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);

        $sql = "UPDATE patient SET FName='$fname', LName='$lname', MName='$mname', DOB='$dob', Gender='$gender',  Address='$address', Number='$number' WHERE ID='$id'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Patient details updated successfully!'); window.location.href = 'patients.php';</script>";
        } else {
            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }

        $conn->close();
    }
    
    ?>