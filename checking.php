<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dating App User Listings</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="perfectmatch.css">
</head>
<body>
    <!-- Title Section moved to the top -->
    <div class="title">
        <h1>Check9ings</h1>
    
    </div>

    <div class="container">
        <form method="GET" action="">
            <!-- Gender selection dropdown -->
            <label for="gender">Select Gender: </label>
            <select name="gender" id="gender">
                <option value="male" <?php echo isset($_GET['gender']) && $_GET['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo isset($_GET['gender']) && $_GET['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <?php
        // Include the database connection file
        include('db_connection.php');

        // Start session to get the logged-in user's username
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION['username'])) {
            echo "User not logged in.";
            exit;
        }

        // Get the logged-in username
        $username = $_SESSION['username'];

        // Create prepared statement for user details query
        $sql_user_details = "SELECT gender, age, city, state, occupation, verified FROM userdetails WHERE username = ?";
        $stmt_user_details = $conn->prepare($sql_user_details);
        $stmt_user_details->bind_param("s", $username);
        $stmt_user_details->execute();
        $result_user_details = $stmt_user_details->get_result();

        // Fetch user details
        if ($result_user_details->num_rows > 0) {
            $user_data = $result_user_details->fetch_assoc();
            $user_gender = $user_data['gender'];
            $user_age = $user_data['age'];
            $user_city = $user_data['city'];
            $user_state = $user_data['state'];
            $user_occupation = $user_data['occupation'];
            $user_verified = $user_data['verified']; // Check if user is verified (0 or 1)
        } else {
            echo "Error fetching logged-in user data.";
            exit;
        }

        // Get selected gender from form
        $selected_gender = isset($_GET['gender']) ? $_GET['gender'] : $user_gender;

        // Prepare SQL query to find matching users based on selected gender
        $sql = "SELECT first_name, last_name, age, city, state, occupation, gender, username, religion, verified 
                FROM userdetails 
                WHERE gender = ?";  

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selected_gender);
        $stmt->execute();
        $result = $stmt->get_result();

        // If users are found, store them in the variable
        if ($result->num_rows > 0) {
            $users = $result;
        } else {
            echo '<p>No users found.</p>';
            exit;
        }

        // Loop through users to display them
        while ($user = $users->fetch_assoc()) {
            $first_name = ucwords(strtolower($user['first_name']));
            $last_name = ucwords(strtolower($user['last_name']));
            $city = ucwords(strtolower($user['city']));
            $state = ucwords(strtolower($user['state']));
            $occupation = ucwords(strtolower($user['occupation']));
            $user_username = $user['username'];
            $verified = $user['verified'];

            $profile_pic_path = "uploads/profile_pics/" . $user_username . ".jpg";
            if (!file_exists($profile_pic_path)) {
                $profile_pic_path = "uploads/profile_pics/p1r2o3f4i5l6e7.png"; // Default profile image
            }

            $address = $city . ', ' . $state;

            // Check if a request has already been sent
            $sql_check_request = "SELECT * FROM request_send WHERE username = ? AND request_send_to = ?";
            $stmt_check_request = $conn->prepare($sql_check_request);
            $stmt_check_request->bind_param("ss", $username, $user_username);
            $stmt_check_request->execute();
            $result_check_request = $stmt_check_request->get_result();

            // Determine the button text based on whether a request has been sent
            $button_text = "Send Request";
            $button_class = "btn-send";
            if ($result_check_request->num_rows > 0) {
                $button_text = "Request Sent";
                $button_class = "btn-sent";
            }

            echo '<div class="matches">';
            echo '  <div class="match-box">';
            echo '    <div class="profile-picture-wrapper">';
            echo '      <img src="' . htmlspecialchars($profile_pic_path) . '" alt="Profile Picture">';
            echo '    </div>';
            echo '    <h3>' . htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name) . ', ' . $user['age'];
            if ($verified == 1) {
                echo '      <img src="uploads/profile_pics/v1e2r3i4f5i6e7d8.png" alt="Verified Badge" class="verified-badge-inline">';
            }
            echo '    </h3>';
            echo '    <p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
            echo '    <div class="button-group">';
            echo '      <a href="viewprofile.php?username=' . urlencode($user['username']) . '" class="btn-view">View Profile</a>';
           
            echo '    </div>';
            echo '  </div>';
            echo '</div>';
        }

        $conn->close();
        ?>
    </div>

</body>

