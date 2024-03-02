<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Your database connection code
include 'config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if filePath and fileName parameters are set
    if (isset($_POST['filePath']) && isset($_POST['fileName'])) {
        // Sanitize the input parameters
        $fileName = $_POST['fileName'];
        $filePath = "uploads/education/" . $fileName;

        // Delete the file from the server
        if (unlink($filePath)) {
            // File deletion successful
            // Now delete the corresponding entry from the tmp_file table
            $sql = "DELETE FROM tmp_files WHERE filename = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $fileName);
            if ($stmt->execute()) {
                // Check if the entry was deleted successfully
                $rowCount = $stmt->affected_rows;
                if ($rowCount > 0) {
                    // Entry deleted successfully
                    http_response_code(200); // OK
                    echo json_encode(array("message" => "File and entry deleted successfully."));
                } else {
                    // Entry not found or not deleted
                    http_response_code(404); // Not Found
                    echo json_encode(array("message" => "Entry not found or not deleted."));
                }
            } else {
                // Error executing SQL query
                http_response_code(500); // Internal Server Error
                echo json_encode(array("message" => "Error executing SQL query."));
            }
        } else {
            // File deletion failed
            http_response_code(500); // Internal Server Error
            echo json_encode(array("message" => "Failed to delete file."));
        }
    } else {
        // Missing parameters
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Missing filePath or fileName parameter."));
    }
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Only POST requests are allowed."));
}
?>
