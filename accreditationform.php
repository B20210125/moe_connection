<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

// Function to handle response
function sendResponse($status, $message) {
    $response = array("status" => $status, "message" => $message);
    echo json_encode($response);
    exit();
}

// Get the JSON string sent in the request body
$jsonString = file_get_contents('php://input');

// Decode the JSON string into a PHP associative array
$data = json_decode($jsonString, true);

// Check if decoding was successful
if ($data === null) {
    sendResponse("error", "Failed to decode JSON data.");
}

// Process the data received from Flutter application
$ic = $data['ic'];
$reasonForAccreditationFor = $data['reasonForAccreditationFor'];
$duration = $data['duration'];
$highestQualification = $data['highestQualification'];
$course = $data['course'];
$institution = $data['institution'];
$country = $data['country'];
$modeOfLearning = $data['modeOfLearning'];
$reasonForApply = $data['reasonForApply'];
$remarks = $data['remarks'];

// Generate unique userid with "AC" prefix and starting from '001'
$prefix = 'AC';
$startNumber = 1;
$maxLength = 3; // Set the maximum length of the user ID

$sqlGetLastUserId = "SELECT userid FROM application_form ORDER BY userid DESC LIMIT 1";
$result = $conn->query($sqlGetLastUserId);

if ($result->num_rows > 0) {
    $lastUserId = $result->fetch_assoc()['userid'];
    $lastIdNumber = (int)substr($lastUserId, strlen($prefix));
    
    if ($lastIdNumber == 10 ** $maxLength - 1) {
        // Increase the length of the user ID
        $newUserId = $prefix . str_pad($lastIdNumber + 1, $maxLength + 1, '0', STR_PAD_LEFT);
    } else {
        // Continue with the existing length
        $newUserId = $prefix . str_pad($lastIdNumber + 1, $maxLength, '0', STR_PAD_LEFT);
    }
} else {
    $newUserId = $prefix . str_pad($startNumber, $maxLength, '0', STR_PAD_LEFT);
}



// Insert applicant data into the application table
$sql = "INSERT INTO application_form (userid, accreditation_for, duration, highest_qualification, course, institution, country, modeoflearning, reasonforapply, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $newUserId, $reasonForAccreditationFor, $duration, $highestQualification, $course, $institution, $country, $modeOfLearning, $reasonForApply, $remarks);
if (!$stmt->execute()) {
    sendResponse("error", "Failed to store application form data.");
}

// Handle education data if it's present
if (isset($data['educationDataList'])) {
    $educationDataList = $data['educationDataList'];
    foreach ($educationDataList as $educationData) {
        // Insert education data into the education_form table
        $educationType = $educationData['educationtype'];
        $school = $educationData['school'];
        $titleOfExamination = $educationData['titleOfExamination'];
        $grade = $educationData['grade'];
        $year = $educationData['year'];
        $fileName = $educationData['fileName'];
        $filePath = "uploads/education/" . $fileName;

        // Check if the file with the same name already exists in tmp_files table
        $sqlCheckEducation = "SELECT * FROM tmp_files WHERE filename=?";
        $stmtCheckEducation = $conn->prepare($sqlCheckEducation);
        $stmtCheckEducation->bind_param("s", $fileName);
        $stmtCheckEducation->execute();
        $resultCheckEducation = $stmtCheckEducation->get_result();

        if ($resultCheckEducation->num_rows > 0) {
            // File with the same name exists, move the file to the new destination
            $newEducationTargetDir = "uploads/{$newUserId}/education/";
            $newEducationTargetFile = $newEducationTargetDir . $fileName;
            if (!is_dir($newEducationTargetDir)) {
                mkdir($newEducationTargetDir, 0777, true);
            }
            // Move the file to the new destination
            if (rename($filePath, $newEducationTargetFile)) {
                // Update the filepath in tmp_files table
                $sqlUpdateFilePath = "UPDATE tmp_files SET filepath = ? WHERE filename = ?";
                $stmtUpdateFilePath = $conn->prepare($sqlUpdateFilePath);
                $stmtUpdateFilePath->bind_param("ss", $newEducationTargetFile, $fileName);
                if (!$stmtUpdateFilePath->execute()) {
                    sendResponse("error", "Failed to update file path.");
                }
            } else {
                sendResponse("error", "Failed to move the file.");
            }
        }

        // Insert education data into the education_form table
        $sqlInsertEducation = "INSERT INTO education_form (userid, educationtype, school, titleofexamination, grade, year, filepath, filename) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertEducation = $conn->prepare($sqlInsertEducation);
        $stmtInsertEducation->bind_param("ssssssss", $newUserId, $educationType, $school, $titleOfExamination, $grade, $year, $newEducationTargetFile, $fileName);

        if (!$stmtInsertEducation->execute()) {
            sendResponse("error", "Failed to store education data.");
        }
    }
}

// Insert document data into the document_form table
$identityFileName = $data['identityFileName'];
$identityFilePath = "uploads/document/identity_card/" . $identityFileName;
$offerLetterFileName = $data['offerLetterFileName'];
$offerLetterFilePath = "uploads/document/offer_letter/" . $offerLetterFileName;
$acceptPrivacyPolicy = $data['acceptPrivacyPolicy'] ? 'true' : 'false';
$acceptCertification = $data['acceptCertification'] ? 'true' : 'false';

date_default_timezone_set("Asia/Brunei");
$currentDateTime = date("Y-m-d H:i:s");

 // Check if the file with the same name already exists in other_tmp_files table for either identityFileName or offerLetterFileName
