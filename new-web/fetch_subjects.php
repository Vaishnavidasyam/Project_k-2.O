<?php
// Database connection
$servername = "localhost";
$username = "root"; // Adjust with your database username
$password = ""; // Adjust with your database password
$dbname = "hod"; // Adjust with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get query parameters
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : ''; 
$department = isset($_GET['department']) ? $_GET['department'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';

// SQL query to fetch subjects based on parameters
$query = "
    SELECT subject_name
    FROM subjects
    WHERE semester = ? 
    AND class = ?
    AND department = ?
    AND room = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('ssss', $semester, $class, $department, $room);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}

// Return subjects as JSON
echo json_encode($subjects);

// Close the database connection
$stmt->close();
$conn->close();
?>
