<?php
// fetch_timetable.php
header('Content-Type: application/json');

// Get filters from URL parameters
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : ''; // Add subject filter

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod"; // Assuming the database is 'hod'

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL Query to fetch timetable data based on multiple filters (semester, class, room, department)
$sql = "SELECT * FROM timetable WHERE semester = ? AND class = ? AND room = ? AND department = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $semester, $class, $room, $department, $subject); // Add subject to query
$stmt->execute();
$result = $stmt->get_result();

// Fetch timetable data
$timetableData = [];
while ($row = $result->fetch_assoc()) {
    $timetableData[] = $row;
}

// Fetch faculty data for the given filters and subject from subfac table
$facultySql = "SELECT faculty FROM subfac WHERE semester = ? AND class = ? AND room = ? AND department = ? AND subject = ?";
$facultyStmt = $conn->prepare($facultySql);
$facultyStmt->bind_param("sssss", $semester, $class, $room, $department, $subject); // Add subject to query
$facultyStmt->execute();
$facultyResult = $facultyStmt->get_result();

// Fetch faculty data
$facultyData = [];
while ($faculty = $facultyResult->fetch_assoc()) {
    $facultyData[] = $faculty;
}

// Combine timetable and faculty data in response
$response = [
    'timetable' => $timetableData,
    'faculty' => $facultyData
];

// Return the response as JSON
echo json_encode($response);

// Close database connections
$stmt->close();
$facultyStmt->close();
$conn->close();
?>
