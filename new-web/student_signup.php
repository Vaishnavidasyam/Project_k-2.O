<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $rollnumber = $_POST['rollnumber'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $department = $_POST['department']; // Capture the department

    // Check if roll number or email already exists
    $check_query = "SELECT * FROM students WHERE rollnumber = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $rollnumber, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Roll number or email already registered.'); window.location.href='stusignin.html';</script>";
    } else {
        // Insert new student record
        $sql = "INSERT INTO students (name, rollnumber, email, password, department) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $rollnumber, $email, $password, $department);

        if ($stmt->execute()) {
            echo "<script>alert('Signup successful! Redirecting to dashboard...'); window.location.href='stu1.html?name={$name}&rollnumber={$rollnumber}&department={$department}';</script>";
        } else {
            echo "<script>alert('Error signing up. Try again.'); window.location.href='stusignin.html';</script>";
        }
    }
}
?>
