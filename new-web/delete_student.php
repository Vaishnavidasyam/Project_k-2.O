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

// Get the roll number from the POST request
$roll = $_POST['roll'];

// Check if the roll number is provided
if (!empty($roll)) {
  // Prepare the SQL query to delete the student from the 'students' table
  $query = "DELETE FROM students WHERE roll = ?";
  
  // Prepare the statement
  if ($stmt = $conn->prepare($query)) {
    // Bind the roll number to the query
    $stmt->bind_param("s", $roll);
    
    // Execute the query
    if ($stmt->execute()) {
      // Return success message if deletion is successful
      echo json_encode(["success" => true]);
    } else {
      // Return error message if the query fails
      echo json_encode(["success" => false, "error" => "Failed to delete the student."]);
    }
    
    // Close the statement
    $stmt->close();
  } else {
    echo json_encode(["success" => false, "error" => "Failed to prepare the query."]);
  }
} else {
  echo json_encode(["success" => false, "error" => "Roll number is required."]);
}

// Close the database connection
$conn->close();
?>
