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
  <title>Manage Accounts</title>
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
    }
    .card {
      width: 70%;
      background-color: steelblue;
      padding: 20px;
      margin: 10px 0;
      border-radius: 10px;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      box-shadow: 2px 2px 12px steelblue;

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
      padding: 12px 20px;
      text-decoration: none;
      border-radius: 5px;
      text-align: center;
      margin: 20px auto;
      cursor: pointer;
      border:none;
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

.btn-back {
  background-color: blue;
  color: white;
  padding: 12px 20px;
  text-decoration: none;
  border-radius: 5px;
  text-align: center;
  margin: 20px auto;
  cursor: pointer;
  border:none;
}

.btn-back:hover {
  background-color: darkblue;
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

    function showAlert() {
      alert("Deleted successfully");
    }

    function openModal(modalId) {
      document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = "none";
    }

    function openUpdateModal(id, username, password) {
      document.getElementById('updateAccountForm').innerHTML = `
        <input type="hidden" name="id" value="${id}">
        <div>
          <label for="id">ID</label>
          <input type="text" id="id" name="id" value="${id}" readonly>
        </div>
        <div>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="${username}" required>
        </div>
        <div>
          <label for="password">Password</label>
          <input type="text" id="password" name="password" value="${password}" required>
        </div>
        <div class="button-group">
          <button type="submit" class="btn-submit">Update Account</button>
        </div>
      `;
      openModal('updateAccountModal');
    }

    function openDeleteModal(id) {
      document.getElementById('deleteAccountId').value = id;
      openModal('deleteAccountModal');
    }

    function openAddModal() {
      <?php
      include 'connectlogin.php';
      $result = $conn->query("SELECT COUNT(*) AS count FROM login");
      $row = $result->fetch_assoc();
      $next_id = $row['count'] + 1;
      ?>
      document.getElementById('addAccountForm').innerHTML = `
        <div>
          <label for="id">ID</label>
          <input type="text" id="id" name="id" value="${<?php echo $next_id; ?>}" readonly>
        </div>
        <div>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div>
          <label for="password">Password</label>
          <input type="text" id="password" name="password" required>
        </div>
        <div class="button-group">
          <button type="submit" class="btn-submit">Add Account</button>
        </div>
      `;
      openModal('addAccountModal');
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
  <h1 style="font-size: 2.5em; text-align: center;"> Manage Accounts</h1>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Password</th>
          <th class="actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include 'connectlogin.php';
        $result = $conn->query("SELECT * FROM login ORDER BY ID ASC");
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . $row['username'] . "</td>";
          echo "<td>" . $row['password'] . "</td>";
          echo "<td>
          <button class='btn-update' onclick=\"openUpdateModal('{$row['id']}', '{$row['username']}', '{$row['password']}')\">Update</button>
          <button class='btn-delete' onclick=\"openDeleteModal('{$row['id']}')\">Delete</button>
        </td>";
          echo "</tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
    <div>
    <button class="btn-back" onclick="location.href='settings.php'">Back </button>
    <button class="btn-add" onclick="openAddModal()">Add a New Account</button>
  </div>
</div>
<div id="updateAccountModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('updateAccountModal')">&times;</span>
    <h2 style="text-align:center;">Update Account</h2>
    <form id="updateAccountForm" action="update_account.php" method="post">
    </form>
  </div>
</div>

<div id="deleteAccountModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('deleteAccountModal')">&times;</span>
    <h2>Delete Account</h2>
    <p>Are you sure you want to delete this account?</p>
    <form id="deleteAccountForm" action="delete_account.php" method="post">
      <input type="hidden" name="id" id="deleteAccountId">
      <div class="button-group">
      <button type="submit" class="btn-submit">Confirm</button>
      <button type="button" class="btn" onclick="closeModal('deleteAccountModal')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div id="addAccountModal" class="modal-background">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addAccountModal')">&times;</span>
    <h2 style="text-align:center;">Add a New Account</h2>
    <form id="addAccountForm" action="add_account.php" method="post">
    </form>
  </div>
</div>

</body>
</html>
