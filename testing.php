<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

include 'config.php'; // Include your database connection configuration

// Check if the file was sent
if ($_FILES['file']) {
    // Define the directory where you want to store the uploaded files
    $uploadDir = 'uploads/';

    // Generate a unique filename to prevent conflicts
    $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
    $uploadFile = $uploadDir . $fileName;

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        // File uploaded successfully

        // Check connection
        if ($conn->connect_error) {
            $response = array("status" => "error", "message" => "Database connection failed: " . $conn->connect_error);
            echo json_encode($response);
            exit();
        }

        // Prepare SQL statement to insert file details into the database
        $sql = "INSERT INTO files (filename, file) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $null = NULL; // Placeholder for BLOB data
        $stmt->bind_param("sb", $fileName, $null);
        
        // Read file data and bind it to the prepared statement
        $fileData = file_get_contents($uploadFile);
        $stmt->send_long_data(1, $fileData);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // File details inserted successfully
            $response = array("status" => "success", "fileName" => $fileName);
            echo json_encode($response);
        } else {
            // Failed to insert file details
            $response = array("status" => "error", "message" => "Failed to insert file details into database: " . $conn->error);
            echo json_encode($response);
        }

        // Close prepared statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        // Failed to move the file
        $response = array("status" => "error", "message" => "Failed to upload file");
        echo json_encode($response);
    }
} else {
    // No file received
    $response = array("status" => "error", "message" => "No file received");
    echo json_encode($response);
}
?>
