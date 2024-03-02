<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code goes here
include 'config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve email and password from the POST request
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Generate a unique token
    $token = uniqid();

    // Perform registration (replace this with your actual registration logic)
    $sql = "INSERT INTO test (email, password, token) VALUES ('$email', '$password', '$token')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Registration successful
        $response = array(
            'success' => true,
            'message' => 'Registration successful'
        );
        echo json_encode($response);
    } else {
        // Registration failed
        $response = array(
            'success' => false,
            'message' => 'Registration failed'
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

?>
