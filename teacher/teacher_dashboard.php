<?php
include '..\config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: ..\login.php");
    exit();
}
$stmt = mysqli_prepare($conn, "SELECT teacher_id FROM teachers WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
if (!$row) {
    // Handle case where teacher record is not found
    session_destroy();
    header("Location: ../login.php?error=invalid_teacher");
    exit();
}
$teacher_id = $row['teacher_id'];

// Fetch teacher's full information including name
$teacher_stmt = mysqli_prepare($conn, "SELECT t.name, t.class_type, t.class_year FROM teachers t WHERE t.teacher_id = ?");
mysqli_stmt_bind_param($teacher_stmt, "s", $teacher_id);
mysqli_stmt_execute($teacher_stmt);
$teacher_result = mysqli_stmt_get_result($teacher_stmt);
$teacher_info = mysqli_fetch_assoc($teacher_result);
$teacher_name = $teacher_info['name'];
$class_type = $teacher_info['class_type'];
$class_year = $teacher_info['class_year'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1a2f4b, #2c5282);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .container {
            flex: 1;
            margin-top: 2rem;
            padding-bottom: 2rem;
            background: linear-gradient(135deg, rgb(255, 255, 255), #e8e8e0);
            border-radius: 15px;
            border: 2px solid #2c3e50;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
        }

        h1,
        h3,
        h4 {
            color: #000;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            color: #000;
            font-weight: 500;
        }

        .table th,
        .table td {
            padding: 15px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            text-align: left;
            color: #000;
        }

        .text-muted {
            font-size: 14px;
            color: #000 !important;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #333;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
            transition: background-color 0.2s ease;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-bordered {
            border: 2px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
        }

        .table-responsive {
            margin-bottom: 2rem;
            border-radius: 8px;
            overflow: hidden;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        h1 {
            font-family: 'Roboto', sans-serif;
            color: #333;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        h3 {
            font-family: 'Roboto', sans-serif;
            color: #000;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn {
            border-radius: 10px;
            padding: 12px;
        }

        .form-control {
            border-radius: 10px;
            font-size: 16px;
        }

        .text-muted {
            font-size: 14px;
        }

        .float-end {
            margin-top: 15px;
        }


        .btn-danger {
            font-size: 16px;
        }

        .alert {
            border-radius: 10px;
        }

        .teacher-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .teacher-name {
            color: red;
            font-size: 20px;
            font-weight: bold;
        }

        .teacher-class {
            color: green;
            font-size: 18px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Teacher Dashboard</h1>
        <div class="teacher-info">
            <div class="teacher-name"><?php echo $teacher_name; ?></div>
            <div class="teacher-class"><?php echo $class_type . " Department - " . $class_year . " Year"; ?></div>
        </div>
        <a href="..\logout.php" class="btn btn-danger float-end">Logout</a>
        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Record Attendance</h3>
                <form method="POST" action="record_attendance.php" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="student_id">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" required>
                                <div id="student_info" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                    <option value="Leave">Leave</option>
                                    <option value="Holiday">Holiday</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">Record Attendance</button>
                            </div>
                        </div>
                    </div>
                </form>

                <h3>Attendance Management</h3>
                <div class="mb-4">
                    <h4>View Attendance Report</h4>
                    <form id="viewAttendanceForm" class="row g-3">
                        <div class="col-md-6">
                            <label for="report_student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="report_student_id" name="student_id" required>
                        </div>
                        <div class="col-md-6">
                            <label for="month" class="form-label">Month</label>
                            <input type="month" class="form-control" id="month" name="month" required>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary d-block w-100" onclick="viewReport()">View Report</button>
                        </div>
                    </form>
                    <div id="attendanceReport" class="mt-4">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceData">
                            </tbody>
                        </table>
                    </div>
                    <script>
                        function viewReport() {
                            const studentId = document.getElementById('report_student_id').value;
                            const month = document.getElementById('month').value;
                            if (studentId && month) {
                                window.open(`get_attendance_report.php?student_id=${studentId}&month=${month}`, '_blank');
                            } else {
                                alert('Please fill in both Student ID and Month fields');
                            }
                        }
                    </script>
                </div>

                <h4>Recent Attendance Records</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($conn, "SELECT a.*, s.name as student_name FROM attendance a 
                            JOIN students s ON a.student_id = s.student_id 
                            WHERE a.teacher_id = ? AND s.class_type = ? AND s.class_year = ? 
                            AND TIMESTAMPDIFF(HOUR, a.date, NOW()) <= 24 
                            ORDER BY a.date DESC, TIMESTAMPDIFF(HOUR, a.date, NOW()) ASC");
                        mysqli_stmt_bind_param($stmt, "sss", $teacher_id, $class_type, $class_year);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>


                <h3>Vacation Calendar</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($conn, "SELECT * FROM vacations ORDER BY start_date");
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('student_id').addEventListener('input', function() {
            const studentId = this.value.trim();
            if (studentId) {
                fetch(`get_student_info.php?student_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        const infoDiv = document.getElementById('student_info');
                        if (data.success) {
                            infoDiv.textContent = `Student Name: ${data.student.name}`;
                            infoDiv.className = 'mt-2 text-success';
                        } else {
                            infoDiv.textContent = data.message;
                            infoDiv.className = 'mt-2 text-danger';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('student_info').textContent = 'Error fetching student information';
                    });
            } else {
                document.getElementById('student_info').textContent = '';
            }
        });
    </script>
    <h3 class="mt-4">Students in <?php echo $class_type . ' ' . $class_year; ?> Year</h3>
    <div class="table-responsive">
        <form action="record_attendance.php" method="post" id="bulkAttendanceForm">
            <input type="hidden" name="bulk_attendance" value="1">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="bulk_date" class="form-label">Select Date for Bulk Attendance:</label>
                    <input type="date" class="form-control" id="bulk_date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = mysqli_prepare($conn, "SELECT s.student_id, s.name, s.email FROM students s WHERE s.class_type = ? AND s.class_year = ? ORDER BY s.name");
                    mysqli_stmt_bind_param($stmt, "ss", $class_type, $class_year);
                    mysqli_stmt_execute($stmt);
                    $students_result = mysqli_stmt_get_result($stmt);
                    while ($student = mysqli_fetch_assoc($students_result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($student['email']) . "</td>";
                        echo "<td>";
                        echo "<select name='status[" . htmlspecialchars($student['student_id']) . "]' class='form-control'>";
                        echo "<option value='Present'>Present</option>";
                        echo "<option value='Absent'>Absent</option>";
                        echo "<option value='Leave'>Leave</option>";
                        echo "<option value='Holiday'>Holiday</option>";
                        echo "</select>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary mt-3">Submit Bulk Attendance</button>
        </form>
    </div>
</body>

</html>