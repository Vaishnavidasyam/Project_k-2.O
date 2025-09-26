<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // update if needed
$dbname = "signin";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Convert email to lowercase for case-insensitive match
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    // Retrieve faculty details from database (lowercased in DB too)
    $stmt = $conn->prepare("SELECT id, name, password FROM faculty WHERE LOWER(email) = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashedPassword);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['faculty_id'] = $id;
            $_SESSION['faculty_name'] = $name;

            $encodedName = urlencode($name);
            header("Location: staff1.html?name=$encodedName");
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='faclogin.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='faclogin.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
