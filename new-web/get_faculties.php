<?php
// Database connection details
$servername = "localhost";  // Change this if your DB is hosted elsewhere
$username = "root";         // Replace with your DB username
$password = "";             // Replace with your DB password
$dbname = "signin";         // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch faculty names
$sql = "SELECT name FROM faculty";
$result = $conn->query($sql);

$faculties = [];
if ($result->num_rows > 0) {
    // Fetch all faculty names into an array
    while ($row = $result->fetch_assoc()) {
        $faculties[] = $row['name'];
    }
}

// Return the list of faculties as JSON
echo json_encode($faculties);

// Close the connection
$conn->close();
?>
