<?php
include '..\config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: ..\login.php");
    exit();
}
$stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student_id = mysqli_fetch_assoc($result)['student_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1a2f4b, #2c5282);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid #2c3e50;
        }

        .table-container {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }

        .table-container .table {
            margin-top: 0;
            margin-bottom: 0;
        }

        .table-container thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table {
            margin-top: 20px;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .table thead {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            font-weight: 600;
        }

        .table tbody tr:nth-child(odd) {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
            transform: scale(1.01);
        }

        .btn {
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        h3 {
            color: #444;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        h3 i {
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1><i class="bi bi-mortarboard-fill"></i>Student Dashboard</h1>
        <a href="..\logout.php" class="btn btn-danger float-end"><i class="bi bi-box-arrow-right"></i> Logout</a>
        <h3><i class="bi bi-person-vcard"></i>Your Details</h3>
        <?php
        $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id=?");
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        echo "<p>Student ID: " . htmlspecialchars($student_id) . "</p>";
        echo "<p>Name: " . htmlspecialchars($row['name']) . "</p>";
        echo "<p>Age: " . htmlspecialchars($row['age']) . "</p>";
        echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
        echo "<p>Class: " . htmlspecialchars($row['class_type']) . " - " . htmlspecialchars($row['class_year']) . " Year</p>";
        ?>
        <h3>Attendance</h3>
        <div class="table-container">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = mysqli_prepare($conn, "SELECT date, DAYNAME(date) as day_name, status FROM (
                        SELECT date, status FROM attendance WHERE student_id=?
                        UNION ALL
                        SELECT date, status FROM attendance_admin WHERE student_id=?
                    ) combined_attendance ORDER BY date DESC");
                    mysqli_stmt_bind_param($stmt, "ss", $student_id, $student_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['day_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h3><i class="bi bi-calendar-event"></i>Vacation Calendar</h3>
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
                $stmt = mysqli_prepare($conn, "SELECT title, description, start_date, end_date FROM vacations WHERE end_date >= CURDATE() ORDER BY start_date ASC");
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
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>