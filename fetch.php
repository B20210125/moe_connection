<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php';

// Check if the ID parameter is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch data based on the specific ID
    $result = $conn->query("SELECT * FROM test WHERE id = $id");

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
        
    } else {
        echo json_encode(['error' => 'No data found for the specified ID']);
    }
} else {
    echo json_encode(['error' => 'ID parameter is missing']);
}

// Close the database connection
$conn->close();
?>
