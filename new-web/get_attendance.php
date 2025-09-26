<?php
$rollnumber = $_GET['rollnumber'];

$conn = new mysqli("localhost", "root", "", "hod");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT 
        subject,
        faculty,
        COUNT(*) AS total_classes,
        SUM(CASE WHEN attendance_status = 'present' THEN 1 ELSE 0 END) AS classes_attended
    FROM attendance
    WHERE roll_no = ?
    GROUP BY subject, faculty
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rollnumber);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $percentage = $row['total_classes'] > 0
        ? round(($row['classes_attended'] / $row['total_classes']) * 100, 2)
        : 0;

    $data[] = [
        'subject' => $row['subject'],
        'faculty' => $row['faculty'],
        'total_classes' => $row['total_classes'],
        'classes_attended' => $row['classes_attended'],
        'percentage' => $percentage
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
