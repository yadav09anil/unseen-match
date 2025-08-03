<?php
// Start the session
session_start();

// Include the database connection
include('db_connection.php');

// Initialize error message variable
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate the user credentials
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch the user data from the database
        $user = $result->fetch_assoc();
        
        // Check if the password matches directly (no hashing)
        if ($password == $user['password']) {
            // Set session variables for the user
            $_SESSION['username'] = $user['username'];
            
            // Redirect to user profile page
            header("Location: userprofile.php");
            exit();
        } else {
            // Password incorrect
            $errorMessage = "Incorrect password!";
        }
    } else {
        // Username not found
        $errorMessage = "Username not found!";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Unseen Match</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <style>
       /* General body styles */
body {
    font-family: 'Dancing Script', cursive; /* Romantic font */
    background: radial-gradient(circle, #6d6769, #000000); /* Circular gradient from light pink to dark pink */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Login container styles */
.login-container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 320px;
    text-align: center;
    border: 5px solid #f03c7e; /* Soft pink border */
}

/* Logo container styles */
.logo {
    margin-bottom: 20px;
}

.logo img {
    width: 200px;
    height: 100px;
}

/* Title styles */
h2 {
    font-size: 30px;
    color: #ff3366; /* Romantic pink color */
    margin-bottom: 20px;
    font-family: 'Dancing Script', cursive;
}

/* Input group styles */
.input-group {
    margin-bottom: 20px;
    text-align: left;
}

.input-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.input-group input {
    width: 100%;
    padding: 12px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
    margin-top: 5px;
    font-family: 'Arial', sans-serif;
}

/* Button styles */
.btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 5px;
    background-color: #ff3366; /* Romantic pink */
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
    font-family: 'Arial', sans-serif;
}

.btn:hover {
    background-color: #d42f57; /* Darker pink for hover effect */
}

/* Button group (Signup and Forgot Password buttons) */
.button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.button-group button {
    width: 48%;
    background-color: #333; /* Black background for secondary buttons */
    color: white;
    padding: 12px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    font-family: 'Arial', sans-serif;
    transition: background-color 0.3s ease;
}

.button-group button:hover {
    background-color: #666; /* Darker black for hover effect */
}

/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4); /* Black with opacity */
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: rgba(255, 51, 102, 0.7); /* Pink background with 70% opacity */
    color: white;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    width: 300px;
}

/* Mobile responsiveness */
@media screen and (max-width: 600px) {
    .login-container {
        width: 320px; /* Keep same width as PC version */
        padding: 30px; /* Keep same padding */
    }

    h2 {
        font-size: 30px; /* Keep title size consistent with PC */
    }

    .input-group input {
        padding: 12px;
        font-size: 16px;
    }

    .btn {
        font-size: 16px;
        padding: 12px;
    }

    .button-group {
        display: flex;
        justify-content: space-between;
    }

    .button-group button {
        width: 48%; /* Keep button size consistent */
        font-size: 14px;
        padding: 12px;
    }
}

/* Small screen devices (max-width: 400px) */
@media screen and (max-width: 400px) {
    h2 {
        font-size: 30px; /* Keep title size consistent with PC */
    }

    .input-group input {
        padding: 12px;
        font-size: 16px;
    }

    .btn {
        font-size: 16px;
        padding: 12px;
    }

    .button-group button {
        width: 48%; /* Keep button size consistent */
        font-size: 14px;
        padding: 12px;
    }
}

    </style>
</head>
<body>

    <!-- Login Form Container -->
    <div class="login-container">
        <div class="logo">
            <img src="uploads\profile_pics\logo.png" alt="Website Logo">
        </div>

        <h2>Login</h2>

        <form id="loginForm" action="login.php" method="POST">
            <div class="input-group">
                <label for="username">Instagram Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your instagram username">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="button-group">
                <button type="button" class="btn" id="signupBtn" onclick="window.location.href='signup.php'">Sign Up</button>
                <button type="button" class="btn" id="forgotBtn" onclick="window.location.href='forgot.php'">Forgot Password</button>
            </div>
        </form>
    </div>

    <!-- Error Message Modal -->
    <div class="modal" id="errorModal">
        <div class="modal-content">
            <p id="errorMessage"><?php echo $errorMessage; ?></p>
        </div>
    </div>

    <script>
        // Show the error modal if there's an error message
        <?php if ($errorMessage != ""): ?>
            document.getElementById("errorModal").style.display = "flex";
        <?php endif; ?>

        // Close the modal when clicked
        document.getElementById("errorModal").addEventListener("click", function() {
            this.style.display = "none";
        });
    </script>

</body>
</html>
