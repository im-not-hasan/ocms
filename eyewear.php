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
  <title>Eyewear</title>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      overflow: hidden;
      font-family: Arial, sans-serif;
      background-color: rgb(98, 153, 193);
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
      overflow: auto;
      height: calc(100vh - 40px);
    }
    .main-content h1 {
      color: white;
      width: 100%;
      text-align: center;
    }
    .filters {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 20px 0;
    }
    .filters label {
      color: white;
      margin-right: 20px;
      margin-left: 40px;
    }

    .filters select {
      width: 200px; 
      height: 40px; 
      padding: 5px 10px;
      border-radius: 5px;
      border: 1px solid #ddd;
      font-size: 1em;
      background-color: white; 
      color: black; 
    }
  
    .filters button {
      margin-left: 10px;
      padding: 10px 10px;
      background-color: steelblue;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.3s
    }

    .filters button img {
      width: 20px; 
      height: 20px;
    }

    .filters button:hover {
      background-color: rgb(98, 153, 193);
    }
    .panel {
      width: 80%;
      display: grid;
      grid-template-columns: repeat(4, 1fr); 
      gap: 30px; 
      padding: 20px;
      background-color: steelblue;
      box-shadow: 2px 2px 12px steelblue;
      border-radius: 10px;
    }
    .card {
      background-color: rgb(98, 153, 193); 
      border-radius: 10px;
      transition: transform 0.3s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      text-align: center;
      padding: 10px
    }
    .card:hover{
      transform: scale(1.05);
    }
    .card img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }
    .card-content {
      font-size: 1em; 
      color: white;
    }
    .modal {
      display: none; 
      position: fixed;
      z-index: 1; 
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      
    }
    .modal-content {
      background-color: rgb(98, 153, 193);
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 60%;
      max-width: 800px;
      border-radius: 10px;
      text-align: center;
      transition: transform 0.2s ease-out, opacity 0.2s ease-out;
      transform: scale(0.8);
      opacity: 0;
        }
        .modal-content.show {
      display: block;
      transform: scale(1);
      opacity: 1;
    }
    .modal-header, .modal-footer {
      padding: 10px;
      background-color: rgb(98, 153, 193);
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    .modal-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }
    .modal-left {
      flex: 1;
      text-align: center;
    }
    .modal-left img {
      width: 90%;
      margin-top: 20px;
      height: auto;
      box-shadow: 5px 5px 12px steelblue;
      border-radius: 10px;
    }
    .modal-right {
      flex: 1;
      text-align: left;
      padding-left: 20px;
    }
    .modal-right h3 {
      margin-bottom: 10px;
      font-size: 1.5em;
      color: white; 
      text-align: center;
      margin-top: -40px;
      margin-left: 20px;
    }
    .modal-right p, .modal-right label, .modal-right select {
      margin-bottom: 10px;
      font-size: 1em;
      color: white; 
      margin-left: 20px;
    }
    .close {
      color: white;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
 
    .btn-confirm {
      background-color: steelblue;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      position: absolute;
      bottom: 20px;
      right: 20px;
    }

    .btn-confirm:hover {
      background-color: darkblue;
    }
 </style>

  <script>
    function toggleSidebar() {
      var sidebar = document.getElementById('sidebar');
      var mainContent = document.getElementById('main-content');
      sidebar.classList.toggle('expanded');
      mainContent.classList.toggle('shifted');
    }

    function sortEyewear() {
    const brand = document.getElementById('brandFilter').value;
    const frameMaterial = document.getElementById('frameMaterialFilter').value;
    const panel = document.querySelector('.panel');
    
    fetch('fetch_images.php?brand=' + brand + '&material=' + frameMaterial)
    .then(response => response.json())
    .then(images => {
      panel.innerHTML = ''; 
      images.forEach(image => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
          <img src="${image.src}" alt="${image.name}">
          <div class="card-content">
            <p>${image.name}</p>
          </div>
        `;
        panel.appendChild(card);
      });
    })
    .catch(error => console.error('Error fetching images:', error));
   }





   document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('cardModal');
  const modalContent = modal.querySelector('.modal-content');
  const span = document.getElementsByClassName('close')[0];

  function showModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').innerText = title;

    fetch(`fetch_quantity.php?name=${title}`)
      .then(response => response.json())
      .then(quantity => {
        document.getElementById('modalQuantity').innerText = `Quantity: ${quantity}`;
      })
      .catch(error => console.error('Error fetching quantity:', error));

    modal.style.display = 'block';
    setTimeout(() => modalContent.classList.add('show'), 10); 
  }

  span.onclick = function() {
    modalContent.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 200); 
  }

  window.onclick = function(event) {
    if (event.target === modal) {
      modalContent.classList.remove('show');
      setTimeout(() => modal.style.display = 'none', 200); 
    }
  }

  document.querySelector('.panel').addEventListener('click', function(event) {
    if (event.target.closest('.card')) {
      const card = event.target.closest('.card');
      const img = card.querySelector('img').src;
      const title = card.querySelector('p').innerText;
      showModal(img, title);
    }
  });
});

function closeModal() {
  const modal = document.getElementById('cardModal');
  const modalContent = modal.querySelector('.modal-content');
  modalContent.classList.remove('show');
  setTimeout(() => modal.style.display = 'none', 200); 
}




function confirmSelection() {
  const title = document.getElementById('modalTitle').innerText;
  const patientFullName = document.getElementById('patientName').value;
  closeModal();
  const [firstName, lastName] = patientFullName.split(' ');

  fetch(`fetch_patient_id.php?fname=${encodeURIComponent(firstName)}&lname=${encodeURIComponent(lastName)}`)
    .then(response => response.json())
    .then(patientData => {
      const patientId = patientData.id;

      fetch('addtocart.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: title, patientid: patientId }),
      })
      .then(() => {
        fetch('update_eyewearquantity.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ name: title }),
        })
        .then(() => {
          window.open(`prescription.php?patientName=${encodeURIComponent(patientFullName)}&patientId=${patientId}`, '_blank');
        })
        .catch(error => console.error('Error updating quantity:', error));
      })
      .catch(error => console.error('Error adding to cart:', error));
    })
    .catch(error => console.error('Error fetching patient ID:', error));
}

document.getElementById('confirmButton').onclick = confirmSelection;

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
    <a href="eyewear.php" style="background-color: rgb(98, 153, 193);">
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

<div id="main-content" class="main-content" style="margin-left: 0px;">
  <h1 style="font-size: 2.5em; text-align: center;"> Eyewear</h1>
  <div class="filters">
    <label for="brandFilter">Brand:</label>
    <select id="brandFilter">
    <option value="All"></option>
      <?php
      include 'connect.php';
      $brandResult = $conn->query("SELECT * FROM brand");
      while ($row = $brandResult->fetch_assoc()) {
        echo "<option value='" . $row['Brand'] . "'>" . $row['Brand'] . "</option>";
      }
      ?>
    </select>
    <label for="frameMaterialFilter">Frame:</label>
    <select id="frameMaterialFilter">
    <option value=""></option>
      <?php
      $materialResult = $conn->query("SELECT * FROM material");
      while ($row = $materialResult->fetch_assoc()) {
        echo "<option value='" . $row['Material'] . "'>" . $row['Material'] . "</option>";
      }
      $conn->close();
      ?>
    </select>
    <button onclick="sortEyewear()">
      <img src="icons/search.png" alt="Sort">
    </button>
  </div>


  <div class="panel">
  <?php
  $dir = 'Eyewear/All/'; 
  $images = glob($dir . '*.png');

  foreach ($images as $image) {
    $imageName = basename($image, ".png"); 
    echo '<div class="card">';
    echo '<img src="' . $image . '" alt="' . $imageName . '">';
    echo '<div class="card-content">';
    echo '<p>' . $imageName . '</p>'; 
    echo '</div>';
    echo '</div>';
  }
  ?>
</div>
</div>

<div id="cardModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <div class="modal-container">
      <div class="modal-left">
        <img id="modalImage" src="" alt="Eyewear Image">
      </div>
      <div class="modal-right">
        <h3 id="modalTitle"></h3>
        <hr>
        <p id="modalQuantity"></p>
        <br>
        <label for="patientName">Patient Name:</label>
        <select id="patientName" style="color: black;">
          <option value=""></option>
           <?php
             include 'connect.php';
             $query = "SELECT FName, LName FROM patient";
             $result = $conn->query($query);
              while ($row = $result->fetch_assoc()) {
                $fullName = $row['FName'] . ' ' . $row['LName'];
                echo '<option value="' . $fullName . '">' . $fullName . '</option>';
              }
            $conn->close();
           ?>
        </select>
        <p id="patientDetails"></p>

        
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-confirm" id="confirmButton" onclick="confirmSelection()">Confirm</button>
    </div>
  </div>
</div>
</body>
</html>
 