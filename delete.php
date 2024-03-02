<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php';

// Check if the ID parameter is set
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete data from the database
    $deleteQuery = "DELETE FROM test WHERE id = $id";

    if ($conn->query($deleteQuery) === TRUE) {
        echo json_encode(['message' => 'Data deleted successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting data: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'ID parameter is missing']);
}

// Close the database connection
$conn->close();
?>
