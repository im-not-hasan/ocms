<?php
include 'connect.php';

$truncateCartQuery = "TRUNCATE TABLE cart";
$conn->query($truncateCartQuery);

$truncatePrescriptionQuery = "TRUNCATE TABLE prescription";
$conn->query($truncatePrescriptionQuery);
?>
