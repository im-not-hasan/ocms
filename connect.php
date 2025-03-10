<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ocms";

    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if(!$conn){
        die("Connection Failed" . mysql_connect_error());
    }

?>