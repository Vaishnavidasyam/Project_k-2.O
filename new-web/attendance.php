<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the query parameters
$semester = $_GET['semester'] ?? '';
$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$department = $_GET['department'] ?? '';

// Prepare the SQL query to fetch students based on query parameters
$query = "SELECT * FROM students WHERE semester = ? AND class = ? AND room = ? AND department = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $semester, $class, $room, $department);
$stmt->execute();

// Fetch the results
$result = $stmt->get_result();
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row; // Store the result as an array
}

// Return the data as JSON
echo json_encode(["students" => $students]);

// Close the statement and connection
$stmt->close();
$conn->close();
?>
