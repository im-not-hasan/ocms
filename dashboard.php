<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
  include 'connect.php';
  $appointmentsData = [];
  $result = $conn->query("SELECT date AS date, COUNT(*) AS count FROM appointments GROUP BY date");
  while ($row = $result->fetch_assoc()) {
      $appointmentsData[] = $row;
  }
  echo "<script>const appointmentsData = " . json_encode($appointmentsData) . ";</script>";
  $eyewearData = [];
  $brandsResult = $conn->query("SELECT brand FROM brand");
  while ($brand = $brandsResult->fetch_assoc()) {
      $brandName = $brand['brand'];
      $quantityResult = $conn->query("SELECT SUM(quantity) AS total FROM eyewear WHERE name LIKE '{$brandName}%'");
      $quantity = $quantityResult->fetch_assoc()['total'] ?? 0;
      $eyewearData[] = ['brand' => $brandName, 'quantity' => $quantity];
  }
  echo "<script>const eyewearData = " . json_encode($eyewearData) . ";</script>";
  $genderData = [];
  $result = $conn->query("SELECT gender, COUNT(*) AS count FROM patient GROUP BY gender");
  while ($row = $result->fetch_assoc()) {
      $genderData[] = $row;
  }
  echo "<script>const genderData = " . json_encode($genderData) . ";</script>";
  $lensData = [];
  $result = $conn->query("SELECT grade, quantity FROM lens");
  while ($row = $result->fetch_assoc()) {
      $lensData[] = $row;
  }
  echo "<script>const lensData = " . json_encode($lensData) . ";</script>";
  
   $counts = [];
  $counts['patients'] = $conn->query("SELECT COUNT(*) AS count FROM patient")->fetch_assoc()['count'];
  $counts['eyewear'] = $conn->query("SELECT sum(quantity) AS count FROM eyewear")->fetch_assoc()['count'];
  $counts['appointments'] = $conn->query("SELECT COUNT(*) AS count FROM appointments")->fetch_assoc()['count'];
  $counts['lens'] = $conn->query("SELECT sum(quantity) AS count FROM lens")->fetch_assoc()['count'];

  $conn->close();
  ?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
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
  z-index: 1;
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
  margin-left: 30px;
}
.sidebar.expanded .link-text {
  display: inline;
}
.main-content {
  margin-left: 60px;
  padding: 20px;
  width: calc(100% - 60px);
  transition: margin-left 0.3s;
  display: flex;
  justify-content: center; 
  align-items: center; 
  flex-wrap: wrap;
}
.main-content h1 {
  color: white;
  text-align: center; 
  padding-left: 20px;
  width: 100%;
}

