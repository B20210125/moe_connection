<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php';

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Send a response indicating successful logout
echo json_encode(array('status' => 'success', 'message' => 'Logout successful'));
?>
