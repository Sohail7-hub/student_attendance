<?php
include '../config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'teacher' && $_SESSION['role'] != 'admin')) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['student_id']) || !isset($_GET['month'])) {
    echo '<div class="alert alert-danger">Missing required parameters</div>';
    exit();
}

$student_id = $_GET['student_id'];
$month = $_GET['month'];

// Get student information
$stmt = mysqli_prepare($conn, "SELECT name FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    echo '<div class="alert alert-danger">Student not found</div>';
    exit();
}

// Convert month to date range
$start_date = $month . '-01';
$end_date = date('Y-m-t', strtotime($start_date));

// Get total records count
$count_sql = "SELECT COUNT(*) as total FROM (
    SELECT date FROM attendance WHERE student_id = ? AND date BETWEEN ? AND ?
    UNION ALL
    SELECT date FROM attendance_admin WHERE student_id = ? AND date BETWEEN ? AND ?
) a";

$stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($stmt, 'ssssss', $student_id, $start_date, $end_date, $student_id, $start_date, $end_date);
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_records = mysqli_fetch_assoc($total_result)['total'];

// Pagination
$records_per_page = 10;
$total_pages = ceil($total_records / $records_per_page);
$current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT a.date, a.status, 
        CASE 
            WHEN a.teacher_id IS NOT NULL THEN CONCAT('Teacher - ', t.name)
            WHEN a.admin_id IS NOT NULL THEN 'Admin'
            ELSE 'System'
        END as recorded_by,
        COUNT(*) OVER () as total_days,
        SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) OVER () as total_presents,
        SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) OVER () as total_absents,
        SUM(CASE WHEN a.status = 'Leave' THEN 1 ELSE 0 END) OVER () as total_leaves
        FROM (
            SELECT date, status, teacher_id, NULL as admin_id
            FROM attendance 
            WHERE student_id = ? AND date BETWEEN ? AND ?
            UNION ALL
            SELECT date, status, NULL as teacher_id, admin_id
            FROM attendance_admin 
            WHERE student_id = ? AND date BETWEEN ? AND ?
        ) a
        LEFT JOIN teachers t ON a.teacher_id = t.teacher_id
        ORDER BY a.date ASC
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssssssii', $student_id, $start_date, $end_date, $student_id, $start_date, $end_date, $records_per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$attendance_records = [];
$total_days = 0;
$total_presents = 0;
$total_absents = 0;
$total_leaves = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $attendance_records[] = $row;
    if (isset($row['total_days'])) {
        $total_days = $row['total_days'];
        $total_presents = $row['total_presents'];
        $total_absents = $row['total_absents'];
        $total_leaves = $row['total_leaves'];
    }
}

// Calculate attendance percentage
$attendance_percentage = $total_days > 0 ? round(((($total_days - $total_absents) / $total_days) * 100), 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Attendance Report - <?php echo htmlspecialchars($student['name']); ?></title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }

        .attendance-summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .summary-box {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 150px;
            color: white;
            font-weight: bold;
        }

        .present-box {
            background-color: #28a745;
        }

        .absent-box {
            background-color: #dc3545;
        }

        .leave-box {
            background-color: #ffc107;
            color: black;
        }

        .status-present {
            background-color: #e8f5e9;
        }

        .status-absent {
            background-color: #ffebee;
        }

        .status-leave {
            background-color: #fff9c4;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .report-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .report-header h2 {
            color: #0066cc;
            background-color: #e6f2ff;
            padding: 10px;
            border-radius: 5px;
        }

        .report-header h4 {
            margin-top: 15px;
        }

        .report-header h5 {
            color: #cc0000;
            background-color: #ffebeb;
            padding: 8px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }

        .table th {
            background: #f8f9fa;
        }
        
        .attendance-percentage {
            color: red;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }

            .container {
                box-shadow: none;
            }

            body {
                padding: 0;
                background: white;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="report-header">
            <h2>Attendance Report</h2>
            <h4>Student: <?php echo htmlspecialchars($student['name']); ?> (ID: <?php echo htmlspecialchars($student_id); ?>)</h4>
            <h5>Month: <?php echo date('F Y', strtotime($start_date)); ?></h5>
            <h5>Overall Attendance: <span class="attendance-percentage"><?php echo $attendance_percentage; ?>%</span></h5>

            <div class="attendance-summary">
                <div class="summary-box present-box">
                    <div>Present</div>
                    <div><?php echo $total_presents; ?> days</div>
                </div>
                <div class="summary-box absent-box">
                    <div>Absent</div>
                    <div><?php echo $total_absents; ?> days</div>
                </div>
                <div class="summary-box leave-box">
                    <div>Leave</div>
                    <div><?php echo $total_leaves; ?> days</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendance_records)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No attendance records found for this period</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr class="status-<?php echo strtolower($record['status']); ?>">
                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                                <td><?php echo htmlspecialchars($record['recorded_by']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center mt-3">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?student_id=<?php echo $student_id; ?>&month=<?php echo $month; ?>&page=<?php echo ($current_page - 1); ?>">
                                Previous
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?student_id=<?php echo $student_id; ?>&month=<?php echo $month; ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?student_id=<?php echo $student_id; ?>&month=<?php echo $month; ?>&page=<?php echo ($current_page + 1); ?>">
                                Next
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

        <div class="mt-4 text-end no-print">
            <button onclick="window.print()" class="btn btn-primary">Print Report</button>
            <button onclick="window.close()" class="btn btn-secondary ms-2">Close</button>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>