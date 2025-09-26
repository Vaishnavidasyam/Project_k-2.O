<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod"; // Adjust your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$subject = $data['subject'];
$faculty = $data['faculty'];
$semester = $data['semester'];
$class = $data['class'];
$department = $data['department'];
$room = $data['room'];

// SQL query to check if faculty is already assigned
$query = "
    SELECT COUNT(*) AS count
    FROM subfac
    WHERE subject = ? 
    AND faculty = ?
    AND semester = ?
    AND class = ?
    AND department = ?
    AND room = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('ssssss', $subject, $faculty, $semester, $class, $department, $room);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();

// Return whether the faculty is already assigned
echo json_encode(['exists' => $count > 0]);

// Close the database connection
$stmt->close();
$conn->close();
?>
