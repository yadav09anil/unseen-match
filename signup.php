<?php
// Start the session (this must be called before any HTML or output)
session_start();

// Include the database connection
include('db_connection.php');

// Initialize error message variable
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];
    $dob_day = $_POST["dob-day"];
    $dob_month = $_POST["dob-month"];
    $dob_year = $_POST["dob-year"];
    $termsAccepted = isset($_POST["terms"]) ? 1 : 0;

    // Validate password and confirmation match
    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Check if the username already exists in the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the username already exists
            $errorMessage = "Username already exists. Please choose a different one.";
        } else {
            // Insert data into the database if the username is available
            $sql = "INSERT INTO users (username, password, dob_day, dob_month, dob_year, terms_accepted)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiiii", $username, $password, $dob_day, $dob_month, $dob_year, $termsAccepted);

            if ($stmt->execute()) {
                // Redirect to login page or success page after registration
                header("Location: login.php");
                exit();  // Don't forget to exit after the redirect
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
        }
    }
}

// Close the connection (optional since the script will end)
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="signup.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

    <style>

        /* General body styles */
body {
    font-family: 'Dancing Script', cursive;
    background: radial-gradient(circle, #6d6769, #000000);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Sign Up container styles */
.container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 320px;
    text-align: center;
    border: 5px solid #f03c7e;
}

/* Logo space */
.logo {
    margin-bottom: 20px;
}

.logo img {
    height: 100px;
    width: 250px;
}

/* Title styles */
h2 {
    font-size: 30px;
    color: #ff3366;
    margin-bottom: 20px;
}

/* Input group styles */
.form-group {
    margin-bottom: 20px;
    text-align: left;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
    margin-top: 5px;
}

/* Terms and Conditions checkbox styles */
.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 8px;
}

/* Button styles */
.btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 5px;
    background-color: #ff3366;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

.btn:hover {
    background-color: #d42f57;
}

/* Button group (Signup and Forgot Password buttons) */
.button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    flex-wrap: wrap; /* Ensures buttons stack on small screens */
}

.button-group button {
    width: 48%;
    background-color: #333;
    color: white;
    padding: 12px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 5px;
}

.button-group button:hover {
    background-color: #666;
}

/* Mobile responsiveness */
@media screen and (max-width: 600px) {
    .container {
        width: 90%;
        padding: 20px;
    }

    h2 {
        font-size: 26px;
    }

    .form-group input {
        padding: 10px;
        font-size: 14px;
    }

    .btn {
        font-size: 14px;
        padding: 10px;
    }

    .button-group button {
        width: 100%;
        font-size: 14px;
        padding: 10px;
        margin-top: 5px;
    }

    .secondary-links {
        display: flex;
        flex-direction: column;
    }

    .secondary-links a {
        margin-bottom: 10px;
        font-size: 14px;
    }
}

@media screen and (max-width: 400px) {
    h2 {
        font-size: 22px;
    }

    .form-group input {
        padding: 8px;
        font-size: 12px;
    }

    .btn {
        font-size: 12px;
        padding: 8px;
    }

    .button-group button {
        width: 100%;
        font-size: 12px;
        padding: 8px;
    }

    .secondary-links a {
        font-size: 12px;
        margin-bottom: 8px;
    }
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    padding-top: 60px;
}

.modal-content {
    background-color: rgba(0, 0, 0, 0.8); /* Black with opacity */
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 8px;
    text-align: center;
    color:rgb(255, 255, 255); /* Pink text color */
}

.close {
    color:rgb(255, 0, 64); /* Red color for the close button */
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #d42f57; /* Dark red when hovered */
    text-decoration: none;
}

#error-message {
    font-size: 18px;
    margin-top: 20px;
}

    </style>
   
</head>
<body>

    <div class="container">
        <!-- Logo Section -->
        <div class="logo">
            <img src="uploads\profile_pics\logo.png" alt="Website Logo">
        </div>

        <h2>Sign Up</h2>
        <form id="signupForm" action="signup.php" method="post" onsubmit="return validateForm()">
            <!-- Username -->
            <div class="form-group">
                <label for="username">Instagram Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your Instagram username" required pattern="[A-Za-z0-9._-]{3,10}">

            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm password" required>
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <label for="dob-day">Date of Birth</label>
                <div style="display: flex; justify-content: space-between;">
                    <input type="number" id="dob-day" name="dob-day" placeholder="DD" min="1" max="31" required>
                    <input type="number" id="dob-month" name="dob-month" placeholder="MM" min="1" max="12" required>
                    <input type="number" id="dob-year" name="dob-year" placeholder="YYYY" min="1900" max="2100" required>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-group">
                <label for="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    I agree with the 
                    <a href="term&condition.php" target="_blank" style="color: #ff3366; text-decoration: none;">Terms and Conditions</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn">Sign Up</button>

            <!-- Secondary Links -->
            <div class="button-group">
                <button type="button" class="btn" id="signupBtn" onclick="window.location.href='login.php'">Login</button>
                <button type="button" class="btn" id="forgotBtn" onclick="window.location.href='forgot.php'">Forgot Password</button>
            </div>
        </form>
    </div>

    <!-- Modal for Error Message -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="error-message">
                <?php
                    if (!empty($errorMessage)) {
                        echo $errorMessage; // Display error message from PHP if it exists
                    }
                ?>
            </p>
        </div>
    </div>

    <script>
    // Show the error modal if there is an error message
    <?php if (!empty($errorMessage)) { echo 'document.getElementById("errorModal").style.display = "block";'; } ?>

    function closeModal() {
        document.getElementById("errorModal").style.display = "none";
    }

    // Validate the form before submitting
    function validateForm() {
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm-password").value;
        let day = document.getElementById("dob-day").value;
        let month = document.getElementById("dob-month").value;
        const year = document.getElementById("dob-year").value;

        // Ensure day and month are two digits
        if (day < 10 && day.length === 1) {
            day = "0" + day;
        }
        if (month < 10 && month.length === 1) {
            month = "0" + month;
        }

        // Update the input fields with the formatted day and month
        document.getElementById("dob-day").value = day;
        document.getElementById("dob-month").value = month;

        // Check if username length is less than 4
        if (username.length < 4) {
            alert("Username must be at least 4 characters long.");
            return false;
        }

        // Check if password length is less than 8
        if (password.length < 8) {
            alert("Password must be at least 8 characters long.");
            return false;
        }

        // Check if password and confirm password match
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }

        // Ensure that the day, month, and year are valid
        if (day < 1 || day > 31 || month < 1 || month > 12 || year < 1900 || year > 2100) {
            alert("Please enter a valid date.");
            return false;
        }

        return true;
    }
    </script>

</body>
</html>
