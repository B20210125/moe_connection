<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "uploads/document/others/";
    $filename = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        echo json_encode(array("message" => "Sorry, file already exists."));
        $uploadOk = 0;
    }

    // Check file size (500KB)
    if ($_FILES["file"]["size"] > 500000) {
        echo json_encode(array("message" => "Sorry, your file is too large."));
        $uploadOk = 0;
    }

    // Allow only PDF files
    if ($fileType != "pdf") {
        echo json_encode(array("message" => "Sorry, only PDF files are allowed."));
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode(array("message" => "Sorry, your file was not uploaded."));
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now save file metadata to the database
            $name = $_POST['name'];
            $ic = $_POST['ic'];
            $otherFileName = $_POST['otherFileName'];
            $otherFilePath =  $target_file;
            
            $query = "INSERT INTO other_tmp_files (name, filepath, filename, ic_number) VALUES ('$name', '$otherFilePath', '$otherFileName', '$ic')";
            if (mysqli_query($conn, $query)) {
                echo json_encode(array("message" => "File uploaded successfully.", "filepath" => $target_file, "filename" => $filename));
            } else {
                echo json_encode(array("message" => "Error: " . mysqli_error($conn)));
            }
        } else {
            echo json_encode(array("message" => "Sorry, there was an error uploading your file."));
        }
    }
} else {
    echo json_encode(array("message" => "Invalid request method."));
}
?>
