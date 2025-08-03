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

// Get the sender's username from the session and the recipient's username from the POST request
$sender = $_POST['sender'];
$recipient = $_POST['recipient'];

// Check if the action is "delete" to handle deletion
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'delete') {
    // Delete the request from the request_send table
    $sql = "DELETE FROM request_send WHERE username = ? AND request_send_to = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $sender, $recipient);

    if ($stmt->execute()) {
        echo "deleted"; // Respond with 'deleted' if the request was successfully deleted
    } else {
        echo "error";
    }
} else {
    // If action is not "delete", insert the request into the request_send table
    $time = $_POST['time']; // Timestamp of when the request was sent
    $sql = "INSERT INTO request_send (username, request_send_to, date) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $sender, $recipient, $time);

    if ($stmt->execute()) {
        echo "success"; // Respond with 'success' if the request was successfully inserted
    } else {
        echo "error";
    }
}

$stmt->close();
$conn->close();
?>
