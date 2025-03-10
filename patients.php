<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<?php
include 'connect.php';

$rowsPerPage = isset($_GET['rows']) ? $_GET['rows'] : 25;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

if ($rowsPerPage == 'All') {
  $query = "SELECT * FROM patient";
} else {
  $query = "SELECT * FROM patient LIMIT $offset, $rowsPerPage";
}
$result = $conn->query($query);

$totalQuery = "SELECT COUNT(*) AS total FROM patient";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];

$conn->close();
?>
<?php
include 'connect.php';

$rowsPerPage = isset($_GET['rows']) ? $_GET['rows'] : 25;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

if ($rowsPerPage == 'All') {
  $query = "SELECT ID, FName, LName, MName, DOB, Gender, Address, Number FROM patient";
} else {
  $query = "SELECT ID, FName, LName, MName, DOB, Gender, Address, Number FROM patient LIMIT $offset, $rowsPerPage";
}
$result = $conn->query($query);

$totalQuery = "SELECT COUNT(*) AS total FROM patient";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Patients</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: rgb(98, 153, 193);
      margin: 0;
      overflow: auto;
      display: flex;
    }
    .sidebar {
      width: 60px;
      background-color: rgb(59, 113, 151);
      height: 100vh;
      position: fixed;
      transition: width 0.3s;
      overflow: hidden;
    }
    .sidebar.expanded {
      width: 200px;
    }
    .sidebar button {
      width: 60px;
      height: 60px;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      outline: none;
    }
    .sidebar button img {
      width: 50px;
      height: 50px;
    }
    .sidebar button:hover {
      background-color: steelblue;
    }
    .sidebar-content {
      color: white;
      text-align: left;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      padding: 10px;
      color: white;
      text-decoration: none;
      transition: background-color 0.3s, padding-left 0.3s;
    }
    .sidebar a:hover {
      background-color: steelblue;
    }
    .sidebar img {
      margin-left: -5px;
      width: 45px;
      height: 45px;
    }
    .sidebar .link-text {
      display: none;
      margin-left: 10px;
    }
    .sidebar.expanded .link-text {
      display: inline;
    }
    .main-content {
      margin-left: 60px;
      padding: 20px;
      width: 100%;
      transition: margin-left 0.3s;
      display: flex;
      flex-wrap: wrap;
    }
    .main-content h1 {
      color: white;
      text-align: left;
      padding-left: 20px;
      width: 100%;
    }
    .card {
      width: 100%;
      background-color: steelblue;
      box-shadow: 2px 2px 12px steelblue;
      border-radius: 10px;
      padding: 20px;
      margin: 10px 0;
      color: white;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid white;
    }
    th, td {
      padding: 15px;
      text-align: center;
    }
    th {
      background-color: rgb(98, 153, 193);
    }
    .btn-update {
      background-color: green;
      color: white;
      padding: 10px 10px;
      text-decoration: none;
      border-radius: 5px;
      border:none;
      cursor: pointer;
    }
    .btn-update:hover{
      background-color: darkgreen;
    }
    .btn-delete {
      background-color: red;
      color: white;
      padding: 10px 10px;
      text-decoration: none;
      border-radius: 5px;
      border:none;
      cursor: pointer;
    }
    .btn-delete:hover{
      background-color:darkred;
    }
    .btn-add {
      background-color: green;
      color: white;
      padding: 15px 20px;
      text-decoration: none;
      border-radius: 5px;
      text-align: center;
      margin: 20px auto;
      cursor: pointer;
      border:none;
      font-weight: bold;
    }
    .btn-add:hover{
      background-color: darkgreen;
    }
    .actions {
      width: 150px;
    }
    
 
.modal-background {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4); 
}


.modal-content {
  background-color: rgb(98, 153, 193);; 
  margin: 5% auto; 
  padding: 20px;
  border: 1px solid rgb(59, 113, 151);
  width: 80%;
  max-width: 450px; 
  color: white; 
  border-radius: 10px; 
  box-shadow: 0 5px 15px rgb(59, 113, 151); 
}


.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  margin-left: 10px;
}

.close:hover,
.close:focus {
  color: white; 
  text-decoration: none;
  cursor: pointer;
}


.modal-content form {
  display: flex;
  flex-direction: column;
}

.modal-content label {
  margin: 10px 0 5px; 
  font-weight: bold;
}

.modal-content input,
.modal-content select {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-sizing: border-box;
  width: 100%;
  margin-bottom: 15px;
}

.button-group {
  display: flex;
  justify-content: flex-end; 
}

.btn-submit,
.btn {
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  color: white;
  background-color: green; 
  margin-left: 10px;
}

.btn-submit:hover,
.btn:hover {
  background-color: darkgreen; 
}

.btn {
  background-color: red; 
}

.btn:hover {
  background-color: darkred;
}
button{
  transition: background-color 0.3s;
}
.pagination a {
  color: white; 
  text-decoration: none;
  margin: 0 10px; 
  padding: 5px 10px; 
  border-radius: 3px; 
  background-color: steelblue;
  transition: background-color 0.3s; 
}

