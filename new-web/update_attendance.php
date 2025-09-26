<?php
$host = "localhost";         // or "127.0.0.1"
$user = "root";              // default username for XAMPP
$password = "";              // default password for XAMPP is empty
$database = "hod";           // your database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $roll = $_POST['roll'];
    $attendance = $_POST['attendance'];
    $faculty = $_POST['faculty'];
    $semester = $_POST['semester'];
    $class = $_POST['class'];
    $subject = $_POST['subject'];
    $date = $_POST['date'];

    // Check if attendance already exists for this roll/date/subject
    $check = $conn->prepare("SELECT * FROM attendance WHERE roll_no = ? AND date = ? AND subject = ?");
    $check->bind_param("sss", $roll, $date, $subject);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE attendance SET attendance_status = ? WHERE roll_no = ? AND date = ? AND subject = ?");
        $stmt->bind_param("ssss", $attendance, $roll, $date, $subject);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO attendance (student_name, roll_no, attendance_status, date, subject, faculty, semester, class) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $roll, $attendance, $date, $subject, $faculty, $semester, $class);
    }

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $check->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
