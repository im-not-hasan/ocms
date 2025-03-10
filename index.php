<?php
session_start();
include 'connectlogin.php';

// Initialize session variables if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 4; // Initialize attempts to 4
}
if (!isset($_SESSION['locked_until'])) {
    $_SESSION['locked_until'] = null; // Initialize lock timer
}

$attempts = $_SESSION['attempts'];
$locked_until = $_SESSION['locked_until'];

$showLockedAlert = false;
$remainingTime = 0;

// Check if the account is locked
if ($locked_until && time() < $locked_until) {
    $showLockedAlert = true;
    $remainingTime = $locked_until - time();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    // Check if the form was actually submitted (not a page refresh)
    if (isset($_POST['form_submitted'])) {
        if ($locked_until && time() < $locked_until) {
            $showLockedAlert = true;
            $remainingTime = $locked_until - time();
        } else {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT fullname, username, password FROM login WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['fullname'] = $row['fullname'];
                    $_SESSION['attempts'] = 4; // Reset attempts on successful login
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Decrement attempts only if the form was submitted
                    if ($_SESSION['attempts'] > 1) {
                        $_SESSION['attempts']--;
                        $errorMessage = "Incorrect Password. " . ($_SESSION['attempts']) . " attempts remaining.";
                    } else {
                        $_SESSION['attempts'] = 0; // Set attempts to 0
                        $_SESSION['locked_until'] = time() + 60; // Lock for 60 seconds
                        $showLockedAlert = true;
                        $remainingTime = 60;
                        $errorMessage = "Account locked. Please try again in 60 seconds.";
                    }
                }
            } else {
                $errorMessage = "No user found with this username.";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="swal/sweetalert2.scss">
  <link rel="stylesheet" href="style.css">
  <script src="swal/sweetalert2.all.js"></script>
  <style>
    /* Fix error message alignment and layout shift */
    #error-message {
      color: red;
      display: none;
      margin: 0;
      padding: 0;
      text-align: left;
      width: 85%; /* Match the width of the textboxes */
      font-size: 14px;
      margin-left: 10px; /* Align with the textboxes */
      margin-top: -10px; /* Adjust spacing */
      margin-bottom: 10px; /* Adjust spacing */
    }
  </style>
  <script>
    function togglePasswordVisibility() {
      var passwordInput = document.getElementById('password');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
      } else {
        passwordInput.type = 'password';
      }
    }

    function showRegisterCard() {
    const loginCard = document.querySelector('.login-card');
    const toggleButton = document.querySelector('.top-bar .links button');
    loginCard.classList.toggle("expand");
    loginCard.innerHTML = `
      <h1 style="margin-bottom: 15px;">Sign Up</h1>
      <form action="register.php" method="post">
        <input type="text" id="new-username" name="new-username" style="width: 89%;" placeholder="Username" required>
        <img src="icons/welcome.png" class="newuser-icon" onclick="togglePasswordVisibility()">  
        <div class="side-by-side">
          <input type="password" id="new-password" name="new-password" placeholder="Password" required minlength="8">
          <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required minlength="8">
        </div>
        <div class="side-by-side">
          <input type="text" id="fullname" name="fullname" placeholder="Full Name" required>
          <input type="text" id="address" name="address" placeholder="Address" required>
        </div>
        <div class="side-by-side">
          <input type="date" id="dob" name="dob" placeholder="Date of Birth" max="2007-02-10" required onchange="calculateAge()">
          <input type="text" id="age" name="age" placeholder="Age" readonly>
        </div>
        <input type="tel" id="contact" name="contact" placeholder="Contact Number" maxlength="11" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        <button type="submit" style="margin-top: 15px;">Sign Up</button>
      </form>
    `;
    toggleButton.textContent = 'Login';
    toggleButton.onclick = function () {
      window.location.href = 'index.php';
    };
}


    function calculateAge() {
      const dob = document.getElementById('dob').value;
      if (dob) {
        const dobDate = new Date(dob);
        const diff = Date.now() - dobDate.getTime();
        const ageDate = new Date(diff);
        const age = Math.abs(ageDate.getUTCFullYear() - 1970);
        document.getElementById('age').value = age;
      } else {
        document.getElementById('age').value = '';
      }
    }
  </script>
</head>
<body>
<div class="background-blur"></div>
<div class="overlay"></div>
<div class="top-bar">
  <div class="leftside">Optical Clinic Management System</div>
  <div class="links">
    <button onclick="showRegisterCard()">Sign Up</button>
  </div>
</div>

<div class="container">
  <div class="login-card">
    <h1>Login</h1>
    <form action="index.php" method="post">
      <input type="hidden" name="form_submitted" value="1"> <!-- Track form submission -->
      <input type="text" id="username" name="username" placeholder="Username" required>
      <img src="icons/welcome.png" class="user-icon" onclick="togglePasswordVisibility()">  
      <input type="password" id="password" name="password" placeholder="Password" required minlength="8">
      <img src="icons/eye-icon.png" class="pass-icon" onclick="togglePasswordVisibility()">  
      <p id="error-message" style="color: red; display: none;"></p>
      <button type="submit" style="margin-top: 74px;">Login</button>
    </form>
    <?php if (isset($errorMessage)): ?>
      <script>
        document.getElementById('error-message').textContent = "<?= $errorMessage ?>";
        document.getElementById('error-message').style.display = 'block';
      </script>
    <?php endif; ?>
  </div>
  <div>
    <div class="welcome-text">Welcome.</div>
    <div class="sub-text">Created By: Hasan, Jovie, Charles.</div>
  </div>
</div>

<?php if ($showLockedAlert): ?>
  <script>
    let timerInterval;

    Swal.fire({
      icon: 'error',
      title: 'Account Locked',
      html: `
        <div style="text-align: center; font-size: 48px; font-weight: bold;">
          <span id="timer"><?= $remainingTime ?></span>
        </div>
        <p>Too many attempts. Please wait until the timer expires.</p>
      `,
      showConfirmButton: false,
      allowOutsideClick: false, // Prevents closing by clicking outside
      heightAuto: false, // Prevents layout shifts
      didOpen: () => {
        const timer = Swal.getHtmlContainer().querySelector('#timer');
        let timeLeft = <?= $remainingTime ?>;
        timerInterval = setInterval(() => {
          if (timeLeft > 0) {
            timeLeft--;
            timer.textContent = timeLeft;
          } else {
            clearInterval(timerInterval);
            // Close the alert automatically when timer reaches 0
            Swal.close();
            // Optionally, reset session variables after the timer expires
            fetch('reset_attempts.php')
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  window.location.reload(); // Reload the page to reset the UI
                }
              });
          }
        }, 1000);
      },
      willClose: () => {
        clearInterval(timerInterval);
      }
    });

    // Disable the login form while the account is locked
    document.querySelector('form').addEventListener('submit', function (e) {
      if (<?= $showLockedAlert ? 'true' : 'false' ?>) {
        e.preventDefault(); // Prevent form submission
        Swal.fire({
          icon: 'error',
          title: 'Locked',
          text: 'Too many attempts. Please wait until the timer expires.',
        });
      }
    });
  </script>
<?php endif; ?>

</body>
</html>