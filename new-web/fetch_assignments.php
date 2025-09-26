<?php
// fetch_assignments.php
header('Content-Type: application/json');
$conn = new mysqli('localhost','root','','hod');
if($conn->connect_error) exit(json_encode([]));

$sem = $_GET['semester']  ?? '';
$cls = $_GET['class']     ?? '';
$dept= $_GET['department']?? '';
$room= $_GET['room']      ?? '';

$stmt = $conn->prepare(
  "SELECT subject, faculty
     FROM subfac
    WHERE semester=? AND class=? AND department=? AND room=?"
);
$stmt->bind_param("ssss",$sem,$cls,$dept,$room);
$stmt->execute();
$stmt->bind_result($subject,$faculty);

$out = [];
while($stmt->fetch()){
  $out[] = ['subject'=>$subject,'faculty'=>$faculty];
}
$stmt->close();
$conn->close();

echo json_encode($out);
