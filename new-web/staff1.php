<?php
session_start();
if (!isset($_SESSION['faculty_name'])) {
    header("Location: faclogin.html");
    exit();
}
$facultyName = $_SESSION['faculty_name'];
include("staff1.html");
?>
