<?php
// Start the session
session_start();

// Include database connection
include('db_connection.php');

// Check if the session has the username, otherwise redirect to login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Redirect to login if no session username
    exit();
}

// Get form data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign form values to variables and convert them to lowercase
    $username = $_SESSION['username'];
    $first_name = strtolower(mysqli_real_escape_string($conn, $_POST['first-name']));
    $last_name = strtolower(mysqli_real_escape_string($conn, $_POST['last-name']));
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $gender = strtolower(mysqli_real_escape_string($conn, $_POST['gender']));
    $occupation = strtolower(mysqli_real_escape_string($conn, $_POST['occupation']));
    $religion = strtolower(mysqli_real_escape_string($conn, $_POST['religion']));
    $city = strtolower(mysqli_real_escape_string($conn, $_POST['city']));
    $state = strtolower(mysqli_real_escape_string($conn, $_POST['state']));
    $description = strtolower(mysqli_real_escape_string($conn, $_POST['description']));

    // Check if the user already has a record in the userdetails table
    $check_sql = "SELECT * FROM userdetails WHERE username = '$username'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // If the record exists, update the user's information
        $update_sql = "UPDATE userdetails SET 
                        first_name = '$first_name', 
                        last_name = '$last_name', 
                        age = '$age', 
                        gender = '$gender', 
                        occupation = '$occupation', 
                        religion = '$religion', 
                        city = '$city', 
                        state = '$state', 
                        description = '$description' 
                        WHERE username = '$username'";

        if ($conn->query($update_sql) === TRUE) {
            // If update is successful, redirect to the user profile page
            header("Location: userprofile.php");
            exit();
        } else {
            // If there is an error, show the error message
            echo "Error: " . $update_sql . "<br>" . $conn->error;
        }
    } else {
        // If the record doesn't exist, insert a new record
        $insert_sql = "INSERT INTO userdetails (username, first_name, last_name, age, gender, occupation, religion, city, state, description, verified) 
        VALUES ('$username', '$first_name', '$last_name', '$age', '$gender', '$occupation', '$religion', '$city', '$state', '$description', 0)";

        if ($conn->query($insert_sql) === TRUE) {
            // If insertion is successful, redirect to the user profile page
            header("Location: userprofile.php");
            exit();
        } else {
            // If there is an error, show the error message
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
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
    <title>User Information Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="setprofile.css">
</head>
<body>

    <div class="container">
        <h2>User Information Form</h2>
        <form action="setprofile.php" method="post" id="user-form">
            <!-- First Name Group -->
            <div class="form-group" id="first-name-group">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first-name" required pattern="[A-Za-z\s]+" title="First Name should only contain letters and spaces">
                <div class="button-container">
                    <button type="button" class="next-btn" onclick="showNext('last-name-group')">Next</button>
                </div>
            </div>
            <!-- Last Name Group -->
            <div class="form-group" id="last-name-group">
                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last-name" required pattern="[A-Za-z\s]+" title="Last Name should only contain letters and spaces">
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('first-name-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('age-group')">Next</button>
                </div>
            </div>
            <!-- Age Group -->
            <div class="form-group" id="age-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required min="1">
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('last-name-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('gender-group')">Next</button>
                </div>
            </div>
            <!-- Gender Group -->
            <div class="form-group" id="gender-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('age-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('occupation-group')">Next</button>
                </div>
            </div>
            <!-- Occupation Group -->
            <div class="form-group" id="occupation-group">
                <label for="occupation">Occupation</label>
                <select id="occupation" name="occupation" required>
                    <option value="student">Student</option>
                    <option value="job">Job</option>
                    <option value="unemployed">Unemployed</option>
                </select>
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('gender-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('religion-group')">Next</button>
                </div>
            </div>
            <!-- Religion Group -->
            <div class="form-group" id="religion-group">
                <label for="religion">Religion</label>
                <select id="religion" name="religion" required>
                    <option value="hindu">Hindu</option>
                    <option value="muslim">Muslim</option>
                    <option value="christian">Christian</option>
                    <option value="buddhist">Sikh</option>
                    <option value="jain">Jain</option>
                </select>
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('occupation-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('city-group')">Next</button>
                </div>
            </div>
            <!-- City Group -->
            <div class="form-group" id="city-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required pattern="[A-Za-z\s]+" title="City should only contain letters and spaces">
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('religion-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('state-group')">Next</button>
                </div>
            </div>
            <!-- State Group -->
            <div class="form-group" id="state-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required pattern="[A-Za-z\s]+" title="State should only contain letters and spaces">
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('city-group')">Back</button>
                    <button type="button" class="next-btn" onclick="showNext('description-group')">Next</button>
                </div>
            </div>
            <!-- Description Group -->
            <div class="form-group" id="description-group">
                <label for="description">Describe Yourself</label>
                <textarea id="description" name="description" required placeholder="Tell something about yourself..."></textarea>
                <div class="button-container">
                    <button type="button" class="back-btn" onclick="showPrevious('state-group')">Back</button>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let currentGroup = 'first-name-group';

        function showNext(nextGroup) {
            document.getElementById(currentGroup).style.display = 'none';
            document.getElementById(nextGroup).style.display = 'block';
            currentGroup = nextGroup;
        }

        function showPrevious(previousGroup) {
            document.getElementById(currentGroup).style.display = 'none';
            document.getElementById(previousGroup).style.display = 'block';
            currentGroup = previousGroup;
        }

        document.getElementById(currentGroup).style.display = 'block';
    </script>
</body>
</html>
