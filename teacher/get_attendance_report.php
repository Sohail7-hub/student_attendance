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

$sql = "SELECT a.date, a.status, 
        CASE 
            WHEN a.teacher_id IS NOT NULL THEN CONCAT('Teacher - ', t.name)
            WHEN a.admin_id IS NOT NULL THEN 'Admin'
            ELSE 'System'
        END as recorded_by
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
        ORDER BY a.date ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssssss', $student_id, $start_date, $end_date, $student_id, $start_date, $end_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$attendance_records = [];
while ($row = mysqli_fetch_assoc($result)) {
    $attendance_records[] = $row;
}
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

        .table th {
            background: #f8f9fa;
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
                            <tr>
                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                                <td><?php echo htmlspecialchars($record['recorded_by']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-end no-print">
            <button onclick="window.print()" class="btn btn-primary">Print Report</button>
            <button onclick="window.close()" class="btn btn-secondary ms-2">Close</button>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>