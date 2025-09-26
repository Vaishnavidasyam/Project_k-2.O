<?php
$conn = new mysqli("localhost", "root", "", "faculty");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$faculty = $_GET['faculty_name'];
$semester = $_GET['semester'];
$section = $_GET['section'];
$subject = $_GET['subject'];
$sql = "SELECT student_roll, student_name, classes_attended, total_classes,
               ROUND((classes_attended / total_classes) * 100, 2) AS percentage
        FROM attendance
        WHERE faculty = ? AND semester = ? AND section = ? AND subject = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $faculty, $semester, $section, $subject);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
$stmt->close();
$conn->close();
?>