$sqlCheckDocument = "SELECT * FROM other_tmp_files WHERE filename=? OR filename=?";
$stmtCheckDocument = $conn->prepare($sqlCheckDocument);
$stmtCheckDocument->bind_param("ss", $identityFileName, $offerLetterFileName);
$stmtCheckDocument->execute();
$resultCheckDocument = $stmtCheckDocument->get_result();

if ($resultCheckDocument->num_rows > 0) {
    // File with the same name exists, move the file to the new destination
    $IdentityTargetDir = "uploads/{$newUserId}/identity_card/";
    $OfferTargetDir = "uploads/{$newUserId}/offer_letter/";
    $newIdentityTargetDir = $IdentityTargetDir . $identityFileName;
    $newOfferTargetDir = $OfferTargetDir . $offerLetterFileName;
    if (!is_dir($IdentityTargetDir)) {
        mkdir($IdentityTargetDir, 0777, true);
    }

    if (!is_dir($OfferTargetDir)) {
        mkdir($OfferTargetDir, 0777, true);
    }
    // Move the file to the new destination
    if (rename($identityFilePath, $newIdentityTargetDir)) {
        // Update the filepath in tmp_files table
        $sqlUpdateFilePath = "UPDATE other_tmp_files SET filepath = ? WHERE filename = ?";
        $stmtUpdateFilePath = $conn->prepare($sqlUpdateFilePath);
        $stmtUpdateFilePath->bind_param("ss", $newIdentityTargetDir, $identityFileName);
        if (!$stmtUpdateFilePath->execute()) {
            sendResponse("error", "Failed to update file path.");
        }
    } else {
        sendResponse("error", "Failed to move the file.");
    }

     // Move the file to the new destination
     if (rename($offerLetterFilePath, $newOfferTargetDir)) {
        // Update the filepath in tmp_files table
        $sqlUpdateFilePath = "UPDATE other_tmp_files SET filepath = ? WHERE filename = ?";
        $stmtUpdateFilePath = $conn->prepare($sqlUpdateFilePath);
        $stmtUpdateFilePath->bind_param("ss", $newOfferTargetDir, $offerLetterFileName);
        if (!$stmtUpdateFilePath->execute()) {
            sendResponse("error", "Failed to update file path.");
        }
    } else {
        sendResponse("error", "Failed to move the file.");
    }
}

$sqlInsertDocument = "INSERT INTO document_form (userid, identity_card_filename, identity_card_filepath, offer_letter_filename, offer_letter_filepath, acceptPrivacyPolicy, acceptCertification) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmtInsertDocument = $conn->prepare($sqlInsertDocument);
$stmtInsertDocument->bind_param("sssssss", $newUserId, $identityFileName, $newIdentityTargetDir, $offerLetterFileName, $newOfferTargetDir, $acceptPrivacyPolicy, $acceptCertification);

if (!$stmtInsertDocument->execute()) {
    sendResponse("error", "Failed to store document data.");
}

$sqlInsertAccreditation = "INSERT INTO accreditation_form_status (ic_number, userid, status, create_at) VALUES (?, ?, 'Application Submitted', ?)";
$stmtInsertAccreditation = $conn->prepare($sqlInsertAccreditation);
$stmtInsertAccreditation->bind_param("sss", $ic, $newUserId, $currentDateTime);

if (!$stmtInsertAccreditation->execute()) {
    sendResponse("error", "Failed to store document data.");
}

// Check the other files and move or update the file path
$sqlCheckOther = "SELECT * FROM other_tmp_files WHERE (filename != ? OR filename IS NULL) AND (filename != ? OR filename IS NULL) AND ic_number = ?";
$stmtCheckOther = $conn->prepare($sqlCheckOther);
$stmtCheckOther->bind_param("sss", $identityFileName, $offerLetterFileName, $ic);
$stmtCheckOther->execute();
$resultCheckOther = $stmtCheckOther->get_result();

$OtherTargetDir = "uploads/{$newUserId}/others/";
if (!is_dir($OtherTargetDir)) {
    mkdir($OtherTargetDir, 0777, true);
}

// Move and update files
while ($row = $resultCheckOther->fetch_assoc()) {
    $fileName = $row['filename'];
    $filePath = $row['filepath'];
    
    // Move the file to the new folder
    $newFilePath = $OtherTargetDir . $fileName;
    if (rename($filePath, $newFilePath)) {
        // Update the filepath in the database
        $sqlUpdateFilePath = "UPDATE other_tmp_files SET filepath = ? WHERE filename = ? AND ic_number = ?";
        $stmtUpdateFilePath = $conn->prepare($sqlUpdateFilePath);
        $stmtUpdateFilePath->bind_param("sss", $newFilePath, $fileName, $ic);
        if (!$stmtUpdateFilePath->execute()) {
            // Handle update error
        }
    } else {
        // Handle file move error
    }
}


// Check if both the application form, education data, and document data were stored successfully
if ($stmt->errno || $stmtInsertEducation->errno || $stmtInsertDocument->errno || $stmtInsertAccreditation->errno) {
    // If any of the statements encountered an error, send an error response
    sendResponse("error", "Failed to store all data.");
} else {
    // Close database connection
    $stmt->close();
    $stmtInsertEducation->close();
    $stmtInsertDocument->close();
    $stmtInsertAccreditation->close();
    $conn->close();

    // If all operations were successful, send a success response
    sendResponse("success", "Application form data, education data, document data, and other files stored successfully.");
}
?>
