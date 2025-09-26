<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'hod';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$name = $data['name'] ?? '';
$department = $data['department'] ?? '';

// ✅ Your end semester logic here

// Redirect back
$encodedName = urlencode($name);
$encodedDept = urlencode($department);
echo json_encode(["status" => "success", "redirect" => "hodlog.html?name=$encodedName&department=$encodedDept"]);
?>
