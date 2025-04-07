<?php
include '../config.php';
if ($_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}
$stmt = mysqli_prepare($conn, "SELECT teacher_id FROM teachers WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$teacher_id = mysqli_fetch_assoc($result)['teacher_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    // Check if it's a bulk attendance submission
    if (isset($_POST['bulk_attendance'])) {
        $statuses = $_POST['status'];
        foreach ($statuses as $student_id => $status) {
            // Verify student exists before processing
            $check_student = mysqli_prepare($conn, "SELECT student_id FROM students WHERE student_id = ?");
            mysqli_stmt_bind_param($check_student, "s", $student_id);
            mysqli_stmt_execute($check_student);
            $student_result = mysqli_stmt_get_result($check_student);

            if (mysqli_num_rows($student_result) == 0) {
                header("Location: teacher_dashboard.php?error=Invalid student ID: " . htmlspecialchars($student_id));
                exit();
            }

            $status = mysqli_real_escape_string($conn, $status);

            // Validate attendance status
            $valid_statuses = array('Present', 'Absent', 'Leave', 'Holiday');
            if (!in_array($status, $valid_statuses)) {
                header("Location: teacher_dashboard.php?error=Invalid attendance status");
                exit();
            }

            // Check if attendance already exists for this date
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM attendance WHERE student_id = ? AND date = ?");
            mysqli_stmt_bind_param($check_stmt, "ss", $student_id, $date);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {
                // Update existing attendance
                $update_stmt = mysqli_prepare($conn, "UPDATE attendance SET status = ? WHERE student_id = ? AND date = ?");
                mysqli_stmt_bind_param($update_stmt, "sss", $status, $student_id, $date);
                mysqli_stmt_execute($update_stmt);
            } else {
                // Insert new attendance
                $insert_stmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, date, status, teacher_id) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($insert_stmt, "ssss", $student_id, $date, $status, $teacher_id);
                mysqli_stmt_execute($insert_stmt);
            }
        }
        header("Location: teacher_dashboard.php?success=Bulk attendance recorded");
    } else {
        // Single student attendance
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Check if attendance already exists for this date
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM attendance WHERE student_id = ? AND date = ?");
        mysqli_stmt_bind_param($check_stmt, "ss", $student_id, $date);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            // Update existing attendance
            $update_stmt = mysqli_prepare($conn, "UPDATE attendance SET status = ? WHERE student_id = ? AND date = ?");
            mysqli_stmt_bind_param($update_stmt, "sss", $status, $student_id, $date);

            if (mysqli_stmt_execute($update_stmt)) {
                header("Location: teacher_dashboard.php?success=Attendance updated");
            } else {
                echo "Error updating attendance: " . mysqli_error($conn);
            }
        } else {
            // Insert new attendance
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, date, status, teacher_id) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "ssss", $student_id, $date, $status, $teacher_id);

            if (mysqli_stmt_execute($insert_stmt)) {
                header("Location: teacher_dashboard.php?success=Attendance recorded");
            } else {
                echo "Error recording attendance: " . mysqli_error($conn);
            }
        }
    }
}
mysqli_close($conn);
