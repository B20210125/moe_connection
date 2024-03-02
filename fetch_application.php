<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Include your database connection configuration
include 'config.php';

// Fetch course/qualification options
$sqlCourse = "SELECT course_name FROM courses";
$resultCourse = $conn->query($sqlCourse);

// Fetch institution options
$sqlInstitution = "SELECT ins_name FROM institutions";
$resultInstitution = $conn->query($sqlInstitution);

// Fetch country options
$sqlCountry = "SELECT country_code, label FROM countries";
$resultCountry = $conn->query($sqlCountry);

// Fetch country options
$sqlDuration = "SELECT years FROM duration_years";
$resultDuration = $conn->query($sqlDuration);

// Process results
$response = array();

if ($resultCourse->num_rows > 0) {
    $courses = array();
    while($row = $resultCourse->fetch_assoc()) {
        $courses[] = $row['course_name'];
    }
    $response['course_options'] = $courses;
} else {
    $response['course_options'] = array();
}

if ($resultInstitution->num_rows > 0) {
    $institutions = array();
    while($row = $resultInstitution->fetch_assoc()) {
        $institutions[] = $row['ins_name'];
    }
    $response['institution_options'] = $institutions;
} else {
    $response['institution_options'] = array();
}

if ($resultCountry->num_rows > 0) {
    $countries = array();
    while($row = $resultCountry->fetch_assoc()) {
        $countries[] = $row['label'];
    }
    $response['country_options'] = $countries;
} else {
    $response['country_options'] = array();
}

if ($resultDuration->num_rows > 0) {
    $duration = array();
    while($row = $resultDuration->fetch_assoc()) {
        $duration[] = $row['years'] . " years";
    }
    $response['duration_options'] = $duration;
} else {
    $response['duration_options'] = array();
}

echo json_encode($response);
$conn->close();
?>
