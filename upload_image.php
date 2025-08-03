<?php
// Include the database connection
include('db_connection.php');

// Start session
session_start();

// Check if a file has been uploaded
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $targetDir = 'uploads/profile_pics/';  // Target directory where files will be saved
    $fileName = $_SESSION['username'] . '.jpg';  // Use the username as the file name

    // Get the uploaded file's temporary path
    $tempPath = $_FILES['profile_pic']['tmp_name'];

    // Validate file type (JPEG/JPG only)
    $fileType = mime_content_type($tempPath);
    if ($fileType == 'image/jpeg' || $fileType == 'image/jpg') {
        // Move the uploaded file to the target directory
        $targetPath = $targetDir . $fileName;
        
        if (move_uploaded_file($tempPath, $targetPath)) {
            echo 'File uploaded successfully.';
        } else {
            echo 'Error: Could not move the file to the target directory.';
        }
    } else {
        echo 'Error: Only JPEG/JPG files are allowed.';
    }
} else {
    echo 'Error: No file uploaded or an upload error occurred.';
}
?>
