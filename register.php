<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

// Function to sanitize user input
function sanitizeInput($input)
{
    // Implement your sanitization logic here
    return $input;
}

// Get the user inputs (sanitize them to prevent SQL injection)
$name = sanitizeInput($_POST['name']);
$icNumber = sanitizeInput($_POST['ic_number']);
$email = sanitizeInput($_POST['email']);
$password = password_hash(sanitizeInput($_POST['password']), PASSWORD_BCRYPT);
$confirmPassword = password_hash(sanitizeInput($_POST['confirmpassword']), PASSWORD_BCRYPT);
$termsConditions = sanitizeInput($_POST['acceptTerms']);

$profileid = 'PRO' . sanitizeInput($_POST['ic_number']);
$date_created = date("Y-m-d H:i:s");

$icColor = sanitizeInput($_POST['icColor']);
$birthDate = sanitizeInput($_POST['birthDate']);
$gender = sanitizeInput($_POST['gender']);
$address = sanitizeInput($_POST['address']);
$postalCode = sanitizeInput($_POST['postalCode']);
$phoneHome = sanitizeInput($_POST['phoneHome']);
$phoneMobile = sanitizeInput($_POST['phoneMobile']);
$phoneOffice = sanitizeInput($_POST['phoneOffice']);

// Check if the email is already registered
$sqlCheckEmail = "SELECT * FROM users WHERE email=?";
$stmtCheckEmail = $conn->prepare($sqlCheckEmail);
if (!$stmtCheckEmail) {
    die("Error preparing statement: " . $conn->error);
}
$stmtCheckEmail->bind_param('s', $email);
$stmtCheckEmail->execute();
$resultCheckEmail = $stmtCheckEmail->get_result();

if ($resultCheckEmail->num_rows > 0) {
    $response = array('status' => 'error', 'message' => 'Email is already registered');
} else {
    // Check if the IC number is already registered
    $sqlCheckICNumber = "SELECT * FROM users WHERE ic_number=?";
    $stmtCheckICNumber = $conn->prepare($sqlCheckICNumber);
    if (!$stmtCheckICNumber) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmtCheckICNumber->bind_param('s', $icNumber);
    $stmtCheckICNumber->execute();
    $resultCheckICNumber = $stmtCheckICNumber->get_result();

    if ($resultCheckICNumber->num_rows > 0) {
        $response = array('status' => 'error', 'message' => 'IC number is already registered');
    } else {
        // Check if password and confirm password are similar
        if ($_POST['password'] !== $_POST['confirmpassword']) {
            $response = array('status' => 'error', 'message' => 'Password and confirm password do not match');
        } else {
            // Insert the new user into the database
            $sqlInsertUser = "INSERT INTO users (name, ic_number, email, password, confirmpass, type, terms_conditions, profileid, date_created) VALUES (?, ?, ?, ?, ?, 'user', ?, ?, ?)";
            $stmtInsertUser = $conn->prepare($sqlInsertUser);
            if (!$stmtInsertUser) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmtInsertUser->bind_param('ssssssss', $name, $icNumber, $email, $password, $confirmPassword, $termsConditions, $profileid, $date_created);

            $sqlInsertProfile = "INSERT INTO profile (profileid, ic_color, gender, birthdate, address, postalcode, phonehome, phonemobile, phoneoffice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertProfile = $conn->prepare($sqlInsertProfile);
            if (!$stmtInsertProfile) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmtInsertProfile->bind_param('sssssssss', $profileid, $icColor, $gender, $birthDate, $address, $postalCode, $phoneHome, $phoneMobile, $phoneOffice);

            // Begin the transaction
            $conn->begin_transaction();

            try {
                // Execute the first query
                $stmtInsertUser->execute();

                // Get the user ID from the first insertion
                $userId = $stmtInsertUser->insert_id;

                // Execute the second query
                $stmtInsertProfile->execute();

                // Commit the transaction if all queries were successful
                $conn->commit();

                $response = array('status' => 'success', 'message' => 'Registration successful');
            } catch (Exception $e) {
                // An error occurred, rollback the transaction
                $conn->rollback();
                $response = array('status' => 'error', 'message' => 'Error registering user');
            }
        }
    }
}

// Close the statements
$stmtCheckEmail->close();
$stmtCheckICNumber->close();
$stmtInsertUser->close();
$stmtInsertProfile->close();

echo json_encode($response);

// Close the connection
$conn->close();

?>
