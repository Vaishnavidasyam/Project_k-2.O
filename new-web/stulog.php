<?php
// Get parameters from the URL
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "hod"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch room based on semester, class, and department
$sql = "SELECT room FROM log WHERE semester = ? AND class = ? AND department = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $semester, $class, $department);
$stmt->execute();
$result = $stmt->get_result();

// Default room value if not found
$room = "No room assigned";

// Check if a room was found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $room = $row['room'];
}

$conn->close();

// Redirect to the timetable page with all parameters including room
$url = "hodct3.html?semester=" . urlencode($semester) .
       "&class=" . urlencode($class) .
       "&name=" . urlencode($_GET['name']) .
       "&rollnumber=" . urlencode($_GET['rollnumber']) .
       "&department=" . urlencode($department) .
       "&room=" . urlencode($room);

// Perform redirection to the timetable page with room information
header("Location: " . $url);
exit();
?>
