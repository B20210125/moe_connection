<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

// Sanitize user inputs
$userId = intval($_POST['userid']);
$oldPassword = $_POST['old_password']; // No need to hash here
$newPassword = $_POST['new_password'];

// Perform a query to fetch the user's current password from the database
$sqlCheckPassword = "SELECT password FROM users WHERE id=?";
$stmtCheckPassword = $conn->prepare($sqlCheckPassword);
if (!$stmtCheckPassword) {
    die(json_encode(array('success' => false, 'message' => 'Error preparing statement: ' . $conn->error)));
}
$stmtCheckPassword->bind_param('i', $userId);
$stmtCheckPassword->execute();
$stmtCheckPassword->bind_result($currentPassword);
$stmtCheckPassword->fetch();
$stmtCheckPassword->close();

// Check if a record with the given user ID exists in the database
if (!$currentPassword) {
    http_response_code(404); // User not found
    echo json_encode(array('success' => false, 'message' => 'User not found'));
} else {
    // Check if the old password provided by the user matches the current password retrieved from the database
    if (password_verify($oldPassword, $currentPassword)) {
        // Hash the new password before storing it in the database
        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Perform a query to update the user's password in the database
        $sqlUpdatePassword = "UPDATE users SET password=? WHERE id=?";
        $stmtUpdatePassword = $conn->prepare($sqlUpdatePassword);
        if (!$stmtUpdatePassword) {
            die(json_encode(array('success' => false, 'message' => 'Error preparing statement: ' . $conn->error)));
        }
        $stmtUpdatePassword->bind_param('si', $hashedNewPassword, $userId);
        $stmtUpdatePassword->execute();
        $stmtUpdatePassword->close();

        http_response_code(200); // Success
        echo json_encode(array('success' => true, 'message' => 'Password changed successfully'));
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(array('success' => false, 'message' => 'Old password is incorrect'));
    }
}

// Close the connection
$conn->close();

?>
