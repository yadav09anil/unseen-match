<?php
// Include the database connection file
include('db_connection.php');

// Start session to get the logged-in user's username
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "User not logged in.";
    exit;
}

// Get the username from the URL query string
if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // Query to get the user's details from the userdetails table
    $sql = "SELECT first_name, last_name, age, gender, occupation, religion, city, state, description, verified 
            FROM userdetails WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user data if the user exists
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        // Fetch the user's details
        $first_name = ucwords(strtolower($user_data['first_name']));
        $last_name = ucwords(strtolower($user_data['last_name']));
        $age = $user_data['age'];
        $gender = ucwords(strtolower($user_data['gender']));
        $occupation = ucwords(strtolower($user_data['occupation']));
        $religion = ucwords(strtolower($user_data['religion']));
        $city = ucwords(strtolower($user_data['city']));
        $state = ucwords(strtolower($user_data['state']));
        $description = ucwords(htmlspecialchars($user_data['description']));
        $verified = $user_data['verified'];

        // Profile picture handling
        $profile_pic_path = "uploads/profile_pics/" . $username . ".jpg";
        
        // Check if the user's profile picture exists, otherwise use a default image
        if (!file_exists($profile_pic_path)) {
            $profile_pic_path = "uploads/profile_pics/p1r2o3f4i5l6e7.png"; // Default profile image
        }
    } else {
        echo "User not found.";
        exit;
    }
} else {
    echo "No user specified.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Dating App</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="viewprofile.css">
</head>
<body>
    <div class="container">
        <!-- Profile Heading -->
        <h2><?php echo $first_name . "'s Profile"; ?></h2>

        <!-- Profile Picture with Plus Icon -->
        <div class="profile-img-container">
            <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="<?php echo htmlspecialchars($first_name); ?>'s Profile Picture" class="profile-img" id="profile-img">
        </div>

        <!-- Profile Details -->
        <div class="profile-details">
            <p>
                <strong>Name:</strong>
                <span class="name-container">
                    <span class="name"><?php echo htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name); ?></span>
                    <?php
                    // Display verified badge if the user is verified
                    if ($verified == 1) {
                        echo '<img src="https://thecloutmagazine.com/wp-content/uploads/2021/09/Legitly-Verified-Blue-Badge-top-page.png" alt="Verified Badge" class="verified-badge">';
                    }
                    ?>
                </span>
            </p>
            <p><strong>Age :</strong> <?php echo $age; ?></p>
            <p><strong>Gender :</strong> <?php echo $gender; ?></p>

            <p><strong>City :</strong> <?php echo $city; ?></p>
            <p><strong>State :</strong> <?php echo $state; ?></p>
            <p><strong>Occupation :</strong> <?php echo $occupation; ?></p>
            <p><strong>Religion :</strong> <?php echo $religion; ?></p>
            <p><strong>Bio :</strong><?php echo $description; ?></p>
        </div>
    </div>

    <!-- Fixed Bottom Navigation -->
    <div class="fixed-bottom-nav">
        <a href="lovebirds.php">Love Birds</a>
        <a href="requestsent.php">Requests Sent</a>
        <a href="perfectmatch.php">Perfect Matches</a>
        <a href="userprofile.php">My Profile</a>
    </div>

</body>
</html>