<style>

    
<style>
        .disabled-btn {
            
            opacity: 0.5;
        }

        .verification-message {
            font-family: 'Arial', sans-serif;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .message-content {
            text-align: center;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
        }

        .verify-link {
            color: #00aaff;
            text-decoration: none;
        }
</style>

<style>
    /* Global Styles */
body {
    font-family: 'Dancing Script', cursive;
    background: radial-gradient(circle, #6d6769, #000000);
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
}

.title {
    text-align: center;
    margin-bottom: 20px;
}

.title h1 {
    font-size: 2.5em;
    color: #ff3366;
}

.title p {
    font-size: 1.2em;
    color: #ffffff;
}

.matches {
    padding: 20px;
    text-align: center;
    display: block;
}

.match-box {
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    max-width: 320px;
    margin: 0 auto;
}

/* Adjust profile picture inside the match box */
.profile-picture-wrapper {
    width: 320px; /* Set width */
    height: 320px; /* Set height */
    margin: 0 auto 15px auto; /* Center horizontally and add margin to the bottom */
    overflow: hidden; /* Ensure no part of the image goes out of bounds */
    border-radius: 10%; /* Circular image */
    position: relative; /* Relative positioning to ensure no overflow */
}

.profile-picture-wrapper img {
    width: 100%; /* Ensure image takes up the full width of the wrapper */
    height: 100%; /* Ensure image takes up the full height of the wrapper */
    object-fit: cover; /* Ensure the image covers the area without stretching */
}

/* Verified badge styles */
.verified-badge-inline {
    width: 25px;
    height: 25px;
    margin-bottom: 6px;
    vertical-align: middle; /* Align it with the text */
}

.match-box h3 {
    font-size: 1.6em;
    color: #ff3366;
    margin: 10px 0;
}

.match-box p {
    font-size: 1.1em;
    color: #555;
    margin: 5px 0;
}

.button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-family: 'Arial', sans-serif;
}

.button-group button {
    font-size: 1em;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 150px;
    text-align: center;
}

/* Anchor tag styled as button (matching the button group style) */
.button-group a.btn-view {
    font-size: 1em;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 150px;
    text-align: center; /* Ensure text is centered */
    display: inline-block; /* Make it behave like a block element */
    text-decoration: none; /* Remove underline */
    background-color: #ff3366; /* Default button background color */
    color: white; /* Text color */
    padding: 12px 20px; /* Add padding to create space around the text */
    box-sizing: border-box; /* Include padding in the element's total width and height */
}

/* Anchor Hover Effect (same as button) */
.button-group a.btn-view:hover {
    background-color: #e62e57; /* Darker shade for hover effect */
}

.btn-send {
    background-color: #ff3366;
    color: white;
}

.btn-send:hover {
    background-color: #d42f57;
}

.btn-sent {
    background-color: lightgray;
    color: #555;
}

/* Fixed Bottom Navigation */
.fixed-bottom-nav {
    font-family: 'Arial', sans-serif;
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #ff3366;
    text-align: center;
    padding: 10px 0;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: center;
}

.fixed-bottom-nav a {
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    font-size: 16px;
    transition: background-color 0.3s;
}

.fixed-bottom-nav a:hover {
    background-color: #d42f57;
}

.fixed-bottom-nav a:not(:last-child) {
    border-right: 1px solid #fff;
}

/* Modal styles */
#confirmationModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background-color: white;
    color: black;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    text-align: center;
}

.modal-content button {
    margin: 10px;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.modal-content .btn-yes {
    background-color: #ff3366;
    color: white;
}

.modal-content .btn-no {
    background-color: grey;
    color: white;
}

.modal-content .btn-close {
    background-color: #f44336;
    color: white;
}

/* Responsive Styling */
@media screen and (max-width: 768px) {
    .container {
        width: 90%;
    }

    .matches {
        display: block;
    }

    .match-box {
        max-width: 100%;
        margin: 10px auto;
    }

    .profile-picture-wrapper {
        width: 200px;
        height: 200px;
    }

    .match-box h3 {
        font-size: 1.4em;
    }

    .match-box p {
        font-size: 1em;
    }

    .button-group {
        flex-direction: column;
        align-items: center;
    }

    .button-group button,
    .button-group a.btn-view {
        width: 100%;
        margin-bottom: 10px;
    }

    .fixed-bottom-nav a {
        padding: 12px;
        font-size: 14px;
    }
}

@media screen and (max-width: 480px) {
    .container {
        width: 95%;
    }

    .title h1 {
        font-size: 1.8em;
    }

    .title p {
        font-size: 1em;
    }

    .match-box h3 {
        font-size: 1.2em;
    }

    .match-box p {
        font-size: 0.9em;
    }

    .profile-picture-wrapper {
        width: 150px;
        height: 150px;
    }

    .button-group button,
    .button-group a.btn-view {
        font-size: 0.9em;
    }

    .fixed-bottom-nav a {
        padding: 8px;
        font-size: 12px;
    }
}


</style>
</html>
