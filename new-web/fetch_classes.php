<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "hod";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$semester = $_GET['semester'] ?? '';
$section = $_GET['section'] ?? '';

$sql = "SELECT * FROM class WHERE semester = ? AND section = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $semester, $section);
$stmt->execute();

$result = $stmt->get_result();
$rows = [];

while ($row = $result->fetch_assoc()) {
  $rows[] = $row;
}

echo json_encode($rows);

$stmt->close();
$conn->close();
?>
