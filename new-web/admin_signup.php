<?php
$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL password
$dbname = "signin"; // connect to signin database

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $department = $_POST["department"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $checkQuery = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($result) > 0) {
        echo "Email already exists!";
        exit();
    }
// Check if department already has an admin
$checkDeptQuery = "SELECT * FROM admin WHERE department = '$department'";
$deptResult = mysqli_query($conn, $checkDeptQuery);
if (mysqli_num_rows($deptResult) > 0) {
    echo "<script>alert('An HOD is already registered for this department.'); window.location.href='adsignin.html';</script>";
    exit();
}

    // Insert into database
    $query = "INSERT INTO admin (name, email, department, password) VALUES ('$name', '$email', '$department', '$hashed_password')";
    if (mysqli_query($conn, $query)) {
        $encodedName = urlencode($name);
    $encodedDept = urlencode($department);
    header("Location: hodlog.html?name=$encodedName&department=$encodedDept");
    
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
