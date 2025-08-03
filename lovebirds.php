<?php
// Start the session to access session variables
session_start();

// Include the database connection file
include('db_connection.php');

// Check if the session username exists
if (isset($_SESSION['username'])) {
    // Get the session username
    $session_username = $_SESSION['username'];

    // SQL query to fetch requests sent by the logged-in user along with user details
    $sql = "
    SELECT a.username, a.request_send_to, u.first_name, u.last_name, u.age, u.city, u.state, u.verified
    FROM request_send a
    JOIN request_send b ON a.username = b.request_send_to
                          AND a.request_send_to = b.username
    JOIN userdetails u ON a.request_send_to = u.username
    WHERE a.username = '$session_username'
    AND a.request_send_to = b.username
    ";

    // Execute the query
    $result = $conn->query($sql);
} else {
    // Redirect to login page or show an error if the user is not logged in
    echo "Please log in to view your requests.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dating App Perfect Matches</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="lovebirds.css">
</head>
<body>
    <div class="container">
        <!-- Title Section -->
        <div class="title">
            <h1>Our Love Birds</h1>
            <p>Two hearts have found each other, creating a bond that grows stronger every day.</p>
            <p>These are the matches where both hearts have embraced the journey of love together.</p>
        </div>

        <!-- Perfect Matches Section -->
        <div class="matches" id="match1">
            <?php
            // Check if there are results
            if ($result && $result->num_rows > 0) {
                // Loop through the results and display them
                while($row = $result->fetch_assoc()) {
                    $request_sent_to = $row['request_send_to'];
                    $first_name = ucwords(strtolower($row['first_name'])); // Capitalize the first letter of each word
                    $last_name = ucwords(strtolower($row['last_name'])); // Capitalize the first letter of each word
                    $age = $row['age'];
                    $city = ucwords(strtolower($row['city'])); // Capitalize the first letter of each word
                    $state = ucwords(strtolower($row['state'])); // Capitalize the first letter of each word
                    $verified = $row['verified'] ? "Yes" : "No"; // Verified value as Yes or No
                    $username = $row['username']; // Get the username of the matched user

                    // Define the path to the profile picture
                    $profile_pic_path = 'uploads/profile_pics/' . $request_sent_to . '.jpg';

                    // Check if the profile picture exists, otherwise use the default one
                    if (!file_exists($profile_pic_path)) {
                        $profile_pic_path = 'uploads/profile_pics/p1r2o3f4i5l6e7.png'; // Default picture
                    }

                    // Define the verification image path
                    $verification_img = $row['verified'] ? 'uploads/profile_pics/v1e2r3i4f5i6e7d8.png' : '';

                    // Displaying user information
                    echo "
                    <div class='match-box'>
                        <button class='btn-cross' onclick='confirmDelete()'>×</button>
                        <img src='$profile_pic_path' alt='Profile Picture'>
                        <h3>$first_name $last_name, $age";

                    // Display the verification image if the user is verified
                    if ($verification_img) {
                        echo " <img src='$verification_img' alt='Verified' style='width: 25px; height: 25px; vertical-align: middle; margin-left: 5px;'>";
                    }

                    echo "</h3>
                        <p><strong>Address:</strong> $city, $state</p>

                        <div class='button-group'>
                        <button class='btn-view' onclick='viewProfile(\"$request_sent_to\")'>View Profile</button>

                        <button class='btn-send' onclick='openInstagram(\"$request_sent_to\")'>Instagram</button>
                        </div>
                    </div>";
                }
            } else {
                echo "<p>No requests found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Fixed Bottom Navigation -->
    <div class="fixed-bottom-nav">
        <a href="lovebirds.php">Love Birds</a>
        <a href="requestsent.php">Requests Received</a>
        <a href="perfectmatch.php">Perfect Matches</a>
        <a href="userprofile.php">My Profile</a>
    </div>

    <!-- Modal (Hidden by default) -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p id="modalMessage">Do you want to unsend this request?</p>
            <div class="modal-button-group">
                <button class="btn-confirm" onclick="confirmUnsend()">Yes</button>
                <button class="btn-cancel" onclick="cancelUnsend()">No</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <p>Do you want to delete this match?</p>
            <div class="modal-button-group">
                <button class="btn-confirm" onclick="deleteMatch()">Yes</button>
                <button class="btn-cancel" onclick="cancelDelete()">No</button>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle button toggle and modal pop-up -->
    <script>
        // Redirect to viewprofile.php
        function viewProfile(username) {
            window.location.href = 'viewprofile.php?username=' + username;
        }

        // Open Instagram with the correct username
        function openInstagram(username) {
            window.location.href = 'https://www.instagram.com/' + username; // Instagram profile URL with the username
        }

        // Show delete confirmation modal
        function confirmDelete() {
            matchBox = document.getElementById('match-box1');
            document.getElementById('deleteModal').style.display = 'flex';
        }

        // Delete the match if confirmed
        function deleteMatch() {
            matchBox.remove();
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Cancel delete action
        function cancelDelete() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
</body>
</html>
