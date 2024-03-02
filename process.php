<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code goes here
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "moe_project"; // Replace with your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve email and password from the POST request
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Perform authentication (replace this with your actual authentication logic)
    if (isValidUser($email, $password)) {
        // Generate a unique token
        $token = uniqid();

        // Save the token in your database
        $sql = "INSERT INTO tokens (email, token) VALUES ('$email', '$token')";
        if ($conn->query($sql) === TRUE) {
            // Return the token in JSON format
            $response = array(
                'success' => true,
                'token' => $token
            );
            echo json_encode($response);
        } else {
            // Token insertion failed
            $response = array(
                'success' => false,
                'message' => 'Token generation failed'
            );
            echo json_encode($response);
        }
    } else {
        // Authentication failed
        $response = array(
            'success' => false,
            'message' => 'Invalid email or password'
        );
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response = array(
        'success' => false,
        'message' => 'Invalid request method'
    );
    echo json_encode($response);
}

// Function to validate user credentials
function isValidUser($email, $password) {
    // Here, you should query your database to check if the email and password are valid
    global $conn;
    $sql = "SELECT * FROM test WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

$conn->close();

?>
