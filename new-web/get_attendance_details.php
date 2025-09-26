<?php
$rollnumber = $_GET['rollnumber'];
$subject = $_GET['subject'];
$faculty = $_GET['faculty'];

$conn = new mysqli("localhost", "root", "", "hod");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT date, attendance_status
    FROM attendance
    WHERE roll_no = ? AND subject = ? AND faculty = ?
    ORDER BY date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $rollnumber, $subject, $faculty);
$stmt->execute();
$result = $stmt->get_result();

$details = [];
 
while ($row = $result->fetch_assoc()) {
    $details[] = [
        'date' => date("d-m-Y", strtotime($row['date'])),
        'attendance_status' => $row['attendance_status']
    ];
}


$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($details);
?>
