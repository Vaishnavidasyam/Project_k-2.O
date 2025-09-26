<?php
$conn = new mysqli("localhost", "root", "", "hod");
if ($conn->connect_error) {
  die(json_encode(["success" => false, "message" => "DB error"]));
}

$semester = $_POST['semester'] ?? '';
$class = $_POST['class'] ?? '';
$department = $_POST['department'] ?? '';
$room = $_POST['room'] ?? '';

$sql = "UPDATE students SET room=? WHERE semester=? AND class=? AND department=? AND (room IS NULL OR room = '')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $room, $semester, $class, $department);
$stmt->execute();

echo json_encode(["success" => true]);
$conn->close();
?>
