<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "faculty";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get parameters from the query string
$semester = $_GET['semester'] ?? '';
$section = $_GET['section'] ?? '';
$subject = $_GET['subject'] ?? '';
$faculty = $_GET['faculty'] ?? '';

// Prepare and execute query
$sql = "SELECT * FROM students WHERE semester = ? AND section = ? AND subject = ? AND faculty = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $semester, $section, $subject, $faculty);
$stmt->execute();
$result = $stmt->get_result();
$students = [];

while ($row = $result->fetch_assoc()) {
  $students[] = $row;
}

header('Content-Type: application/json');
echo json_encode($students);

$stmt->close();
$conn->close();
?>
                  