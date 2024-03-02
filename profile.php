<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

if(isset($_POST['profileId'])) {

    $id = $_POST['profileId'];

    $sql = "SELECT *
    FROM profile
    JOIN users ON profile.profileid = users.profileid
    WHERE users.profileid = ?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Send the profile data as a JSON response
        echo json_encode($row);
    } else {
        // Handle case when no profile data is found
        echo json_encode(['error' => 'No profile data found']);
    }
} else {
    echo json_encode(['error' => 'No Profile ID provided']);
}


// Close the connection
$conn->close();
?>
