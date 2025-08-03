<?php
// Start the session to access session variables
session_start();

// Check if the username is stored in the session, if not redirect to admin.php
if (!isset($_SESSION['username'])) {
    header("Location: admin.php");
    exit(); // Terminate the script to ensure the redirect occurs
}

// Handle logout
if (isset($_POST['logout'])) {
    // Destroy the session to log out the user
    session_destroy();
    
    // Redirect to admin.php
    header("Location: admin.php");
    exit(); // Make sure the script terminates after the redirect
}

// Include the database connection
include('db_connection.php');

// Check if the database connection was successful
if ($conn === false) {
    die("ERROR: Could not connect to the database.");
}

// Initialize variables for search query, result, and update message
$searchQuery = "";
$updateMessage = "";

// Search user based on username (exact match)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['searchQuery'])) {
    // Convert the search query to lowercase
    $searchQuery = strtolower($_POST['searchQuery']);
    
    // Query the database for exact matches
    $sql = "SELECT * FROM userdetails WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Check if the statement preparation failed
    if ($stmt === false) {
        die('MySQL Error: ' . $conn->error);
    }

    // Bind the search query parameter
    $stmt->bind_param("s", $searchQuery);

    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
    } else {
        $errorMessage = "Error executing query: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Update user details based on the username (if the form is submitted)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Get the updated user data from the form and convert to lowercase
    $updatedUsername = strtolower($_POST['username']);
    $updatedFirstName = strtolower($_POST['first_name']);
    $updatedLastName = strtolower($_POST['last_name']);
    $updatedAge = $_POST['age']; // No need to change age as it's a number
    $updatedGender = strtolower($_POST['gender']);
    $updatedOccupation = strtolower($_POST['occupation']);
    $updatedReligion = strtolower($_POST['religion']);
    $updatedCity = strtolower($_POST['city']);
    $updatedState = strtolower($_POST['state']);
    $updatedDescription = strtolower($_POST['description']);
    $updatedVerified = isset($_POST['verified']) ? 1 : 0;  // Checkbox for verification

    // Update the user details in the database
    $updateSql = "UPDATE userdetails SET 
                  first_name = ?, 
                  last_name = ?, 
                  age = ?, 
                  gender = ?, 
                  occupation = ?, 
                  religion = ?, 
                  city = ?, 
                  state = ?, 
                  description = ?, 
                  verified = ? 
                  WHERE username = ?";

    $updateStmt = $conn->prepare($updateSql);
    
    // Check if the statement preparation failed
    if ($updateStmt === false) {
        die('MySQL Error: ' . $conn->error);
    }

    // Bind the updated values to the statement
    $updateStmt->bind_param("sssssssssis", $updatedFirstName, $updatedLastName, $updatedAge, $updatedGender, 
                            $updatedOccupation, $updatedReligion, $updatedCity, $updatedState, $updatedDescription, 
                            $updatedVerified, $updatedUsername);

    // Execute the update query
    if ($updateStmt->execute()) {
        $updateMessage = "User details updated successfully.";
    } else {
        $updateMessage = "Error updating user details: " . $updateStmt->error;
    }

    // Close the update statement
    $updateStmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and View User</title>
    <link rel="stylesheet" href="adminpanal.css">
</head>
<body>

    <div class="search-container">
        <h1>Search for User</h1>
        <form method="POST" action="">
            <input type="text" class="search-box" name="searchQuery" placeholder="Enter Username" required value="<?php echo htmlspecialchars($searchQuery); ?>" />
            <button type="submit" class="search-button">Search</button>
        </form>

        <?php
        // Show update form only if user details are found
        if (isset($result) && $result->num_rows > 0) {
            // Fetch the user data
            $row = $result->fetch_assoc();

            // Construct the profile picture filename based on the search query
            $profilePic = 'uploads/profile_pics/' . $searchQuery . '.jpg'; // Profile picture named after the username with a .jpg extension

            // Check if the profile picture file exists, if not, use a default image
            if (!file_exists($profilePic)) {
                $profilePic = 'uploads/profile_pics/p1r2o3f4i5l6e7.png'; // Use default image if file doesn't exist
            }

            // Display user data in a box before showing the update form
            echo "<div class='user-box'>";
            echo "<h3>User Details:</h3>";

            // Display the profile picture
            echo "<div class='profile-pic-container'>";
            echo "<img src='" . htmlspecialchars($profilePic) . "' alt='Profile Picture' class='profile-pic' style='width: 150px; height: 150px;' />";
            echo "</div>";

            echo "<p><strong>Username:</strong> " . htmlspecialchars($row['username']) . "</p>";
            echo "<p><strong>First Name:</strong> " . htmlspecialchars($row['first_name']) . "</p>";
            echo "<p><strong>Last Name:</strong> " . htmlspecialchars($row['last_name']) . "</p>";
            echo "<p><strong>Age:</strong> " . htmlspecialchars($row['age']) . "</p>";
            echo "<p><strong>Gender:</strong> " . htmlspecialchars($row['gender']) . "</p>";
            echo "<p><strong>Occupation:</strong> " . htmlspecialchars($row['occupation']) . "</p>";
            echo "<p><strong>Religion:</strong> " . htmlspecialchars($row['religion']) . "</p>";
            echo "<p><strong>City:</strong> " . htmlspecialchars($row['city']) . "</p>";
            echo "<p><strong>State:</strong> " . htmlspecialchars($row['state']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
            echo "<p><strong>Verified:</strong> " . ($row['verified'] ? 'Yes' : 'No') . "</p>";
            echo "</div>"; // End of user data box

            // Display the update form with current values
            echo "<h3>Update User Details:</h3>";
            echo "<form method='POST' action='' class='update-form'>
                    <input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "' />
                    <label for='first_name'>First Name:</label>
                    <input type='text' name='first_name' value='" . htmlspecialchars($row['first_name']) . "' required><br>

                    <label for='last_name'>Last Name:</label>
                    <input type='text' name='last_name' value='" . htmlspecialchars($row['last_name']) . "' required><br>

                    <label for='age'>Age:</label>
                    <input type='number' name='age' value='" . htmlspecialchars($row['age']) . "' required><br>

                    <!-- Replace the gender select with a simple text input -->
                    <label for='gender'>Gender:</label>
                    <input type='text' name='gender' value='" . htmlspecialchars($row['gender']) . "' required><br>

                    <label for='occupation'>Occupation:</label>
                    <input type='text' name='occupation' value='" . htmlspecialchars($row['occupation']) . "' required><br>

                    <label for='religion'>Religion:</label>
                    <input type='text' name='religion' value='" . htmlspecialchars($row['religion']) . "' required><br>

                    <label for='city'>City:</label>
                    <input type='text' name='city' value='" . htmlspecialchars($row['city']) . "' required><br>

                    <label for='state'>State:</label>
                    <input type='text' name='state' value='" . htmlspecialchars($row['state']) . "' required><br>

                    <label for='description'>Description:</label>
                    <textarea name='description' required>" . htmlspecialchars($row['description']) . "</textarea><br>

                    <label for='verified'>Verified:</label>
                    <input type='checkbox' name='verified' " . ($row['verified'] ? 'checked' : '') . "><br><br>

                    <button type='submit' name='update'>Update</button>
                </form>";
        } elseif (!empty($searchQuery)) {
            echo "<p>No matches found for '$searchQuery'.</p>";
        }

        // Display update message
        if ($updateMessage) {
            echo "<p>$updateMessage</p>";
        }
        ?>

        <!-- Logout button form -->
        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>

    </div>

</body>
</html>
