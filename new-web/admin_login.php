<?php
session_start();

$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL password
$dbname = "signin"; // connect to signin database

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower($_POST["email"]); // normalize to lowercase
    $password = $_POST["password"];

    // Use BINARY to enforce case-sensitivity on password only
    $query = "SELECT * FROM admin WHERE LOWER(email)='$email'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["admin"] = $row["name"];
            $encodedName = urlencode($row["name"]);
            $encodedDept = urlencode($row["department"]);
            header("Location: hodlog.html?name=$encodedName&department=$encodedDept");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='adlogin.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No user found with this email!'); window.location.href='adlogin.html';</script>";
        exit();
    }
}

?>
