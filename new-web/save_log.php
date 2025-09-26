<?php
// Database connection
$servername = "localhost"; 
$username = "root";  // default for XAMPP
$password = "";      // default for XAMPP
$dbname = "hod";     // your database

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Collect POST data
$semester = $_POST['semester'];
$class = $_POST['class'];
$room = $_POST['room'];
$department = $_POST['department'];

// Check if the same semester and class already exist
$sql_check = "SELECT * FROM log WHERE semester = ? AND class = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ss", $semester, $class);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $existing = $result->fetch_assoc();
  if ($existing['room'] != $room) {
    // Semester + Class already assigned to a different room
    echo "Error: This semester and class are already allocated to Room: " . $existing['room'];
    exit();
  }
}

// If not already assigned, insert
$sql_insert = "INSERT INTO log (semester, class, room, department) VALUES (?, ?, ?, ?) 
ON DUPLICATE KEY UPDATE room = room"; // Avoid duplicate insert

$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("ssss", $semester, $class, $room, $department);

if ($stmt->execute()) {
  echo "success";
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
