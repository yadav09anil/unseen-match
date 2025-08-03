<?php
// Include the database connection configuration
include('db_connection.php');

// Start the session to store session data
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username and password from the POST request
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Query to fetch the admin record with the provided username
    $sql = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    // Check if the user exists and validate the password
    if ($result->num_rows > 0) {
        // Fetch the user record
        $row = $result->fetch_assoc();

        // Compare the entered password with the password in the database
        if ($password === $row['password']) {
            // Correct credentials, set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            
            // Redirect to admin panel
            header('Location: adminpanal.php');
            exit();
        } else {
            // Incorrect password
            echo '<script>alert("Incorrect password!"); window.location.href="admin.php";</script>';
        }
    } else {
        // Incorrect username
        echo '<script>alert("Incorrect username!"); window.location.href="admin.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Unseen Match</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Login Form Container -->
    <div class="login-container">
      
        <h2>Admin Login</h2>

        <!-- Submit the form to this page -->
        <form id="loginForm" action="admin.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn" id="loginBtn">Login</button>
        </form>
    </div>

    <!-- Error Message Modal -->
    <div class="modal" id="errorModal">
        <div class="modal-content">
            <p id="errorMessage">Incorrect username or password!</p>
        </div>
    </div>

</body>
</html>
