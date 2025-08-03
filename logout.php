<?php
// Start the session
session_start();

// Destroy all session data
session_unset();  // This will clear all session variables
session_destroy();  // This will destroy the session

// Redirect to the login page
header("Location: login.php");
exit();
?>
