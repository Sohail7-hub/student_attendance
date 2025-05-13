<?php
include '../config.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Get parameters
$student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : null;
$start_date = isset($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : null;
$end_date = isset($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : null;
$page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
$records_per_page = 10;

if (!$student_id) {
    echo json_encode(['error' => 'Student ID is required']);
    exit();
}

// Initialize response array
$response = [
    'summary' => [
        'present' => 0,
        'absent' => 0,
        'leave' => 0,
        'total_days' => 0,
        'attendance_rate' => 0
    ],
    'records' => []
];

// Build the query for overall summary
$summary_query = "SELECT status, COUNT(*) as count 
                 FROM attendance 
                 WHERE student_id = ?";
$summary_params = [$student_id];
$summary_types = 's';

if ($start_date) {
    $summary_query .= " AND date >= ?";
    $summary_params[] = $start_date;
    $summary_types .= 's';
}

if ($end_date) {
    $summary_query .= " AND date <= ?";
    $summary_params[] = $end_date;
    $summary_types .= 's';
}

$summary_query .= " GROUP BY status";

// Build the query for detailed records
$query = "SELECT a.*, t.name as teacher_name 
          FROM attendance a 
          LEFT JOIN teachers t ON a.teacher_id = t.teacher_id 
          WHERE a.student_id = ?";
$params = [$student_id];
$types = 's';

if ($start_date) {
    $query .= " AND a.date >= ?";
    $params[] = $start_date;
    $types .= 's';
}

if ($end_date) {
    $query .= " AND a.date <= ?";
    $params[] = $end_date;
    $types .= 's';
}

$query .= " ORDER BY a.date DESC";

try {
    // Get overall summary first
    $summary_stmt = mysqli_prepare($conn, $summary_query);
    mysqli_stmt_bind_param($summary_stmt, $summary_types, ...$summary_params);
    mysqli_stmt_execute($summary_stmt);
    $summary_result = mysqli_stmt_get_result($summary_stmt);

    // Process summary results
    while ($row = mysqli_fetch_assoc($summary_result)) {
        $status = strtolower($row['status']);
        if (isset($response['summary'][$status])) {
            $response['summary'][$status] = $row['count'];
        }
    }

    // Get total records count for pagination
    $count_query = "SELECT COUNT(*) as total FROM (" . $query . ") as subquery";
    $count_stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_records / $records_per_page);

    // Add pagination to the main query
    $offset = ($page - 1) * $records_per_page;
    $query .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $records_per_page;
    $types .= 'ii';

    // Prepare and execute the query for detailed records
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Add to records array
        while ($row = mysqli_fetch_assoc($result)) {
            $response['records'][] = [
                'date' => $row['date'],
                'status' => $row['status'],
                'recorded_by' => $row['teacher_name'] ?? 'Unknown',
                'type' => $row['type'] ?? 'Regular'
            ];
        }
    }

    // Calculate total days excluding holidays
    if ($start_date && $end_date) {
        // First calculate total calendar days
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        $calendar_days = $interval->days + 1;
        
        // Get holidays between start and end date
        $holiday_query = "SELECT COUNT(*) as holiday_count FROM (
            SELECT DATEDIFF(LEAST(v.end_date, ?), GREATEST(v.start_date, ?)) + 1 as days
            FROM vacations v
            WHERE (v.start_date <= ? AND v.end_date >= ?)
        ) as holiday_dates";
        
        $holiday_stmt = mysqli_prepare($conn, $holiday_query);
        mysqli_stmt_bind_param($holiday_stmt, 'ssss', $end_date, $start_date, $end_date, $start_date);
        mysqli_stmt_execute($holiday_stmt);
        $holiday_result = mysqli_stmt_get_result($holiday_stmt);
        $holiday_count = mysqli_fetch_assoc($holiday_result)['holiday_count'];

        // Calculate total working days by subtracting holidays
        $total_days = $calendar_days - $holiday_count;
    } else {
        // If no date range specified, calculate from attendance records
        $total_days = $response['summary']['present'] + $response['summary']['absent'] + $response['summary']['leave'];
    }
    
    // Update response with total days and attendance rate
    $response['summary']['total_days'] = $total_days;
    $response['summary']['attendance_rate'] = $total_days > 0 ? round(($response['summary']['present'] / $total_days) * 100, 2) : 0;
    $response['summary']['holidays'] = isset($holiday_count) ? $holiday_count : 0;

    mysqli_stmt_close($stmt);

    // Add pagination info to response
    $response['pagination'] = [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'records_per_page' => $records_per_page,
        'total_records' => $total_records
    ];

    // Return the response
    echo json_encode($response);
    exit();
} catch (Exception $e) {
    error_log('Error in get_student_info.php: ' . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while fetching records']);
    exit();
}