.pagination a:hover {
  background-color: darkblue; 
}

  </style>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const rowsPerPage = urlParams.get('rows');
    
    if (rowsPerPage) {
      document.getElementById('rows-per-page').value = rowsPerPage;
    }
  });

  function changeRowsPerPage() {
    const rowsPerPage = document.getElementById('rows-per-page').value;
    window.location.href = `patients.php?rows=${rowsPerPage}&page=1`;
  }

function changeRowsPerPage() {
  const rowsPerPage = document.getElementById('rows-per-page').value;
  window.location.href = `patients.php?rows=${rowsPerPage}&page=1`;
}


  function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var mainContent = document.getElementById('main-content');
    sidebar.classList.toggle('expanded');
    mainContent.classList.toggle('shifted');
  }

  function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
  }

  function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
  }

 
  function openUpdateModal(id, fname, lname, mname, dob, gender, address, number) {
    var today = new Date().toISOString().split('T')[0];

    document.getElementById('updatePatientForm').innerHTML = `
      <input type="hidden" name="id" value="${id}">
      <div>
        <label for="fname">First Name</label>
        <input type="text" id="fname" name="fname" value="${fname}" required>
      </div>
      <div>
        <label for="lname">Last Name</label>
        <input type="text" id="lname" name="lname" value="${lname}" required>
      </div>
      <div>
        <label for="mname">Middle Initial</label>
        <input type="text" id="mname" name="mname" value="${mname}" maxlength="1">
      </div>
      <div>
        <label for="dob">Date of Birth</label>
        <br>
        <input type="date" id="dob" name="dob" max="${today}" value="${dob}" style="width: 150px;" required>
      </div>
      <div>
        <label for="gender">Gender</label>
        <br>
        <select id="gender" name="gender" style="width: 100px;" required>
          <option value="Male" ${gender === 'Male' ? 'selected' : ''}>Male</option>
          <option value="Female" ${gender === 'Female' ? 'selected' : ''}>Female</option>
          <option value="Other" ${gender === 'Other' ? 'selected' : ''}>Other</option>
        </select>
      </div>
      <div>
        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="${address}" required>
      </div>
      <div>
        <label for="number">Phone Number</label>
        <br>
        <input type="text" id="number" name="number" value="${number}" maxlength="11" required>
      </div>
      <div class="button-group">
        <button type="submit" class="btn-submit">Update</button>
      </div>
    `;
    openModal('updatePatientModal');
  }




function openAddModal() {
  <?php
  include 'connect.php';
  $result = $conn->query("SELECT COUNT(*) AS count FROM patient");
  $row = $result->fetch_assoc();
  $next_id = $row['count'] + 1;
  ?>
  var today = new Date().toISOString().split('T')[0];
  document.getElementById('addPatientForm').innerHTML = `
    <div>
      <label for="id">ID</label>
      <br>
      <input type="text" id="id" name="id" value="${<?php echo $next_id; ?>}" style="width: 40px;" readonly>
    </div>
    <div>
      <label for="fname">First Name</label>
      <input type="text" id="fname" name="fname" required>
    </div>
    <div>
      <label for="lname">Last Name</label>
      <input type="text" id="lname" name="lname" required>
    </div>
    <div>
      <label for="mname">Middle Initial</label>
      <input type="text" id="mname" name="mname" maxlength="1">
    </div>
    <div>
      <label for="dob">Date of Birth</label>
      <br>
      <input type="date" id="dob" name="dob" max="${today}" style="width: 150px;" required>
    </div>
    <div>
      <label for="gender">Gender</label>
      <br>
      <select id="gender" name="gender" style="width: 100px;" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>
    </div>
    <div>
      <label for="address">Address</label>
      <input type="text" id="address" name="address" required>
    </div>
    <div>
      <label for="number">Phone Number</label>
      <br>
      <input type="text" id="number" name="number" maxlength="11" required>
    </div>
    <div class="button-group">
      <button type="submit" class="btn-submit">Add Patient</button>
    </div>
  `;
  openModal('addPatientModal');
}


  function openDeleteModal(id) {
    document.getElementById('deletePatientForm').innerHTML = 
    `<input type="hidden" name="id" value="${id}">
      <p style="margin-top: 0px;">Are you sure you want to delete this patient's records?</p>
      <div class="button-group">
      <button type="submit" class="btn-submit" style="margin-top:10px;">Confirm</button>
      <button type="button" class="btn" style="margin-top:10px;" onclick="closeModal('deletePatientModal')">Cancel</button>
      </div>
    `;
    openModal('deletePatientModal');
  }

  function showNotificationAndRedirect() {
      alert('Patient added successfully!');
      window.location.href = 'patients.php';
    }

    function validateForm() {
      var number = document.getElementById('number').value;
      if (/\D/.test(number)) {
          alert("Please enter only numbers.");
          return false;
      }
      if (number.length !== 11 || !number.startsWith('09')) {
        alert('Phone number must be 11 digits and start with 09.');
        return false;
      }
      return true;
    }
    function confirmLogout() {
      if (confirm("Do you want to logout? Any unsaved progress will be lost.")) {
    window.location.href = 'logout.php';
    } 
    }
    function deleteRecord() {
      confirm("Are you sure you want to delete this record?")
    }

    function showAlert() {
      alert("Deleted successfully");
    }
