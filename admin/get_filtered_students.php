<?php
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get parameters from request
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
$class_type = isset($_POST['class_type']) ? $_POST['class_type'] : null;
$class_year = isset($_POST['class_year']) ? $_POST['class_year'] : null;
$student_id = isset($_POST['student_id']) ? $_POST['student_id'] : null;

// Validate required parameters
if (!$class_type || !$class_year) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Class type and year are required']);
    exit();
}

// Base query - Improved to ensure proper joining and filtering
$sql = "SELECT 
        s.name as student_name,
        s.student_id,
        s.class_type,
        s.class_year,
        COALESCE(a.date, aa.date) as date,
        COALESCE(a.status, aa.status) as status,
        COALESCE(t.name, u.username) as recorded_by,
        CASE WHEN a.date IS NOT NULL THEN 'Teacher' ELSE 'Admin' END as record_type,
        COUNT(DISTINCT COALESCE(a.date, aa.date)) as total_days,
        SUM(CASE WHEN COALESCE(a.status, aa.status) = 'present' THEN 1 ELSE 0 END) as present_days,
        SUM(CASE WHEN COALESCE(a.status, aa.status) = 'absent' THEN 1 ELSE 0 END) as absent_days,
        SUM(CASE WHEN COALESCE(a.status, aa.status) = 'leave' THEN 1 ELSE 0 END) as leave_days,
        ROUND((SUM(CASE WHEN COALESCE(a.status, aa.status) = 'present' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT COALESCE(a.date, aa.date)), 0)), 1) as attendance_percentage
        FROM students s
        LEFT JOIN attendance a ON s.student_id = a.student_id
        LEFT JOIN attendance_admin aa ON s.student_id = aa.student_id
        LEFT JOIN teachers t ON a.teacher_id = t.teacher_id
        LEFT JOIN users u ON aa.admin_id = u.id";

$conditions = [];
$params = [];
$types = '';

// Add class type condition
if ($class_type) {
    $conditions[] = "s.class_type = ?";
    $params[] = $class_type;
    $types .= 's';
}

// Add class year condition
if ($class_year) {
    $conditions[] = "s.class_year = ?";
    $params[] = $class_year;
    $types .= 's';
}

// Add student id condition if provided
if ($student_id) {
    $conditions[] = "s.student_id = ?";
    $params[] = $student_id;
    $types .= 'i'; // Changed from 's' to 'i' as student_id is an integer
}

// Add date range conditions - Improved to handle date filtering with better performance
if ($start_date && $end_date) {
    // Convert to proper date format for comparison
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    $conditions[] = "((a.date BETWEEN ? AND ?) OR (aa.date BETWEEN ? AND ?))";
    $params[] = $start_date;
    $params[] = $end_date;
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ssss';
} elseif ($start_date) {
    // Convert to proper date format for comparison
    $start_date = date('Y-m-d', strtotime($start_date));

    $conditions[] = "(a.date >= ? OR aa.date >= ?)";
    $params[] = $start_date;
    $params[] = $start_date;
    $types .= 'ss';
} elseif ($end_date) {
    // Convert to proper date format for comparison
    $end_date = date('Y-m-d', strtotime($end_date));

    $conditions[] = "(a.date <= ? OR aa.date <= ?)";
    $params[] = $end_date;
    $params[] = $end_date;
    $types .= 'ss';
}

// Make sure we only get records with valid dates and statuses
$conditions[] = "((a.date IS NOT NULL AND a.status IS NOT NULL) OR (aa.date IS NOT NULL AND aa.status IS NOT NULL))";

// Combine conditions
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Only group by student_id and name when no specific student is selected
if (!$student_id) {
    $sql .= " GROUP BY s.student_id, s.name";
}
$sql .= " ORDER BY s.name ASC, COALESCE(a.date, aa.date) DESC";

// Debug information
if (isset($_POST['debug'])) {
    error_log("SQL Query: " . $sql);
    error_log("Types: " . $types);
    error_log("Params: " . print_r($params, true));
}

// Add error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Prepare and execute statement
try {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
    }

    if (!empty($params)) {
        // Create references array for binding parameters properly
        $refs = array();
        $refs[0] = &$stmt;
        $refs[1] = &$types;

        // Create references to each parameter value
        foreach ($params as $key => &$value) {
            $refs[$key + 2] = &$value;
        }

        // Call bind_param with references to avoid issues
        if (!call_user_func_array('mysqli_stmt_bind_param', $refs)) {
            throw new Exception('Failed to bind parameters: ' . mysqli_stmt_error($stmt));
        }
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute query: ' . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        throw new Exception('Failed to get result: ' . mysqli_error($conn));
    }

    $records = [];
    $student_stats = [];

    // Process records
    $records = [];
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['student_name'])) {
            $records[] = [
                'student_id' => $row['student_id'],
                'student_name' => $row['student_name'],
                'class_type' => $row['class_type'],
                'class_year' => $row['class_year'],
                'date' => $row['date'],
                'status' => $row['status'],
                'recorded_by' => $row['recorded_by'],
                'record_type' => $row['record_type'],
                'attendance_percentage' => $row['attendance_percentage'],
                'absent_count' => $row['absent_days'],
                'total_days' => $row['total_days'],
                'present_days' => $row['present_days'],
                'leave_days' => $row['leave_days']
            ];
        }
    }

    // Set response headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');

    if (empty($records)) {
        echo json_encode([
            'status' => 'empty',
            'message' => 'No attendance records found for the selected criteria',
            'students' => []
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => count($records) . ' attendance records found',
            'students' => $records
        ]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
        'sql' => $sql,
        'types' => $types,
        'params_count' => count($params)
    ]);
    exit();
}
