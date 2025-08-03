<?php
// Start the session to access session variables
session_start();

// Destroy all session data
session_destroy();

// Redirect to the admin page (or login page if needed)
header("Location: admin.php");
exit();
?>
