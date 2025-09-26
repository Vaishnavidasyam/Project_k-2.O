<?php
header('Content-Type: application/json');

// DB Connection
$host = 'localhost';
$db = 'hod';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

// Get query parameters
$semester = $_GET['semester'] ?? '';
$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$department = $_GET['department'] ?? '';

// Fetch the semester start date
$sql = "SELECT start_date FROM semester_dates WHERE semester=? AND class=? AND room=? AND department=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $semester, $class, $room, $department);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
$formattedDate = date("d/m/Y", strtotime($row['start_date']));
echo json_encode(["status" => "success", "start_date" => $formattedDate]);
} else {
    echo json_encode(["status" => "not_found"]);
}

$conn->close();
?>
