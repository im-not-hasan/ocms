<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<?php
include 'connect.php';

$totalAppointmentsQuery = "SELECT COUNT(*) AS total FROM appointments";
$totalAppointmentsResult = $conn->query($totalAppointmentsQuery);
$totalAppointments = $totalAppointmentsResult->fetch_assoc()['total'];

$now = date('Y-m-d H:i:s');
$activeAppointmentsQuery = "SELECT COUNT(*) AS active FROM appointments WHERE CONCAT(Date, ' ', Time) >= '$now'";
$activeAppointmentsResult = $conn->query($activeAppointmentsQuery);
$activeAppointments = $activeAppointmentsResult->fetch_assoc()['active'];

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
  <title>Appointments</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: rgb(98, 153, 193);
      margin: 0;
      overflow: hidden;
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
  </style>
  <script>
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
document.addEventListener('DOMContentLoaded', function() {
  const dateInput = document.getElementById('date');
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); 
  const dd = String(today.getDate()).padStart(2, '0');

  const minDate = `${yyyy}-${mm}-${dd}`;
  dateInput.setAttribute('min', minDate);
});

function openAddModal() {
  <?php
  include 'connect.php';
  $result = $conn->query("SELECT MAX(ID) AS max_id FROM appointments");
  $row = $result->fetch_assoc();
  $next_id = $row['max_id'] + 1;

  $patient_result = $conn->query("SELECT ID, FName, LName, Number FROM patient");
  $patients = [];
  while ($patient_row = $patient_result->fetch_assoc()) {
    $patients[] = $patient_row;
  }
  ?>
  document.getElementById('addAppointmentForm').innerHTML = `
    <input type="hidden" id="id" name="id" value="${<?php echo $next_id; ?>}">
    <div>
      <label for="date">Date</label>
      <input type="date" id="date" name="date" required>
    </div>
    <div>
      <label for="time">Time</label>
      <select id="time" name="time" required>
        <option value="09:00">9:00 AM</option>
        <option value="09:30">9:30 AM</option>
        <option value="10:00">10:00 AM</option>
        <option value="10:30">10:30 AM</option>
        <option value="11:00">11:00 AM</option>
        <option value="11:30">11:30 AM</option>
        <option value="12:00">12:00 PM</option>
        <option value="12:30">12:30 PM</option>
        <option value="13:00">1:00 PM</option>
        <option value="13:30">1:30 PM</option>
        <option value="14:00">2:00 PM</option>
        <option value="14:30">2:30 PM</option>
        <option value="15:00">3:00 PM</option>
        <option value="15:30">3:30 PM</option>
        <option value="16:00">4:00 PM</option>
        <option value="16:30">4:30 PM</option>
        <option value="17:00">5:00 PM</option>
      </select>
    </div>
    <div style="display: flex;">
      <div style="flex: 4;">
        <label for="patient_name">Name</label>
        <select id="patient_name" name="patient_name" onchange="populatePatientDetails()" required>
          <option value="">Select a patient</option>
          <?php foreach ($patients as $patient) { ?>
            <option value="<?php echo $patient['ID']; ?>"><?php echo $patient['FName'] . ' ' . $patient['LName']; ?></option>
          <?php } ?>
        </select>
      </div>
      <div style="flex: 1; margin-left: 10px;">
        <label for="patient_id">Patient ID</label>
        <input type="text" id="patient_id" name="patient_id" readonly>
      </div>
    </div>
    <div>
      <label for="contact_number">Contact Number</label>
      <input type="text" id="contact_number" name="contact_number" readonly>
    </div>
    <div class="button-group">
      <button type="submit" class="btn-submit">Add Appointment</button>
    </div>
  `;
  openModal('addAppointmentModal');
  

  const dateInput = document.getElementById('date');
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); 
  const dd = String(today.getDate()).padStart(2, '0');
  const minDate = `${yyyy}-${mm}-${dd}`;
  dateInput.setAttribute('min', minDate);
}

function populatePatientDetails() {
  const patientSelect = document.getElementById('patient_name');
  const selectedPatientID = patientSelect.value;
  const patients = <?php echo json_encode($patients); ?>;

  const selectedPatient = patients.find(patient => patient.ID == selectedPatientID);

  document.getElementById('patient_id').value = selectedPatient ? selectedPatient.ID : '';
  document.getElementById('contact_number').value = selectedPatient ? selectedPatient.Number : '';
}


  function openDeleteModal(id) {
    document.getElementById('deleteAppointmentForm').innerHTML = 
    `<input type="hidden" name="id" value="${id}">
      <p style="margin-top: 0px;">Are you sure you want to delete this appointment?</p>
      <div class="button-group">
      <button type="submit" class="btn-submit" style="margin-top:10px;">Confirm</button>
      <button type="button" class="btn" style="margin-top:10px;" onclick="closeModal('deleteAppointmentModal')">Cancel</button>
      </div>
    `;
    openModal('deleteAppointmentModal');
  }

  function showNotificationAndRedirect() {
      alert('Appointment added successfully!');
      window.location.href = 'appointments.php';
    }

    function validateForm() {
      var contact_number = document.getElementById('contact_number').value;
      if (contact_number.length !== 11 || !contact_number.startsWith('09')) {
        alert('Contact number must be 11 digits and start with 09.');
        return false;
      }
      return true;
    }
    function confirmLogout() {
      if (confirm("Do you want to logout? Any unsaved progress will be lost.")) {
    window.location.href = 'logout.php';
    } 
    }
    document.addEventListener('DOMContentLoaded', function() {
  loadAppointments();
  loadPatientNames();
});

