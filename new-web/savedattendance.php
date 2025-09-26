<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters from the query string
$facultyName = isset($_GET['facultyName']) ? $_GET['facultyName'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$classParam = isset($_GET['class']) ? $_GET['class'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$dateParam = isset($_GET['date']) ? $_GET['date'] : '';

// Validate parameters if necessary
if (empty($facultyName) || empty($semester) || empty($classParam) || empty($subject) || empty($dateParam)) {
    echo json_encode(["error" => "Invalid parameters."]);
    exit();
}

// Prepare the SQL query to fetch attendance data
$query = "
    SELECT roll_no, attendance_status
    FROM attendance
    WHERE faculty = ? 
      AND semester = ? 
      AND class = ? 
      AND subject = ? 
      AND date = ?
";

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind the parameters to the prepared statement
$stmt->bind_param('sssss', $facultyName, $semester, $classParam, $subject, $dateParam);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if there are any results
if ($result->num_rows > 0) {
    // Initialize an array to store attendance data
    $attendanceData = [];
    
    // Fetch the results into an associative array
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
    
    // Return the attendance data as JSON
    echo json_encode($attendanceData);
} else {
    // If no records found, return an empty array
    echo json_encode([]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
