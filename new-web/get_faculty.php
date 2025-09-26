<?php
$subject = $_GET['subject'];
// Fetch faculty based on subject
$stmt = $pdo->prepare("SELECT faculty_name FROM subfac WHERE subject = ?");
$stmt->execute([$subject]);
$faculty = $stmt->fetchColumn();
echo json_encode(['faculty' => $faculty]);
?>
