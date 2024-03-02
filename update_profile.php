<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

$response = array(); // Initialize response array

try {
    // Check if the required parameters are set
    if (isset($_POST['profileId'])) {
        $profileId = $_POST['profileId'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $icNumber = $_POST['icNumber'];
        $passportNumber = $_POST['passportNumber'];
        $dateOfBirth = $_POST['dateOfBirth'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $postalCode = $_POST['postalCode'];
        $citizenship = $_POST['citizenship'];
        $race = $_POST['race'];
        $religion = $_POST['religion'];
        $telephoneHome = $_POST['telephoneHome'];
        $telephoneMobile = $_POST['telephoneMobile'];
        $telephoneOffice = $_POST['telephoneOffice'];
        $martialStatus = isset($_POST['martialStatus']) ? $_POST['martialStatus'] : '';


        if (!in_array($martialStatus, ['Single', 'Divorced', 'Married'])) {
            $martialStatus = isset($_POST['otherMartialStatus']) ? $_POST['otherMartialStatus'] : '';
        }

        // Define the update queries
        $sqlUpdateProfile = "UPDATE profile SET
                passport_number = ?,
                birthdate = ?,
                gender = ?,
                address = ?,
                postalcode = ?,
                citizenship = ?,
                race = ?,
                religion = ?,
                phonehome = ?,
                phonemobile = ?,
                phoneoffice = ?,
                martial_status = ?
                WHERE profileid = ?";
        $stmtUpdateProfile = $conn->prepare($sqlUpdateProfile);
        $stmtUpdateProfile->bind_param('sssssssssssss', $passportNumber, $dateOfBirth, $gender, $address, $postalCode, $citizenship, $race, $religion, $telephoneHome, $telephoneMobile, $telephoneOffice,  $martialStatus, $profileId);

        $sqlUpdateUser = "UPDATE users SET
                name = ?,
                email = ?
                WHERE profileid = ?";
        $stmtUpdateUser = $conn->prepare($sqlUpdateUser);
        $stmtUpdateUser->bind_param('sss', $name, $email, $profileId);

        // Execute the update queries
        $stmtUpdateProfile->execute();
        $stmtUpdateUser->execute();

        // Check if both queries executed successfully
        if ($stmtUpdateProfile->affected_rows > 0 || $stmtUpdateUser->affected_rows > 0) {
            // Success
            $response = array('status' => 'success', 'message' => 'Profile updated successfully');
        } else {
            // Error
            $response = array('status' => 'error', 'message' => 'Error updating profile');
        }

        // Close the update statements
        $stmtUpdateProfile->close();
        $stmtUpdateUser->close();
    } else {
        $response = array('status' => 'error', 'message' => 'No Profile ID provided');
    }
} catch (Exception $e) {
    $response = array('status' => 'error', 'message' => 'Exception: ' . $e->getMessage());
}

// Close the connection
$conn->close();

// Add this line to log the response
error_log(json_encode($response));

// Send a clean JSON response
echo json_encode($response);
?>
