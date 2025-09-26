<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$semester = $_POST['semester'];
$class = $_POST['class'];
$room = $_POST['room'];
$department = $_POST['department'];

// Insert into log table
$sql = "INSERT INTO log (semester, class, room, department) VALUES ('$semester', '$class', '$room', '$department')";
if ($conn->query($sql) === TRUE) {
  echo "success";
} else {
  echo "error: " . $conn->error;
}

$conn->close();
?>
