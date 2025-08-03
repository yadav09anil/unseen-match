<?php
// Start session
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include('db_connection.php');

// Check if the user is logged in (i.e., the username exists in the session)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user details from the database
$sql = "SELECT username, first_name, last_name, age, occupation, city, state, description, verified 
        FROM userdetails 
        WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // Bind the username to the query
$stmt->execute();
$result = $stmt->get_result();

// Default values in case no user data is found
$user = [
    'username' => $username,  // Ensure we use the session username
    'first_name' => '',
    'last_name' => '',
    'age' => '',
    'occupation' => '',
    'city' => '',
    'state' => '',
    'description' => '',
    'verified' => 0 // Default to unverified
];

// If the user exists in the database, fetch the data
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Dating App</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="userprofile.css">
</head>
<body>
    <div class="container">
        <h2>Your Profile</h2>

        <!-- Profile Picture with Plus Icon -->
        <div class="profile-img-container">
            <?php
            // Check if the profile picture exists
            $profilePicPath = 'uploads/profile_pics/' . $username . '.jpg';
            $defaultPicPath = 'uploads/profile_pics/p1r2o3f4i5l6e7.png';
            // Set the image to the profile picture if exists, else default
            $profilePic = file_exists($profilePicPath) ? $profilePicPath : $defaultPicPath;
            ?>
            <!-- Display the profile picture with a timestamp to prevent caching -->
            <img src="<?php echo $profilePic . '?t=' . time(); ?>" alt="Profile Picture" class="profile-img" id="profile-img">
            <button class="plus-icon" onclick="document.getElementById('file-input').click()">+</button>
            <input type="file" id="file-input" accept="image/jpeg,image/jpg" onchange="uploadProfilePic(event)">
        </div>

        <!-- Profile Details -->
        <div class="profile-details">
            <p>
                <strong>Username :</strong>
                <span class="name-container">
                    <span class="name"><?php echo htmlspecialchars($user['username']); ?></span>
                    <?php 
                    // Check if the user is verified and display the verified badge if true
                    if ($user['verified'] == 1) {
                        echo '<img src="uploads/profile_pics/v1e2r3i4f5i6e7d8.png" alt="Verified Badge" class="verified-badge">';
                    }
                    ?>
                </span>
            </p>
            <p><strong>Full Name :</strong> <?php echo ucfirst(htmlspecialchars($user['first_name'])) . ' ' . ucfirst(htmlspecialchars($user['last_name'])); ?></p>
            <p><strong>Age :</strong> <?php echo htmlspecialchars($user['age']) ?: ''; ?></p>
            <p><strong>Occupation :</strong> <?php echo ucfirst(htmlspecialchars($user['occupation'])) ?: ''; ?></p>
            <p><strong>Address :</strong> 
                <?php 
                echo ucfirst(htmlspecialchars($user['city'])) ?: ''; 
                echo " , ";
                echo ucfirst(htmlspecialchars($user['state'])) ?: '';
                ?>
            </p>
            <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($user['description'])) ?: ''; ?></p>
        </div>

        <!-- Edit Profile and Settings Buttons -->
        <?php if ($user['verified'] == 0): ?>
            <button class="btn" onclick="window.location.href='setprofile.php'">Edit Profile</button>
        <?php endif; ?>

        <!-- Logout Button -->
        <button class="btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>

    <!-- Fixed Bottom Navigation -->
    <div class="fixed-bottom-nav">
        <!-- Conditional check for unverified users -->
        <?php if ($user['verified'] == 0): ?>
            <a href="#" class="disabled-btn" onclick="showVerificationMessage()">Love Birds</a>
            <a href="#" class="disabled-btn" onclick="showVerificationMessage()">Requests Received</a>
        <?php else: ?>
            <a href="lovebirds.php">Love Birds</a>
            <a href="requestsent.php">Requests Received</a>
        <?php endif; ?>

        <a href="perfectmatch.php">Perfect Matches</a>
        <a href="userprofile.php">My Profile</a>
    </div>

    <!-- Verification Message -->
    <div id="verification-message" class="verification-message" style="display: none;">
        <div class="message-content">
            <p>Please verify your profile.</p>
            <a href="https://instagram.com/unseen.match" class="verify-link">Click here to verify your profile</a>
        </div>
    </div>

    <script>
        // Show the verification message for unverified users
        function showVerificationMessage() {
            document.getElementById('verification-message').style.display = 'block';
        }

        // Handle file upload (same as before)
        function uploadProfilePic(event) {
            const file = event.target.files[0];
            if (file && (file.type === 'image/jpeg' || file.type === 'image/jpg')) {
                const formData = new FormData();
                formData.append('profile_pic', file);

                // Send the image to the server via AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'upload_image.php', true);  // Send to upload_image.php
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // On success, reload the page to display the updated profile image
                        location.reload();  // This will reload the current page
                    } else {
                        alert('Failed to upload image. Please try again.');
                    }
                };
                xhr.send(formData);
            } else {
                alert('Please upload a valid image file (JPEG, JPG only).');
            }
        }
    </script>

    <style>
        .disabled-btn {
            opacity: 0.5;
        }

        .verification-message {
            position: fixed;
            font-family: 'Arial', sans-serif;
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
</body>
</html>
