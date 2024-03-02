<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

if (isset($_POST['profileId'])) {
    $profileId = $_POST['profileId'];

    $sql = "SELECT *
    FROM profile
    JOIN users ON profile.profileid = users.profileid
    WHERE users.profileid = ?";    

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $profileId);
    $stmt->execute();

    // Check if the query execution was successful
    if ($stmt->error) {
        $response = array('status' => 'error', 'message' => 'Error executing query: ' . $stmt->error);
    } else {
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = array('status' => 'success', 'data' => $row);
        } else {
            $response = array('status' => 'error', 'message' => 'No profile data found');
        }
    }
} else {
    $response = array('status' => 'error', 'message' => 'No Profile ID provided');
}

echo json_encode($response);

// Close the connection
$conn->close();
?>
