<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php'; // Make sure to include your database configuration file

// Check if the ic parameter is provided
if(isset($_POST['ic'])) {
    // Retrieve POST data and sanitize it
    $ic = mysqli_real_escape_string($conn, $_POST['ic']);

    // Prepare SQL statement with a parameterized query
    $sql = "SELECT * FROM accreditation_form_status WHERE ic_number = ?";
    
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    // Bind parameters
    $stmt->bind_param("s", $ic);
    // Execute SQL query
    $stmt->execute();
    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Initialize an array to store the results
        $data = array();

        // Fetch rows from the result set
        while ($row = $result->fetch_assoc()) {
            // Add each row to the data array
            $data[] = $row;
        }

        // Convert the data array to JSON format and echo it
        echo json_encode(array("success" => true, "data" => $data));
    } else {
        // If no rows were found, return an empty array
        echo json_encode(array("success" => true, "data" => array()));
    }

    // Close statement
    $stmt->close();
} else {
    // If the ic parameter is not provided, return an error message
    echo json_encode(array("success" => false, "message" => "IC parameter is missing"));
}

// Close database connection
$conn->close();
?>
