<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'hod';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Get input
$data = json_decode(file_get_contents("php://input"), true);
$semester   = $data["semester"] ?? null;
$class      = $data["class"] ?? null;
$room       = $data["room"] ?? null;
$department = $data["department"] ?? null;
$type       = $data["type"] ?? null;
$date       = $data["date"] ?? null;

if (!$semester || !$class || !$room || !$department || !$type || !$date) {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

// Store semester date
$sqlCheck = "SELECT id FROM semester_dates WHERE semester=? AND class=? AND room=? AND department=?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ssss", $semester, $class, $room, $department);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    $field = $type === "start" ? "start_date" : "end_date";
    $sqlUpdate = "UPDATE semester_dates SET $field=? WHERE semester=? AND class=? AND room=? AND department=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sssss", $date, $semester, $class, $room, $department);
    $stmtUpdate->execute();
} else {
    $startDate = ($type === "start") ? $date : null;
    $endDate   = ($type === "end") ? $date : null;
    $sqlInsert = "INSERT INTO semester_dates (semester, class, room, department, start_date, end_date)
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ssssss", $semester, $class, $room, $department, $startDate, $endDate);
    $stmtInsert->execute();
}

// 🎯 Advance semester logic (when type is "end")
function getNextSemester($current) {
    list($year, $part) = explode('-', $current);
    if ($part == '1') {
        return "$year-2";
    } else {
        return ((int)$year + 1) . "-1";
    }
}

if ($type === "end") {
    $nextSemester = getNextSemester($semester);

    // ✅ Update students to next semester
    $sqlUpdateStudents = "UPDATE students SET semester=?, room='' WHERE semester=? AND class=? AND room=? AND department=?";
$stmtStudents = $conn->prepare($sqlUpdateStudents);
$stmtStudents->bind_param("sssss", $nextSemester, $semester, $class, $room, $department);
$stmtStudents->execute();

 // ✅ Delete the entire row from semester_dates
$sqlDeleteSemesterDate = "DELETE FROM semester_dates WHERE semester=? AND class=? AND room=? AND department=?";
$stmtDeleteSemester = $conn->prepare($sqlDeleteSemesterDate);
$stmtDeleteSemester->bind_param("ssss", $semester, $class, $room, $department);
$stmtDeleteSemester->execute();


// ✅ Delete the full row from log table
$sqlDeleteLogRow = "DELETE FROM log WHERE semester=? AND class=? AND department=?";
$stmtDeleteLog = $conn->prepare($sqlDeleteLogRow);
$stmtDeleteLog->bind_param("sss", $semester, $class, $department);
$stmtDeleteLog->execute();


    // ❌ Delete timetable data
    $sqlDeleteTimetable = "DELETE FROM timetable WHERE semester=? AND class=? AND room=? AND department=?";
    $stmtTimetable = $conn->prepare($sqlDeleteTimetable);
    $stmtTimetable->bind_param("ssss", $semester, $class, $room, $department);
    $stmtTimetable->execute();

    // ❌ Delete from faculty_subjects (subfac)
    $sqlDeleteSubFac = "DELETE FROM subfac WHERE semester=? AND class=? AND room=? AND department=?";
    $stmtSubFac = $conn->prepare($sqlDeleteSubFac);
    $stmtSubFac->bind_param("ssss", $semester, $class, $room, $department);
    $stmtSubFac->execute();

    // ❌ Delete from subjects table
    $sqlDeleteSubjects = "DELETE FROM subjects WHERE semester=? AND class=? AND room=? AND department=?";
    $stmtSubjects = $conn->prepare($sqlDeleteSubjects);
    $stmtSubjects->bind_param("ssss", $semester, $class, $room, $department);
    $stmtSubjects->execute();
}

echo json_encode(["status" => "success", "message" => "Semester processing completed."]);
$conn->close();
?>
