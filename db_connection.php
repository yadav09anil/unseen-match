<?php
// db_config.php

// Database connection settings
$servername = "localhost";  // Your database host, e.g., "localhost"
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password (leave empty for local server if no password)
$dbname = "unseenmatch";    // Database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
