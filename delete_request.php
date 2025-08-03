<?php
// Include the database connection file
include('db_connection.php');

// Start the session to get the logged-in user's username
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "User not logged in.";
    exit;
}

// Get the logged-in user's username
$loggedInUser = $_SESSION['username'];

// Get the other user's username and request_send_to from the POST request
if (isset($_POST['username']) && isset($_POST['request_send_to'])) {
    $userUsername = $_POST['username'];
    $requestSendTo = $_POST['request_send_to'];

    // Check if the request exists before attempting to delete
    $sql_check_request = "SELECT * FROM request_send WHERE username = ? AND request_send_to = ?";
    $stmt_check_request = $conn->prepare($sql_check_request);
    $stmt_check_request->bind_param("ss", $userUsername, $requestSendTo);
    $stmt_check_request->execute();
    $result_check_request = $stmt_check_request->get_result();

    if ($result_check_request->num_rows > 0) {
        // Delete the request from the request_send table
        $sql_delete_request = "DELETE FROM request_send WHERE username = ? AND request_send_to = ?";
        $stmt_delete_request = $conn->prepare($sql_delete_request);
        $stmt_delete_request->bind_param("ss", $userUsername, $requestSendTo);
        if ($stmt_delete_request->execute()) {
            echo "success";
        } else {
            echo "Error deleting request.";
        }
    } else {
        echo "Request not found.";
    }
} else {
    echo "Invalid data.";
}

// Close the database connection
$conn->close();
?>
