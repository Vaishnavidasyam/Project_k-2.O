<?php
// load_attendance.php

// Database connection
$servername = "localhost";
$username = "root";  // your username
$password = "";      // your password
$dbname = "hod";  // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters from the URL
$facultyName = $_GET['facultyName'];
$semester = $_GET['semester'];
$class = $_GET['class'];
$subject = $_GET['subject'];
$date = $_GET['date'];

// Query to get students based on semester and class
$sql = "SELECT name, roll FROM students WHERE semester = ? AND class = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $semester, $class);
$stmt->execute();
$result = $stmt->get_result();

$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Return the data as JSON
echo json_encode($students);

// Close the connection
$stmt->close();
$conn->close();
?>
