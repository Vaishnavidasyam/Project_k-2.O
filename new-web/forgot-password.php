<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$host = 'localhost';
$dbname = 'signin';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $role = $_POST['role']; // 'admin', 'faculty', 'student'

    $table = '';
    if ($role === 'admin') {
        $table = 'admin';
    } elseif ($role === 'faculty') {
        $table = 'faculty';
    } elseif ($role === 'student') {
        $table = 'students';
    } else {
        echo "<script>alert('Invalid role'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $name = isset($user['name']) ? $user['name'] : $user['username']; // fallback if no 'name' field
        $password = $user['password'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; // Replace
            $mail->Password = 'your_app_password'; // Replace with App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'University System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Recovery';
            $mail->Body    = "Hello <b>{$name}</b>,<br><br>Your password is: <b>{$password}</b><br><br>Please change your password after logging in for security.";

            $mail->send();
            echo "<script>alert('Password sent to your email.'); window.location.href = '{$_SERVER['HTTP_REFERER']}';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Mail could not be sent. Error: {$mail->ErrorInfo}'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with this email in $table table.'); window.history.back();</script>";
    }
}
?>
