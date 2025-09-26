<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Get POST data
$name = $_POST['name'];
$roll = $_POST['roll'];
$semester = $_POST['semester'];
$class = $_POST['class'];
$room = $_POST['room'];
$department = $_POST['department'];

// Validate inputs
if (empty($name) || empty($roll) || empty($semester) || empty($class) || empty($room) || empty($department)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

// Prepare the SQL query with placeholders
$sql = "INSERT INTO students (name, roll, semester, class, room, department) VALUES (?, ?, ?, ?, ?, ?)";

// Prepare the statement
if ($stmt = $conn->prepare($sql)) {
    // Bind parameters to the query
    $stmt->bind_param("ssssss", $name, $roll, $semester, $class, $room, $department);

    // Execute the query
   if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    if ($stmt->errno == 1062) {
        // Duplicate entry error
        echo json_encode(['success' => false, 'error' => 'Duplicate entry: Student already exists']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $stmt->error]);
    }
}

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $conn->error]);
}

// Close the connection
$conn->close();
?>
