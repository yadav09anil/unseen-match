<?php
// Start the session and include the database connection
session_start();
include('db_connection.php');

// Initialize error message and success message variables
$errorMessage = "";
$successMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST["username"];
    $dobDay = $_POST["dob-day"];
    $dobMonth = $_POST["dob-month"];
    $dobYear = $_POST["dob-year"];
    $newPassword = $_POST["new-password"];
    $confirmNewPassword = $_POST["confirm-new-password"];

    // Validate if new password and confirm password match
    if ($newPassword !== $confirmNewPassword) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Query to check if the username and the date of birth (day, month, year) match
        $sql = "SELECT * FROM users WHERE username = ? AND dob_day = ? AND dob_month = ? AND dob_year = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $username, $dobDay, $dobMonth, $dobYear); // 's' for username (string), 'i' for integer values
        $stmt->execute();
        $result = $stmt->get_result();

        // If username and date of birth match
        if ($result->num_rows > 0) {
            // Directly update the password in the database (no hashing)
            $updateSql = "UPDATE users SET password = ? WHERE username = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $newPassword, $username); // 's' for both parameters (string)

            if ($updateStmt->execute()) {
                $successMessage = "Password successfully reset!";
                // Redirect after successful password reset
                header("Location: login.php");  // Redirect to login page
                exit();  // Ensure the script ends here after redirect
            } else {
                $errorMessage = "Error updating the password. Please try again.";
            }
        } else {
            // Username and date of birth do not match
            $errorMessage = "Incorrect username or date of birth.";
        }
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="signup.css">
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

/* Forgot Password container styles */
.container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 320px; /* Ensure the container doesn't stretch too much on larger screens */
    text-align: center;
    border: 5px solid #f03c7e; /* Soft pink border */
}

/* Title styles */
h2 {
    font-size: 30px;
    color: #ff3366; /* Romantic pink color */
    margin-bottom: 20px;
    font-family: 'Dancing Script', cursive;
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

/* Button group (Login and Back to Sign Up buttons) */
.button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    flex-wrap: wrap; /* Allow buttons to stack on smaller screens */
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
    margin-top: 5px;
}

.button-group button:hover {
    background-color: #666; /* Darker black for hover effect */
}

.secondary-links a {
    color: #007BFF;
    text-decoration: none;
    margin: 0 5px;
}

.secondary-links a:hover {
    text-decoration: underline;
}

/* Mobile responsiveness */
@media screen and (max-width: 600px) {
    /* Adjust container width for small screens */
    .container {
        width: 90%;
        padding: 20px;
    }

    /* Title font size */
    h2 {
        font-size: 26px;
    }

    /* Adjust input field sizes */
    .form-group input {
        padding: 10px;
        font-size: 14px;
    }

    /* Button size and padding */
    .btn {
        font-size: 14px;
        padding: 10px;
    }

    /* Adjust button group layout */
    .button-group {
        flex-direction: column;
    }

    .button-group button {
        width: 100%;
        margin-bottom: 10px;
    }

    /* Adjust secondary links for small screens */
    .secondary-links {
        display: flex;
        flex-direction: column;
    }

    .secondary-links a {
        width: 100%;
        margin-bottom: 10px;
        font-size: 14px;
    }
}

@media screen and (max-width: 400px) {
    /* Adjust title size for extra small screens */
    h2 {
        font-size: 22px;
    }

    /* Adjust form fields for very small screens */
    .form-group input {
        padding: 8px;
        font-size: 12px;
    }

    /* Button adjustments for very small screens */
    .btn {
        font-size: 12px;
        padding: 8px;
    }

    /* Button group adjustments for very small screens */
    .button-group button {
        width: 100%;
        font-size: 12px;
        padding: 8px;
    }

    /* Adjust secondary links */
    .secondary-links a {
        font-size: 12px;
        margin-bottom: 8px;
    }
}

    </style>
</head>
<body>

    <div class="container">
        <!-- Logo Section -->
        <div class="logo">
            <img src="uploads\profile_pics\logo.png" alt="Website Logo">
        </div>

        <h2>Forgot Password</h2>

        <!-- Display error or success messages -->
        <?php if ($errorMessage): ?>
            <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($successMessage): ?>
            <div class="success-message" style="color: green; text-align: center; margin-bottom: 15px;">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Forgot password form -->
        <form action="forgot.php" method="post">
            <!-- Username -->
            <div class="form-group">
                <label for="username">Instagram Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your registered username" required>
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <label for="dob-day">Confirm Your Date of Birth</label>
                <div style="display: flex; justify-content: space-between;">
                    <input type="number" id="dob-day" name="dob-day" placeholder="DD" min="1" max="31" required>
                    <input type="number" id="dob-month" name="dob-month" placeholder="MM" min="1" max="12" required>
                    <input type="number" id="dob-year" name="dob-year" placeholder="YYYY" min="1900" max="2100" required>
                </div>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password" placeholder="Enter new password" required>
            </div>

            <!-- Confirm New Password -->
            <div class="form-group">
                <label for="confirm-new-password">Confirm New Password</label>
                <input type="password" id="confirm-new-password" name="confirm-new-password" placeholder="Confirm new password" required>
            </div>

            <!-- Reset Password Button -->
            <button type="submit" class="btn">Reset Password</button>

            <!-- Secondary Links -->
            <div class="button-group">
                <!-- Login Button -->
                <button type="button" class="btn" id="loginBtn" onclick="window.location.href='login.php'">Login</button>
                
                <!-- Back to Sign Up Button -->
                <button type="button" class="btn" id="signupBtn" onclick="window.location.href='signup.php'">Back to Sign Up</button>
            </div>
        </form>
    </div>

</body>
</html>
