<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<?php
include 'connect.php';

$patientName = $_GET['patientName'];
$patientId = $_GET['patientId'];

$lensQuery = "SELECT type FROM type";
$lensResult = $conn->query($lensQuery);

$frameQuery = "SELECT material FROM material";
$frameResult = $conn->query($frameQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Prescription</title>
    <style>
        body {
            background-color: rgb(98, 153, 193);
            color: white;
            font-family: Arial, sans-serif;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            font-size: 36px;
        }
        .patient-name {
            text-align: center;
            font-size: 24px;
        }
        .prescription-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .prescription-section {
            flex: 0 0 45%;
        }
        .prescription-section h3 {
            text-align: center;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        select {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button-group {
            display: flex;
            justify-content: flex-end;
        }
        .btn-cancel {
            background-color: red;
            color: white;
            border: 1px solid red;
            padding: 10px 20px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-save {
            background-color: green;
            color: white;
            border: 1px solid green;
            padding: 10px 20px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-cancel:hover {
            background-color: darkred;
        }
        .btn-save:hover {
            background-color: darkgreen;
        }
    </style>
   
</head>
<body>

<div class="container">
    <h2>Add Prescription</h2>
    <p class="patient-name">Patient: <?php echo htmlspecialchars($patientName); ?></p>
    <form id="prescriptionForm" method="post" action="save_prescription.php">
        <input type="hidden" name="patientId" value="<?php echo htmlspecialchars($patientId); ?>">
        <div class="prescription-form">
            <div class="prescription-section">
                <h3>OS (Left Eye)</h3>
                <label for="os-sph">SPH:</label>
                <input type="text" id="os-sph" name="os_sph" required>
                <label for="os-cyl">CYL:</label>
                <input type="text" id="os-cyl" name="os_cyl" required>
                <label for="os-axis">AXIS:</label>
                <input type="text" id="os-axis" name="os_axis" required>
                <label for="os-add">ADD:</label>
                <input type="text" id="os-add" name="os_add" required>
            </div>
            <div class="prescription-section">
                <h3>OD (Right Eye)</h3>
                <label for="od-sph">SPH:</label>
                <input type="text" id="od-sph" name="od_sph" required>
                <label for="od-cyl">CYL:</label>
                <input type="text" id="od-cyl" name="od_cyl" required>
                <label for="od-axis">AXIS:</label>
                <input type="text" id="od-axis" name="od_axis" required>
                <label for="od-add">ADD:</label>
                <input type="text" id="od-add" name="od_add" required>
            </div>
        </div>
        <label for="pd">PD:</label>
        <input type="text" id="pd" name="pd" style="width: 50%;" required>
        <label for="lens">LENS:</label>
        <select id="lens" name="lens" style="width: 50%;" required>
            <?php while ($row = $lensResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row['type']); ?>"><?php echo htmlspecialchars($row['type']); ?></option>
            <?php endwhile; ?>
        </select>
        <label for="frame">FRAME:</label>
        <select id="frame" name="frame" style="width: 50%;" required>
            <?php while ($row = $frameResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row['material']); ?>"><?php echo htmlspecialchars($row['material']); ?></option>
            <?php endwhile; ?>
        </select>
        <div class="button-group">
            <button type="button" class="btn-cancel" onclick="window.close();">Cancel</button>
            <button type="submit" class="btn-save">Save</button>
        </div>
    </form>
</div>
<iframe id="printFrame" style="display: none;"></iframe>

<script>
function validateLensGrade(sph, eye) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_lens_grade.php', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('sph=' + encodeURIComponent(sph) + '&eye=' + encodeURIComponent(eye));

    if (xhr.status === 200) {
        return xhr.responseText === 'true';
    } else {
        console.error('Failed to validate lens grade:', xhr.statusText);
        return false;
    }
}

document.getElementById('prescriptionForm').onsubmit = function(event) {
    event.preventDefault(); 
    var form = this;

    var odSph = document.getElementById('od-sph').value;
    var osSph = document.getElementById('os-sph').value;

    if (!validateLensGrade(odSph, 'OD') || !validateLensGrade(osSph, 'OS')) {
        alert('The entered lens grade is either unavailable or invalid.');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var formData = new FormData(form);
    var params = new URLSearchParams();
    for (var pair of formData.entries()) {
        params.append(pair[0], pair[1]);
    }

    xhr.onload = function() {
        if (xhr.status === 200) {
            showPopup();
        } else {
            console.error('Form submission failed:', xhr.statusText);
        }
    };

    xhr.send(params.toString());
};

function showPopup() {
    alert('Saved Successfully!'); 
    openPrintDialog('<?php echo htmlspecialchars($patientId); ?>');
}

function openPrintDialog(patientId) {
    if (confirm("Do you want to print the receipt?")) {
        var printFrame = document.getElementById('printFrame');
        printFrame.src = `receipt.php?patientId=${encodeURIComponent(patientId)}`;
        
        printFrame.onload = function() {
            printFrame.contentWindow.print();
            printFrame.contentWindow.onafterprint = function(){
                window.close();
            }
        };
    }
}
</script>





</body>
</html>
