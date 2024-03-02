<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

// Check if the IC number is provided in the POST request
if(isset($_POST['icNumber'])) {
    // Get the IC number from the POST request
    $icNumber = $_POST['icNumber'];

    // Prepare and execute the SQL query to fetch the latest user ID
    $sql = "SELECT userid FROM accreditation_form_status WHERE ic_number = ? ORDER BY userid DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $icNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the latest user ID
        $row = $result->fetch_assoc();
        $userId = $row['userid'];

        // Send the user ID as JSON response
        echo json_encode(['userId' => $userId]);
    } else {
        // If no user ID is found, return an error message
        echo json_encode(['error' => 'No user ID found for the provided IC number']);
    }

    // Close the statement
    $stmt->close();
} else {
    // If IC number is not provided, return an error message
    echo json_encode(['error' => 'IC number not provided']);
}

// Close the database connection
$conn->close();
?>
