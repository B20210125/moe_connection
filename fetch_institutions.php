<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

$sql = "SELECT * FROM institutions";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $institutions = array();

    while ($row = $result->fetch_assoc()) {
        $institutions[] = $row['ins_name'];
    }

    // Send the institutions as a JSON response
    echo json_encode($institutions);
} else {
    // Handle case when no institutions are found
    echo json_encode(['error' => 'No institutions found']);
}

// Close the connection
$stmt->close();
$conn->close();
?>
