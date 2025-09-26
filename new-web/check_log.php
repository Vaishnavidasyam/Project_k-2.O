<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod";

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$semester = htmlspecialchars($_GET['semester']);
$class = htmlspecialchars($_GET['class']);
$room = htmlspecialchars($_GET['room']);

$exact_match = false;
$room_conflict = false;
$semclass_conflict = false;

// 1. ✅ Check if exact row exists (same semester, class, and room)
$sql_exact = "SELECT * FROM log WHERE semester = ? AND class = ? AND room = ?";
$stmt = $conn->prepare($sql_exact);
$stmt->bind_param("sss", $semester, $class, $room);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $exact_match = true;
} else {
  // 2. ❌ Check if this room is used for a different semester/class
  $sql_room = "SELECT * FROM log WHERE room = ? AND (semester != ? OR class != ?)";
  $stmt2 = $conn->prepare($sql_room);
  $stmt2->bind_param("sss", $room, $semester, $class);
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  if ($result2->num_rows > 0) {
    $room_conflict = true;
  }
  $stmt2->close();

  // 3. ❌ Check if this semester/class exists with a different room
  $sql_semclass = "SELECT * FROM log WHERE semester = ? AND class = ? AND room != ?";
  $stmt3 = $conn->prepare($sql_semclass);
  $stmt3->bind_param("sss", $semester, $class, $room);
  $stmt3->execute();
  $result3 = $stmt3->get_result();
  if ($result3->num_rows > 0) {
    $semclass_conflict = true;
  }
  $stmt3->close();
}

$stmt->close();
$conn->close();

echo json_encode([
  "exact_match" => $exact_match,
  "room_conflict" => $room_conflict,
  "semclass_conflict" => $semclass_conflict
]);
?>
