<?php
// get_sub.php
// db_connection.php
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

// Get query parameters
$faculty = $_GET['name'];
$semester = $_GET['semester'];
$class = $_GET['class'];

// Query the subfac table for matching entries
$sql = "SELECT subject FROM subfac WHERE faculty = ? AND semester = ? AND class = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $faculty, $semester, $class);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the subject if found
    $row = $result->fetch_assoc();
    $subject = $row['subject'];

    // Replace spaces with dashes
    $subject = str_replace(" ", "-", $subject);

    echo json_encode(['subject' => $subject]);
} else {
    // No subject found
    echo json_encode(['subject' => null]);
}

$stmt->close();
$conn->close();
?>
