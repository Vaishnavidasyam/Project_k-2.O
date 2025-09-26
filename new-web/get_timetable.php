<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "hod");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$sem = $_GET['semester']   ?? '';
$cls = $_GET['class']      ?? '';
$room = $_GET['room']      ?? '';
$dept = $_GET['department'] ?? '';

$sql = "SELECT day, time_slot, subject, faculty, two_sections 
        FROM timetable
        WHERE semester = ?
          AND class = ?
          AND room = ?
          AND department = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssss", $sem, $cls, $room, $dept);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}
$stmt->close();
$mysqli->close();

echo json_encode($out);
?>
