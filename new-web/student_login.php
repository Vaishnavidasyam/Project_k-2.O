<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollnumber = strtoupper($_POST['rollnumber']); // Normalize input
    $password = $_POST['password'];

    // Prepare SQL query with UPPER() for case-insensitive match
    $sql = "SELECT * FROM students WHERE UPPER(rollnumber) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rollnumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            $_SESSION['rollnumber'] = $row['rollnumber'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['department'] = $row['department']; // assuming this column exists

            $name = urlencode($row['name']);
            $roll = urlencode($row['rollnumber']);
            $department = urlencode($row['department']);

            echo "<script>
              alert('Login successful!');
              window.location.href = 'stu1.html?name={$name}&rollnumber={$roll}&department={$department}';
            </script>";
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='stulogin.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with this roll number!'); window.location.href='stulogin.html';</script>";
    }
}
?>
