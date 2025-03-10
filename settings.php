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
  <title>Settings</title>
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
      flex-direction: column;
      align-items: center;
    }

    .main-content h1 {
      color: white;
      padding-left: 20px;
      width: 100%;
      text-align: center;
    }
    .button-group {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }
    .square-button {
      width: 200px;
      height: 200px;
      background-color: steelblue;
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin: 30px;
      border-radius: 10px;
      transition: background-color 0.3s, transform 0.3s;
      cursor: pointer;
      border: none;
      outline: none;
    }
    .square-button img {
      width: 100px;
      height: 100px;
    }
    .square-button:hover {
      background-color: rgb(59, 113, 151);
      transform: scale(1.05);
    }
    .square-button span {
      margin-top: 10px;
      font-size: 1.5em;
    }

  </style>
  <script>
    function toggleSidebar() {
      var sidebar = document.getElementById('sidebar');
      var mainContent = document.getElementById('main-content');
      sidebar.classList.toggle('expanded');
      mainContent.classList.toggle('shifted');
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
    <a href="settings.php" style="background-color: rgb(98, 153, 193);">
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
  <h1 style="font-size: 2.5em; margin-left: -20px;">Settings</h1>

  <div class="button-group">
    <button class="square-button" onclick="location.href='eyewearstock.php'">
      <img src="icons/eyewear.png" alt="Eyewear Stocks">
      <span>Eyewear Stocks</span>
    </button>
    <button class="square-button" onclick="location.href='lenstock.php'">
      <img src="icons/dashboard_lens.png" alt="Lens Stocks">
      <span>Lens Stocks</span>
    </button>
    <button class="square-button" onclick="location.href='accounts.php'">
      <img src="icons/patients.png" alt="Manage Accounts">
      <span>Manage Accounts</span>
    </button>
  </div>
</div>

</body>
</html>
