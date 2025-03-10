<!DOCTYPE html>
<html>
<head>
  <title>Add a New Patient</title>
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
      justify-content: center;
      flex-direction: column;
      align-items: center; 
    }
    .main-content.shifted {
      margin-left: 200px;
    }
    .main-content h1 {
      color: white;
      padding-left: 20px;
      width: 100%;
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
      align-items: flex-start; 
    }
    form {
      width: 100%;
    }
    form div {
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }
    form label {
      width: 150px;
      margin-right: 10px; 
      text-align: left;
    }
    form input, form select {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
      width: calc(100% - 200px); 
    }
    .input-id {
      width: 40px;
    }
    .input-number {
      width: 150px; 
    }
    .button-group {
      display: flex;
      justify-content: flex-end;
      width: 100%;
    }
    .btn-submit, .btn-back {
      padding: 10px 20px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      color: white;
    }
    .btn-submit {
      background-color: green;
      margin-left: 10px;
    }
    .btn-back {
      background-color: blue;
      text-decoration: none;
    }
  </style>
  <script>
    function toggleSidebar() {
      var sidebar = document.getElementById('sidebar');
      var mainContent = document.getElementById('main-content');
      sidebar.classList.toggle('expanded');
      mainContent.classList.toggle('shifted');
    }

    function showNotificationAndRedirect() {
      alert('Patient added successfully!');
      window.location.href = 'patients.php';
    }

    function validateForm() {
      var number = document.getElementById('number').value;
      if (number.length !== 11 || !number.startsWith('09')) {
        alert('Phone number must be 11 digits and start with 09.');
        return false;
      }
      return true;
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
    <a href="logout.php">
      <img src="icons/logout.png" alt="Logout">
      <span class="link-text">Logout</span>
    </a>
  </div>
</div>

<div id="main-content" class="main-content">
  <h1 style="font-size: 2.5em;">Add a New Patient</h1>

  <div class="card">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['number']) && preg_match('/^09[0-9]{9}$/', $_POST['number'])) {
        include 'connect.php';
        $id = $_POST['id'];
        $name = $_POST['name'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $address = $_POST['address'];
        $number = $_POST['number'];

        $sql = "INSERT INTO patients (ID, Name, Gender, DOB, Address, Number) VALUES ('$id', '$name', '$gender', '$dob', '$address', '$number')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>showNotificationAndRedirect();</script>";
        } else {
            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }

        $conn->close();
    }
    ?>

    <form action="" method="post" onsubmit="return validateForm()">
      <?php
      include 'connect.php';
      $result = $conn->query("SELECT COUNT(*) AS count FROM patients");
      $row = $result->fetch_assoc();
      $next_id = $row['count'] + 1;
      ?>
      <div>
        <label for="id">ID</label>
        <input type="text" id="id" name="id" class="input-id" value="<?php echo $next_id; ?>" readonly>
      </div>
      <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" class="input-name" required>
      </div>
      <div>
        <label for="gender">Gender</label>
        <select id="gender" name="gender" class="input-gender" style="width:100px"required>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div>
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" class="input-dob" style="width: 150px" required>
      </div>
      <div>
        <label for="address">Address</label>
        <input type="text" id="address" name="address" class="input-address" required>
      </div>
      <div>
        <label for="number">Phone Number</label>
        <input type="text" id="number" name="number" class="input-number" maxlength="11" required>
      </div>
      <div class="button-group">
        <a class="btn-back" href="patients.php">Back</a>
        <button type="submit" class="btn-submit">Add Patient</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