.countcard {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  margin: 20px; 
  box-shadow: 2px 2px 12px steelblue;
  background-color: steelblue;
  color: white;
  border-radius: 10px;
  width: 200px; 
  height: 150px; 
  transition: transform 0.3s;
}
.countcard:hover{
  transform: scale(1.03); 
}
.countcard .content {
  text-align: left;
}
.countcard img {
  width: 90px;
  height: 90px; 
  margin-right: -10px;
  margin-top: 10px;
}
.card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  margin: 20px; 
  box-shadow: 2px 2px 12px steelblue;
  background-color: steelblue;
  color: white;
  border-radius: 10px;
  width: 250px; 
  height: 250px; 
  transition: transform 0.3s;
}
.card:hover{
  transform: scale(1.03); 
}
.card .content {
  text-align: left;
}
.card img {
  width: 90px;
  height: 90px; 
  margin-right: -10px;
  margin-top: 10px;
}
.count {
  font-size: 4.5em; 
}
.label {
  font-size: 1em; 
  margin-left: -5px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-weight: bold;
}
canvas {
  max-width: 100%;
  margin: auto;
  display: block;
}
#appointmentsChart {
  width: 100%;
  max-width: 900px;
  height: 500px;
}

  </style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <a href="dashboard.php" style="background-color: rgb(98, 153, 193);">
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
<!--<div>  
<h1 style="text-align: left; font-size: 2.5em; ">Dashboard</h1>
</div>-->

  <div class="countcard">
      <div class="content">
      <div class="label">Total Appointments</div>
      <div class="count"><?php echo $counts['appointments']; ?></div>
    </div>
    <img src="icons/dashboard_appointment.png" alt="Appointments">
  </div>
  <div class="countcard">
    <div class="content">
      <div class="label">Eyewear Stock</div>
      <div class="count"><?php echo $counts['eyewear']; ?></div>
    </div>
    <img src="icons/dashboard_eyewear.png" alt="Eyewear" style="width:90px;height:90px;margin-right:-20px;">
  </div>
  <div class="countcard">
    <div class="content">
      <div class="label">Number of Patients</div>
      <div class="count"><?php echo $counts['patients']; ?></div>
    </div>
    <img src="icons/dashboard_patient.png" alt="Patients">
  </div>
  <div class="countcard">
    <div class="content">
      <div class="label">Lens Stock</div>
      <div class="count"><?php echo $counts['lens']; ?></div>
    </div>
    <img src="icons/dashboard_lens.png" alt="Lens">
  </div>

  
  <div class="card" style="width: 450px; height: 400px;" >
    <div class="content">
      <div class="label">Eyewear Stock</div>
      <canvas id="eyewearChart" style="height: 355px;"></canvas>
    </div>
  </div>
  <div class="card"  style="width: 250px; height: 330px;">
    <div class="content">
      <div class="label">Number of Patients</div>
      <br>
      <canvas id="patientsChart" style="margin-bottom: 30px;"></canvas>
    </div>
  </div>
  <div class="card" style="width: 400px; height: 400px;">
    <div class="content">
      <div class="label">Lens Stock</div>
      <canvas id="lensChart" style="height: 400px;"></canvas>
    </div>
  </div>
</div>
<script>
  const appointmentsLabels = appointmentsData.map(item => item.date);
  const appointmentsCounts = appointmentsData.map(item => item.count);

  const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
  new Chart(appointmentsCtx, {
    type: 'line',
    data: {
      labels: appointmentsLabels,
      datasets: [{
        label: 'Appointments Over Time',
        data: appointmentsCounts,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true },
      },
      scales: {
        y: { beginAtZero: true },
        x: { title: { display: true, text: 'Date' } }
      }
    }
  });
</script>
<script>
  const eyewearLabels = eyewearData.map(item => item.brand);
  const eyewearCounts = eyewearData.map(item => item.quantity);

  const eyewearCtx = document.getElementById('eyewearChart').getContext('2d');
  new Chart(eyewearCtx, {
    type: 'bar',
    data: {
      labels: eyewearLabels,
      datasets: [{
        label: 'Eyewear Stock',
        data: eyewearCounts,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, labels: {color: 'white'} },
      },
      scales: {
        y: { beginAtZero: true, ticks: {color: 'white'} },
        x: { title: { display: true, text: 'Brand', color: 'white' }, ticks: {color: 'white'} }
      }
    }
  });
</script>
<script>
  const genderLabels = genderData.map(item => item.gender);
  const genderCounts = genderData.map(item => item.count);

  const genderCtx = document.getElementById('patientsChart').getContext('2d');
  new Chart(genderCtx, {
    type: 'pie',
    data: {
      labels: genderLabels,
      datasets: [{
        label: 'Gender Distribution',
        data: genderCounts,
        backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)'],
        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, labels: {color: 'white'}},
      }
    }
  });
</script>
<script>
  const lensLabels = lensData.map(item => item.grade);
  const lensCounts = lensData.map(item => item.quantity);

  const lensCtx = document.getElementById('lensChart').getContext('2d');
  new Chart(lensCtx, {
    type: 'doughnut',
    data: {
      labels: lensLabels,
      datasets: [{
        label: 'Lens Stock',
        data: lensCounts,
        backgroundColor: [
          'rgba(255, 206, 86, 0.5)',
          'rgba(75, 192, 192, 0.5)',
          'rgba(153, 102, 255, 0.5)'
        ],
        borderColor: [
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, labels: {color: 'white'} },
      }
    }
  });
</script>
</body>
</html>

