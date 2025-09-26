<?php
header('Content-Type: application/json');

$host       = 'localhost';
$dbname     = 'hod';
$username   = 'root';
$password   = '';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

$semester = $_GET['semester'] ?? '';
$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$department = $_GET['department'] ?? '';

if (!$semester || !$class || !$room || !$department) {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

$sql = "SELECT start_date, end_date FROM semester_dates WHERE semester=? AND class=? AND room=? AND department=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $semester, $class, $room, $department);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["status" => "success", "start_date" => $row['start_date'], "end_date" => $row['end_date']]);
} else {
    echo json_encode(["status" => "empty"]);
}

$conn->close();
?>
