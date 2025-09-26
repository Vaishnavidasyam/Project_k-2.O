<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";  // Update if needed
$password = "";      // Update if needed
$database = "hod";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Read raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Debugging: Log received data
file_put_contents("debug_log.txt", print_r($data, true));

if (!isset($data["id"]) || empty($data["id"])) {
    echo json_encode(["error" => "No ID received!"]);
    exit;
}

$id = intval($data["id"]);  // Ensure it's an integer

$sql = "DELETE FROM class WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);



if ($stmt->execute()) {
    echo json_encode(["success" => "Class deleted successfully!"]);
} else {
    echo json_encode(["error" => "Error deleting class: " . $conn->error]);
}
$sql = "DELETE FROM tt WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);



if ($stmt->execute()) {
    echo json_encode(["success" => "Class deleted successfully!"]);
} else {
    echo json_encode(["error" => "Error deleting class: " . $conn->error]);
}
$stmt->close();
$conn->close();
?>
