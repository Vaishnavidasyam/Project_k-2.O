<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod"; // or your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get query parameters
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$room = isset($_GET['room']) ? $_GET['room'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

// Prepare the SQL query with conditions based on provided parameters
$sql = "SELECT subject FROM subfac WHERE semester='$semester' 
                AND class='$class' 
                AND room='$room' 
                AND department='$department'";

$result = $conn->query($sql);

$subjects = [];

if ($result->num_rows > 0) {
  // Fetch each row and add to the subjects array
  while($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject'];
  }
}

$conn->close();

// Return subjects as a JSON response
echo json_encode($subjects);
?>
