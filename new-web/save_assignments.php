<?php
header('Content-Type: application/json');

// Database connection parameters
$host       = 'localhost';
$dbname     = 'hod';
$username   = 'root';
$password   = '';

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Read incoming JSON
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data) || empty($data)) {
        throw new Exception('Invalid input data');
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Prepare statements once
    $selectStmt = $pdo->prepare("
        SELECT `id`
          FROM `subfac`
         WHERE `subject`    = :subject
           AND `semester`   = :semester
           AND `class`      = :class
           AND `department` = :department
           AND `room`       = :room
    ");

    $updateStmt = $pdo->prepare("
        UPDATE `subfac`
           SET `faculty` = :faculty
         WHERE `id`      = :id
    ");

    $insertStmt = $pdo->prepare("
        INSERT INTO `subfac`
            (`subject`, `faculty`, `semester`, `class`, `department`, `room`)
        VALUES
            (:subject, :faculty, :semester, :class, :department, :room)
    ");

    // Loop through each assignment
    foreach ($data as $assignment) {
        // Validate required fields
        foreach (['subject','faculty','semester','class','department','room'] as $key) {
            if (!isset($assignment[$key]) || $assignment[$key] === '') {
                throw new Exception("Missing field: $key");
            }
        }

        // Check if existing
        $selectStmt->execute([
            ':subject'    => $assignment['subject'],
            ':semester'   => $assignment['semester'],
            ':class'      => $assignment['class'],
            ':department' => $assignment['department'],
            ':room'       => $assignment['room'],
        ]);

        $row = $selectStmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Update existing
            $updateStmt->execute([
                ':faculty' => $assignment['faculty'],
                ':id'      => $row['id']
            ]);
        } else {
            // Insert new
            $insertStmt->execute([
                ':subject'    => $assignment['subject'],
                ':faculty'    => $assignment['faculty'],
                ':semester'   => $assignment['semester'],
                ':class'      => $assignment['class'],
                ':department' => $assignment['department'],
                ':room'       => $assignment['room'],
            ]);
        }
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback if we began a transaction
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Return error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
