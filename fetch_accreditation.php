<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

if(isset($_POST['userid'])) {
    $userid = $_POST['userid'];

    // Fetch accreditation data for the provided user ID
    $sqlAccreditation = "SELECT * FROM application_form WHERE userid=?";
    $stmt = $conn->prepare($sqlAccreditation);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the first row (assuming only one accreditation entry per user)
        $accreditationData = $result->fetch_assoc();

        // Send the accreditation data as a JSON response
        echo json_encode($accreditationData);
    } else {
        // Handle case when no accreditation data is found for the provided user
        echo json_encode(['error' => 'No accreditation data found for the user']);
    }
} else {
    // Handle case when userid parameter is not provided
    echo json_encode(['error' => 'User ID parameter not provided']);
}

$conn->close();
?>
