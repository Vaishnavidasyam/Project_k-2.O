<?php
// DB credentials
$host = 'localhost';
$db = 'hod'; // Database name
$user = 'root'; // Adjust if your MySQL user is different
$pass = '';     // Adjust if your MySQL password is set

header('Content-Type: application/json');

if (!isset($_GET['faculty'])) {
    echo json_encode(['error' => 'Faculty name is required']);
    exit;
}

$faculty = $_GET['faculty'];

try {
    // Connect to database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL query
    $stmt = $pdo->prepare("SELECT day, time_slot AS time, subject, semester, class AS section, room 
                           FROM timetable 
                           WHERE faculty = ?");
    $stmt->execute([$faculty]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
