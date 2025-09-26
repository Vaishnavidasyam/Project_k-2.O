<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "signin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = trim($_POST['email']);
    $department = $_POST['department'];
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($department) || empty($password)) {
        echo "<script>alert('All fields are required.'); window.location.href='facsignin.html';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.location.href='facsignin.html';</script>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $checkStmt = $conn->prepare("SELECT id FROM faculty WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='facsignin.html';</script>";
        exit();
    }
    $checkStmt->close();

    $insertStmt = $conn->prepare("INSERT INTO faculty (name, email, department, password) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $name, $email, $department, $hashedPassword);

    if ($insertStmt->execute()) {
        $encodedName = urlencode($name);
        header("Location: staff1.html?name=$encodedName");
        exit();
    } else {
        error_log("Insert error: " . $insertStmt->error);
        echo "<script>alert('Something went wrong. Please try again later.'); window.location.href='facsignin.html';</script>";
    }

    $insertStmt->close();
}

$conn->close();
?>