</script>
</head>
<body>
<div id="sidebar" class="sidebar">
  <button onclick="toggleSidebar()">
    <img src="icons/eye-clinic.png" alt="Eye Clinic">
  </button>
  <div class="sidebar-content">
    <a href="dashboard.php">
      <img src="icons/dashboard.png" alt="Dashboard">
      <span class="link-text">Dashboard</span>
    </a>
    <a href="appointments.php">
      <img src="icons/appointments.png" alt="Appointments">
      <span class="link-text">Appointments</span>
    </a>
    <a href="eyewear.php">
      <img src="icons/eyewear.png" alt="Eyewear">
      <span class="link-text">Eyewear</span>
    </a>
    <a href="patients.php" style="background-color: rgb(98, 153, 193);">
      <img src="icons/patients.png" alt="Patients">
      <span class="link-text">Patients</span>
    </a>
    <a href="settings.php">
      <img src="icons/settings.png" alt="Settings">
      <span class="link-text">Settings</span>
    </a>
    <a onclick="confirmLogout()">
      <img src="icons/logout.png" alt="Logout" style="margin-left: 0;">
      <span class="link-text">Logout</span>
    </a>
  </div>
</div>

<div id="main-content" class="main-content">
  <h1 style="font-size: 2.5em;">Patients</h1>
  <label for="rows-per-page" style="color: white; margin-left: 10px; ">Rows per page: </label>
<select id="rows-per-page" onchange="changeRowsPerPage()">
  <option value="25">25</option>
  <option value="50">50</option>
  <option value="75">75</option>
  <option value="100">100</option>
  <option value="100">250</option>
  <option value="100">500</option>
</select>

  <div class="card">
  <?php
include 'connect.php';

$rowsPerPage = isset($_GET['rows']) ? $_GET['rows'] : 25;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

if ($rowsPerPage == 'All') {
  $query = "SELECT ID, FName, LName, MName, DOB, Gender, Address, Number FROM patient";
} else {
  $query = "SELECT ID, FName, LName, MName, DOB, Gender, Address, Number FROM patient LIMIT $offset, $rowsPerPage";
}
$result = $conn->query($query);

$totalQuery = "SELECT COUNT(*) AS total FROM patient";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
?>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Middle Name</th>
      <th>Date of Birth</th>
      <th>Gender</th>
      <th>Address</th>
      <th>Contact Number</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['ID']; ?></td>
      <td><?php echo $row['FName']; ?></td>
      <td><?php echo $row['LName']; ?></td>
      <td><?php echo $row['MName']; ?></td>
      <td><?php echo $row['DOB']; ?></td>
      <td><?php echo $row['Gender']; ?></td>
      <td><?php echo $row['Address']; ?></td>
      <td><?php echo $row['Number']; ?></td>
      <td>
        <button class='btn-update' onclick="openUpdateModal('<?php echo $row['ID']; ?>', '<?php echo $row['FName']; ?>', '<?php echo $row['LName']; ?>', '<?php echo $row['MName']; ?>', '<?php echo $row['DOB']; ?>', '<?php echo $row['Gender']; ?>', '<?php echo $row['Address']; ?>', '<?php echo $row['Number']; ?>')">Update</button>
        <button class='btn-delete' onclick="openDeleteModal('<?php echo $row['ID']; ?>')">Delete</button>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</div>
<div class="pagination">
<?php
$totalPages = $rowsPerPage == 'All' ? 1 : ceil($totalRows / $rowsPerPage);

for ($i = 1; $i <= $totalPages; $i++): ?>
  <a href="patients.php?rows=<?php echo $rowsPerPage; ?>&page=<?php echo $i; ?>" <?php if ($i == $page); ?>>
    <?php echo $i; ?>
  </a>
<?php endfor; 
?>
</div>
  
  <button class="btn-add" onclick="openAddModal()">Add a New Patient</button> 
</div>

<div id="updatePatientModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('updatePatientModal')">&times;</span>
    <h2 style="text-align:center; margin-left:30px;">Update Patient</h2>
    <form id="updatePatientForm" action="update_patient.php" method="post">
    </form>
  </div>
</div>

<div id="deletePatientModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('deletePatientModal')">&times;</span>
    <h2>Delete Patient</h2>
    <form id="deletePatientForm" action="delete_patient.php" method="post">
      </form>
    
  </div>
</div>

<div id="addPatientModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addPatientModal')">&times;</span>
    <h2 style="text-align:center; margin-left:30px;" >Add a New Patient</h2>
    <php>
    <form id="addPatientForm" action="add_patient.php" method="post" onsubmit="return validateForm()">>
    </form>
  </div>
</div>

</body>
</html>
