<?php
$conn = new mysqli("localhost", "root", "", "faculty");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (
    !isset($_POST['faculty_name'], $_POST['semester'], $_POST['section'], $_POST['subject'],
            $_POST['roll'], $_POST['classes_attended'], $_POST['total_classes'], $_POST['name'])
) {
    echo "Missing fields!";
    exit;
}

$faculty = $_POST['faculty_name'];
$semester = $_POST['semester'];
$section = $_POST['section'];
$subject = $_POST['subject'];
$roll = $_POST['roll'];
$classes_attended = (int)$_POST['classes_attended'];
$total_classes = (int)$_POST['total_classes'];
$name = $_POST['name'];

$percentage = $total_classes > 0 ? round(($classes_attended / $total_classes) * 100, 2) : 0;

// Check if record exists
$check = $conn->prepare("SELECT * FROM attendance WHERE roll = ? AND faculty = ? AND semester = ? AND section = ? AND subject = ?");
$check->bind_param("sssss", $roll, $faculty, $semester, $section, $subject);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $sql = "UPDATE attendance SET name = ?, classes_attended = ?, total_classes = ?, percentage = ? 
            WHERE roll = ? AND faculty = ? AND semester = ? AND section = ? AND subject = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siissssss", $name, $classes_attended, $total_classes, $percentage,
                      $roll, $faculty, $semester, $section, $subject);
    if ($stmt->execute()) {
        echo "Updated attendance for $roll!";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
} else {
    // Insert new record
    $sql = "INSERT INTO attendance (roll, name, faculty, semester, section, subject, classes_attended, total_classes, percentage) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiii", $roll, $name, $faculty, $semester, $section, $subject, $classes_attended, $total_classes, $percentage);
    if ($stmt->execute()) {
        echo "Record inserted for $roll!";
    } else {
        echo "Error inserting record: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
