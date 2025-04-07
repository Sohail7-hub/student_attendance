<?php
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$class_type = isset($_GET['class_type']) ? mysqli_real_escape_string($conn, $_GET['class_type']) : '';
$class_year = isset($_GET['class_year']) ? mysqli_real_escape_string($conn, $_GET['class_year']) : '';

if (empty($class_type) || empty($class_year)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}

$query = "SELECT student_id, name FROM students WHERE class_type = ? AND class_year = ? ORDER BY name ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $class_type, $class_year);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = [
        'student_id' => $row['student_id'],
        'name' => $row['name'] . ' (ID: ' . $row['student_id'] . ')'
    ];
}

header('Content-Type: application/json');
echo json_encode(empty($students) ? ['message' => 'No students found'] : $students);
exit();
