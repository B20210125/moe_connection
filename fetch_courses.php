<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

$sql = "SELECT * FROM courses";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $courses = array();

    while ($row = $result->fetch_assoc()) {
        $courses[] = $row['course_name'];
    }

    // Send the courses as a JSON response
    echo json_encode($courses);
} else {
    // Handle case when no courses are found
    echo json_encode(['error' => 'No courses found']);
}

// Close the connection
$stmt->close();
$conn->close();
?>
