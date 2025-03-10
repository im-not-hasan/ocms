<?php
include 'connect.php';
$patientId = $_GET['patientId'];

$patientQuery = "SELECT FName, LName, DOB, Address, Number FROM patient WHERE id = ?";
$stmt = $conn->prepare($patientQuery);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

$dob = new DateTime($patient['DOB']);
$now = new DateTime();
$age = $now->diff($dob)->y;

$prescriptionQuery = "
    SELECT 
        odsph, odcyl, odaxis, odadd, 
        ossph, oscyl, osaxis, osadd, 
        pd, frame, lens,
        (SELECT price FROM lens WHERE grade = p.ossph) AS osPrice,
        (SELECT price FROM lens WHERE grade = p.odsph) AS odPrice
    FROM 
        prescription p 
    WHERE 
        id = ?
";
$stmt = $conn->prepare($prescriptionQuery);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();
$prescription = $result->fetch_assoc();
$stmt->close();

$cartEyewearQuery = "SELECT name FROM cart WHERE patientid = ?";
$stmt = $conn->prepare($cartEyewearQuery);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();
$cartEyewear = $result->fetch_assoc();
$stmt->close();

$eyewearPriceQuery = "SELECT price FROM eyewear WHERE name = ?";
$stmt = $conn->prepare($eyewearPriceQuery);
$stmt->bind_param("s", $cartEyewear['name']);
$stmt->execute();
$result = $stmt->get_result();
$eyewearPrice = $result->fetch_assoc()['price'];
$stmt->close();

$totalPrice = $prescription['osPrice'] + $prescription['odPrice'] + $eyewearPrice;



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sayson Almaras Optical Clinic Prescription</title>
    <style>

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: white; 
            }
        }


       body {
          font-family: Arial, sans-serif;
          background-color: white;
          color: black;
          background-image: url('logos/printbg2.png');
          background-size: 75%;
          background-position: center;
          background-repeat: no-repeat;
      }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid black;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            page-break-after: always;
        }
        .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid black;
    margin-bottom: 20px;
    padding-bottom: 10px;
}
.header .date {
    text-align: left;
}
.header .time {
    text-align: right;
}

        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin-bottom: 10px;
        }
        .section p {
            margin: 5px 0;
        }
        .prescription-details {
            display: flex;
            justify-content: space-between;
        }
        .eye-section {
            width: 45%;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid black;
            padding-top: 10px;
        }
        .footer p {
            margin: 5px 0;
        }
        @media print {
            @page {
                margin: 1in; 
            }
            body, html {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
            }
            
            .container {
                width: calc(100% - 1in); 
                height: calc(100% - 1in); 
                padding: 20px;
                margin: 1in auto; 
                border: 1px solid black !important;
                box-shadow: none;
                page-break-after: always;
                box-sizing: border-box;
            }
            @page {
                margin: 1in;
                size: auto;
            }
            @page :first {
                margin: 0;
            }
            body::before, body::after {
                display: none;
            }
         
            header, footer {
                display: none !important;
                height: 0 !important;
                visibility: hidden !important;
            }
          }
          .prescription-table {
              width: 100%;
              border-collapse: collapse;
              margin-bottom: 20px;
              margin-left: auto;
              margin-right: auto;
          }

          .prescription-table th, .prescription-table td {
              border: 1px solid black;
              width: 100px; 
              height: 100px;
              text-align: center; 
              vertical-align: middle; 
              box-sizing: border-box; 
          }



    </style>
</head>
<body>

<div class="container">
        <h1 style="text-align:center;">Sayson Almaras Optical Clinic</h1>
    <div class="header">
    <div class="date">
        <p>Date: <?php echo date("d/m/Y"); ?></p>
    </div>
    <div class="time">
        <p>Time: <?php echo date("H:i"); ?></p>
    </div>
</div>


    <div class="section">
        <h3>Patient Information</h3>
        <p><strong>Patient's Name:</strong> <?php echo htmlspecialchars($patient['FName'] . ' ' . $patient['LName']); ?></p>
        <p><strong>Age:</strong> <?php echo $age; ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['Address']); ?></p>
        <p><strong>Contact #:</strong> <?php echo htmlspecialchars($patient['Number']); ?></p>
        <p><strong>Chief Complaint/s:</strong> N/A</p>
    </div>

    <div class="section">
        <h3>Prescription Details</h3>
        <table class="prescription-table">
            <tr>
                <th></th>
                <th>SPH</th>
                <th>CYL</th>
                <th>AXIS</th>
                <th>ADD</th>
            </tr>
            <tr>
                <th>OD (Right Eye)</th>
                <td><?php echo htmlspecialchars($prescription['odsph']); ?></td>
                <td><?php echo htmlspecialchars($prescription['odcyl']); ?></td>
                <td><?php echo htmlspecialchars($prescription['odaxis']); ?></td>
                <td><?php echo htmlspecialchars($prescription['odadd']); ?></td>
            </tr>
            <tr>
                <th>OS (Left Eye)</th>
                <td><?php echo htmlspecialchars($prescription['ossph']); ?></td>
                <td><?php echo htmlspecialchars($prescription['oscyl']); ?></td>
                <td><?php echo htmlspecialchars($prescription['osaxis']); ?></td>
                <td><?php echo htmlspecialchars($prescription['osadd']); ?></td>
            </tr>
        </table>
        <p><strong>PD:</strong> <?php echo htmlspecialchars($prescription['pd']); ?></p>
        <p><strong>FRAME:</strong> <?php echo htmlspecialchars($prescription['frame']); ?></p>
        <p><strong>LENS:</strong> <?php echo htmlspecialchars($prescription['lens']); ?></p>
        <p><strong>EXPIRY DATE:</strong> <?php echo date('d/m/Y', strtotime('+1 year')); ?></p>
    </div>

    <div class="footer">
    <p><strong>TOTAL PRICE:</strong> PHP <?php echo number_format($totalPrice, 2); ?></p>
    <p><strong>Prescribed by:</strong> Dr. Ana Eva N. Sayson</p>
</div>

</div>
<script>
    window.addEventListener('beforeunload', function (e) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_entries.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send();
    });
</script>

</body>
</html>