function loadAppointments() {
  fetch('get_appointments.php')
    .then(response => response.json())
    .then(appointments => {
      const appointmentTableBody = document.getElementById('appointment-table-body');
      appointmentTableBody.innerHTML = '';

      let totalAppointments = 0;
      let activeAppointments = 0;
      const now = new Date();

      appointments.forEach(appointment => {
        const appointmentDateTime = new Date(appointment.Date + 'T' + appointment.Time);
        
        if (document.getElementById('show-active-only').checked && appointmentDateTime < now) {
          return; 
        }

        if (appointmentDateTime >= now) {
          activeAppointments++;
        }
        totalAppointments++;

        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${appointment.ID}</td>
          <td>${appointment.Name}</td>
          <td>${appointment.Date}</td>
          <td>${appointment.Time}</td>
          <td>${appointment.Contact}</td>
          <td>${appointment.Prescription}</td>
        `;
        appointmentTableBody.appendChild(row);
      });
    })
    .catch(error => console.error('Error fetching appointments:', error));
}

function filterAppointments() {
  const patientNameFilter = document.getElementById('patient-name-filter').value.toLowerCase();
  const rows = document.querySelectorAll('#appointment-table-body tr');
  
  rows.forEach(row => {
    const name = row.children[4].textContent.toLowerCase();
    const isVisible = (patientNameFilter === '' || name.includes(patientNameFilter)) &&
                      (!document.getElementById('show-active-only').checked || new Date(row.children[1].textContent + 'T' + row.children[2].textContent) >= new Date());
    row.style.display = isVisible ? '' : 'none';
  });

  updateCounts();
}

function updateCounts() {
  const rows = document.querySelectorAll('#appointment-table-body tr');
  let totalAppointments = 0;
  let activeAppointments = 0;

  rows.forEach(row => {
    if (row.style.display !== 'none') {
      totalAppointments++;
      const appointmentDateTime = new Date(row.children[1].textContent + 'T' + row.children[2].textContent);
      if (appointmentDateTime >= new Date()) {
        activeAppointments++;
      }
    }
  });
}

function loadPatientNames() {
  fetch('get_patients.php')
    .then(response => response.json())
    .then(patients => {
      const patientNameFilter = document.getElementById('patient-name-filter');
      patients.forEach(patient => {
        const option = document.createElement('option');
        option.value = patient.Name;
        option.textContent = patient.Name;
        patientNameFilter.appendChild(option);
      });
    })
    .catch(error => console.error('Error fetching patient names:', error));
}


function filterAppointments() {
  loadAppointments();
}

</script>
</head>
<body>
<script>
    function deleteRecord() {
      confirm("Are you sure you want to delete this record?")
    }

    function showAlert() {
      alert("Deleted successfully");
    }
  </script>
<div id="sidebar" class="sidebar">
  <button onclick="toggleSidebar()">
    <img src="icons/eye-clinic.png" alt="Eye Clinic">
  </button>
  <div class="sidebar-content">
    <a href="dashboard.php">
      <img src="icons/dashboard.png" alt="Dashboard">
      <span class="link-text">Dashboard</span>
    </a>
    <a href="appointments.php" style="background-color: rgb(98, 153, 193);">
      <img src="icons/appointments.png" alt="Appointments">
      <span class="link-text">Appointments</span>
    </a>
    <a href="eyewear.php">
      <img src="icons/eyewear.png" alt="Eyewear">
      <span class="link-text">Eyewear</span>
    </a>
    <a href="patients.php">
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
  <h1 style="font-size: 2.5em;">Appointments</h1>
  
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <p>Total Appointments: <span><?php echo $totalAppointments; ?></span></p>
  </div>
  <div>
        <p>Active Appointments: <span><?php echo $activeAppointments; ?></span></p>
      </div>
     <!--<div>
        <label for="patient-name-filter">Patient Name:</label>
        <select id="patient-name-filter" onchange="filterAppointments()">
          <option value="">All</option>
           Options will be populated dynamically 
        </select>
      </div>-->
      <div>
        <label>
          <input type="checkbox" id="show-active-only" onclick="filterAppointments()"> Show Active Only
        </label>
      </div>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Date</th>
          <th>Time</th>
          <th>Contact</th>
          <th>Prescription</th>
        </tr>
      </thead>
      <tbody id="appointment-table-body">
      </tbody>
    </table>
  </div>
  
  <button class="btn-add" onclick="openAddModal()">Add a New Appointment</button>
</div>



<div id="updateAppointmentModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('updateAppointmentModal')">&times;</span>
    <h2 style="text-align:center; margin-left:30px;">Update Appointment</h2>
    <form id="updateAppointmentForm" action="update_appointment.php" method="post">
    </form>
  </div>
</div>

<div id="deleteAppointmentModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('deleteAppointmentModal')">&times;</span>
    <h2>Delete Appointment</h2>
    <form id="deleteAppointmentForm" action="delete_appointment.php" method="post">
      </form>
    
  </div>
</div>

<div id="addAppointmentModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addAppointmentModal')">&times;</span>
    <h2 style="text-align:center; margin-left:30px;" >Add a New Appointment</h2>
    <form id="addAppointmentForm" action="add_appointment.php" method="post" onsubmit="return validateForm()">
    </form>
  </div>
</div>

</body>
</html>
