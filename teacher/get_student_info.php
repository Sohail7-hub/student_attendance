<?php
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['student_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Student ID is required']);
    exit();
}

$student_id = $_GET['student_id'];
$stmt = mysqli_prepare($conn, "SELECT name, class_type, class_year FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($student = mysqli_fetch_assoc($result)) {
    $student['class'] = $student['class_type'] . ' - ' . $student['class_year'] . ' Year';
    echo json_encode(['success' => true, 'student' => $student]);
} else {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
}