<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php';

// Check if the necessary parameters are set
if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Update data in the database using prepared statements
    $updateQuery = $conn->prepare("UPDATE test SET name = ?, email = ?, message = ? WHERE id = ?");
    $updateQuery->bind_param("sssi", $name, $email, $message, $id);

    if ($updateQuery->execute()) {
        echo json_encode(['message' => 'Data updated successfully']);
    } else {
        // Log the error for debugging purposes
        error_log("Error updating data: " . $updateQuery->error);

        echo json_encode(['error' => 'Error updating data']);
    }

    $updateQuery->close();
} else {
    echo json_encode(['error' => 'Required parameters are missing']);
}

// Close the database connection
$conn->close();
?>
