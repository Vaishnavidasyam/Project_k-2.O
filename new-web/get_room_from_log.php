<?php
$semester = $_GET['semester'] ?? '';
$class = $_GET['class'] ?? '';

$conn = new mysqli("localhost", "root", "", "hod");
if ($conn->connect_error) {
  die(json_encode(["success" => false, "message" => "DB error"]));
}

$sql = "SELECT room FROM log WHERE semester=? AND class=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $semester, $class);
$stmt->execute();
$result = $stmt->get_result();

$response = ["room" => null];
if ($row = $result->fetch_assoc()) {
  $response["room"] = $row["room"];
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
