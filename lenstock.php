<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Lens Stock</title>
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
      flex-direction: column;
      align-items: center;
    }
    .main-content h1 {
      color: white;
      padding-left: 20px;
      width: 100%;
      text-align: center;
    }
    .card {
      width: 50%;
      background-color: steelblue;
      padding: 20px;
      margin: 10px 0;
      border-radius: 10px;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.2);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.1);
    }
    table, th, td {
      border: 1px solid white;
    }
    th, td {
      padding: 5px;
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
      border: none;
      cursor: pointer;
    }
    .btn-update:hover {
      background-color: darkgreen;
    }
    .btn-manage {
      background-color: rgb(32,196,32);
      color: white;
      padding: 10px 10px;
      text-decoration: none;
      border-radius: 5px;
      border: none;
      cursor: pointer;
    }
    .btn-delete {
      background-color: red;
      color: white;
      padding: 10px 10px;
      text-decoration: none;
      border-radius: 5px;
      border: none;
      cursor: pointer;
    }
    .btn-manage:hover {
      background-color: darkgreen;
    }
    .btn-delete:hover {
      background-color: darkred;
    }
    .btn-add {
      background-color: green;
      color: white;
      padding: 12px 20px;
      text-decoration: none;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      border: none;
    }
    .btn-add:hover {
      background-color: darkgreen;
    }
    .btn-back {
      background-color: blue;
      color: white;
      padding: 12px 20px;
      text-decoration: none;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      border: none;
      margin-right: 10px;
    }
    .btn-back:hover {
      background-color: darkblue;
    }
    .actions {
      width: 190px;
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
      background-color: rgb(98, 153, 193);
      margin: 5% auto;
      padding: 20px;
      border: 1px solid rgb(59, 113, 151);
      width: 80%;
      max-width: 450px;
      color: white;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
    .btn-submit {
      padding: 10px 20px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      color: white;
      background-color: green;
      margin-left: 10px;
    }
    .btn-submit:hover {
      background-color: darkgreen;
    }
    .btn-cancel {
      background-color: grey;
    }
    .btn-cancel:hover {
      background-color: darkgrey;
    }
    button {
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

    function deleteRecord() {
      confirm("Are you sure you want to delete this record?");
    }

    function showAlert() {
      alert("Deleted successfully");
    }

    function openModal(modalId) {
      document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = "none";
    }

    function openUpdateModal(grade, quantity, lifespan, price) {
  document.getElementById('updateLensForm').innerHTML = `
    <div>
      <label for="grade">Grade</label>
      <input type="text" id="grade" name="grade" value="${grade}" required readonly>
    </div>
    <div>
      <label for="quantity">Quantity</label>
      <input type="number" id="quantity" name="quantity" value="${quantity}" required>
    </div>
    <div>
      <label for="lifespan">Lifespan</label>
      <input type="number" id="lifespan" name="lifespan" value="${lifespan}" required>
    </div>
    <div>
      <label for="price">Price</label>
      <input type="number" id="price" name="price" value="${price}" required>
    </div>
    <div class="button-group">
      <button type="submit" class="btn-submit">Update Lens</button>
    </div>
  `;
  openModal('updateLensModal');
}

function openManageStocksModal(grade, quantity) {
  document.getElementById('manageStocksForm').innerHTML = `
    <input type="hidden" id="grade" name="grade" value="${grade}">
    <div>
      <label for="stockChange">Change Quantity</label>
      <input type="number" id="stockChange" name="stockChange" required>
    </div>
    <div class="button-group">
      <button type="button" class="btn-submit" onclick="updateStock('add', ${quantity})">Add</button>
      <button type="button" style="margin-left:5px;" class="btn-delete" onclick="updateStock('remove', ${quantity})">Remove</button>
    </div>
  `;
  openModal('manageStocksModal');
}

function updateStock(action, currentQuantity) {
  const stockChange = parseInt(document.getElementById('stockChange').value);
  let newQuantity;
if(stockChange<=0){
  alert("Quantity cannot be less than one.");
  return;
}
else{
  if (action === 'add') {
    newQuantity = currentQuantity + stockChange;
  } else if (action === 'remove') {
    newQuantity = currentQuantity - stockChange;
  }
}
  const grade = document.getElementById('grade').value;
  document.getElementById('newGrade').value = grade;
  document.getElementById('newQuantity').value = newQuantity;
  document.getElementById('updateStocksForm').submit();
}
function confirmLogout() {
      if (confirm("Do you want to logout? Any unsaved progress will be lost.")) {
    window.location.href = 'logout.php';
    } 
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
  <h1 style="font-size: 2.5em; text-align: center;"> Manage Lens Stock</h1>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Grade</th>
          <th>Quantity</th>
          <th>Lifespan</th>
          <th>Price</th>
          <th class="actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include 'connect.php';
        $result = $conn->query("SELECT * FROM lens order by Grade desc");
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['Grade'] . "</td>";
          echo "<td>" . $row['Quantity'] . "</td>";
          echo "<td>" . $row['Lifespan'] . "</td>";
          echo "<td>" . $row['Price'] . "</td>";
          echo "<td>
          <button class='btn-update' onclick=\"openUpdateModal('{$row['Grade']}', '{$row['Quantity']}', '{$row['Lifespan']}', '{$row['Price']}')\">Update</button>
          <button class='btn-manage' onclick=\"openManageStocksModal('{$row['Grade']}', {$row['Quantity']})\">Manage Stocks</button>
        </td>";
          echo "</tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
    <div>
    <button class="btn-back" onclick="location.href='settings.php'">Back</button>
  </div>
</div>

<div id="updateLensModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('updateLensModal')">&times;</span>
    <h2 style="text-align:center;">Update Lens</h2>
    <form id="updateLensForm" action="update_lens.php" method="post">
    </form>
  </div>
</div>

<div id="manageStocksModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('manageStocksModal')">&times;</span>
    <h2 style="text-align:center;">Manage Stocks</h2>
    <form id="manageStocksForm" action="update_lenstocks.php" method="post">
    </form>
  </div>
</div>

<form id="updateStocksForm" action="update_lenstocks.php" method="post" style="display: none;">
  <input type="hidden" name="grade" id="newGrade">
  <input type="hidden" name="newQuantity" id="newQuantity">
</form>

</body>
</html>
