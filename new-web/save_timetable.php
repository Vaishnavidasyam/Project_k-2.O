<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hod";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$conn->begin_transaction();

try {
    foreach ($data as $entry) {
        $day = $entry['day'];
        $time = $entry['time'];
        $subject = $entry['subject'];
        $twoSections = $entry['twoSections'] ? 1 : 0;
        $semester = $entry['semester'];
        $class = $entry['class'];
        $room = $entry['room'];
        $department = $entry['department'];

        // Get the faculty for this subject
        $facultyQuery = $conn->prepare("SELECT faculty FROM subfac 
            WHERE subject=? AND semester=? AND class=? AND room=? AND department=?");
        $facultyQuery->bind_param("sssss", $subject, $semester, $class, $room, $department);
        $facultyQuery->execute();
        $facultyResult = $facultyQuery->get_result();

        if ($facultyResult->num_rows == 0) {
            throw new Exception("Faculty not assigned to this subject.");
        }

        $faculty = $facultyResult->fetch_assoc()['faculty'];

        // Check for existing timetable entry (to update vs insert)
        $existingCheck = $conn->prepare("SELECT * FROM timetable 
            WHERE day=? AND time_slot=? AND semester=? AND class=? AND room=? AND department=?");
        $existingCheck->bind_param("ssssss", $day, $time, $semester, $class, $room, $department);
        $existingCheck->execute();
        $existingResult = $existingCheck->get_result();
        $isExisting = $existingResult->num_rows > 0;

        // Check for faculty conflict (exclude same entry to avoid false positive)
        $conflictCheck = $conn->prepare("SELECT * FROM timetable 
            WHERE faculty=? AND day=? AND time_slot=? 
              AND NOT (semester=? AND class=? AND room=? AND department=?)");
        $conflictCheck->bind_param("sssssss", $faculty, $day, $time, $semester, $class, $room, $department);
        $conflictCheck->execute();
        $conflictResult = $conflictCheck->get_result();

        if ($conflictResult->num_rows > 0) {
            $conflictRow = $conflictResult->fetch_assoc();
            throw new Exception("Faculty $faculty is already assigned to {$conflictRow['semester']} semester, {$conflictRow['class']} class, {$conflictRow['room']} room at {$conflictRow['time_slot']} on {$conflictRow['day']}.");
        }

        if ($isExisting) {
            // Update the existing entry
            $update = $conn->prepare("UPDATE timetable 
                SET subject=?, two_sections=?, faculty=? 
                WHERE day=? AND time_slot=? AND semester=? AND class=? AND room=? AND department=?");
            $update->bind_param("sisssssss", $subject, $twoSections, $faculty, $day, $time, $semester, $class, $room, $department);
            $update->execute();
        } else {
            // Insert new entry
            $insert = $conn->prepare("INSERT INTO timetable 
                (day, time_slot, subject, two_sections, semester, class, room, department, faculty) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("sssisssss", $day, $time, $subject, $twoSections, $semester, $class, $room, $department, $faculty);
            $insert->execute();
        }
    }

    $conn->commit();
    echo "Success";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
