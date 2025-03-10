<?php
session_start();

if (isset($_SESSION['locked_until']) && time() >= $_SESSION['locked_until']) {
    $_SESSION['attempts'] = 4;  // Reset attempts after lockout time expires
    $_SESSION['locked_until'] = null; // Clear the lock timer
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
